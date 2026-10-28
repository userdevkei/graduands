@extends('layouts.student')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h2 class="h5">Find your record</h2>
        <p class="text-muted">Enter your registration number and first name <strong>exactly as they appear on your student portal</strong>.</p>

        <form method="POST" action="{{ route('student.lookup.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="registration_number">Registration number</label>
                <input id="registration_number" name="registration_number" value="{{ old('registration_number') }}"
                       class="form-control @error('registration_number') is-invalid @enderror" required autofocus>
                @error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="first_name">First name</label>
                <input id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" required>
                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-primary w-100">Continue</button>
        </form>
    </div>
</div>

<div class="alert alert-warning mt-3 small mb-0">
    <strong>Please note:</strong> you can review your details as many times as you like, but you can <strong>submit changes only once</strong>.
    After you submit, your record is locked.
</div>
@endsection
