<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    // Rutas del Dominio Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });

    // Rutas del Dominio Comercial
    Route::prefix('commercial')->name('commercial.')->group(function () {
        Route::resource('clients', \App\Domains\Commercial\Controllers\ClientController::class);
        Route::resource('contracts', \App\Domains\Commercial\Controllers\ContractController::class);
        Route::get('contracts/{contract}/pdf', [\App\Domains\Commercial\Controllers\ContractController::class, 'downloadPdf'])->name('contracts.pdf');
    });

    // Rutas del Dominio Selección
    Route::prefix('selection')->name('selection.')->group(function () {
        Route::get('/dashboard', [\App\Domains\Selection\Controllers\DashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('vacancies', \App\Domains\Selection\Controllers\VacancyController::class);
        Route::post('vacancies/{vacancy}/assign', [\App\Domains\Selection\Controllers\VacancyController::class, 'assignAnalyst'])->name('vacancies.assign');
        Route::post('vacancies/{vacancy}/urgent', [\App\Domains\Selection\Controllers\VacancyController::class, 'markUrgent'])->name('vacancies.urgent');
        Route::post('vacancies/{vacancy}/magic-link', [\App\Domains\Selection\Controllers\VacancyController::class, 'generateMagicLink'])->name('vacancies.magic-link');
        
        // Candidatos
        Route::post('vacancies/{vacancy}/candidates', [\App\Domains\Selection\Controllers\CandidateController::class, 'storeForVacancy'])->name('vacancies.candidates.store');

        // Postulaciones
        Route::post('applications/{application}/status', [\App\Domains\Selection\Controllers\ApplicationController::class, 'updateStatus'])->name('applications.status');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas del Dominio Empresas (Clientes)
    Route::prefix('company')->name('company.')->group(function () {
        
        // Rutas de primer ingreso
        Route::get('/setup-password', [\App\Domains\Company\Controllers\FirstLoginController::class, 'create'])->name('first-login');
        Route::post('/setup-password', [\App\Domains\Company\Controllers\FirstLoginController::class, 'store'])->name('first-login.store');

        Route::get('/dashboard', [\App\Domains\Company\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('vacancies', \App\Domains\Company\Controllers\VacancyController::class);
        
        // Profile
        Route::get('/profile', [\App\Domains\Company\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Domains\Company\Controllers\ProfileController::class, 'update'])->name('profile.update');
    });
});

require __DIR__.'/auth.php';

// Public Magic Link Routes (No Auth Required)
Route::get('/client/review/{token}', [\App\Domains\Selection\Controllers\MagicLinkController::class, 'show'])->name('magic-link.show');
Route::post('/client/review/{token}/application/{application}', [\App\Domains\Selection\Controllers\MagicLinkController::class, 'updateApplicationStatus'])->name('magic-link.application.update');
