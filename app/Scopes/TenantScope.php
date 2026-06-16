<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Propiedad estática para evitar recursión infinita al resolver el usuario.
     *
     * @var bool
     */
    protected static $resolving = false;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Si ya estamos resolviendo la sesión o el usuario, evitamos la re-entrada recursiva.
        if (self::$resolving) {
            return;
        }

        self::$resolving = true;

        try {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->tenant_id) {
                    $builder->where($model->getTable() . '.tenant_id', $user->tenant_id);
                }
            }
        } finally {
            self::$resolving = false;
        }
    }
}
