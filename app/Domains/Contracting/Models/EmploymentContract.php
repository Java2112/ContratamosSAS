<?php

namespace App\Domains\Contracting\Models;

use App\Models\User;
use App\Enums\Contracting\ContractStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentContract extends Model
{
    protected $fillable = [
        'contracting_process_id',
        'file_path',
        'status',
        'signed_at',
        'signed_by',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'status' => ContractStatus::class,
        'signed_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(ContractingProcess::class, 'contracting_process_id');
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
}
