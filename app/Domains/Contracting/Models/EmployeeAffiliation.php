<?php

namespace App\Domains\Contracting\Models;

use App\Enums\Contracting\AffiliationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAffiliation extends Model
{
    protected $fillable = [
        'contracting_process_id',
        'type',
        'entity_name',
        'affiliation_number',
        'affiliation_date',
        'status',
        'file_path',
    ];

    protected $casts = [
        'affiliation_date' => 'date',
        'status' => AffiliationStatus::class,
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(ContractingProcess::class, 'contracting_process_id');
    }
}
