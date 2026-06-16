<?php

namespace App\Domains\Disciplinary\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryFile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'disciplinary_record_id',
        'file_path',
        'file_name',
        'file_size',
        'version',
        'generated_by',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryRecord::class, 'disciplinary_record_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
