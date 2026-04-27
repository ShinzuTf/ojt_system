<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OJT System') — PHILCST CCS</title>
    <meta name="description" content="PHILCST CCS On-the-Job Training Document Submission, Monitoring, and Record Management System">
    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" href="{{ asset('images/philcst_logo.png') }}" type="image/png">
</head>
<body>
    <div class="app-shell">
        {{-- Sidebar Overlay (Mobile) --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <div class="app-main">
            {{-- Top Navigation Bar --}}
            @include('partials.topbar')

            {{-- Page Content --}}
            <main class="app-content">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ session('warning') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Force Password Change Modal --}}
    @include('modals.force_password_change')

    {{-- Modal Container --}}
    @yield('modals')

    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Profile Dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const profileBtn = document.getElementById('profileDropdownBtn');
            const profileMenu = document.getElementById('profileDropdown');

            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('show');
                });

                document.addEventListener('click', function() {
                    profileMenu.classList.remove('show');
                });
            }

            // Modal functions
            window.openModal = function(id) {
                document.getElementById(id).classList.add('show');
                document.body.style.overflow = 'hidden';
                
                // Initialize add user form when opening
                if (id === 'addUserModal') {
                    if (typeof initializeAddUserForm === 'function') {
                        initializeAddUserForm();
                    }
                }
            };

            window.closeModal = function(id) {
                document.getElementById(id).classList.remove('show');
                document.body.style.overflow = '';
                
                // Reset add user form when closing
                if (id === 'addUserModal') {
                    const form = document.getElementById('addUserForm');
                    const submitBtn = document.getElementById('submitBtn');
                    const cancelBtn = document.getElementById('cancelBtn');
                    const loadingIndicator = document.getElementById('loadingIndicator');
                    const errorAlert = document.getElementById('formErrorAlert');
                    
                    if (form) form.reset();
                    if (submitBtn) submitBtn.style.display = '';
                    if (cancelBtn) cancelBtn.disabled = false;
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                    if (errorAlert) errorAlert.style.display = 'none';
                }
            };

            // Tab functions
            document.querySelectorAll('.tab-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const group = this.closest('.tabs');
                    group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const target = this.dataset.tab;
                    if (target) {
                        document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
                        const panel = document.getElementById(target);
                        if (panel) panel.style.display = 'block';
                    }
                });
            });

            // File upload zones
            document.querySelectorAll('.file-upload-zone').forEach(function(zone) {
                const input = zone.querySelector('input[type="file"]');
                if (input) {
                    zone.addEventListener('click', () => input.click());
                    zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.style.borderColor = 'var(--purple-400)'; });
                    zone.addEventListener('dragleave', () => { zone.style.borderColor = 'var(--gray-300)'; });
                    zone.addEventListener('drop', (e) => {
                        e.preventDefault();
                        zone.style.borderColor = 'var(--gray-300)';
                        if (e.dataTransfer.files.length) {
                            input.files = e.dataTransfer.files;
                            zone.querySelector('.upload-text').textContent = e.dataTransfer.files[0].name;
                        }
                    });
                    input.addEventListener('change', () => {
                        if (input.files.length) {
                            zone.querySelector('.upload-text').textContent = input.files[0].name;
                        }
                    });
                }
            });
        });
    </script>
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
