<?php

namespace App\Domains\Contracting\Models;

use App\Models\Tenant;
use App\Domains\Selection\Models\Application;
use App\Enums\Contracting\ProcessStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractingProcess extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'application_id',
        'status',
        'agreed_salary',
        'contract_type',
        'cargo',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => ProcessStatus::class,
        'agreed_salary' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function medicalExam(): HasOne
    {
        return $this->hasOne(MedicalExam::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(EmploymentContract::class);
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(EmployeeAffiliation::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }
}
