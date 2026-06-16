<?php

namespace App\Domains\Disciplinary\Models;

use App\Models\Tenant;
use App\Models\User;
use App\Domains\Contracting\Models\Employee;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisciplinaryRecord extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'record_number',
        'witness_name',
        'representative_name',
        'representative_role',
        'scheduled_date',
        'scheduled_time',
        'reason',
        'rules_violated',
        'introductory_text',
        'initial_observations',
        'final_observations',
        'status', // BORRADOR, EN_PROCESO, FINALIZADO, PDF_GENERADO, CERRADO
        'employee_signature_path',
        'employee_signed_at',
        'employer_signature_path',
        'employer_signed_at',
        'created_by',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'employee_signed_at' => 'datetime',
        'employer_signed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DisciplinaryQuestion::class)->orderBy('sort_order');
    }

    public function states(): HasMany
    {
        return $this->hasMany(DisciplinaryState::class)->latest();
    }

    public function files(): HasMany
    {
        return $this->hasMany(DisciplinaryFile::class)->latest();
    }
}
