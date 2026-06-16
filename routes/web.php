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
    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });

    // Rutas del Dominio Comercial
    Route::prefix('commercial')->middleware(['role:admin,coordinador', 'area:comercial', 'audit:comercial'])->name('commercial.')->group(function () {
        Route::resource('clients', \App\Domains\Commercial\Controllers\ClientController::class);
        Route::resource('contracts', \App\Domains\Commercial\Controllers\ContractController::class);
        Route::get('contracts/{contract}/pdf', [\App\Domains\Commercial\Controllers\ContractController::class, 'downloadPdf'])->name('contracts.pdf');
    });

    // Rutas del Dominio Selección
    Route::prefix('selection')->middleware(['role:admin,coordinador,asistente,analista', 'area:seleccion', 'audit:seleccion'])->name('selection.')->group(function () {
        Route::get('/dashboard', [\App\Domains\Selection\Controllers\DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('vacancies/{vacancy}/template', [\App\Domains\Selection\Controllers\VacancyController::class, 'getTemplate'])->name('vacancies.template');
        Route::resource('vacancies', \App\Domains\Selection\Controllers\VacancyController::class);
        Route::post('vacancies/{vacancy}/assign', [\App\Domains\Selection\Controllers\VacancyController::class, 'assignAnalyst'])->name('vacancies.assign');
        Route::post('vacancies/{vacancy}/urgent', [\App\Domains\Selection\Controllers\VacancyController::class, 'markUrgent'])->name('vacancies.urgent');
        Route::post('vacancies/{vacancy}/magic-link', [\App\Domains\Selection\Controllers\VacancyController::class, 'generateMagicLink'])->name('vacancies.magic-link');
        
        // Candidatos
        Route::get('candidates/{candidate}', [\App\Domains\Selection\Controllers\CandidateController::class, 'show'])->name('candidates.show');
        Route::post('vacancies/{vacancy}/candidates', [\App\Domains\Selection\Controllers\CandidateController::class, 'storeForVacancy'])->name('vacancies.candidates.store');

        // Postulaciones
        Route::post('applications/{application}/status', [\App\Domains\Selection\Controllers\ApplicationController::class, 'updateStatus'])->name('applications.status');
    });

    // Rutas del Dominio Contratación
    Route::prefix('contracting')->middleware(['role:admin,jefe-contratacion', 'area:contratacion', 'audit:contratacion'])->name('contracting.')->group(function () {
        Route::get('/dashboard', [\App\Domains\Contracting\Controllers\ContractingDashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/process', [\App\Domains\Contracting\Controllers\ContractingProcessController::class, 'index'])->name('process.index');
        Route::get('/process/{process}', [\App\Domains\Contracting\Controllers\ContractingProcessController::class, 'show'])->name('process.show');
        Route::post('/document/{document}/validate', [\App\Domains\Contracting\Controllers\ContractingProcessController::class, 'validateDocument'])->name('document.validate');
        Route::post('/process/{process}/medical', [\App\Domains\Contracting\Controllers\ContractingProcessController::class, 'recordMedicalExam'])->name('process.medical');
        Route::post('/process/{process}/contract/upload', [\App\Domains\Contracting\Controllers\ContractingProcessController::class, 'uploadContract'])->name('process.contract.upload');
        Route::post('/process/{process}/contract/sign', [\App\Domains\Contracting\Controllers\ContractingProcessController::class, 'signContract'])->name('process.contract.sign');
        Route::post('/process/{process}/affiliation', [\App\Domains\Contracting\Controllers\ContractingProcessController::class, 'saveAffiliation'])->name('process.affiliation');
        Route::post('/process/{process}/send-to-payroll', [\App\Domains\Contracting\Controllers\ContractingProcessController::class, 'sendToPayroll'])->name('process.send-to-payroll');
    });

    // Rutas del Dominio Descargos Disciplinarios
    Route::prefix('disciplinary')->middleware(['role:admin,descargos', 'area:descargos', 'audit:descargos'])->name('disciplinary.')->group(function () {
        Route::get('/dashboard', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'dashboard'])->name('dashboard');
        Route::get('/create/{employee}', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'create'])->name('create');
        Route::post('/store', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'store'])->name('store');
        Route::get('/show/{record}', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'show'])->name('show');
        
        // Cuestionario
        Route::get('/form/{record}', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'form'])->name('form');
        Route::post('/form/{record}/save', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'saveQuestionsAndAnswers'])->name('form.save');
        
        // Acciones del Proceso
        Route::post('/finalize/{record}', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'finalize'])->name('finalize');
        Route::get('/pdf/{record}', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'generatePdf'])->name('pdf');
        Route::post('/close/{record}', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'close'])->name('close');
        
        // Historial individual
        Route::get('/history/{employee}', [\App\Domains\Disciplinary\Controllers\DisciplinaryController::class, 'history'])->name('history');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Secure private downloads (Internal HR)
    Route::get('/secure-download/disciplinary/{file}', [\App\Http\Controllers\SecureDownloadController::class, 'downloadDisciplinaryFile'])
        ->name('secure.download.disciplinary')
        ->middleware('signed');
});

require __DIR__.'/auth.php';

// ── Portal Empresas ──────────────────────────────────────────────────────────
Route::prefix('company')->name('company.')->group(function () {
    
    // Redirección base del prefijo
    Route::get('/', function () {
        return redirect()->route('company.dashboard');
    });

    // Rutas públicas (sin autenticación)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Domains\Company\Controllers\CompanySessionController::class, 'create'])->name('login');
        Route::post('/login', [\App\Domains\Company\Controllers\CompanySessionController::class, 'store'])->name('login.store');

        Route::get('/setup-password', [\App\Domains\Company\Controllers\FirstLoginController::class, 'create'])->name('first-login');
        Route::post('/setup-password', [\App\Domains\Company\Controllers\FirstLoginController::class, 'store'])->name('first-login.store');
    });

    // Rutas protegidas (solo empresas autenticadas)
    Route::middleware('company.auth')->group(function () {
        Route::get('/dashboard', [\App\Domains\Company\Controllers\DashboardController::class, 'index'])->name('dashboard');
        
        // Force change password
        Route::get('/force-change-password', [\App\Domains\Company\Controllers\FirstLoginController::class, 'forceChangeShow'])->name('password.force-change');
        Route::post('/force-change-password', [\App\Domains\Company\Controllers\FirstLoginController::class, 'forceChangeStore'])->name('password.force-change.store');

        Route::get('vacancies/{vacancy}/template', [\App\Domains\Company\Controllers\VacancyController::class, 'getTemplate'])->name('vacancies.template');
        Route::resource('vacancies', \App\Domains\Company\Controllers\VacancyController::class);

        // Candidate Reviews
        Route::get('/reviews/{application}', [\App\Domains\Company\Controllers\CompanyReviewController::class, 'show'])->name('reviews.show');
        Route::post('/reviews/{application}/approve', [\App\Domains\Company\Controllers\CompanyReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{application}/reject', [\App\Domains\Company\Controllers\CompanyReviewController::class, 'reject'])->name('reviews.reject');

        Route::get('/profile', [\App\Domains\Company\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Domains\Company\Controllers\ProfileController::class, 'update'])->name('profile.update');

        Route::post('/logout', [\App\Domains\Company\Controllers\CompanySessionController::class, 'destroy'])->name('logout');
    });
});

// Public Magic Link Routes (No Auth Required)
Route::get('/client/review/{token}', [\App\Domains\Selection\Controllers\MagicLinkController::class, 'show'])->name('magic-link.show');
Route::post('/client/review/{token}/application/{application}', [\App\Domains\Selection\Controllers\MagicLinkController::class, 'updateApplicationStatus'])->name('magic-link.application.update');

// Secure CV download (Supports authenticated users and guest magic links)
Route::get('/secure-download/cv/{candidate}', [\App\Http\Controllers\SecureDownloadController::class, 'downloadCandidateCv'])->name('secure.download.cv');
