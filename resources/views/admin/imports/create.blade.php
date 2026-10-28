@extends('layouts.admin')
@section('title', 'Import graduands')
@section('subtitle', 'Upload a list, check it, then import. Nothing is saved until you confirm.')
@section('actions')
    <a href="{{ route('admin.imports.template') }}" class="btn btn-ghost"><i class="bi bi-file-earmark-arrow-down me-1"></i>Download template</a>
@endsection

@section('content')
<div class="mb-4">
    @include('partials.steps', ['current' => 1, 'light' => true, 'labels' => ['Upload a file', 'Review the preview', 'Import']])
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <form method="POST" action="{{ route('admin.imports.store') }}" enctype="multipart/form-data" class="panel mb-4">
            @csrf
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label" for="cohort_id">Cohort</label>
                    <select id="cohort_id" name="cohort_id" class="form-select @error('cohort_id') is-invalid @enderror" required>
                        <option value="">Choose the cohort these graduands belong to</option>
                        @foreach ($cohorts as $c)
                            <option value="{{ $c->id }}" @selected(old('cohort_id') === $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('cohort_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if ($cohorts->isEmpty())<div class="form-text">Create a cohort first from the Cohorts page.</div>@endif
                </div>

                <label class="dropzone" id="dropzone" for="file">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span class="fw-semibold d-block" id="file-title">Choose a file or drop it here</span>
                    <span class="small" id="file-hint">CSV, XLSX or XLS, up to 10 MB</span>
                    <input type="file" id="file" name="file" accept=".csv,.xlsx,.xls,.txt" required>
                </label>
                @error('file')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </div>
            <div class="panel-body" style="border-top:1px solid var(--line-2)">
                <button class="btn btn-primary"><i class="bi bi-eye me-1"></i>Upload and preview</button>
            </div>
        </form>

        <div class="panel">
            <div class="panel-head"><h2 class="panel-title">Columns in your file</h2></div>
            <div class="panel-body">
                <p class="muted mb-2">Put these names in the first row. Highlighted columns are required. Dates can be written as 15/04/2000 or 2000-04-15.</p>
                @foreach ($columns as $col)
                    <span class="col-chip {{ in_array($col, ['registration_number', 'first_name', 'last_name', 'programme']) ? 'req' : '' }}">{{ $col }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="panel">
            <div class="panel-head"><h2 class="panel-title">Recent imports</h2></div>
            @forelse ($batches as $b)
                <div class="row-item">
                    <div class="grow">
                        <div class="ttl text-truncate">{{ $b->original_filename }}</div>
                        <div class="sub">{{ $b->cohort->name }}, {{ $b->created_at->diffForHumans() }}</div>
                        <div class="sub">{{ $b->valid_rows }} valid, {{ $b->duplicate_rows }} duplicate, {{ $b->invalid_rows }} invalid</div>
                    </div>
                    <span class="chip chip-{{ $b->status }}">{{ ucfirst($b->status) }}</span>
                    @if ($b->isStaged())<a href="{{ route('admin.imports.preview', $b) }}" class="btn btn-ghost btn-sm">Resume</a>@endif
                </div>
            @empty
                <div class="empty"><i class="bi bi-cloud-arrow-up"></i>No imports yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const input = document.getElementById('file'), zone = document.getElementById('dropzone');
    input.addEventListener('change', () => {
        const f = input.files[0];
        document.getElementById('file-title').textContent = f ? f.name : 'Choose a file or drop it here';
        document.getElementById('file-hint').textContent = f ? (f.size / 1024).toFixed(0) + ' KB. Ready to preview.' : 'CSV, XLSX or XLS, up to 10 MB';
        zone.classList.toggle('has-file', !!f);
    });
    ['dragover', 'dragenter'].forEach(e => zone.addEventListener(e, ev => { ev.preventDefault(); zone.classList.add('has-file'); }));
    zone.addEventListener('drop', ev => { ev.preventDefault(); if (ev.dataTransfer.files.length) { input.files = ev.dataTransfer.files; input.dispatchEvent(new Event('change')); } });
</script>
@endpush
