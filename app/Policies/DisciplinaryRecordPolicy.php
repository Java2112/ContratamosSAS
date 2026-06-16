<?php

namespace App\Policies;

use App\Domains\Disciplinary\Models\DisciplinaryRecord;
use App\Models\User;

class DisciplinaryRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $role = is_object($user->role) ? $user->role->value : $user->role;
        return in_array($role, ['admin', 'descargos']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DisciplinaryRecord $record): bool
    {
        // 1. Strict Tenant check
        if ($user->tenant_id !== $record->tenant_id) {
            return false;
        }

        // 2. Role check
        $role = is_object($user->role) ? $user->role->value : $user->role;
        return in_array($role, ['admin', 'descargos']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $role = is_object($user->role) ? $user->role->value : $user->role;
        return in_array($role, ['admin', 'descargos']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DisciplinaryRecord $record): bool
    {
        // 1. Strict Tenant check
        if ($user->tenant_id !== $record->tenant_id) {
            return false;
        }

        // 2. Block modifications if the process is already finalized or closed
        if (in_array($record->status, ['FINALIZADO', 'PDF_GENERADO', 'CERRADO'])) {
            return false;
        }

        // 3. Role check
        $role = is_object($user->role) ? $user->role->value : $user->role;
        return in_array($role, ['admin', 'descargos']);
    }
}
