@extends('layouts.admin')
@section('title', 'Edit record')
@section('subtitle', $graduand->registration_number.', '.$graduand->full_name)

@section('content')
<form method="POST" action="{{ route('admin.graduands.update', $graduand) }}" class="panel" style="max-width:860px">
    @csrf @method('PUT')
    <div class="panel-body pb-1">
        @include('partials.graduand-fields', ['graduand' => $graduand])

        <fieldset class="field-group">
            <legend>Academic record</legend>
            <div class="mb-2"></div>
            <div class="row g-3">
                @foreach (['programme' => 'Programme', 'award' => 'Award', 'classification' => 'Classification'] as $name => $label)
                    <div class="col-md-4">
                        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
                        <input id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $graduand->{$name}) }}" class="form-control @error($name) is-invalid @enderror">
                        @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>
        </fieldset>
    </div>
    <div class="panel-body d-flex gap-2" style="border-top:1px solid var(--line-2)">
        <button class="btn btn-primary">Save changes</button>
        <a href="{{ route('admin.graduands.show', $graduand) }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
