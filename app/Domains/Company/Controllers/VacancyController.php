<?php

namespace App\Domains\Company\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Domains\Selection\Models\Vacancy;
use App\Domains\Selection\Requests\StoreVacancyRequest;

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
        $client = auth()->user()->client;
        $previousVacancies = Vacancy::where('client_id', $client->id)
            ->latest()
            ->get(['id', 'title', 'created_at']);

        return Inertia::render('Company/Vacancies/Create', [
            'previousVacancies' => $previousVacancies
        ]);
    }

    public function store(StoreVacancyRequest $request)
    {
        $validated = $request->validated();
        $client = $request->user()->client;

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

        $vacancy->load(['applications' => function($q) {
            $q->whereIn('status', [
                \App\Enums\Selection\ApplicationStatus::SENT_TO_CLIENT->value,
                \App\Enums\Selection\ApplicationStatus::APPROVED_BY_CLIENT->value,
                \App\Enums\Selection\ApplicationStatus::REJECTED_BY_CLIENT->value,
            ])->with(['candidate', 'stages']);
        }]);

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

    public function update(StoreVacancyRequest $request, Vacancy $vacancy)
    {
        $client = $request->user()->client;

        if ($vacancy->client_id !== $client->id) {
            abort(403);
        }

        $vacancy->update($request->validated());

        return redirect()->route('company.vacancies.show', $vacancy->id)->with('success', 'Vacante actualizada exitosamente.');
    }

    public function getTemplate(Vacancy $vacancy)
    {
        $client = auth()->user()->client;
        if ($vacancy->client_id !== $client->id) {
            abort(403);
        }

        return response()->json($vacancy);
    }
}
