@extends('layouts.admin')
@section('title', 'Import preview')

@section('content')
@php $badge = ['valid' => 'success', 'duplicate' => 'warning', 'invalid' => 'danger']; @endphp

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h1 class="h4 mb-1">Preview import</h1>
        <div class="text-muted">{{ $batch->original_filename }} &rarr; {{ $batch->cohort->name }}</div>
    </div>
    @if ($batch->isStaged())
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.imports.destroy', $batch) }}" onsubmit="return confirm('Discard this upload?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-secondary">Cancel import</button>
            </form>
            <form method="POST" action="{{ route('admin.imports.commit', $batch) }}">
                @csrf
                <button class="btn btn-success" @disabled($batch->valid_rows === 0)>Import {{ $batch->valid_rows }} valid row(s)</button>
            </form>
        </div>
    @endif
</div>

@if ($batch->isStaged())
    <div class="alert alert-info">Nothing has been saved yet. Only <strong>valid</strong> rows are imported; duplicates and invalid rows are skipped.</div>
@else
    <div class="alert alert-secondary">This import is <strong>{{ $batch->status }}</strong>.</div>
@endif

<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link {{ ! $status ? 'active' : '' }}" href="{{ route('admin.imports.preview', $batch) }}">All ({{ $batch->total_rows }})</a></li>
    @foreach (['valid' => $batch->valid_rows, 'duplicate' => $batch->duplicate_rows, 'invalid' => $batch->invalid_rows] as $s => $n)
        <li class="nav-item"><a class="nav-link {{ $status === $s ? 'active' : '' }}" href="{{ route('admin.imports.preview', [$batch, 'status' => $s]) }}">{{ ucfirst($s) }} ({{ $n }})</a></li>
    @endforeach
</ul>

<div class="card"><div class="table-responsive"><table class="table table-sm mb-0 align-middle">
    <thead><tr><th>Row</th><th>Status</th><th>Reg. no.</th><th>Name</th><th>Gender</th><th>DOB</th><th>Programme</th><th>Issues</th></tr></thead>
    @forelse ($rows as $r)
        @php $d = $r->data; @endphp
        <tr>
            <td>{{ $r->row_number }}</td>
            <td><span class="badge text-bg-{{ $badge[$r->status] }}">{{ $r->status }}</span></td>
            <td>{{ $d['registration_number'] ?? '' }}</td>
            <td>{{ trim(($d['first_name'] ?? '').' '.($d['middle_name'] ?? '').' '.($d['last_name'] ?? '')) }}</td>
            <td>{{ $d['gender'] ?? '' }}</td>
            <td>{{ $d['date_of_birth'] ?? '' }}</td>
            <td class="small">{{ $d['programme'] ?? '' }}</td>
            <td class="small text-danger">{{ $r->errors ? implode(' ', $r->errors) : '' }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="text-center text-muted py-4">No rows.</td></tr>
    @endforelse
</table></div></div>
<div class="mt-3">{{ $rows->links() }}</div>
@endsection
