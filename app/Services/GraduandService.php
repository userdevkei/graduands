<?php

namespace App\Services;

use App\Models\Graduand;

/** Registry-side operations on a graduand record. */
class GraduandService
{
    public function adminUpdate(Graduand $graduand, array $data): Graduand
    {
        $graduand->fill($data);
        $dirty = $graduand->getDirty();

        if ($dirty) {
            $old = array_intersect_key($graduand->getOriginal(), $dirty);
            $graduand->save();
            ActivityLogService::log('admin.updated_record', $graduand, $old, $dirty);
        }

        return $graduand;
    }

    /** Re-opens a locked record so the student gets one more update. */
    public function unlock(Graduand $graduand, string $reason): void
    {
        $old = ['status' => $graduand->status, 'submitted_at' => $graduand->submitted_at?->toDateTimeString()];

        $graduand->update([
            'status' => Graduand::STATUS_PENDING,
            'submitted_at' => null,
            'submitted_ip' => null,
        ]);

        ActivityLogService::log('admin.unlocked_record', $graduand, $old, [
            'status' => Graduand::STATUS_PENDING,
            'reason' => $reason,
        ]);
    }
}
