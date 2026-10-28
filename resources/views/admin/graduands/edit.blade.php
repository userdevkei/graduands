@extends('layouts.admin')
@section('title', 'Edit record')

@section('content')
<h1 class="h4 mb-3">Edit {{ $graduand->registration_number }}</h1>
<div class="card" style="max-width: 820px;"><div class="card-body">
    <form method="POST" action="{{ route('admin.graduands.update', $graduand) }}">
        @csrf @method('PUT')
        @include('partials.graduand-fields', ['graduand' => $graduand])

        <div class="row g-3 mt-1">
            @foreach (['programme' => 'Programme', 'award' => 'Award', 'classification' => 'Classification'] as $name => $label)
                <div class="col-md-4">
                    <label class="form-label">{{ $label }}</label>
                    <input name="{{ $name }}" value="{{ old($name, $graduand->{$name}) }}" class="form-control @error($name) is-invalid @enderror">
                    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            <button class="btn btn-dark">Save changes</button>
            <a href="{{ route('admin.graduands.show', $graduand) }}" class="btn btn-link">Cancel</a>
        </div>
    </form>
</div></div>
@endsection
