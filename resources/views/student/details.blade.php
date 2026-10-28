@extends('layouts.student')
@section('title', 'Your details')

@section('content')
@php
    $locked = $graduand->isLocked();
    $canEdit = ! $locked && $windowOpen;
    $nameLabels = ['last_name' => 'Surname / Last name', 'first_name' => 'First name', 'middle_name' => 'Middle / Other names'];
    // names first (Surname, First, Middle), then the remaining fields in their usual order
    $fieldLabels = $nameLabels + \App\Models\Graduand::EDITABLE_LABELS;
    $certificateName = trim(implode(' ', array_filter([
        old('last_name', $graduand->last_name), old('first_name', $graduand->first_name), old('middle_name', $graduand->middle_name),
    ])));
@endphp

<header class="student-head">
    <div class="wrap">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a class="brand p-0" href="{{ route('student.lookup') }}">
                <span class="brand-mark"><img src="{{ asset('images/teau-logo.png') }}" alt="TEAU crest"></span>
                <span class="brand-name">{{ config('app.name') }}</span>
            </a>
            <form method="POST" action="{{ route('student.exit') }}">@csrf
                <button class="btn btn-ghost-dark btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Exit</button>
            </form>
        </div>

        @include('partials.steps', ['current' => $locked ? 3 : 2, 'labels' => ['Find your record', 'Review your details', 'Submit and lock']])

        <h1 class="student-name">{{ $graduand->full_name }}</h1>
        <div class="d-flex flex-wrap gap-2">
            <span class="meta-chip"><i class="bi bi-person-badge"></i>{{ $graduand->registration_number }}</span>
            <span class="meta-chip"><i class="bi bi-journal-bookmark"></i>{{ $graduand->programme }}</span>
            @if ($graduand->award)<span class="meta-chip"><i class="bi bi-award"></i>{{ $graduand->award }}</span>@endif
            @if ($graduand->classification)<span class="meta-chip"><i class="bi bi-star"></i>{{ $graduand->classification }}</span>@endif
        </div>
    </div>
</header>

<main class="wrap student-body">
    @include('partials.flash')

    @if ($locked)
        <div class="panel mb-3"><div class="panel-body d-flex gap-3 align-items-start">
            <span class="lock-icon"><i class="bi bi-lock-fill"></i></span>
            <div>
                <h2 class="h5 mb-1">Your record is locked</h2>
                <p class="mb-0 muted">
                    You {{ $graduand->status === 'updated' ? 'submitted updated details' : 'confirmed your details' }}
                    on {{ $graduand->submitted_at->format('j F Y \a\t H:i') }}. Changes are no longer possible online.
                    If something is wrong, please contact the Registry.
                </p>
            </div>
        </div></div>
    @elseif (! $windowOpen)
        <div class="note note-muted mb-3">
            <i class="bi bi-clock-history"></i>
            <div>
                @if ($graduand->cohort->opens_at && $graduand->cohort->opens_at->isFuture())
                    Confirmation for your cohort opens on <strong>{{ $graduand->cohort->opens_at->format('j F Y, H:i') }}</strong>. You can come back then.
                @else
                    Confirmation for your cohort closed on <strong>{{ $graduand->cohort->closes_at?->format('j F Y, H:i') }}</strong>. Please contact the Registry to make changes.
                @endif
            </div>
        </div>
    @else
        <div class="note note-warn mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>You can submit only once.</strong> Check every detail before you submit. After that, your record is locked and cannot be changed online.
                @if ($graduand->cohort->closes_at)
                    <div class="mt-1">Deadline: <strong>{{ $graduand->cohort->closes_at->format('j F Y, H:i') }}</strong></div>
                @endif
            </div>
        </div>
    @endif

    <div class="note note-info mb-3">
        <i class="bi bi-award"></i>
        <div>
            <strong>This is how your name will appear on your certificate:</strong>
            <div class="fs-4 mt-1" id="name-preview" style="font-family:var(--font-display)">{{ $certificateName }}</div>
            <div class="small mt-1">Names are listed in this order: Surname / Last name, First name, Middle / Other names.</div>
        </div>
    </div>

    @if ($canEdit)
        <form method="POST" action="{{ route('student.update') }}" class="panel"
              onsubmit="return confirm('This can only be submitted once. Are you sure all your details are correct?');">
            @csrf
            @method('PUT')
            <div class="panel-body pb-1">
                @include('partials.graduand-fields', ['graduand' => $graduand, 'nameOrder' => ['last_name', 'first_name', 'middle_name'], 'labelOverrides' => $nameLabels])
            </div>
            <div class="panel-body" style="border-top:1px solid var(--line-2);background:#f9fbfa;border-radius:0 0 16px 16px">
                <div class="form-check mb-3">
                    <input class="form-check-input @error('acknowledge') is-invalid @enderror" type="checkbox" name="acknowledge" id="acknowledge" value="1">
                    <label class="form-check-label" for="acknowledge">
                        I understand that I can submit only once and that my record will be locked afterwards.
                    </label>
                    @error('acknowledge')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary btn-lg">Submit and lock my record</button>
                <p class="small-muted mt-2 mb-0">If everything is already correct, tick the box and submit to confirm it.</p>
            </div>
        </form>
    @else
        <div class="panel">
            <div class="panel-head"><h2 class="panel-title">Your details on record</h2></div>
            <div class="kv">
                @foreach ($fieldLabels as $field => $label)
                    @php $v = $field === 'date_of_birth' ? $graduand->date_of_birth?->format('j F Y') : $graduand->{$field}; @endphp
                    <div class="k">{{ $label }}</div>
                    <div>{{ $v ?: '-' }}</div>
                @endforeach
            </div>
        </div>
    @endif
</main>

<script>
    (function () {
        const ids = ['last_name', 'first_name', 'middle_name'];
        const out = document.getElementById('name-preview');
        if (!out) return;
        const update = () => {
            out.textContent = ids.map(id => (document.getElementById(id)?.value || '').trim()).filter(Boolean).join(' ') || '-';
        };
        ids.forEach(id => document.getElementById(id)?.addEventListener('input', update));
    })();
</script>
@endsection
