<?php

namespace App\Domains\Company\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Domains\Selection\Models\Vacancy;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // For the company portal, we limit vacancies to their client id
        $client = $request->user()->client;

        if (!$client) {
            abort(403, 'User is not associated with any company client.');
        }

        $activeVacancies = Vacancy::where('client_id', $client->id)
            ->whereNotIn('status', [
                \App\Enums\Selection\VacancyStatus::CLOSED->value,
                \App\Enums\Selection\VacancyStatus::CANCELLED->value
            ])
            ->count();

        // Other metrics can be fetched here

        return Inertia::render('Company/Dashboard', [
            'activeVacanciesCount' => $activeVacancies,
        ]);
    }
}
