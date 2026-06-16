<?php

namespace App\Domains\Disciplinary\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryState extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'disciplinary_record_id',
        'state',
        'changed_by',
        'notes',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryRecord::class, 'disciplinary_record_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
