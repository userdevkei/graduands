<?php

namespace App\Models;

use App\Models\Concerns\HasCommonId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cohort extends Model
{
    use HasCommonId;

    protected $fillable = ['name', 'graduation_year', 'opens_at', 'closes_at', 'is_active'];

    protected $casts = [
        'opens_at' => 'datetime',
        'closes_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function graduands(): HasMany
    {
        return $this->hasMany(Graduand::class);
    }

    public function importBatches(): HasMany
    {
        return $this->hasMany(ImportBatch::class);
    }

    /** Students can only confirm/update while the window is open. */
    public function isOpen(): bool
    {
        return $this->is_active
            && (! $this->opens_at || $this->opens_at->isPast())
            && (! $this->closes_at || $this->closes_at->isFuture());
    }
}
