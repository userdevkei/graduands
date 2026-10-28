<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cohort;
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
            'cohorts' => Cohort::orderByDesc('graduation_year')->get(),
            'progress' => $dashboard->cohortProgress(),
            'recent' => Log::with('user')->latest()->limit(10)->get(),
            'cohortId' => $cohortId,
        ]);
    }
}
