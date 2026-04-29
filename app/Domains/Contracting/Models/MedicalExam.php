<?php

namespace App\Domains\Contracting\Models;

use App\Enums\Contracting\MedicalResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalExam extends Model
{
    protected $fillable = [
        'contracting_process_id',
        'provider_name',
        'scheduled_date',
        'result',
        'observations',
        'file_path',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'result' => MedicalResult::class,
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(ContractingProcess::class, 'contracting_process_id');
    }
}
