<?php

namespace App\Models;

use App\Models\Concerns\HasCommonId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportBatch extends Model
{
    use HasCommonId;

    protected $fillable = [
        'cohort_id', 'user_id', 'original_filename', 'status',
        'total_rows', 'valid_rows', 'duplicate_rows', 'invalid_rows', 'committed_at',
    ];

    protected $casts = ['committed_at' => 'datetime'];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rows(): HasMany
    {
        return $this->hasMany(ImportRow::class);
    }

    public function isStaged(): bool
    {
        return $this->status === 'staged';
    }
}
