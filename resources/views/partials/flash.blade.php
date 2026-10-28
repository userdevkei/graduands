@foreach (['success' => ['ok', 'bi-check-circle-fill'], 'error' => ['danger', 'bi-exclamation-octagon-fill']] as $key => [$tone, $icon])
    @if (session($key))
        <div class="note note-{{ $tone }} mb-3" role="alert">
            <i class="bi {{ $icon }}"></i>
            <div>{{ session($key) }}</div>
        </div>
    @endif
@endforeach
