@extends('layouts.app')

@section('title', 'Change Password')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Change Password</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Security Settings</h1>
    <p class="page-subtitle">Update your password to keep your account secure</p>
</div>

<div style="max-width: 600px;">
    @if(session('success'))
        <div class="alert alert-success mb-3">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Change Password</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profile.change-password') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label" for="current_password">Current Password <span class="required">*</span></label>
                    <input type="password" name="current_password" id="current_password" class="form-input @error('current_password') is-invalid @enderror" placeholder="Enter current password">
                    @error('current_password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label class="form-label" for="new_password">New Password <span class="required">*</span></label>
                    <input type="password" name="new_password" id="new_password" class="form-input @error('new_password') is-invalid @enderror" placeholder="Minimum 8 characters">
                    @error('new_password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label class="form-label" for="new_password_confirmation">Confirm New Password <span class="required">*</span></label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-input" placeholder="Repeat new password">
                </div>

                <div class="form-actions" style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Update Password
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
