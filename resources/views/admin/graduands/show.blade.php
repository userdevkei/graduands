@extends('layouts.admin')
@section('title', $graduand->full_name)

@section('content')
@php
    $badge = ['pending' => 'warning', 'confirmed' => 'success', 'updated' => 'primary'][$graduand->status];
    $original = $graduand->original_data;
@endphp

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h1 class="h4 mb-1">{{ $graduand->full_name }}</h1>
        <span class="text-muted">{{ $graduand->registration_number }}</span>
        <span class="badge text-bg-{{ $badge }} ms-2">{{ ucfirst($graduand->status) }}</span>
    </div>
    <a href="{{ route('admin.graduands.edit', $graduand) }}" class="btn btn-outline-dark">Edit record</a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card mb-3"><div class="card-header">Details {{ $original ? '(original vs current)' : '' }}</div>
        <div class="table-responsive"><table class="table mb-0">
            @foreach (\App\Models\Graduand::EDITABLE_LABELS as $field => $label)
                @php
                    $current = $field === 'date_of_birth' ? $graduand->date_of_birth?->format('Y-m-d') : $graduand->{$field};
                    $before = $original[$field] ?? null;
                    $changed = $original && $before !== $current;
                @endphp
                <tr class="{{ $changed ? 'table-warning' : '' }}">
                    <th style="width: 30%">{{ $label }}</th>
                    <td>
                        @if ($changed)
                            <del class="text-muted">{{ $before ?? '-' }}</del> &rarr; <strong>{{ $current ?? '-' }}</strong>
                        @else
                            {{ $current ?? '-' }}
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr><th>Programme</th><td>{{ $graduand->programme }}</td></tr>
            <tr><th>Award</th><td>{{ $graduand->award ?? '-' }}</td></tr>
            <tr><th>Classification</th><td>{{ $graduand->classification ?? '-' }}</td></tr>
            <tr><th>Cohort</th><td>{{ $graduand->cohort->name }}</td></tr>
            <tr><th>Portal first name</th><td>{{ $graduand->portal_first_name }}</td></tr>
            <tr><th>Submitted</th><td>{{ $graduand->submitted_at?->format('d M Y H:i') ?? '-' }} {{ $graduand->submitted_ip ? '('.$graduand->submitted_ip.')' : '' }}</td></tr>
        </table></div></div>

        @if ($graduand->isLocked() && auth()->user()->isSuperAdmin())
            <div class="card border-danger"><div class="card-body">
                <h2 class="h6">Unlock record</h2>
                <p class="small text-muted">Gives the student one more update. The reason is written to the audit log.</p>
                <form method="POST" action="{{ route('admin.graduands.unlock', $graduand) }}" class="d-flex gap-2">
                    @csrf
                    <input name="reason" class="form-control @error('reason') is-invalid @enderror" placeholder="Reason" required>
                    <button class="btn btn-danger">Unlock</button>
                </form>
                @error('reason')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div></div>
        @endif
    </div>

    <div class="col-lg-5">
        <div class="card"><div class="card-header">History</div>
        <ul class="list-group list-group-flush">
            @forelse ($history as $log)
                <li class="list-group-item small">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $log->action }}</strong>
                        <span class="text-muted">{{ $log->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="text-muted">{{ $log->user?->name ?? 'student/system' }} &middot; {{ $log->ip_address }}</div>
                </li>
            @empty
                <li class="list-group-item text-muted">No activity yet.</li>
            @endforelse
        </ul></div>
    </div>
</div>
@endsection
