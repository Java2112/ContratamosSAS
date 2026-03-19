<?php

namespace App\Domains\Selection\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MagicLink extends Model
{
    protected $fillable = [
        'tenant_id',
        'vacancy_id',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($magicLink) {
            if (empty($magicLink->token)) {
                $magicLink->token = Str::random(60);
            }
        });
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
