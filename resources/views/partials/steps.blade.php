{{-- $current (1-based), optional $labels, optional $light --}}
@php
    $labels = $labels ?? ['Find your record', 'Review your details', 'Submit and lock'];
    $light = $light ?? false;
@endphp
<div class="steps {{ $light ? 'light' : '' }}" aria-label="Progress">
    @foreach ($labels as $i => $label)
        @php $n = $i + 1; $state = $n < $current ? 'done' : ($n === $current ? 'active' : ''); @endphp
        <div class="step {{ $state }}" @if($state === 'active') aria-current="step" @endif>
            <span class="n">@if($state === 'done')<i class="bi bi-check-lg"></i>@else{{ $n }}@endif</span>
            <span>{{ $label }}</span>
        </div>
        @unless ($loop->last)<span class="step-line"></span>@endunless
    @endforeach
</div>
