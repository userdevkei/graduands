@extends('layouts.admin')
@section('title', 'Audit log')
@section('subtitle', 'Who did what, and when. Student actions appear as Student / system.')

@section('content')
<div class="panel">
    <div class="dt-wrap">
        <table id="logs" class="table table-pro align-middle w-100">
            <thead><tr><th>When</th><th>Activity</th><th>Who</th><th>Record</th><th>IP address</th><th></th></tr></thead>
        </table>
    </div>
</div>

<div class="modal fade" id="changes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content" style="border-radius:16px">
        <div class="modal-header"><h2 class="modal-title h5" id="changes-title">Changes</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body p-0">
            <table class="table table-pro mb-0"><thead><tr><th>Field</th><th>Before</th><th>After</th></tr></thead><tbody id="changes-body"></tbody></table>
        </div>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
    const table = $('#logs').DataTable({
        processing: true, serverSide: true, searchDelay: 350, pageLength: 25, dom: dtDom, order: [[0, 'desc']],
        language: { ...dtLang, emptyTable: 'No activity yet.', zeroRecords: 'No activity matches your search.', searchPlaceholder: 'Search action, record or IP' },
        ajax: @json(route('admin.logs.data')),
        columns: [
            { data: 'when', className: 'text-nowrap', render: $.fn.dataTable.render.text() },
            { data: 'action', orderable: false, render: (d, t, r) => t === 'display'
                ? `<div class="who"><span class="tl-dot tone-${esc(r.meta.tone)}"><i class="bi ${esc(r.meta.icon)}"></i></span><div><div class="fw-semibold">${esc(r.meta.label)}</div><div class="sub">${esc(d)}</div></div></div>` : d },
            { data: 'who', orderable: false, render: $.fn.dataTable.render.text() },
            { data: 'record', orderable: false, render: (d, t) => t === 'display' ? `<span class="sub">${esc(d)}</span>` : d },
            { data: 'ip', orderable: false, render: $.fn.dataTable.render.text() },
            { data: null, orderable: false, className: 'text-end', render: (d, t, r) => t === 'display' && (r.old || r.new) ? '<button class="btn btn-ghost btn-sm btn-diff">Changes</button>' : '' }
        ]
    });

    const modal = new bootstrap.Modal('#changes');
    $('#logs').on('click', '.btn-diff', function () {
        const r = table.row($(this).closest('tr')).data();
        const keys = [...new Set([...Object.keys(r.old || {}), ...Object.keys(r.new || {})])];
        const body = $('#changes-body').empty();
        const fmt = v => v === null || v === undefined || v === '' ? '-' : (typeof v === 'object' ? JSON.stringify(v) : String(v));
        keys.forEach(k => body.append($('<tr>').append($('<td class="fw-semibold">').text(k.replace(/_/g, ' ')), $('<td class="muted">').text(fmt((r.old || {})[k])), $('<td>').text(fmt((r.new || {})[k])))));
        $('#changes-title').text(r.meta.label);
        modal.show();
    });
</script>
@endpush
