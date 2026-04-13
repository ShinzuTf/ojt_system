@extends('layouts.app')

@section('title', 'Activity Details')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <a href="{{ route('admin.activity-logs') }}">Activity Logs</a>
    <span>›</span>
    <span class="current">Details</span>
@endsection

@section('content')
<div class="page-header-row mb-3">
    <div class="page-header">
        <h1 class="page-title">Activity Details</h1>
        <p class="page-subtitle">{{ str_replace('_', ' ', ucfirst($activity->activity)) }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.activity-logs') }}" class="btn btn-ghost">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
            {{-- Left Column --}}
            <div>
                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 6px;">Activity Type</label>
                    <div style="font-size: 1.1rem; font-weight: 600;">
                        <span class="badge" style="background:var(--blue-100); color:var(--blue-700);">
                            {{ str_replace('_', ' ', ucfirst($activity->activity)) }}
                        </span>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 6px;">Module</label>
                    <div style="font-size: 1rem;">{{ ucfirst($activity->module) }}</div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 6px;">Action</label>
                    <div style="font-size: 1rem;">{{ ucfirst($activity->action) }}</div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 6px;">Status</label>
                    <div>
                        @if($activity->status === 'success')
                            <span class="badge badge-approved"><span class="badge-dot"></span> Success</span>
                        @elseif($activity->status === 'failed')
                            <span class="badge badge-rejected"><span class="badge-dot"></span> Failed</span>
                        @else
                            <span class="badge badge-pending"><span class="badge-dot"></span> Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div>
                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 6px;">Timestamp</label>
                    <div style="font-family: monospace; font-size: 0.95rem;">
                        {{ $activity->created_at->format('F d, Y H:i:s') }}
                        <div style="color: var(--gray-400); font-size: 0.84rem;">{{ $activity->created_at->timezone('Asia/Manila')->format('l (PH Time)') }}</div>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 6px;">IP Address</label>
                    <div style="font-family: monospace; font-size: 0.95rem;">{{ $activity->ip_address ?? '—' }}</div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 6px;">User Agent</label>
                    <div style="font-size: 0.9rem; color: var(--gray-600); word-break: break-word;">{{ $activity->user_agent ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- User Information --}}
        <hr style="margin: 32px 0; border: none; border-top: 1px solid var(--gray-200);">

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
            <div>
                <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 12px;">User (Action Performer)</label>
                @if($activity->user)
                    <div style="padding: 12px; background: var(--blue-50); border-radius: 8px; border-left: 3px solid var(--blue-500);">
                        <div style="font-weight: 600;">{{ $activity->user->full_name }}</div>
                        <div style="font-size: 0.9rem; color: var(--gray-600);">{{ $activity->user->email }}</div>
                        <div style="font-size: 0.85rem; margin-top: 6px;">
                            <span class="badge" style="background:var(--purple-100); color:var(--purple-700);">{{ ucfirst($activity->user->role) }}</span>
                        </div>
                    </div>
                @else
                    <div style="color: var(--gray-400);">System</div>
                @endif
            </div>

            <div>
                <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 12px;">Target User (Affected)</label>
                @if($activity->targetUser)
                    <div style="padding: 12px; background: var(--orange-50); border-radius: 8px; border-left: 3px solid var(--orange-500);">
                        <div style="font-weight: 600;">{{ $activity->targetUser->full_name }}</div>
                        <div style="font-size: 0.9rem; color: var(--gray-600);">{{ $activity->targetUser->email }}</div>
                        <div style="font-size: 0.85rem; margin-top: 6px;">
                            <span class="badge" style="background:var(--purple-100); color:var(--purple-700);">{{ ucfirst($activity->targetUser->role) }}</span>
                        </div>
                    </div>
                @else
                    <div style="color: var(--gray-400);">N/A</div>
                @endif
            </div>
        </div>

        {{-- Description --}}
        @if($activity->description)
        <hr style="margin: 32px 0; border: none; border-top: 1px solid var(--gray-200);">
        <div>
            <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 12px;">Description</label>
            <div style="padding: 12px; background: var(--gray-50); border-radius: 8px; font-size: 0.95rem; color: var(--gray-700);">
                {{ $activity->description }}
            </div>
        </div>
        @endif

        {{-- Additional Data --}}
        @if($activity->data)
        <hr style="margin: 32px 0; border: none; border-top: 1px solid var(--gray-200);">
        <div>
            <label style="font-size: 0.84rem; color: var(--gray-500); font-weight: 600; display: block; margin-bottom: 12px;">Additional Data</label>
            <div style="padding: 12px; background: var(--gray-50); border-radius: 8px; font-family: monospace; font-size: 0.85rem; overflow-x: auto;">
                <pre style="margin: 0; white-space: pre-wrap; word-wrap: break-word;">{{ json_encode($activity->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
