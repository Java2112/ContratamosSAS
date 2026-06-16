<?php

namespace App\Domains\Selection\Models;

use App\Models\User;
use App\Domains\Commercial\Models\Client;
use App\Enums\Selection\VacancyStatus;
use App\Enums\Selection\VacancyPriority;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vacancy extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'anonymous_company',
        'title',
        'description',
        'positions_required',
        'status',
        'priority',
        'coordinator_id',
        'analyst_id',
        'expires_at',
        'closed_at',
        'department',
        'employer_type',
        'contract_type',
        'payroll_frequency',
        'workday_type',
        'schedule',
        'salary_min',
        'salary_max',
        'has_bonuses',
        'bonus_average',
        'work_modality',
        'address',
        'city',
        'department_name',
        'min_education_level',
        'experience_value',
        'experience_unit',
        'languages',
        'soft_skills',
        'hard_skills',
        'main_functions',
        'optional_features',
        'estimated_duration_months',
    ];

    protected $casts = [
        'status' => VacancyStatus::class,
        'priority' => VacancyPriority::class,
        'anonymous_company' => 'boolean',
        'expires_at' => 'datetime',
        'closed_at' => 'datetime',
        'has_bonuses' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'bonus_average' => 'decimal:2',
        'languages' => 'array',
        'soft_skills' => 'array',
        'hard_skills' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function analyst(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
