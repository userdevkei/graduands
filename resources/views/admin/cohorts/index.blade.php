@extends('layouts.admin')
@section('title', 'Cohorts')
@section('subtitle', 'Each cohort has its own confirmation window. Students can only submit while it is open.')
@section('actions')
    <a href="{{ route('admin.cohorts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New cohort</a>
@endsection

@section('content')
<div class="panel">
    <div class="dt-wrap">
        <table id="cohorts" class="table table-pro align-middle w-100">
            <thead>
                <tr><th>Cohort</th><th>Confirmation window</th><th style="min-width:180px">Progress</th><th>State</th><th class="text-end" data-orderable="false"></th></tr>
            </thead>
            <tbody>
                @foreach ($cohorts as $c)
                    @php
                        [$label, $tone] = $c->windowState();
                        $p = $c->graduands_count ? round($c->completed_count / $c->graduands_count * 100) : 0;
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $c->name }}</div>
                            <div class="sub">Graduating {{ $c->graduation_year }}</div>
                        </td>
                        <td data-order="{{ $c->closes_at?->timestamp ?? 0 }}">
                            <div>{{ $c->opens_at?->format('j M Y, H:i') ?? 'Any time' }}</div>
                            <div class="sub">to {{ $c->closes_at?->format('j M Y, H:i') ?? 'no deadline' }}</div>
                        </td>
                        <td data-order="{{ $p }}">
                            <div class="d-flex justify-content-between sub mb-1"><span>{{ $c->completed_count }} of {{ $c->graduands_count }}</span><span>{{ $p }}%</span></div>
                            <div class="bar"><span style="width:{{ $p }}%"></span></div>
                        </td>
                        <td data-order="{{ $label }}"><span class="chip chip-{{ $tone }}">{{ $label }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.graduands.index', ['cohort_id' => $c->id]) }}" class="btn btn-ghost btn-sm">Graduands</a>
                            <a href="{{ route('admin.cohorts.edit', $c) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.cohorts.destroy', $c) }}" class="d-inline"
                                  onsubmit="return confirm('Delete {{ addslashes($c->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger-soft btn-sm" aria-label="Delete cohort"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#cohorts').DataTable({
        dom: dtDom, language: { ...dtLang, emptyTable: 'No cohorts yet. Create one to start importing graduands.' },
        pageLength: 10, order: [[0, 'desc']], columnDefs: [{ orderable: false, targets: 4 }]
    });
</script>
@endpush
