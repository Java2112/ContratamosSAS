<?php

namespace App\Domains\Contracting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractingDocumentType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];
}
