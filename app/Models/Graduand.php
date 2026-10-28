<?php

namespace App\Models;

use App\Models\Concerns\HasCommonId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Graduand extends Model
{
    use HasCommonId;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed'; // student confirmed as-is
    public const STATUS_UPDATED = 'updated';     // student changed something

    /** Fields a student may edit (once). */
    public const EDITABLE_LABELS = [
        'first_name' => 'First name',
        'middle_name' => 'Middle name(s)',
        'last_name' => 'Last name / Surname',
        'gender' => 'Gender',
        'date_of_birth' => 'Date of birth',
        'nationality' => 'Nationality',
        'national_id_number' => 'National ID / Passport number',
        'email' => 'Email',
        'phone' => 'Phone',
    ];

    protected $fillable = [
        'cohort_id', 'registration_number', 'portal_first_name',
        'first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth',
        'nationality', 'national_id_number', 'email', 'phone',
        'programme', 'award', 'classification',
        'status', 'original_data', 'submitted_at', 'submitted_ip',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'original_data' => 'array',
        'submitted_at' => 'datetime',
    ];

    public static function editableFields(): array
    {
        return array_keys(self::EDITABLE_LABELS);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    /** One-time update rule: anything other than "pending" is locked. */
    public function isLocked(): bool
    {
        return $this->status !== self::STATUS_PENDING;
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->first_name, $this->middle_name, $this->last_name])));
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['cohort_id'] ?? null, fn ($q, $v) => $q->where('cohort_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['search'] ?? null, function ($q, $v) {
                $q->where(function ($w) use ($v) {
                    $w->where('registration_number', 'like', "%{$v}%")
                        ->orWhere('first_name', 'like', "%{$v}%")
                        ->orWhere('last_name', 'like', "%{$v}%");
                });
            });
    }
}
