@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        {{-- Logo & Title --}}
        <div class="auth-logo">
            <img src="{{ asset('images/philcst_logo.png') }}" alt="PHILCST Logo">
            <h1>Set New Password</h1>
            <p>Create a strong password for your account</p>
        </div>

        {{-- Reset Password Form --}}
        <form class="auth-form" method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    value="{{ $email ?? old('email') }}" 
                    required 
                    readonly
                >
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    placeholder="Minimum 8 characters" 
                    required 
                    autofocus
                >
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password-confirm">Confirm New Password</label>
                <input 
                    type="password" 
                    id="password-confirm" 
                    name="password_confirmation" 
                    class="form-input" 
                    placeholder="Repeat new password" 
                    required
                >
            </div>

            <div class="form-actions" style="margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
