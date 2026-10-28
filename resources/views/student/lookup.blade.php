@extends('layouts.student')
@section('title', 'Find your record')

@section('content')
<div class="split">
    <section class="split-side">
        <a class="brand p-0" href="{{ route('student.lookup') }}">
            <span class="brand-mark"><img src="{{ asset('images/teau-logo.png') }}" alt="TEAU crest"></span>
            <span class="brand-name">{{ config('app.name') }}</span>
        </a>

        <div>
            <h1>Check the details that will be printed on your certificate.</h1>
            <p>Your name and personal details go onto your certificate and the graduation booklet exactly as you confirm them here.</p>

            <ol class="process mt-4">
                <li><span class="n">1</span><span>Find your record with your registration number and first name.</span></li>
                <li><span class="n">2</span><span>Review each detail and correct anything that is wrong.</span></li>
                <li><span class="n">3</span><span>Submit once. Your record is then locked.</span></li>
            </ol>
        </div>

        <p class="small mb-0" style="color:#9cc2ab">Need help? Contact the Registry.</p>
    </section>

    <section class="split-main">
        <div class="auth-card">
            <h2 class="h3 mb-1">Find your record</h2>
            <p class="muted mb-4">Use the registration number and first name exactly as they appear on your student portal.</p>

            <form method="POST" action="{{ route('student.lookup.submit') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="registration_number">Registration number</label>
                    <input id="registration_number" name="registration_number" value="{{ old('registration_number') }}"
                           class="form-control form-control-lg @error('registration_number') is-invalid @enderror"
                           placeholder="e.g. ST/2022/001" autocomplete="off" required autofocus>
                    @error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label" for="first_name">First name</label>
                    <input id="first_name" name="first_name" autocomplete="off"
                           class="form-control form-control-lg @error('first_name') is-invalid @enderror" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary btn-lg w-100">Find my record</button>
            </form>

            <div class="note note-warn mt-4">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>You can look at your details as often as you like, but you can <strong>submit changes only once</strong>. After you submit, your record is locked.</div>
            </div>
        </div>
    </section>
</div>
@endsection
