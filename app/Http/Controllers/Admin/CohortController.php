<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cohort;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class CohortController extends Controller
{
    public function index()
    {
        return view('admin.cohorts.index', [
            'cohorts' => Cohort::withCount([
                'graduands',
                'graduands as completed_count' => fn ($q) => $q->where('status', '!=', 'pending'),
            ])->orderByDesc('graduation_year')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.cohorts.form', ['cohort' => new Cohort(['is_active' => true, 'graduation_year' => now()->year])]);
    }

    public function store(Request $request)
    {
        $cohort = Cohort::create($this->validated($request));
        ActivityLogService::log('cohort.created', $cohort, null, $cohort->only(['name', 'graduation_year', 'opens_at', 'closes_at', 'is_active']));

        return redirect()->route('admin.cohorts.index')->with('success', 'Cohort created.');
    }

    public function edit(Cohort $cohort)
    {
        return view('admin.cohorts.form', compact('cohort'));
    }

    public function update(Request $request, Cohort $cohort)
    {
        $cohort->fill($this->validated($request));
        $dirty = $cohort->getDirty();

        if ($dirty) {
            $old = array_intersect_key($cohort->getOriginal(), $dirty);
            $cohort->save();
            ActivityLogService::log('cohort.updated', $cohort, $old, $dirty);
        }

        return redirect()->route('admin.cohorts.index')->with('success', 'Cohort updated.');
    }

    public function destroy(Cohort $cohort)
    {
        if ($cohort->graduands()->exists()) {
            return back()->with('error', 'This cohort has graduands and cannot be deleted. Deactivate it instead.');
        }

        ActivityLogService::log('cohort.deleted', $cohort, $cohort->only(['name', 'graduation_year']));
        $cohort->delete();

        return redirect()->route('admin.cohorts.index')->with('success', 'Cohort deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'graduation_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after:opens_at'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
