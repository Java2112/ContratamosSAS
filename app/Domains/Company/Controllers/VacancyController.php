<?php

namespace App\Domains\Company\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Domains\Selection\Models\Vacancy;

class VacancyController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->user()->client;
        
        $vacancies = Vacancy::where('client_id', $client->id)->latest()->paginate(10);

        return Inertia::render('Company/Vacancies/Index', [
            'vacancies' => $vacancies
        ]);
    }

    public function create()
    {
        return Inertia::render('Company/Vacancies/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'positions_required' => 'required|integer|min:1',
            'department' => 'nullable|string|max:255',
            'employer_type' => 'required|string|in:directa,contratamos',
            
            'contract_type' => 'required|string',
            'payroll_frequency' => 'required|string',
            'workday_type' => 'required|string',
            'schedule' => 'nullable|string|max:255',
            
            'salary_type' => 'required|string',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'has_bonuses' => 'boolean',
            'bonus_average' => 'nullable|numeric|min:0',
            
            'work_modality' => 'required|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'department_name' => 'nullable|string|max:255',
            
            'min_education_level' => 'required|string',
            'min_experience_years' => 'nullable|integer|min:0',
            'languages' => 'nullable|string', // Se podría procesar a array si es necesario
            'soft_skills' => 'nullable|string',
            'hard_skills' => 'nullable|string',
            
            'main_functions' => 'nullable|string',
            'optional_features' => 'nullable|string',
            'estimated_duration_months' => 'nullable|integer|min:1',
            'data_treatment_accepted' => 'accepted'
        ]);

        $client = $request->user()->client;

        // Limpiar el campo de aceptacion que no está en DB
        unset($validated['data_treatment_accepted']);
        
        // Convert comma-separated string to an array for JSON columns if needed
        $validated['languages'] = $validated['languages'] ? array_map('trim', explode(',', $validated['languages'])) : [];
        $validated['soft_skills'] = $validated['soft_skills'] ? array_map('trim', explode(',', $validated['soft_skills'])) : [];
        $validated['hard_skills'] = $validated['hard_skills'] ? array_map('trim', explode(',', $validated['hard_skills'])) : [];

        $validated['client_id'] = $client->id;
        $validated['tenant_id'] = $client->tenant_id;
        $validated['status'] = \App\Enums\Selection\VacancyStatus::IN_PROGRESS->value;

        Vacancy::create($validated);

        return redirect()->route('company.vacancies.index')->with('success', 'Vacante creada exitosamente.');
    }

    public function show(Request $request, Vacancy $vacancy)
    {
        $client = $request->user()->client;

        if ($vacancy->client_id !== $client->id) {
            abort(403);
        }

        $vacancy->load(['applications.candidate', 'applications.stages']);

        return Inertia::render('Company/Vacancies/Show', [
            'vacancy' => $vacancy
        ]);
    }

    public function edit(Request $request, Vacancy $vacancy)
    {
        $client = $request->user()->client;

        if ($vacancy->client_id !== $client->id) {
            abort(403);
        }

        return Inertia::render('Company/Vacancies/Edit', [
            'vacancy' => $vacancy
        ]);
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $client = $request->user()->client;

        if ($vacancy->client_id !== $client->id) {
            abort(403);
        }

        // Si solo se manda cambiar el estado (desde Show.vue)
        if ($request->has('status') && count($request->all()) <= 2) {
            $validated = $request->validate([
                'status' => 'required|string|in:nueva,en_proceso,en_busqueda,en_validacion,cerrada,cancelada',
            ]);
            $vacancy->update(['status' => $validated['status']]);
            return back()->with('success', 'Estado de vacante actualizado.');
        }

        // Validacion completa para la edición
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'positions_required' => 'required|integer|min:1',
            'department' => 'nullable|string|max:255',
            'employer_type' => 'required|string|in:directa,contratamos',
            
            'contract_type' => 'required|string',
            'payroll_frequency' => 'required|string',
            'workday_type' => 'required|string',
            'schedule' => 'nullable|string|max:255',
            
            'salary_type' => 'required|string',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'has_bonuses' => 'boolean',
            'bonus_average' => 'nullable|numeric|min:0',
            
            'work_modality' => 'required|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'department_name' => 'nullable|string|max:255',
            
            'min_education_level' => 'required|string',
            'min_experience_years' => 'nullable|integer|min:0',
            'languages' => 'nullable|string|array', // En Vue 3 puede venir como string o array
            'soft_skills' => 'nullable|string|array',
            'hard_skills' => 'nullable|string|array',
            
            'main_functions' => 'nullable|string',
            'optional_features' => 'nullable|string',
            'estimated_duration_months' => 'nullable|integer|min:1',
        ]);

        // Manejar strings separados por coma si vienen de esa forma, o mantener el array
        $processArrayField = function($field) {
            if (is_array($field)) return $field;
            if (is_string($field) && $field !== '') {
                return array_map('trim', explode(',', $field));
            }
            return [];
        };

        if (array_key_exists('languages', $validated)) {
            $validated['languages'] = $processArrayField($request->languages);
        }
        if (array_key_exists('soft_skills', $validated)) {
            $validated['soft_skills'] = $processArrayField($request->soft_skills);
        }
        if (array_key_exists('hard_skills', $validated)) {
            $validated['hard_skills'] = $processArrayField($request->hard_skills);
        }

        $vacancy->update($validated);

        return redirect()->route('company.vacancies.show', $vacancy->id)->with('success', 'Vacante actualizada exitosamente.');
    }
}
