<?php

namespace App\Http\Controllers;

use App\Domains\Disciplinary\Models\DisciplinaryFile;
use App\Domains\Selection\Models\Candidate;
use App\Domains\Selection\Models\MagicLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SecureDownloadController extends Controller
{
    /**
     * Download a private disciplinary PDF securely.
     */
    public function downloadDisciplinaryFile(Request $request, DisciplinaryFile $file): BinaryFileResponse
    {
        // 1. Validate Signed URL (integrity and expiration)
        if (!$request->hasValidSignature()) {
            abort(401, 'El enlace de descarga ha expirado o la firma es inválida.');
        }

        // 2. Validate Tenant ownership (double check, though TenantScope handles this)
        if ($file->record->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Acceso Denegado: No tiene permisos sobre el archivo de este Tenant.');
        }

        // 3. Validate Role authorization
        if (!in_array(auth()->user()->role->value, ['admin', 'descargos'])) {
            abort(403, 'Acceso Denegado: Permisos insuficientes.');
        }

        $privatePath = $file->file_path; // e.g., tenant_1/descargos/Descargos_DESC-2026-0001_V1.pdf

        if (!Storage::disk('private')->exists($privatePath)) {
            abort(404, 'El archivo solicitado no existe en el almacenamiento privado.');
        }

        // 4. Register access in audit log
        activity('archivos')
            ->performedOn($file)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'file_name' => $file->file_name,
            ])
            ->log('Descarga segura de archivo disciplinario.');

        return response()->download(storage_path('app/private/' . $privatePath));
    }

    /**
     * Download a private candidate CV securely.
     */
    public function downloadCandidateCv(Request $request, Candidate $candidate): BinaryFileResponse
    {
        $hasAccess = false;

        // 1. Check if authenticated user has access
        if (auth()->check()) {
            if ($candidate->tenant_id === auth()->user()->tenant_id) {
                $allowedRoles = ['admin', 'coordinador', 'asistente', 'analista', 'empresa'];
                if (in_array(auth()->user()->role->value, $allowedRoles)) {
                    $hasAccess = true;
                }
            }
        } 
        // 2. Check if valid magic link token is provided for external review
        elseif ($request->has('token')) {
            $magicLink = MagicLink::where('token', $request->token)
                ->where('expires_at', '>', now())
                ->first();

            if ($magicLink) {
                // Ensure candidate is applied to the vacancy of this magic link
                $isApplied = $candidate->applications()
                    ->where('vacancy_id', $magicLink->vacancy_id)
                    ->exists();

                if ($isApplied) {
                    $hasAccess = true;
                }
            }
        }

        if (!$hasAccess) {
            abort(403, 'Acceso Denegado: No está autorizado para ver este archivo.');
        }

        $privatePath = $candidate->cv_path;
        if (!$privatePath) {
            abort(404, 'La hoja de vida no está registrada.');
        }

        // Clean up path
        $privatePath = ltrim($privatePath, '/');
        if (str_starts_with($privatePath, 'storage/')) {
            $privatePath = substr($privatePath, 8);
        }

        if (!Storage::disk('private')->exists($privatePath)) {
            abort(404, 'El archivo de hoja de vida no existe en el almacenamiento privado.');
        }

        // 3. Register access in audit log
        activity('archivos')
            ->performedOn($candidate)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'file_name' => basename($privatePath),
            ])
            ->log('Descarga segura de hoja de vida de candidato.');

        return response()->download(storage_path('app/private/' . $privatePath));
    }
}
