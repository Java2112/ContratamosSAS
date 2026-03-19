<?php

namespace App\Domains\Selection\Models;

use App\Enums\Selection\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'vacancy_id',
        'candidate_id',
        'status',
        'rejection_reason',
        'is_active',
    ];

    protected $appends = ['report_url'];

    protected $casts = [
        'status' => ApplicationStatus::class,
        'is_active' => 'boolean',
    ];

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(ApplicationStage::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function getReportUrlAttribute(): ?string
    {
        $fileName = "informe_perfil_cand_{$this->candidate_id}_vac_{$this->vacancy_id}.pdf";
        $path = "tenant_{$this->tenant_id}/reports/{$fileName}";
        
        if (Storage::disk('public')->exists($path)) {
            return asset("storage/{$path}");
        }
        
        return null;
    }
}
