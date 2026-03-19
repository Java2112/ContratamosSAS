<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

trait HasTenant
{
    /**
     * Boot the trait to add the global scope.
     */
    protected static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model) {
            // Asigna automáticamente el tenant de la sesión al crear modelos
            if (auth()->hasUser() && !$model->tenant_id) {
                 $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /**
     * Relación con el Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
