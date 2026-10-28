@extends('layouts.admin')
@section('title', $graduand->full_name)

@php
    $original = $graduand->original_data;
    $inits = mb_strtoupper(mb_substr($graduand->first_name, 0, 1).mb_substr($graduand->last_name, 0, 1));
@endphp

@section('header')
<div class="page-head">
    <div class="d-flex align-items-center gap-3">
        <span class="ini ini-lg">{{ $inits }}</span>
        <div>
            <h1 class="page-title">{{ $graduand->full_name }}</h1>
            <p class="page-sub d-flex flex-wrap align-items-center gap-2">
                <span>{{ $graduand->registration_number }}</span>
                <span class="muted">in {{ $graduand->cohort->name }}</span>
                @include('partials.status-chip', ['status' => $graduand->status])
            </p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.graduands.index', ['cohort_id' => $graduand->cohort_id]) }}" class="btn btn-ghost">Back to list</a>
        <a href="{{ route('admin.graduands.edit', $graduand) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit record</a>
    </div>
</div>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-xl-7">
        <div class="row g-3 mb-4">
            @foreach (['Programme' => $graduand->programme, 'Award' => $graduand->award, 'Classification' => $graduand->classification] as $k => $v)
                <div class="col-md-4"><div class="panel panel-body py-3"><div class="sub">{{ $k }}</div><div class="fw-semibold">{{ $v ?: '-' }}</div></div></div>
            @endforeach
        </div>

        <div class="panel mb-4">
            <div class="panel-head">
                <h2 class="panel-title">Details</h2>
                @if ($original)<span class="small-muted">Highlighted rows were changed by the student</span>@endif
            </div>
            <div class="kv">
                @foreach (\App\Models\Graduand::EDITABLE_LABELS as $field => $label)
                    @php
                        $current = $field === 'date_of_birth' ? $graduand->date_of_birth?->format('Y-m-d') : $graduand->{$field};
                        $before = $original[$field] ?? null;
                        $changed = $original && $before !== $current;
                    @endphp
                    <div class="k {{ $changed ? 'changed' : '' }}">{{ $label }}</div>
                    <div class="{{ $changed ? 'changed' : '' }}">
                        @if ($changed)
                            <span class="diff-old">{{ $before ?: 'empty' }}</span><strong>{{ $current ?: 'empty' }}</strong>
                        @else
                            {{ $current ?: '-' }}
                        @endif
                    </div>
                @endforeach
                <div class="k">Name on portal</div><div>{{ $graduand->portal_first_name }}</div>
                <div class="k">Submitted</div>
                <div>{{ $graduand->submitted_at ? $graduand->submitted_at->format('j M Y, H:i') : 'Not yet' }} @if($graduand->submitted_ip)<span class="sub">from {{ $graduand->submitted_ip }}</span>@endif</div>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        @if ($graduand->isLocked() && auth()->user()->isSuperAdmin())
            <div class="panel mb-4">
                <div class="panel-head"><h2 class="panel-title">Unlock record</h2></div>
                <div class="panel-body">
                    <p class="muted">This gives the student one more update. The reason is saved in the audit log.</p>
                    <form method="POST" action="{{ route('admin.graduands.unlock', $graduand) }}">
                        @csrf
                        <input name="reason" class="form-control mb-2 @error('reason') is-invalid @enderror" placeholder="Why is this being unlocked?" required>
                        @error('reason')<div class="invalid-feedback d-block mb-2">{{ $message }}</div>@enderror
                        <button class="btn btn-danger-soft"><i class="bi bi-unlock me-1"></i>Unlock record</button>
                    </form>
                </div>
            </div>
        @endif

        <div class="panel">
            <div class="panel-head"><h2 class="panel-title">History</h2></div>
            <ul class="timeline">
                @forelse ($history as $log)
                    @php $m = \App\Support\ActionLabel::for($log->action); @endphp
                    <li>
                        <span class="tl-dot tone-{{ $m['tone'] }}"><i class="bi {{ $m['icon'] }}"></i></span>
                        <div>
                            <div class="fw-semibold">{{ $m['label'] }}</div>
                            <div class="sub">{{ $log->user?->name ?? 'Student / system' }}, {{ $log->created_at->format('j M Y, H:i') }}</div>
                            @if ($log->action === 'admin.unlocked_record' && ($log->new_values['reason'] ?? null))
                                <div class="sub">Reason: {{ $log->new_values['reason'] }}</div>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="empty w-100">No activity on this record yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
