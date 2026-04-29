<?php

namespace App\Domains\Contracting\Models;

use App\Models\User;
use App\Enums\Contracting\DocumentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'contracting_process_id',
        'document_type_id',
        'file_path',
        'status',
        'rejection_reason',
        'validated_at',
        'validated_by',
    ];

    protected $casts = [
        'status' => DocumentStatus::class,
        'validated_at' => 'datetime',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(ContractingProcess::class, 'contracting_process_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(ContractingDocumentType::class, 'document_type_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
