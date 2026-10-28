@extends('layouts.admin')
@section('title', 'Import')

@section('content')
<h1 class="h4 mb-3">Import graduands</h1>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('admin.imports.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Cohort</label>
                    <select name="cohort_id" class="form-select @error('cohort_id') is-invalid @enderror" required>
                        <option value="">Select...</option>
                        @foreach ($cohorts as $c)
                            <option value="{{ $c->id }}" @selected(old('cohort_id') === $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('cohort_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">File (.csv, .xlsx, .xls)</label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-dark">Upload and preview</button>
                <a href="{{ route('admin.imports.template') }}" class="btn btn-link">Download template</a>
                <div class="form-text mt-2">Nothing is saved until you confirm on the preview page.</div>
            </form>
        </div></div>
        <div class="card mt-3"><div class="card-body small">
            <strong>Expected columns (first row):</strong><br>
            <code>{{ implode(', ', $columns) }}</code>
            <div class="text-muted mt-2">Required: registration_number, first_name, last_name, programme. Dates as YYYY-MM-DD.</div>
        </div></div>
    </div>

    <div class="col-lg-7">
        <div class="card"><div class="card-header">Recent imports</div>
        <div class="table-responsive"><table class="table table-sm mb-0">
            <thead><tr><th>File</th><th>Cohort</th><th>Rows</th><th>Status</th><th></th></tr></thead>
            @forelse ($batches as $b)
                <tr>
                    <td class="small">{{ $b->original_filename }}<div class="text-muted">{{ $b->created_at->format('d M Y H:i') }} &middot; {{ $b->user?->name }}</div></td>
                    <td class="small">{{ $b->cohort->name }}</td>
                    <td class="small">{{ $b->valid_rows }} ok / {{ $b->duplicate_rows }} dup / {{ $b->invalid_rows }} invalid</td>
                    <td><span class="badge text-bg-{{ ['staged' => 'warning', 'committed' => 'success', 'cancelled' => 'secondary'][$b->status] }}">{{ $b->status }}</span></td>
                    <td>@if ($b->isStaged())<a href="{{ route('admin.imports.preview', $b) }}" class="btn btn-sm btn-outline-secondary">Resume</a>@endif</td>
                </tr>
            @empty
                <tr><td class="text-muted">No imports yet.</td></tr>
            @endforelse
        </table></div></div>
    </div>
</div>
@endsection
