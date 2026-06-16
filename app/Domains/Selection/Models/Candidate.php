<?php

namespace App\Domains\Selection\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'document_type',
        'document_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cv_path',
        'source',
        'is_active',
    ];

    protected $casts = [
        'document_number' => 'encrypted',
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

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
    
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
