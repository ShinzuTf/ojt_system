@extends('layouts.app')

@section('title', 'Notifications')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Notifications</span>
@endsection

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1 class="page-title">Notifications</h1>
        <p class="page-subtitle">Stay updated with your OJT document reviews and reminders</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-ghost btn-sm">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
            Mark All as Read
        </button>
    </div>
</div>
@endsection
