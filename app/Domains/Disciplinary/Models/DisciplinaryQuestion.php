<?php

namespace App\Domains\Disciplinary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DisciplinaryQuestion extends Model
{
    protected $fillable = [
        'disciplinary_record_id',
        'question_text',
        'sort_order',
        'is_from_template',
    ];

    protected $casts = [
        'is_from_template' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryRecord::class, 'disciplinary_record_id');
    }

    public function answer(): HasOne
    {
        return $this->hasOne(DisciplinaryAnswer::class, 'disciplinary_question_id');
    }
}
