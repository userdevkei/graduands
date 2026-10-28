@extends('layouts.admin')
@section('title', 'Graduands')

@section('content')
@php $badge = ['pending' => 'warning', 'confirmed' => 'success', 'updated' => 'primary']; @endphp
<div class="d-flex justify-content-between mb-3">
    <h1 class="h4">Graduands</h1>
    <a href="{{ route('admin.graduands.export', $filters) }}" class="btn btn-outline-dark">Export CSV</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="cohort_id" class="form-select">
            <option value="">All cohorts</option>
            @foreach ($cohorts as $c)
                <option value="{{ $c->id }}" @selected(($filters['cohort_id'] ?? null) === $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Any status</option>
            @foreach (['pending', 'confirmed', 'updated'] as $s)
                <option value="{{ $s }}" @selected(($filters['status'] ?? null) === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4"><input name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Search reg. no. or name"></div>
    <div class="col-md-2"><button class="btn btn-dark w-100">Filter</button></div>
</form>

<div class="card"><div class="table-responsive"><table class="table table-hover mb-0 align-middle">
    <thead><tr><th>Reg. no.</th><th>Name</th><th>Programme</th><th>Cohort</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
    @forelse ($graduands as $g)
        <tr>
            <td>{{ $g->registration_number }}</td>
            <td>{{ $g->full_name }}</td>
            <td class="small">{{ $g->programme }}</td>
            <td class="small">{{ $g->cohort->name }}</td>
            <td><span class="badge text-bg-{{ $badge[$g->status] }}">{{ ucfirst($g->status) }}</span></td>
            <td class="small text-muted">{{ $g->submitted_at?->format('d M Y H:i') ?? '-' }}</td>
            <td class="text-end"><a href="{{ route('admin.graduands.show', $g) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-muted text-center py-4">No graduands found.</td></tr>
    @endforelse
</table></div></div>
<div class="mt-3">{{ $graduands->links() }}</div>
@endsection
