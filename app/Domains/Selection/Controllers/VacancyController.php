<?php

namespace App\Domains\Selection\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Selection\Models\Vacancy;
use App\Domains\Commercial\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VacancyController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = Vacancy::with(['client', 'coordinator', 'analyst', 'applications']);
        
        // RBAC Scoping
        if ($user->role?->value === \App\Enums\UserRole::ANALISTA->value) {
            $query->where('analyst_id', $user->id);
        }

        $vacancies = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Selection/Vacancies/Index', [
            'vacancies' => $vacancies,
            'analysts' => \App\Models\User::where('system_area', \App\Enums\SystemArea::SELECCION->value)
                ->where('role', \App\Enums\UserRole::ANALISTA->value)->get(),
        ]);
    }

    public function show(Vacancy $vacancy)
    {
        $user = auth()->user();
        if ($user->role?->value === \App\Enums\UserRole::ANALISTA->value && $vacancy->analyst_id !== $user->id) {
            abort(403, 'No tienes permiso para ver esta vacante, no te ha sido asignada.');
        }

        $vacancy->load(['client', 'coordinator', 'analyst', 'applications.candidate', 'applications.stages']);

        $magicLinkUrl = null;
        $magicLink = \App\Domains\Selection\Models\MagicLink::where('vacancy_id', $vacancy->id)
            ->where('expires_at', '>', now())
            ->first();
        if ($magicLink) {
            $magicLinkUrl = route('magic-link.show', $magicLink->token);
        }

        return Inertia::render('Selection/Vacancies/Show', [
            'vacancy' => $vacancy,
            'magicLinkUrl' => $magicLinkUrl,
        ]);
    }

    public function assignAnalyst(Request $request, Vacancy $vacancy)
    {
        if (!in_array(auth()->user()->role?->value, [\App\Enums\UserRole::COORDINADOR->value, \App\Enums\UserRole::ASISTENTE->value])) {
            abort(403, 'Solo el coordinador o asistente pueden asignar analistas.');
        }

        $request->validate(['analyst_id' => 'required|exists:users,id']);
        
        $vacancy->update([
            'analyst_id' => $request->analyst_id,
            'status' => \App\Enums\Selection\VacancyStatus::ASSIGNED->value
        ]);

        // Triggers Event conceptually: event(new VacancyAssigned($vacancy));

        return back()->with('success', 'Analista asignado correctamente a la vacante.');
    }

    public function create()
    {
        return Inertia::render('Selection/Vacancies/Create', [
            'clients' => Client::select('id', 'business_name')->get(),
            'priorities' => \App\Enums\Selection\VacancyPriority::toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'positions_required' => 'required|integer|min:1',
            'priority' => 'required|string',
            'expires_at' => 'nullable|date',
        ]);

        $validated['tenant_id'] = $request->user()->tenant_id;
        $validated['status'] = \App\Enums\Selection\VacancyStatus::NEW->value;

        Vacancy::create($validated);

        return redirect()->route('selection.vacancies.index')->with('success', 'Vacante creada exitosamente.');
    }
    
    public function markUrgent(Vacancy $vacancy)
    {
        $vacancy->update(['priority' => \App\Enums\Selection\VacancyPriority::URGENT->value]);
        return back()->with('success', 'Vacante marcada como prioritaria (Urgente).');
    }

    public function generateMagicLink(Vacancy $vacancy)
    {
        $magicLink = \App\Domains\Selection\Models\MagicLink::updateOrCreate(
            ['vacancy_id' => $vacancy->id, 'tenant_id' => $vacancy->tenant_id],
            ['expires_at' => now()->addDays(7), 'token' => \Illuminate\Support\Str::random(60)]
        );

        return back()->with('success', 'Enlace Mágico generado correctamente y será válido por 7 días.');
    }
}
