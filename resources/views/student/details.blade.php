@extends('layouts.student')

@section('content')
@php
    $locked = $graduand->isLocked();
    $canEdit = ! $locked && $windowOpen;
@endphp

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="text-muted small">Registration number</div>
                <div class="fw-semibold">{{ $graduand->registration_number }}</div>
            </div>
            <form method="POST" action="{{ route('student.exit') }}">@csrf
                <button class="btn btn-sm btn-outline-secondary">Exit</button>
            </form>
        </div>
        <hr>
        <div class="row small">
            <div class="col-md-4"><div class="text-muted">Programme</div>{{ $graduand->programme }}</div>
            <div class="col-md-4"><div class="text-muted">Award</div>{{ $graduand->award ?? '-' }}</div>
            <div class="col-md-4"><div class="text-muted">Classification</div>{{ $graduand->classification ?? '-' }}</div>
        </div>
        <div class="text-muted small mt-2">{{ $graduand->cohort->name }}</div>
    </div>
</div>

@if ($locked)
    <div class="alert alert-success">
        <strong>Your record is locked.</strong>
        You {{ $graduand->status === 'updated' ? 'submitted updated details' : 'confirmed your details' }}
        on {{ $graduand->submitted_at->format('d M Y, H:i') }}. Further changes are not possible online.
        If something is wrong, please contact the Registry.
    </div>
@elseif (! $windowOpen)
    <div class="alert alert-secondary">
        Confirmation is not open for your cohort right now
        @if ($graduand->cohort->opens_at && $graduand->cohort->opens_at->isFuture())
            (opens {{ $graduand->cohort->opens_at->format('d M Y, H:i') }}).
        @else
            (it closed on {{ $graduand->cohort->closes_at?->format('d M Y, H:i') }}). Please contact the Registry.
        @endif
    </div>
@else
    <div class="alert alert-warning">
        <strong>One-time submission.</strong> Check every detail carefully. Once you press <em>Submit and lock</em>,
        you <strong>cannot change anything again</strong>. These are the names and details that will be printed on your
        certificate and graduation booklet.
        @if ($graduand->cohort->closes_at)
            <div class="mt-1">Deadline: <strong>{{ $graduand->cohort->closes_at->format('d M Y, H:i') }}</strong></div>
        @endif
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('student.update') }}"
              onsubmit="return confirm('This can only be submitted ONCE. Are you sure all your details are correct?');">
            @csrf
            @method('PUT')

            @include('partials.graduand-fields', ['graduand' => $graduand, 'disabled' => ! $canEdit])

            @if ($canEdit)
                <div class="form-check mt-4">
                    <input class="form-check-input @error('acknowledge') is-invalid @enderror" type="checkbox" name="acknowledge" id="acknowledge" value="1">
                    <label class="form-check-label" for="acknowledge">
                        I understand that I can submit only once and that my record will be locked afterwards.
                    </label>
                    @error('acknowledge')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary mt-3">Submit and lock my record</button>
                <div class="text-muted small mt-2">If everything is already correct, just tick the box and submit to confirm it.</div>
            @endif
        </form>
    </div>
</div>
@endsection
