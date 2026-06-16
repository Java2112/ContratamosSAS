<?php

namespace App\Domains\Disciplinary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryAnswer extends Model
{
    protected $fillable = [
        'disciplinary_question_id',
        'answer_text',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryQuestion::class, 'disciplinary_question_id');
    }
}
