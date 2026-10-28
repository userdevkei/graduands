<?php

namespace App\Services;

use App\Models\Graduand;
use DomainException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StudentVerificationService
{
    /** Match on registration number + the first name as it is on the student portal. */
    public function find(string $registrationNumber, string $firstName): ?Graduand
    {
        return Graduand::with('cohort')
            ->whereRaw('LOWER(registration_number) = ?', [mb_strtolower(trim($registrationNumber))])
            ->whereRaw('LOWER(portal_first_name) = ?', [mb_strtolower(trim($firstName))])
            ->first();
    }

    /**
     * The one-time submission. Re-checks lock state inside a transaction with a row lock,
     * so a double-click or two tabs cannot sneak in a second update.
     */
    public function submit(Graduand $graduand, array $data, string $ip): Graduand
    {
        return DB::transaction(function () use ($graduand, $data, $ip) {
            $g = Graduand::with('cohort')->lockForUpdate()->findOrFail($graduand->id);

            if ($g->isLocked()) {
                throw new DomainException('Your record has already been submitted and is now locked. Contact the Registry if something is wrong.');
            }
            if (! $g->cohort->isOpen()) {
                throw new DomainException('The confirmation window for your cohort is not open. Please contact the Registry.');
            }

            $old = $this->snapshot($g);
            $new = $this->normalise(Arr::only($data, Graduand::editableFields()));

            $changed = array_filter($new, fn ($value, $key) => ($old[$key] ?? null) !== $value, ARRAY_FILTER_USE_BOTH);

            $g->original_data ??= $old;
            $g->fill($new);
            $g->status = $changed ? Graduand::STATUS_UPDATED : Graduand::STATUS_CONFIRMED;
            $g->submitted_at = now();
            $g->submitted_ip = $ip;
            $g->save();

            ActivityLogService::log(
                $changed ? 'student.updated_record' : 'student.confirmed_record',
                $g,
                $changed ? array_intersect_key($old, $changed) : null,
                $changed ?: null,
            );

            return $g;
        });
    }

    private function snapshot(Graduand $g): array
    {
        $out = [];
        foreach (Graduand::editableFields() as $field) {
            $out[$field] = $field === 'date_of_birth'
                ? $g->date_of_birth?->format('Y-m-d')
                : $g->{$field};
        }

        return $out;
    }

    private function normalise(array $data): array
    {
        return array_map(function ($v) {
            $v = is_string($v) ? trim($v) : $v;

            return $v === '' ? null : $v;
        }, $data);
    }
}
