@extends('layouts.admin')
@section('title', 'Dashboard')

@php
    $total = $stats['total'];
    $done = $stats['confirmed'] + $stats['updated'];
    $pct = fn ($n) => $total ? round($n / $total * 100, 2) : 0;
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $firstName = explode(' ', trim(auth()->user()->name))[0];
@endphp

@section('header')
<div class="page-head">
    <div>
        <h1 class="page-title">{{ $greeting }}, {{ $firstName }}</h1>
        <p class="page-sub">{{ now()->format('l, j F Y') }}</p>
    </div>
    <form method="GET">
        <select name="cohort_id" class="form-select" onchange="this.form.submit()" aria-label="Filter by cohort">
            <option value="">All cohorts</option>
            @foreach ($cohorts as $c)
                <option value="{{ $c->id }}" @selected($cohortId === $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </form>
</div>
@endsection

@section('content')
<section class="hero mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-5">
            <div class="hero-label">Confirmation progress</div>
            @if ($total)
                <div class="hero-figure">{{ $stats['percent_done'] }}<small>%</small></div>
                <p class="hero-sub">{{ number_format($done) }} of {{ number_format($total) }} graduands have submitted their details.</p>
            @else
                <div class="hero-figure" style="font-size:2.4rem">No graduands yet</div>
                <p class="hero-sub">Import a list to start collecting confirmations.</p>
            @endif
            <div class="d-flex flex-wrap gap-2 mt-3">
                <a href="{{ route('admin.imports.create') }}" class="btn btn-gold"><i class="bi bi-cloud-arrow-up me-1"></i>Import a list</a>
                <a href="{{ route('admin.graduands.export', array_filter(['cohort_id' => $cohortId])) }}" class="btn btn-ghost-dark"><i class="bi bi-download me-1"></i>Export CSV</a>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="seg" role="img" aria-label="{{ $stats['confirmed'] }} confirmed, {{ $stats['updated'] }} updated, {{ $stats['pending'] }} pending">
                @if ($stats['confirmed'])<span class="s-confirmed" data-w="{{ $pct($stats['confirmed']) }}"></span>@endif
                @if ($stats['updated'])<span class="s-updated" data-w="{{ $pct($stats['updated']) }}"></span>@endif
                @if ($stats['pending'] || ! $total)<span class="s-pending" data-w="{{ $total ? $pct($stats['pending']) : 100 }}" @if(! $total) style="opacity:.25" @endif></span>@endif
            </div>
            <div class="legend">
                @foreach ([
                    ['Confirmed as is', $stats['confirmed'], '#5ed38f'],
                    ['Updated by student', $stats['updated'], '#9fb4ee'],
                    ['Not yet submitted', $stats['pending'], '#f1ea14'],
                ] as [$label, $n, $color])
                    <div>
                        <div class="lbl"><span class="dot" style="background:{{ $color }}"></span>{{ $label }}</div>
                        <b>{{ number_format($n) }}</b>
                        <span class="pc">{{ $total ? round($n / $total * 100) : 0 }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="panel h-100">
            <div class="panel-head">
                <h2 class="panel-title">Submissions in the last 14 days</h2>
                <span class="small-muted">{{ array_sum(array_column($trend, 'count')) }} in total</span>
            </div>
            <div class="panel-body"><div style="height:250px"><canvas id="trend" aria-label="Submissions per day" role="img"></canvas></div></div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="panel h-100">
            <div class="panel-head">
                <h2 class="panel-title">Cohorts</h2>
                <a href="{{ route('admin.cohorts.index') }}" class="small fw-semibold">Manage</a>
            </div>
            @forelse ($progress as $c)
                @php
                    [$stateLabel, $tone] = $c->windowState();
                    $p = $c->graduands_count ? round($c->completed_count / $c->graduands_count * 100) : 0;
                @endphp
                <div class="row-item d-block">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="ttl">{{ $c->name }}</div>
                            <div class="sub">{{ number_format($c->completed_count) }} of {{ number_format($c->graduands_count) }} submitted</div>
                        </div>
                        <span class="chip chip-{{ $tone }}">{{ $stateLabel }}</span>
                    </div>
                    <div class="bar"><span style="width:{{ $p }}%"></span></div>
                </div>
            @empty
                <div class="empty"><i class="bi bi-collection"></i>No cohorts yet. <a href="{{ route('admin.cohorts.create') }}">Create the first one.</a></div>
            @endforelse
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="panel h-100">
            <div class="panel-head">
                <h2 class="panel-title">Recent corrections</h2>
                <a href="{{ route('admin.graduands.index', ['status' => 'updated']) }}" class="small fw-semibold">See all updated</a>
            </div>
            @forelse ($recentUpdates as $g)
                <div class="row-item">
                    <span class="ini">{{ collect(explode(' ', $g->full_name))->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->first() }}{{ mb_strtoupper(mb_substr($g->last_name, 0, 1)) }}</span>
                    <div class="grow">
                        <div class="ttl">{{ $g->full_name }}</div>
                        <div class="sub">{{ $g->registration_number }}, {{ $g->cohort->name }}</div>
                    </div>
                    <div class="sub text-end d-none d-sm-block">{{ $g->submitted_at?->diffForHumans() }}</div>
                    <a href="{{ route('admin.graduands.show', $g) }}" class="btn btn-ghost btn-sm">Review</a>
                </div>
            @empty
                <div class="empty"><i class="bi bi-pencil-square"></i>No corrections yet. Students who change their details will show up here for review.</div>
            @endforelse
        </div>
    </div>

    <div class="col-xl-5">
        <div class="panel h-100">
            <div class="panel-head">
                <h2 class="panel-title">Recent activity</h2>
                <a href="{{ route('admin.logs.index') }}" class="small fw-semibold">Audit log</a>
            </div>
            <ul class="timeline">
                @forelse ($recent as $log)
                    @php $m = \App\Support\ActionLabel::for($log->action); @endphp
                    <li>
                        <span class="tl-dot tone-{{ $m['tone'] }}"><i class="bi {{ $m['icon'] }}"></i></span>
                        <div>
                            <div class="fw-semibold">{{ $m['label'] }}</div>
                            <div class="sub">{{ $log->user?->name ?? 'Student / system' }}, {{ $log->created_at->diffForHumans() }}</div>
                        </div>
                    </li>
                @empty
                    <li class="empty w-100">Nothing has happened yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    document.querySelectorAll('.seg > span[data-w]').forEach(el => requestAnimationFrame(() => requestAnimationFrame(() => { el.style.width = el.dataset.w + '%'; })));

    const trend = @json($trend);
    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.color = '#5d6f64';
    new Chart(document.getElementById('trend'), {
        type: 'bar',
        data: {
            labels: trend.map(t => t.label),
            datasets: [{ data: trend.map(t => t.count), backgroundColor: '#05994b', borderRadius: 6, maxBarThickness: 28 }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => c.parsed.y + (c.parsed.y === 1 ? ' submission' : ' submissions') } } },
            scales: {
                x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 7 } },
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#ebf1ed' }, border: { display: false } }
            }
        }
    });
</script>
@endpush
