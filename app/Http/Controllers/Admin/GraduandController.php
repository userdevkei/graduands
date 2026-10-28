<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminGraduandRequest;
use App\Models\Cohort;
use App\Models\Graduand;
use App\Models\Log;
use App\Services\GraduandExportService;
use App\Services\GraduandService;
use Illuminate\Http\Request;

class GraduandController extends Controller
{
    public function __construct(private GraduandService $service)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['cohort_id', 'status', 'search']);

        return view('admin.graduands.index', [
            'graduands' => Graduand::with('cohort')->filter($filters)->orderBy('last_name')->orderBy('first_name')->paginate(25)->withQueryString(),
            'cohorts' => Cohort::orderByDesc('graduation_year')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(Graduand $graduand)
    {
        return view('admin.graduands.show', [
            'graduand' => $graduand->load('cohort'),
            'history' => Log::with('user')
                ->where('model_type', Graduand::class)
                ->where('model_id', $graduand->id)
                ->latest()->get(),
        ]);
    }

    public function edit(Graduand $graduand)
    {
        return view('admin.graduands.edit', compact('graduand'));
    }

    public function update(AdminGraduandRequest $request, Graduand $graduand)
    {
        $this->service->adminUpdate($graduand, $request->validated());

        return redirect()->route('admin.graduands.show', $graduand)->with('success', 'Record updated.');
    }

    public function unlock(Request $request, Graduand $graduand)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);
        abort_unless($graduand->isLocked(), 422);

        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        $this->service->unlock($graduand, $data['reason']);

        return back()->with('success', 'Record unlocked. The student can now submit one more update.');
    }

    public function export(Request $request, GraduandExportService $export)
    {
        return $export->download(Graduand::query()->filter($request->only(['cohort_id', 'status', 'search'])));
    }
}
