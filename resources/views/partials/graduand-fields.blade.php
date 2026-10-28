{{-- expects $graduand; optional $disabled --}}
@php
    $disabled = $disabled ?? false;
    $labels = \App\Models\Graduand::EDITABLE_LABELS;
    $types = ['gender' => 'select', 'date_of_birth' => 'date', 'email' => 'email'];
    $groups = [
        'Names' => ['hint' => 'Spell each name exactly as it should appear on your certificate.', 'fields' => ['first_name', 'middle_name', 'last_name'], 'col' => 4],
        'Personal details' => ['hint' => null, 'fields' => ['gender', 'date_of_birth', 'nationality', 'national_id_number'], 'col' => 6],
        'Contact' => ['hint' => null, 'fields' => ['email', 'phone'], 'col' => 6],
    ];
@endphp

@foreach ($groups as $title => $group)
    <fieldset class="field-group">
        <legend>{{ $title }}</legend>
        @if ($group['hint'])<p class="hint">{{ $group['hint'] }}</p>@else<div class="mb-2"></div>@endif
        <div class="row g-3">
            @foreach ($group['fields'] as $name)
                @php
                    $type = $types[$name] ?? 'text';
                    $value = old($name, $name === 'date_of_birth' ? $graduand->date_of_birth?->format('Y-m-d') : $graduand->{$name});
                @endphp
                <div class="col-md-{{ $group['col'] }}">
                    <label class="form-label" for="{{ $name }}">{{ $labels[$name] }}</label>
                    @if ($type === 'select')
                        <select id="{{ $name }}" name="{{ $name }}" class="form-select @error($name) is-invalid @enderror" @disabled($disabled)>
                            <option value="">Select</option>
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
    </fieldset>
@endforeach
