<?php

namespace App\Domains\Contracting\Models;

use App\Models\User;
use App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'contracting_process_id',
        'document_type',
        'document_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cargo',
        'salary',
        'hired_at',
        'is_active',
    ];

    protected $casts = [
        'document_number' => 'encrypted',
        'salary' => 'encrypted',
        'hired_at' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Register model event listeners.
     */
    protected static function booted(): void
    {
        static::saving(function ($model) {
            if ($model->isDirty('document_number') && $model->document_number) {
                $salt = config('app.key') ?: 'base-salt-string';
                $model->document_number_hash = hash_hmac('sha256', $model->document_number, $salt);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(ContractingProcess::class, 'contracting_process_id');
    }

    public function getClientAttribute()
    {
        $process = $this->process;
        if ($process) {
            $application = $process->relationLoaded('application') 
                ? $process->application 
                : $process->application()->first();
                
            if ($application) {
                $vacancy = $application->relationLoaded('vacancy') 
                    ? $application->vacancy 
                    : $application->vacancy()->first();
                    
                if ($vacancy) {
                    return $vacancy->relationLoaded('client') 
                        ? $vacancy->client 
                        : $vacancy->client()->first();
                }
            }
        }
        return null;
    }
}
