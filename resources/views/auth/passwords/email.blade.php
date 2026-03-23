@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        {{-- Logo & Title --}}
        <div class="auth-logo">
            <img src="{{ asset('images/philcst_logo.png') }}" alt="PHILCST Logo">
            <h1>Reset Your Password</h1>
            <p>Enter your email to receive a password reset link</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        {{-- Forgot Password Form --}}
        <form class="auth-form" method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="Enter your registered email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                >
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions" style="margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Send Reset Link
                </button>
            </div>
        </form>

        <div class="auth-link" style="margin-top: 20px;">
            Remember your password? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>
</div>
@endsection
