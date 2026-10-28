@extends('layouts.admin')
@section('title', 'Cohorts')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h1 class="h4">Cohorts</h1>
    <a href="{{ route('admin.cohorts.create') }}" class="btn btn-dark">New cohort</a>
</div>
<div class="card"><div class="table-responsive"><table class="table mb-0 align-middle">
    <thead><tr><th>Name</th><th>Year</th><th>Window</th><th>Graduands</th><th>State</th><th></th></tr></thead>
    @foreach ($cohorts as $c)
        <tr>
            <td>{{ $c->name }}</td>
            <td>{{ $c->graduation_year }}</td>
            <td class="small">{{ $c->opens_at?->format('d M Y H:i') ?? 'any time' }} &rarr; {{ $c->closes_at?->format('d M Y H:i') ?? 'no deadline' }}</td>
            <td>{{ $c->graduands_count }}</td>
            <td>
                @if ($c->isOpen()) <span class="badge text-bg-success">Open</span>
                @else <span class="badge text-bg-secondary">Closed</span> @endif
            </td>
            <td class="text-end text-nowrap">
                <a href="{{ route('admin.cohorts.edit', $c) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form method="POST" action="{{ route('admin.cohorts.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Delete this cohort?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
</table></div></div>
@endsection
