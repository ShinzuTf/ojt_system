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
                Student Records
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

            <div class="sidebar-section-label">System</div>
            <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.573-1.066z"/><circle cx="12" cy="12" r="3"/></svg>
                User Management
            </a>
        @elseif(auth()->check() && auth()->user()->role === 'coordinator')
            {{-- ============ COORDINATOR/SUPERVISOR NAVIGATION ============ --}}
            <div class="sidebar-section-label">Main</div>
            <a href="{{ route('supervisor.dashboard') }}" class="sidebar-link {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section-label">Supervision</div>
            <a href="{{ route('supervisor.trainees') }}" class="sidebar-link {{ request()->routeIs('supervisor.trainees*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197v-1"/></svg>
                Trainees
            </a>
            <a href="{{ route('supervisor.evaluations') }}" class="sidebar-link {{ request()->routeIs('supervisor.evaluations*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Evaluations
            </a>
        @else
            {{-- ============ STUDENT NAVIGATION ============ --}}
            <div class="sidebar-section-label">Main</div>
            <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                Dashboard
            </a>

            {{-- OJT Profile — incomplete badge if not yet filled --}}
            @php $ojtInfo = auth()->user()?->ojtInfo; @endphp
            <a href="{{ route('student.ojt-profile') }}" class="sidebar-link {{ request()->routeIs('student.ojt-profile') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My OJT Profile
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
