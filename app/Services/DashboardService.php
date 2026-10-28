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

    /** Submissions per day for the last $days days (zero-filled). */
    public function trend(int $days = 14, ?string $cohortId = null): array
    {
        $from = now()->subDays($days - 1)->startOfDay();

        $counts = Graduand::query()
            ->when($cohortId, fn ($q) => $q->where('cohort_id', $cohortId))
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $from)
            ->select(DB::raw('DATE(submitted_at) as d'), DB::raw('COUNT(*) as c'))
            ->groupBy('d')
            ->pluck('c', 'd');

        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $from->copy()->addDays($i);
            $out[] = ['label' => $day->format('j M'), 'count' => (int) ($counts[$day->format('Y-m-d')] ?? 0)];
        }

        return $out;
    }
}
