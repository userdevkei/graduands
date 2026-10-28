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

    /** Server-side DataTables endpoint. */
    public function data(Request $request)
    {
        $base = Graduand::query()->filter($request->only(['cohort_id', 'status']));
        $total = (clone $base)->count();

        $search = trim((string) $request->input('search.value', ''));
        $query = (clone $base)->with('cohort')->filter(['search' => $search]);
        $filtered = (clone $query)->count();

        // column index => sortable DB column (null = not sortable)
        $sortable = [0 => 'last_name', 1 => 'programme', 2 => null, 3 => 'status', 4 => 'submitted_at', 5 => null];
        $column = $sortable[(int) $request->input('order.0.column', 0)] ?? 'last_name';
        $dir = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        $rows = $query
            ->orderBy($column ?? 'last_name', $column ? $dir : 'asc')
            ->orderBy('first_name')
            ->skip(max((int) $request->input('start', 0), 0))
            ->take(min(max((int) $request->input('length', 25), 1), 100))
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows->map(fn (Graduand $g) => [
                'full_name' => $g->full_name,
                'registration_number' => $g->registration_number,
                'programme' => $g->programme,
                'cohort' => $g->cohort?->name,
                'status' => $g->status,
                'submitted_at' => $g->submitted_at?->format('d M Y, H:i'),
                'url' => route('admin.graduands.show', $g),
            ])->all(),
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
