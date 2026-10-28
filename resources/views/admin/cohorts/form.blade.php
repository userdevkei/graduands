@extends('layouts.admin')
@section('title', $cohort->exists ? 'Edit cohort' : 'New cohort')

@section('content')
<div class="row g-4">
    <div class="col-lg-7 col-xl-6">
        <form method="POST" class="panel" action="{{ $cohort->exists ? route('admin.cohorts.update', $cohort) : route('admin.cohorts.store') }}">
            @csrf
            @if ($cohort->exists) @method('PUT') @endif
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input id="name" name="name" value="{{ old('name', $cohort->name) }}" placeholder="e.g. 2026 Graduation"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="graduation_year">Graduation year</label>
                    <input id="graduation_year" type="number" name="graduation_year" value="{{ old('graduation_year', $cohort->graduation_year) }}"
                           class="form-control @error('graduation_year') is-invalid @enderror" required>
                    @error('graduation_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="opens_at">Window opens</label>
                        <input id="opens_at" type="datetime-local" name="opens_at" value="{{ old('opens_at', $cohort->opens_at?->format('Y-m-d\TH:i')) }}"
                               class="form-control @error('opens_at') is-invalid @enderror">
                        @error('opens_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="closes_at">Window closes</label>
                        <input id="closes_at" type="datetime-local" name="closes_at" value="{{ old('closes_at', $cohort->closes_at?->format('Y-m-d\TH:i')) }}"
                               class="form-control @error('closes_at') is-invalid @enderror">
                        @error('closes_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" @checked(old('is_active', $cohort->is_active))>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="panel-body d-flex gap-2" style="border-top:1px solid var(--line-2)">
                <button class="btn btn-primary">{{ $cohort->exists ? 'Save changes' : 'Create cohort' }}</button>
                <a href="{{ route('admin.cohorts.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
    <div class="col-lg-5 col-xl-4">
        <div class="note note-info">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                Students can only confirm or update their details while the window is open. Leave a date empty for no limit.
                Records that are not submitted by the deadline stay unchanged.
            </div>
        </div>
    </div>
</div>
@endsection
