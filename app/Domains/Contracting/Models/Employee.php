<?php

namespace App\Domains\Contracting\Models;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

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
        'salary' => 'decimal:2',
        'hired_at' => 'date',
        'is_active' => 'boolean',
    ];

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
}
