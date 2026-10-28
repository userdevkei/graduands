@extends('layouts.student')
@section('title', 'Registry sign in')

@section('content')
<div class="split">
    <section class="split-side">
        <a class="brand p-0" href="#">
            <span class="brand-mark"><img src="{{ asset('images/teau-logo.png') }}" alt="TEAU crest"></span>
            <span class="brand-name">{{ config('app.name') }}</span>
        </a>
        <div>
            <h1>Graduation records, confirmed by the students themselves.</h1>
            <p>Import the graduand list, watch confirmations come in, and review every change before certificates are printed.</p>
        </div>
        <p class="small mb-0" style="color:#9cc2ab">Registry staff only.</p>
    </section>

    <section class="split-main">
        <div class="auth-card">
            <h2 class="h3 mb-1">Sign in</h2>
            <p class="muted mb-4">Use your registry account.</p>

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" type="password" name="password" class="form-control form-control-lg" required>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                    <label class="form-check-label" for="remember">Keep me signed in</label>
                </div>
                <button class="btn btn-primary btn-lg w-100">Sign in</button>
            </form>
        </div>
    </section>
</div>
@endsection
