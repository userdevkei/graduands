@extends('layouts.admin')
@section('title', 'Review import')

@section('header')
<div class="page-head">
    <div>
        <h1 class="page-title">Review import</h1>
        <p class="page-sub">{{ $batch->original_filename }}, going into {{ $batch->cohort->name }}</p>
    </div>
    @if ($batch->isStaged())
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="{{ route('admin.imports.destroy', $batch) }}" onsubmit="return confirm('Discard this upload? Nothing has been saved yet.')">
                @csrf @method('DELETE')
                <button class="btn btn-ghost">Cancel import</button>
            </form>
            <form method="POST" action="{{ route('admin.imports.commit', $batch) }}">
                @csrf
                <button class="btn btn-primary" @disabled($batch->valid_rows === 0)><i class="bi bi-check2-circle me-1"></i>Import {{ number_format($batch->valid_rows) }} valid {{ \Illuminate\Support\Str::plural('row', $batch->valid_rows) }}</button>
            </form>
        </div>
    @endif
</div>
@endsection

@section('content')
<div class="mb-4">
    @include('partials.steps', ['current' => $batch->isStaged() ? 2 : 3, 'light' => true, 'labels' => ['Upload a file', 'Review the preview', 'Import']])
</div>

@if ($batch->isStaged())
    <div class="note note-info mb-4">
        <i class="bi bi-info-circle-fill"></i>
        <div><strong>Nothing has been saved yet.</strong> Only valid rows are imported. Duplicates and invalid rows are skipped, so fix them in your file and upload it again if you need them.</div>
    </div>
@else
    <div class="note note-muted mb-4"><i class="bi bi-lock"></i><div>This import is {{ $batch->status }}.</div></div>
@endif

<div class="row g-3 mb-4" id="tiles">
    @foreach ([
        ['', 'All rows', $batch->total_rows, 'bi-list-ul', 'tone-muted'],
        ['valid', 'Ready to import', $batch->valid_rows, 'bi-check2-circle', 'tone-ok'],
        ['duplicate', 'Duplicates', $batch->duplicate_rows, 'bi-files', 'tone-warn'],
        ['invalid', 'Need fixing', $batch->invalid_rows, 'bi-exclamation-triangle', 'tone-danger'],
    ] as [$key, $label, $n, $icon, $tone])
        <div class="col-6 col-xl-3">
            <button type="button" class="tile {{ $key === '' ? 'active' : '' }}" data-filter="{{ $key }}">
                <span class="ic {{ $tone }}"><i class="bi {{ $icon }}"></i></span>
                <span><span class="num d-block">{{ number_format($n) }}</span><span class="lb">{{ $label }}</span></span>
            </button>
        </div>
    @endforeach
</div>

<div class="panel">
    <div class="dt-wrap">
        <table id="preview" class="table table-pro align-middle w-100">
            <thead><tr><th>Row</th><th>Status</th><th>Registration no.</th><th>Name</th><th>Gender</th><th>Date of birth</th><th>Programme</th><th>Issues</th></tr></thead>
            <tbody>
                @foreach ($rows as $r)
                    @php $d = $r->data; @endphp
                    <tr>
                        <td>{{ $r->row_number }}</td>
                        <td data-search="{{ $r->status }}" data-order="{{ $r->status }}"><span class="chip chip-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                        <td>{{ $d['registration_number'] ?? '' }}</td>
                        <td>{{ trim(($d['first_name'] ?? '').' '.($d['middle_name'] ?? '').' '.($d['last_name'] ?? '')) }}</td>
                        <td>{{ $d['gender'] ?? '' }}</td>
                        <td>{{ $d['date_of_birth'] ?? '' }}</td>
                        <td>{{ $d['programme'] ?? '' }}</td>
                        <td class="text-danger small">{{ $r->errors ? implode(' ', $r->errors) : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const table = $('#preview').DataTable({
        dom: dtDom, language: { ...dtLang, emptyTable: 'The file has no rows.', zeroRecords: 'No rows match.' },
        pageLength: 25, order: [[0, 'asc']]
    });
    $('#tiles').on('click', '.tile', function () {
        const key = $(this).data('filter');
        $('#tiles .tile').removeClass('active'); $(this).addClass('active');
        table.column(1).search(key ? '^' + key + '$' : '', true, false).draw();
    });
</script>
@endpush
