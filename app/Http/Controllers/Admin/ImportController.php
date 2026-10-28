<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cohort;
use App\Models\ImportBatch;
use App\Services\ImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct(private ImportService $service)
    {
    }

    public function create()
    {
        return view('admin.imports.create', [
            'cohorts' => Cohort::orderByDesc('graduation_year')->get(),
            'batches' => ImportBatch::with(['cohort', 'user'])->latest()->limit(10)->get(),
            'columns' => ImportService::COLUMNS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cohort_id' => ['required', 'exists:cohorts,id'],
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
        ]);

        $batch = $this->service->stage($request->file('file'), Cohort::findOrFail($data['cohort_id']), $request->user()->id);

        return redirect()->route('admin.imports.preview', $batch);
    }

    public function preview(ImportBatch $batch)
    {
        return view('admin.imports.preview', [
            'batch' => $batch->load('cohort'),
            'rows' => $batch->rows()->orderBy('row_number')->get(),
        ]);
    }

    public function commit(ImportBatch $batch)
    {
        $result = $this->service->commit($batch);

        return redirect()->route('admin.graduands.index', ['cohort_id' => $batch->cohort_id])
            ->with('success', "{$result['inserted']} graduand(s) imported. {$result['skipped']} skipped as duplicates.");
    }

    public function destroy(ImportBatch $batch)
    {
        $this->service->cancel($batch);

        return redirect()->route('admin.imports.create')->with('success', 'Import cancelled. Nothing was saved.');
    }

    public function template()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ImportService::COLUMNS);
            fputcsv($out, ['ST/2022/001', 'Jane', 'Wanjiru', 'Doe', 'Female', '2000-04-15', 'Kenyan', '12345678', 'jane@example.com', '0712345678', 'BSc Computer Science', 'Bachelor of Science', 'Second Class Upper']);
            fclose($out);
        }, 'graduands-template.csv', ['Content-Type' => 'text/csv']);
    }
}
