{{-- Sidebar Navigation --}}
<aside class="sidebar" id="sidebar">
    {{-- Brand --}}
    <div class="sidebar-brand">
        <img src="{{ asset('images/philcst_logo.png') }}" alt="PHILCST Logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">PHILCST CCS</span>
            <span class="sidebar-brand-sub">OJT Management System</span>
        </div>
    </div>

    {{-- Navigation Links --}}
    <nav class="sidebar-nav">
        @if(auth()->check() && auth()->user()->role === 'admin')
            {{-- ============ ADMIN NAVIGATION ============ --}}
            <div class="sidebar-section-label">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section-label">Management</div>
            <a href="{{ route('admin.students') }}" class="sidebar-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197v-1"/></svg>
                Current OJTs
            </a>
            <a href="{{ route('admin.ojt-file-records') }}" class="sidebar-link {{ request()->routeIs('admin.ojt-file-records') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Past OJTs
            </a>
            <a href="{{ route('admin.templates') }}" class="sidebar-link {{ request()->routeIs('admin.templates*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Document Templates
            </a>

            <div class="sidebar-section-label">Reports</div>
            <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Reports & Export
            </a>
            <a href="{{ route('admin.activity-logs') }}" class="sidebar-link {{ request()->routeIs('admin.activity-logs*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Activity Logs
            </a>

            <div class="sidebar-section-label">System</div>
            <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.573-1.066z"/><circle cx="12" cy="12" r="3"/></svg>
                User Management
            </a>

        @elseif(auth()->check() && auth()->user()->role === 'supervisor')
            {{-- ============ SUPERVISOR NAVIGATION ============ --}}
            <div class="sidebar-section-label">Main</div>
            <a href="{{ route('supervisor.dashboard') }}" class="sidebar-link {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section-label">Time Records</div>
            <a href="{{ route('supervisor.dtr.index') }}" class="sidebar-link {{ request()->routeIs('supervisor.dtr*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Verify DTR
            </a>

            <div class="sidebar-section-label">Management</div>
            <a href="{{ route('supervisor.issues.index') }}" class="sidebar-link {{ request()->routeIs('supervisor.issues*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Trainee Issues
            </a>

        @elseif(auth()->check() && auth()->user()->role === 'coordinator')
            {{-- ============ COORDINATOR NAVIGATION ============ --}}
            <div class="sidebar-section-label">Main</div>
            <a href="{{ route('coordinator.dashboard') }}" class="sidebar-link {{ request()->routeIs('coordinator.dashboard') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section-label">Management</div>
            <a href="{{ route('coordinator.trainees.index') }}" class="sidebar-link {{ request()->routeIs('coordinator.trainees*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197v-1"/></svg>
                Trainees
            </a>
            <a href="{{ route('coordinator.placements.index') }}" class="sidebar-link {{ request()->routeIs('coordinator.placements*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/></svg>
                Set Student Company
            </a>
            <a href="{{ route('coordinator.reports.index') }}" class="sidebar-link {{ request()->routeIs('coordinator.reports*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Review Reports
            </a>
            <a href="{{ route('coordinator.supervisor-reports.index') }}" class="sidebar-link {{ request()->routeIs('coordinator.supervisor-reports*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M8 7h8M8 11h8M8 15h5M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                Supervisor Reports
            </a>
            <a href="{{ route('coordinator.certifications.index') }}" class="sidebar-link {{ request()->routeIs('coordinator.certifications*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Certifications
            </a>
            <a href="{{ route('coordinator.issues.index') }}" class="sidebar-link {{ request()->routeIs('coordinator.issues*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Issues
            </a>

        @else
            {{-- ============ STUDENT NAVIGATION ============ --}}
            <div class="sidebar-section-label">Main</div>
            <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section-label">Quick Actions</div>
            <a href="{{ route('student.dtr.create') }}" class="sidebar-link {{ request()->routeIs('student.dtr.create') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 8v8m-4-4h8m4 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                New DTR Entry
            </a>
            <a href="{{ route('student.reports.create') }}" class="sidebar-link {{ request()->routeIs('student.reports.create') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                New Report
            </a>
            <a href="{{ route('student.issues.create') }}" class="sidebar-link {{ request()->routeIs('student.issues.create') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Report Issue
            </a>

            {{-- OJT Profile — incomplete badge if not yet filled --}}
            @php $ojtInfo = auth()->user()?->ojtInfo; @endphp
            <a href="{{ route('student.ojt-profile') }}" class="sidebar-link {{ request()->routeIs('student.ojt-profile') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generate MOA, Endorsement & Completion Letter
                @if(!$ojtInfo || !$ojtInfo->company_name || !$ojtInfo->student_number)
                    <span class="badge" style="background:var(--warning);color:#fff;margin-left:auto;">!</span>
                @endif
            </a>

            <div class="sidebar-section-label">Notifications</div>
            <a href="{{ route('student.notifications') }}" class="sidebar-link {{ request()->routeIs('student.notifications') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifications
            </a>
        @endif
    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->fname ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->lname ?? 'S', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->fname ?? 'User' }} {{ auth()->user()->lname ?? '' }}</div>
                <div class="sidebar-user-role">{{ ucfirst(auth()->user()->role ?? 'Student') }}</div>
            </div>
        </div>
        <a href="{{ route('logout') }}" class="btn btn-sm" style="width:100%; margin-top:12px; background:var(--danger); color:#fff; border:none; justify-content:center; text-decoration:none; font-weight:600;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="margin-right:6px;"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Sign Out
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </div>
</aside>
