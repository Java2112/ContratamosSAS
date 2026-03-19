<?php

namespace App\Domains\Selection\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicationStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'from_status',
        'to_status',
        'notes',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
