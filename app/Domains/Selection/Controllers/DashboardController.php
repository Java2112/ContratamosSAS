<?php

namespace App\Domains\Selection\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Selection\Models\Vacancy;
use App\Domains\Selection\Models\Application;
use App\Domains\Selection\Models\Candidate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Enums\UserRole;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Base Query
        $vacancyQuery = Vacancy::query();

        if ($user->role?->value === UserRole::ANALISTA->value) {
            $vacancyQuery->where('analyst_id', $user->id);
            
            $stats = [
                'active_vacancies' => (clone $vacancyQuery)->where('status', '!=', 'cerrada')->count(),
                'urgent_vacancies' => (clone $vacancyQuery)->where('priority', 'urgent')->where('status', '!=', 'cerrada')->count(),
                'my_candidates' => Application::whereHas('vacancy', function($q) use ($user) {
                    $q->where('analyst_id', $user->id);
                })->count(),
                'pending_interviews' => Application::whereHas('vacancy', function($q) use ($user) {
                    $q->where('analyst_id', $user->id);
                })->where('status', 'entrevista')->count(),
            ];
        } else {
            // Coordinator and Admin see global stats
            $stats = [
                'total_vacancies' => Vacancy::count(),
                'active_vacancies' => Vacancy::where('status', '!=', 'cerrada')->count(),
                'urgent_vacancies' => Vacancy::where('priority', 'urgent')->where('status', '!=', 'cerrada')->count(),
                'total_candidates' => Candidate::count(),
            ];
        }

        return Inertia::render('Selection/Dashboard', [
            'stats' => $stats,
            'recentApplications' => Application::with(['candidate', 'vacancy'])
                ->when($user->role?->value === UserRole::ANALISTA->value, function($q) use ($user) {
                    $q->whereHas('vacancy', fn($v) => $v->where('analyst_id', $user->id));
                })
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
