<?php

namespace App\Services;

use App\Models\Cohort;
use App\Models\Graduand;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function stats(?string $cohortId = null): array
    {
        $counts = Graduand::query()
            ->when($cohortId, fn ($q) => $q->where('cohort_id', $cohortId))
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $pending = (int) ($counts[Graduand::STATUS_PENDING] ?? 0);
        $confirmed = (int) ($counts[Graduand::STATUS_CONFIRMED] ?? 0);
        $updated = (int) ($counts[Graduand::STATUS_UPDATED] ?? 0);
        $total = $pending + $confirmed + $updated;

        return [
            'total' => $total,
            'pending' => $pending,
            'confirmed' => $confirmed,
            'updated' => $updated,
            'percent_done' => $total ? round((($confirmed + $updated) / $total) * 100) : 0,
        ];
    }

    public function cohortProgress()
    {
        return Cohort::withCount([
            'graduands',
            'graduands as completed_count' => fn ($q) => $q->where('status', '!=', Graduand::STATUS_PENDING),
        ])->orderByDesc('graduation_year')->get();
    }
}
