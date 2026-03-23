@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        {{-- Logo & Title --}}
        <div class="auth-logo">
            <img src="{{ asset('images/philcst_logo.png') }}" alt="PHILCST Logo">
            <h1>CCS OJT Template Requirements Generator and Evaluation System</h1>
        </div>

        @if (session('status'))
            <div class="alert alert-success" style="margin-bottom: 20px;">
                {{ session('status') }}
            </div>
        @endif

        {{-- Login Form --}}
        <form class="auth-form" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="login-email">Email Address</label>
                <input
                    type="text"
                    id="login-email"
                    name="email"
                    class="form-input {{ $errors->has('email') ? 'input-error' : '' }}"
                    placeholder="Enter your email address"
                    required
                    autofocus
                >
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="login-password">Password</label>
                <input
                    type="password"
                    id="login-password"
                    name="password"
                    class="form-input {{ $errors->has('password') ? 'input-error' : '' }}"
                    placeholder="Enter your password"
                    required
                >
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 8px;">
                <label class="form-check">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>
                <a href="{{ route('password.request') }}" style="font-size:0.82rem; color:var(--purple-600); font-weight:600;">Forgot password?</a>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Sign In
                </button>
            </div>
        </form>

        <div class="auth-divider">or</div>

        <div class="auth-link">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </div>
    </div>
</div>
@endsection
