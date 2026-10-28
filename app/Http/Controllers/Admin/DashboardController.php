<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cohort;
use App\Models\Graduand;
use App\Models\Log;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardService $dashboard)
    {
        $cohortId = $request->query('cohort_id');

        return view('admin.dashboard', [
            'stats' => $dashboard->stats($cohortId),
            'trend' => $dashboard->trend(14, $cohortId),
            'cohorts' => Cohort::orderByDesc('graduation_year')->get(),
            'progress' => $dashboard->cohortProgress(),
            'recent' => Log::with('user')->latest()->limit(8)->get(),
            'recentUpdates' => Graduand::with('cohort')
                ->where('status', Graduand::STATUS_UPDATED)
                ->when($cohortId, fn ($q) => $q->where('cohort_id', $cohortId))
                ->latest('submitted_at')->limit(6)->get(),
            'cohortId' => $cohortId,
        ]);
    }
}
