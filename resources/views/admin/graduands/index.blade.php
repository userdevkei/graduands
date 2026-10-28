@extends('layouts.admin')
@section('title', 'Graduands')
@section('subtitle', 'Every graduand on record, with their confirmation status.')
@section('actions')
    <a id="export" href="{{ route('admin.graduands.export') }}" class="btn btn-ghost"><i class="bi bi-download me-1"></i>Export CSV</a>
    <a href="{{ route('admin.imports.create') }}" class="btn btn-primary"><i class="bi bi-cloud-arrow-up me-1"></i>Import</a>
@endsection

@section('content')
@php $initialCohort = request('cohort_id'); $initialStatus = request('status'); @endphp
<div class="panel">
    <div class="panel-head">
        <div class="seg-tabs" id="status-tabs" role="group" aria-label="Filter by status">
            @foreach (['' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'updated' => 'Updated'] as $value => $label)
                <button type="button" data-status="{{ $value }}" class="{{ ($initialStatus ?? '') === $value ? 'active' : '' }}">{{ $label }}</button>
            @endforeach
        </div>
        <select id="f-cohort" class="form-select form-select-sm w-auto" aria-label="Filter by cohort">
            <option value="">All cohorts</option>
            @foreach ($cohorts as $c)
                <option value="{{ $c->id }}" @selected($initialCohort === $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="dt-wrap">
        <table id="graduands" class="table table-pro clickable align-middle w-100">
            <thead><tr><th>Graduand</th><th>Programme</th><th>Cohort</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let status = @json($initialStatus ?? '');
    const labels = { pending: 'Pending', confirmed: 'Confirmed', updated: 'Updated' };
    const exportUrl = @json(route('admin.graduands.export'));

    const table = $('#graduands').DataTable({
        processing: true, serverSide: true, searchDelay: 350, pageLength: 25,
        dom: dtDom, order: [[0, 'asc']],
        language: { ...dtLang, emptyTable: 'No graduands yet. Import a list to get started.', zeroRecords: 'No graduands match your search.', searchPlaceholder: 'Search name or registration number' },
        ajax: { url: @json(route('admin.graduands.data')), data: d => { d.cohort_id = $('#f-cohort').val(); d.status = status; } },
        columns: [
            { data: 'full_name', render: (d, t, r) => t === 'display'
                ? `<div class="who"><span class="ini">${esc(initials(r.full_name))}</span><div><div class="fw-semibold">${esc(r.full_name)}</div><div class="sub">${esc(r.registration_number)}</div></div></div>` : d },
            { data: 'programme', render: $.fn.dataTable.render.text() },
            { data: 'cohort', orderable: false, render: $.fn.dataTable.render.text() },
            { data: 'status', render: (d, t) => t === 'display' ? `<span class="chip chip-${esc(d)}">${esc(labels[d] || d)}</span>` : d },
            { data: 'submitted_at', render: (d, t) => d ? esc(d) : (t === 'display' ? '<span class="muted">Not yet</span>' : '') },
            { data: 'url', orderable: false, className: 'text-end', render: (d, t) => t === 'display' ? `<a href="${esc(d)}" class="btn btn-ghost btn-sm">View</a>` : '' }
        ],
        createdRow: (row, data) => $(row).on('click', e => { if (!$(e.target).closest('a,button').length) window.location = data.url; })
    });

    function syncExport() {
        $('#export').attr('href', exportUrl + '?' + $.param({ cohort_id: $('#f-cohort').val(), status: status, search: table.search() }));
    }
    table.on('draw', syncExport);
    $('#f-cohort').on('change', () => table.ajax.reload());
    $('#status-tabs').on('click', 'button', function () {
        status = $(this).data('status');
        $('#status-tabs button').removeClass('active'); $(this).addClass('active');
        table.ajax.reload();
    });
</script>
@endpush
