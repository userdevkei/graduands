{{-- expects $graduand; optional $disabled --}}
@php
    $disabled = $disabled ?? false;
    $types = ['gender' => 'select', 'date_of_birth' => 'date', 'email' => 'email'];
@endphp
<div class="row g-3">
    @foreach (\App\Models\Graduand::EDITABLE_LABELS as $name => $label)
        @php
            $type = $types[$name] ?? 'text';
            $value = old($name, $name === 'date_of_birth' ? $graduand->date_of_birth?->format('Y-m-d') : $graduand->{$name});
        @endphp
        <div class="col-md-6">
            <label class="form-label" for="{{ $name }}">{{ $label }}</label>
            @if ($type === 'select')
                <select id="{{ $name }}" name="{{ $name }}" class="form-select @error($name) is-invalid @enderror" @disabled($disabled)>
                    <option value="">-</option>
                    @foreach (['Male', 'Female', 'Other'] as $opt)
                        <option value="{{ $opt }}" @selected($value === $opt)>{{ $opt }}</option>
                    @endforeach
                </select>
            @else
                <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"
                       class="form-control @error($name) is-invalid @enderror" @disabled($disabled)>
            @endif
            @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach
</div>
