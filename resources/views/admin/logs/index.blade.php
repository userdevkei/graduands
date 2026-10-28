@extends('layouts.admin')
@section('title', 'Audit log')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h1 class="h4">Audit log</h1>
    <form method="GET" class="d-flex gap-2">
        <input name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Filter by action">
        <button class="btn btn-sm btn-dark">Filter</button>
    </form>
</div>
<div class="card"><div class="table-responsive"><table class="table table-sm mb-0 align-middle">
    <thead><tr><th>When</th><th>Who</th><th>Action</th><th>Record</th><th>Old</th><th>New</th><th>IP</th></tr></thead>
    @foreach ($logs as $log)
        <tr>
            <td class="small text-nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
            <td class="small">{{ $log->user?->name ?? 'student/system' }}</td>
            <td>{{ $log->action }}</td>
            <td class="small">{{ $log->model_type ? class_basename($log->model_type) : '' }} {{ $log->model_id }}</td>
            <td class="small"><code>{{ $log->old_values ? json_encode($log->old_values) : '' }}</code></td>
            <td class="small"><code>{{ $log->new_values ? json_encode($log->new_values) : '' }}</code></td>
            <td class="small text-muted">{{ $log->ip_address }}</td>
        </tr>
    @endforeach
</table></div></div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection
