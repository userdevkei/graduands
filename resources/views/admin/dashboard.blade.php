@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Dashboard</h1>
    <form method="GET" class="d-flex gap-2">
        <select name="cohort_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All cohorts</option>
            @foreach ($cohorts as $c)
                <option value="{{ $c->id }}" @selected($cohortId === $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['Total graduands', $stats['total'], 'dark'],
        ['Pending', $stats['pending'], 'warning'],
        ['Confirmed as-is', $stats['confirmed'], 'success'],
        ['Updated by student', $stats['updated'], 'primary'],
    ] as [$label, $value, $color])
        <div class="col-6 col-lg-3">
            <div class="card border-{{ $color }}"><div class="card-body">
                <div class="text-muted small">{{ $label }}</div>
                <div class="display-6">{{ number_format($value) }}</div>
            </div></div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card"><div class="card-header">Progress by cohort ({{ $stats['percent_done'] }}% overall)</div>
        <div class="card-body">
            @forelse ($progress as $c)
                @php $pct = $c->graduands_count ? round($c->completed_count / $c->graduands_count * 100) : 0; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between small">
                        <span>{{ $c->name }} @unless($c->isOpen())<span class="badge text-bg-secondary">window closed</span>@endunless</span>
                        <span>{{ $c->completed_count }} / {{ $c->graduands_count }}</span>
                    </div>
                    <div class="progress"><div class="progress-bar" style="width: {{ $pct }}%">{{ $pct }}%</div></div>
                </div>
            @empty
                <p class="text-muted mb-0">No cohorts yet.</p>
            @endforelse
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card"><div class="card-header">Recent activity</div>
        <div class="table-responsive"><table class="table table-sm mb-0">
            @forelse ($recent as $log)
                <tr>
                    <td class="small text-muted text-nowrap">{{ $log->created_at->diffForHumans() }}</td>
                    <td>{{ $log->action }}</td>
                    <td class="small">{{ $log->user?->name ?? 'student/system' }}</td>
                </tr>
            @empty
                <tr><td class="text-muted">Nothing yet.</td></tr>
            @endforelse
        </table></div></div>
    </div>
</div>
@endsection
