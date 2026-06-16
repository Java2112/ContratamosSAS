<?php

namespace App\Domains\Disciplinary\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Contracting\Models\Employee;
use App\Domains\Disciplinary\Models\DisciplinaryRecord;
use App\Domains\Disciplinary\Models\DisciplinaryQuestion;
use App\Domains\Disciplinary\Models\DisciplinaryAnswer;
use App\Domains\Disciplinary\Models\DisciplinaryState;
use App\Domains\Disciplinary\Models\DisciplinaryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class DisciplinaryController extends Controller
{
    /**
     * Display the dashboard of disciplinary processes.
     */
    public function dashboard(Request $request)
    {
        Gate::authorize('viewAny', DisciplinaryRecord::class);

        $tenantId = auth()->user()->tenant_id;

        // KPI Counts
        $kpis = [
            'total' => DisciplinaryRecord::where('tenant_id', $tenantId)->count(),
            'borrador' => DisciplinaryRecord::where('tenant_id', $tenantId)->where('status', 'BORRADOR')->count(),
            'en_proceso' => DisciplinaryRecord::where('tenant_id', $tenantId)->where('status', 'EN_PROCESO')->count(),
            'finalizado' => DisciplinaryRecord::where('tenant_id', $tenantId)->where('status', 'FINALIZADO')->count(),
            'cerrado' => DisciplinaryRecord::where('tenant_id', $tenantId)->where('status', 'CERRADO')->count(),
        ];

        // Search active employees
        $search = $request->input('search');
        $searchHash = $search ? hash_hmac('sha256', $search, config('app.key') ?: 'base-salt-string') : null;

        $activeEmployees = Employee::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->when($search, function ($query, $search) use ($searchHash) {
                $query->where(function ($q) use ($search, $searchHash) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                    if ($searchHash) {
                        $q->orWhere('document_number_hash', $searchHash);
                    }
                });
            })
            ->limit(15)
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => "{$employee->first_name} {$employee->last_name}",
                    'document_number' => $employee->document_number,
                    'cargo' => $employee->cargo,
                    'client_name' => $employee->client ? $employee->client->business_name : 'Sede Principal',
                ];
            });

        // Records list
        $records = DisciplinaryRecord::where('tenant_id', $tenantId)
            ->with(['employee'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Disciplinary/Dashboard', [
            'kpis' => $kpis,
            'activeEmployees' => $activeEmployees,
            'records' => $records,
            'filters' => $request->only('search')
        ]);
    }

    /**
     * Show the form to create a new disciplinary record.
     */
    public function create(Employee $employee)
    {
        Gate::authorize('create', DisciplinaryRecord::class);

        $tenantId = auth()->user()->tenant_id;
        
        // Safety check: employee must be active
        if (!$employee->is_active) {
            return redirect()->route('disciplinary.dashboard')
                ->with('error', 'El empleado seleccionado no se encuentra activo.');
        }

        // Get Client Company
        $clientName = $employee->client ? $employee->client->business_name : 'Sede Principal';

        // Introductory default text
        $defaultIntroductoryText = "La presente diligencia de descargos se lleva a cabo en cumplimiento de las normas del Código Sustantivo del Trabajo colombianas, específicamente sobre la verificación de faltas laborales, garantizando el debido proceso (Artículo 29 de la Constitución Política) y el derecho a la defensa (Artículo 115 del Código Sustantivo del Trabajo). Se procede a escuchar al trabajador respecto a los hechos descritos.";

        // Default base questions
        $defaultQuestions = [
            "Indique su nombre completo, número de cédula, cargo que desempeña actualmente y fecha de ingreso a la compañía.",
            "Indique su dirección actual de residencia, número telefónico y correo electrónico de contacto.",
            "Relate detalladamente su versión de los hechos ocurridos en relación con el motivo del presente descargo disciplinario.",
            "¿Desea agregar, corregir o modificar algo más a lo manifestado en esta diligencia de descargos?"
        ];

        return Inertia::render('Disciplinary/Create', [
            'employee' => [
                'id' => $employee->id,
                'name' => "{$employee->first_name} {$employee->last_name}",
                'document_number' => $employee->document_number,
                'cargo' => $employee->cargo,
                'hired_at' => $employee->hired_at ? $employee->hired_at->format('d/m/Y') : 'N/A',
                'client_name' => $clientName
            ],
            'defaultIntroductoryText' => $defaultIntroductoryText,
            'defaultQuestions' => $defaultQuestions,
            'representativeName' => auth()->user()->name,
            'representativeRole' => auth()->user()->role ? auth()->user()->role->label() : 'Gestor de Descargos'
        ]);
    }

    /**
     * Store a newly created disciplinary record.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', DisciplinaryRecord::class);

        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'witness_name' => 'nullable|string|max:255',
            'representative_name' => 'required|string|max:255',
            'representative_role' => 'required|string|max:255',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'reason' => 'required|string|min:10',
            'rules_violated' => 'nullable|string',
            'introductory_text' => 'required|string',
            'questions' => 'required|array|min:1',
            'questions.*' => 'required|string'
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        if (!$employee->is_active) {
            return back()->with('error', 'Solo se pueden abrir procesos para empleados activos.');
        }

        // Generate consecutive record number: DESC-YYYY-0001
        $year = now()->year;
        $count = DisciplinaryRecord::whereYear('created_at', $year)->count() + 1;
        $recordNumber = 'DESC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $record = DB::transaction(function () use ($tenantId, $validated, $recordNumber) {
            // 1. Create record
            $record = DisciplinaryRecord::create([
                'tenant_id' => $tenantId,
                'employee_id' => $validated['employee_id'],
                'record_number' => $recordNumber,
                'witness_name' => $validated['witness_name'],
                'representative_name' => $validated['representative_name'],
                'representative_role' => $validated['representative_role'],
                'scheduled_date' => $validated['scheduled_date'],
                'scheduled_time' => $validated['scheduled_time'],
                'reason' => $validated['reason'],
                'rules_violated' => $validated['rules_violated'],
                'introductory_text' => $validated['introductory_text'],
                'status' => 'BORRADOR',
                'created_by' => auth()->id(),
            ]);

            // 2. Create questions
            foreach ($validated['questions'] as $index => $questionText) {
                DisciplinaryQuestion::create([
                    'disciplinary_record_id' => $record->id,
                    'question_text' => $questionText,
                    'sort_order' => $index + 1,
                    'is_from_template' => $index < 4 // The first 4 are the defaults
                ]);
            }

            // 3. Log initial state
            DisciplinaryState::create([
                'disciplinary_record_id' => $record->id,
                'state' => 'BORRADOR',
                'changed_by' => auth()->id(),
                'notes' => 'Apertura inicial del proceso disciplinario.',
            ]);

            return $record;
        });

        return redirect()->route('disciplinary.show', $record->id)
            ->with('success', "Proceso disciplinario {$recordNumber} creado exitosamente.");
    }

    /**
     * Display the full disciplinary record.
     */
    public function show(DisciplinaryRecord $record)
    {
        Gate::authorize('view', $record);

        $record->load([
            'employee',
            'questions.answer',
            'states.user',
            'files.generator',
            'creator'
        ]);

        // Get Client Company
        $clientName = $record->employee->client ? $record->employee->client->business_name : 'Sede Principal';

        return Inertia::render('Disciplinary/Show', [
            'record' => $record,
            'clientName' => $clientName,
        ]);
    }

    /**
     * Render the live questionnaire form.
     */
    public function form(DisciplinaryRecord $record)
    {
        Gate::authorize('update', $record);

        // Block if closed
        if (in_array($record->status, ['FINALIZADO', 'PDF_GENERADO', 'CERRADO'])) {
            return redirect()->route('disciplinary.show', $record->id)
                ->with('error', 'El proceso se encuentra finalizado o cerrado y no admite modificaciones.');
        }

        // Change status to EN_PROCESO when form is opened (if it was BORRADOR)
        if ($record->status === 'BORRADOR') {
            DB::transaction(function () use ($record) {
                $record->update(['status' => 'EN_PROCESO']);
                DisciplinaryState::create([
                    'disciplinary_record_id' => $record->id,
                    'state' => 'EN_PROCESO',
                    'changed_by' => auth()->id(),
                    'notes' => 'Ingreso al formulario de diligencia para registro de descargos.',
                ]);
            });
        }

        $record->load(['employee', 'questions.answer']);
        $clientName = $record->employee->client ? $record->employee->client->business_name : 'Sede Principal';

        return Inertia::render('Disciplinary/Form', [
            'record' => $record,
            'clientName' => $clientName,
        ]);
    }

    /**
     * Save dynamic questions and their answers.
     */
    public function saveQuestionsAndAnswers(Request $request, DisciplinaryRecord $record)
    {
        Gate::authorize('update', $record);

        if (in_array($record->status, ['FINALIZADO', 'PDF_GENERADO', 'CERRADO'])) {
            return response()->json(['error' => 'No se puede editar un proceso finalizado.'], 403);
        }

        $validated = $request->validate([
            'witness_name' => 'nullable|string|max:255',
            'representative_name' => 'required|string|max:255',
            'representative_role' => 'required|string|max:255',
            'reason' => 'required|string',
            'rules_violated' => 'nullable|string',
            'introductory_text' => 'required|string',
            'initial_observations' => 'nullable|string',
            'final_observations' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|integer',
            'questions.*.question_text' => 'required|string',
            'questions.*.sort_order' => 'required|integer',
            'questions.*.is_from_template' => 'required|boolean',
            'questions.*.answer.answer_text' => 'nullable|string'
        ]);

        DB::transaction(function () use ($record, $validated) {
            // Update main fields
            $record->update([
                'witness_name' => $validated['witness_name'],
                'representative_name' => $validated['representative_name'],
                'representative_role' => $validated['representative_role'],
                'reason' => $validated['reason'],
                'rules_violated' => $validated['rules_violated'],
                'introductory_text' => $validated['introductory_text'],
                'initial_observations' => $validated['initial_observations'],
                'final_observations' => $validated['final_observations'],
            ]);

            // Sync questions
            $incomingQuestionIds = collect($validated['questions'])->pluck('id')->filter()->toArray();

            // Delete questions not in the request
            DisciplinaryQuestion::where('disciplinary_record_id', $record->id)
                ->whereNotIn('id', $incomingQuestionIds)
                ->delete();

            // Insert/Update questions and answers
            foreach ($validated['questions'] as $qData) {
                if (isset($qData['id']) && $qData['id']) {
                    $question = DisciplinaryQuestion::findOrFail($qData['id']);
                    $question->update([
                        'question_text' => $qData['question_text'],
                        'sort_order' => $qData['sort_order'],
                    ]);
                } else {
                    $question = DisciplinaryQuestion::create([
                        'disciplinary_record_id' => $record->id,
                        'question_text' => $qData['question_text'],
                        'sort_order' => $qData['sort_order'],
                        'is_from_template' => $qData['is_from_template'],
                    ]);
                }

                // Answer sync
                $answerText = $qData['answer']['answer_text'] ?? '';
                DisciplinaryAnswer::updateOrCreate(
                    ['disciplinary_question_id' => $question->id],
                    ['answer_text' => $answerText]
                );
            }
        });

        return back()->with('success', 'Respuestas guardadas con éxito.');
    }

    /**
     * Transition status to FINALIZADO.
     */
    public function finalize(DisciplinaryRecord $record)
    {
        Gate::authorize('update', $record);

        if (in_array($record->status, ['FINALIZADO', 'PDF_GENERADO', 'CERRADO'])) {
            return redirect()->route('disciplinary.show', $record->id)
                ->with('error', 'El proceso ya ha sido finalizado previamente.');
        }

        $record->load('questions.answer');

        if ($record->questions->count() === 0) {
            return back()->with('error', 'Debe existir al menos una pregunta para poder finalizar la diligencia.');
        }

        // Validate that all questions have a non-empty answer
        foreach ($record->questions as $question) {
            if (!$question->answer || trim($question->answer->answer_text) === '') {
                return back()->with('error', "La pregunta N° {$question->sort_order} no cuenta con una respuesta registrada.");
            }
        }

        DB::transaction(function () use ($record) {
            $record->update(['status' => 'FINALIZADO']);
            
            DisciplinaryState::create([
                'disciplinary_record_id' => $record->id,
                'state' => 'FINALIZADO',
                'changed_by' => auth()->id(),
                'notes' => 'Finalización formal de la diligencia de descargos disciplinarios. Bloqueo de edición.',
            ]);
        });

        return redirect()->route('disciplinary.show', $record->id)
            ->with('success', 'La diligencia de descargos ha sido finalizada con éxito. Ahora puede proceder a generar el documento PDF.');
    }

    /**
     * Generate PDF and trigger download.
     */
    public function generatePdf(DisciplinaryRecord $record)
    {
        Gate::authorize('view', $record);

        // Must be FINALIZADO or later to generate PDF
        if (in_array($record->status, ['BORRADOR', 'EN_PROCESO'])) {
            return back()->with('error', 'Debe finalizar formalmente la diligencia de descargos antes de generar el PDF.');
        }

        $record->load([
            'employee',
            'questions.answer',
            'creator'
        ]);

        $clientName = $record->employee->client ? $record->employee->client->business_name : 'Sede Principal';

        $data = [
            'record' => $record,
            'employee' => $record->employee,
            'clientName' => $clientName,
            'date' => now()->format('d/m/Y'),
        ];

        // Renders PDFs using Blade
        $pdf = Pdf::loadView('pdfs.disciplinary-record', $data);

        // Control versions
        $currentVersion = DisciplinaryFile::where('disciplinary_record_id', $record->id)->max('version') ?? 0;
        $nextVersion = $currentVersion + 1;

        $fileName = "Descargos_{$record->record_number}_V{$nextVersion}.pdf";
        $folderPath = "tenant_" . auth()->user()->tenant_id . "/descargos";
        $fullPath = "{$folderPath}/{$fileName}";

        // Save file in Storage disk 'private'
        Storage::disk('private')->put($fullPath, $pdf->output());

        DB::transaction(function () use ($record, $fullPath, $fileName, $pdf, $nextVersion) {
            // Save log file
            DisciplinaryFile::create([
                'disciplinary_record_id' => $record->id,
                'file_path' => $fullPath,
                'file_name' => $fileName,
                'file_size' => strlen($pdf->output()),
                'version' => $nextVersion,
                'generated_by' => auth()->id(),
            ]);

            // Update status if it was FINALIZADO
            if ($record->status === 'FINALIZADO') {
                $record->update(['status' => 'PDF_GENERADO']);
                
                DisciplinaryState::create([
                    'disciplinary_record_id' => $record->id,
                    'state' => 'PDF_GENERADO',
                    'changed_by' => auth()->id(),
                    'notes' => "Documento PDF generado exitosamente (Versión {$nextVersion}).",
                ]);
            }
        });

        // Downloads independently
        return Storage::disk('private')->download($fullPath, $fileName);
    }

    /**
     * Close the process.
     */
    public function close(Request $request, DisciplinaryRecord $record)
    {
        Gate::authorize('update', $record);

        if ($record->status === 'CERRADO') {
            return redirect()->route('disciplinary.show', $record->id)
                ->with('error', 'El proceso ya se encuentra cerrado.');
        }

        $validated = $request->validate([
            'employee_signed' => 'required|boolean',
            'employer_signed' => 'required|boolean',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($record, $validated) {
            $record->update([
                'status' => 'CERRADO',
                'employee_signed_at' => $validated['employee_signed'] ? now() : null,
                'employer_signed_at' => $validated['employer_signed'] ? now() : null,
                'closed_at' => now(),
                'closed_by' => auth()->id(),
            ]);

            DisciplinaryState::create([
                'disciplinary_record_id' => $record->id,
                'state' => 'CERRADO',
                'changed_by' => auth()->id(),
                'notes' => 'Cierre definitivo y archivo formal del proceso. ' . ($validated['notes'] ?? ''),
            ]);
        });

        return redirect()->route('disciplinary.show', $record->id)
            ->with('success', 'El proceso disciplinario se ha cerrado y archivado formalmente en el historial del empleado.');
    }

    /**
     * Return disciplinary history of a single employee.
     */
    public function history(Employee $employee)
    {
        Gate::authorize('viewAny', DisciplinaryRecord::class);

        $tenantId = auth()->user()->tenant_id;
        
        $records = DisciplinaryRecord::where('tenant_id', $tenantId)
            ->where('employee_id', $employee->id)
            ->with(['creator', 'files'])
            ->latest()
            ->get();

        return response()->json([
            'employee' => "{$employee->first_name} {$employee->last_name}",
            'records' => $records
        ]);
    }
}
