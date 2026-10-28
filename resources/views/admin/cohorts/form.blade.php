@extends('layouts.admin')
@section('title', $cohort->exists ? 'Edit cohort' : 'New cohort')

@section('content')
<h1 class="h4 mb-3">{{ $cohort->exists ? 'Edit cohort' : 'New cohort' }}</h1>
<div class="card" style="max-width: 640px;"><div class="card-body">
    <form method="POST" action="{{ $cohort->exists ? route('admin.cohorts.update', $cohort) : route('admin.cohorts.store') }}">
        @csrf
        @if ($cohort->exists) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" value="{{ old('name', $cohort->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Graduation year</label>
            <input type="number" name="graduation_year" value="{{ old('graduation_year', $cohort->graduation_year) }}" class="form-control @error('graduation_year') is-invalid @enderror" required>
            @error('graduation_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Window opens</label>
                <input type="datetime-local" name="opens_at" value="{{ old('opens_at', $cohort->opens_at?->format('Y-m-d\TH:i')) }}" class="form-control @error('opens_at') is-invalid @enderror">
                @error('opens_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Window closes (deadline)</label>
                <input type="datetime-local" name="closes_at" value="{{ old('closes_at', $cohort->closes_at?->format('Y-m-d\TH:i')) }}" class="form-control @error('closes_at') is-invalid @enderror">
                @error('closes_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $cohort->is_active))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <button class="btn btn-dark">Save</button>
        <a href="{{ route('admin.cohorts.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div></div>
@endsection
