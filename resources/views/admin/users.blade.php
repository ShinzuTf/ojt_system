@extends('layouts.app')

@section('title', 'User Management')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">User Management</span>
@endsection

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1 class="page-title">User Management</h1>
        <p class="page-subtitle">Manage student accounts and coordinators</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary" onclick="openModal('addUserModal')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add User
        </button>
    </div>
</div>

{{-- Tabs --}}
<div class="tabs">
    <button class="tab-btn active" data-tab="tab-students">Students</button>
    <button class="tab-btn" data-tab="tab-coordinators">Coordinators</button>
</div>

{{-- Toolbar --}}
<div class="card mb-3" style="border:none; box-shadow:none; background:transparent;">
    <div class="table-toolbar">
        <div class="table-search">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" placeholder="Search by name or email..." id="searchInput" oninput="filterTable()">
        </div>
        <div class="table-filters">
            <select class="form-select" id="courseFilter" onchange="filterTable()" style="padding:8px 32px 8px 14px; font-size:0.84rem; width:auto;">
                <option value="">All Courses</option>
                <option value="BSIT">BSIT</option>
                <option value="BSCS">BSCS</option>
            </select>
        </div>
    </div>
</div>

{{-- Students Tab --}}
<div class="card" id="tab-students">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table" id="studentsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Student No.</th>
                        <th>Course</th>
                        <th>Coordinator</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    @php
                        $initials = substr($student->fname, 0, 1) . substr($student->lname, 0, 1);
                    @endphp
                    <tr class="student-row" data-name="{{ strtolower($student->full_name) }}" data-email="{{ strtolower($student->email) }}" data-course="{{ $student->ojtInfo?->course ?? '' }}">
                        <td>
                            <div class="d-flex items-center gap-2">
                                <div style="width:32px; height:32px; border-radius:50%; background:var(--purple-100); color:var(--purple-700); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.72rem; flex-shrink:0;">{{ strtoupper($initials) }}</div>
                                <div>
                                    <div style="font-weight:600;">{{ $student->full_name }}</div>
                                    <div class="text-small text-muted">{{ $student->ojtInfo?->year_level ? ordinal($student->ojtInfo->year_level) . ' Year' : 'No Year' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->ojtInfo?->student_number ?? '—' }}</td>
                        <td>{{ $student->ojtInfo?->course ?? '—' }}</td>
                        <td>{{ $student->ojtInfo?->supervisor_name ?? '—' }}</td>
                        <td><span class="badge badge-approved"><span class="badge-dot"></span> Active</span></td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn" title="Edit" onclick="openEditUserModal({{ $student->id }}, '{{ addslashes($student->fname) }}', '{{ addslashes($student->lname) }}', '{{ $student->email }}', 'student', '{{ $student->ojtInfo?->course ?? 'BSIT' }}')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <form style="display:inline;" method="POST" action="{{ route('admin.users.destroy', $student->id) }}" onsubmit="return confirm('Are you sure?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="table-action-btn reject" title="Deactivate"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 24px; color:var(--gray-400);">No students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Coordinators Tab --}}
<div class="card" id="tab-coordinators" style="display:none;">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coordinators as $coordinator)
                    @php
                        $initials = substr($coordinator->fname, 0, 1) . substr($coordinator->lname, 0, 1);
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex items-center gap-2">
                                <div style="width:32px; height:32px; border-radius:50%; background:var(--green-100); color:var(--green-700); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.72rem; flex-shrink:0;">{{ strtoupper($initials) }}</div>
                                <div>
                                    <div style="font-weight:600;">{{ $coordinator->full_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $coordinator->email }}</td>
                        <td><span class="badge badge-approved"><span class="badge-dot"></span> Active</span></td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn" title="Edit" onclick="openEditUserModal({{ $coordinator->id }}, '{{ addslashes($coordinator->fname) }}', '{{ addslashes($coordinator->lname) }}', '{{ $coordinator->email }}', 'coordinator', '', '{{ $coordinator->company_email }}')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <form style="display:inline;" method="POST" action="{{ route('admin.users.destroy', $coordinator->id) }}" onsubmit="return confirm('Are you sure?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="table-action-btn reject" title="Deactivate"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 24px; color:var(--gray-400);">No coordinators found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('modals')
{{-- Add User Modal --}}
<div class="modal-overlay" id="addUserModal">
    <div class="modal" style="max-width:600px;">
        <div class="modal-header">
            <h3 class="modal-title">Add New User</h3>
            <button class="modal-close" onclick="closeModal('addUserModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" onsubmit="return validateNewUserForm()">
            @csrf
            <div class="modal-body">
                <div class="form-group" style="margin-bottom:18px;">
                    <label class="form-label">Account Type <span class="required">*</span></label>
                    <div style="display:flex; gap:12px;">
                        <label class="form-check" style="padding:10px 16px; border:1px solid var(--gray-200); border-radius:8px; flex:1; cursor:pointer;">
                            <input type="radio" name="role" value="student" checked onchange="toggleCourseField()"> <span>Student</span>
                        </label>
                        <label class="form-check" style="padding:10px 16px; border:1px solid var(--gray-200); border-radius:8px; flex:1; cursor:pointer;">
                            <input type="radio" name="role" value="coordinator" onchange="toggleCourseField()"> <span>Coordinator</span>
                        </label>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="required">*</span></label>
                        <input type="text" name="fname" class="form-input" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name <span class="required">*</span></label>
                        <input type="text" name="lname" class="form-input" placeholder="Last Name" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="form-input" placeholder="user@philcst.edu.ph" required>
                    </div>
                    <div class="form-group" id="courseField" style="display:block;">
                        <label class="form-label">Course <span class="required">*</span></label>
                        <select name="course" class="form-select" id="courseSelect" required>
                            <option value="">Select Course</option>
                            <option value="BSIT">BSIT</option>
                            <option value="BSCS">BSCS</option>
                        </select>
                    </div>
                    <div class="form-group" id="companyField" style="display:none;">
                        <label class="form-label">Company <span class="required">*</span></label>
                        <select name="company_email" class="form-select" id="companySelect" required>
                            <option value="">Select Company</option>
                        </select>
                        <small style="color: var(--gray-500); display: block; margin-top: 6px;">Choose from companies submitted by students</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" class="form-input" placeholder="Minimum 8 characters" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Re-type password" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal-overlay" id="editUserModal">
    <div class="modal" style="max-width:600px;">
        <div class="modal-header">
            <h3 class="modal-title">Edit User</h3>
            <button class="modal-close" onclick="closeModal('editUserModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" id="editUserForm" action="" onsubmit="return validateEditUserForm()">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="required">*</span></label>
                        <input type="text" name="fname" class="form-input" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name <span class="required">*</span></label>
                        <input type="text" name="lname" class="form-input" placeholder="Last Name" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="form-input" placeholder="user@philcst.edu.ph" required>
                    </div>
                    <div class="form-group" id="editCourseField" style="display:none;">
                        <label class="form-label">Course <span class="required">*</span></label>
                        <select name="course" class="form-select">
                            <option value="">Select Course</option>
                            <option value="BSIT">BSIT</option>
                            <option value="BSCS">BSCS</option>
                        </select>
                    </div>
                    <div class="form-group" id="editCompanyField" style="display:none;">
                        <label class="form-label">Company <span class="required">*</span></label>
                        <select name="company_email" class="form-select" id="editCompanySelect" required>
                            <option value="">Select Company</option>
                        </select>
                        <small style="color: var(--gray-500); display: block; margin-top: 6px;">Choose from companies submitted by students</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('[id^="tab-"]').forEach(tab => tab.style.display = 'none');
        document.getElementById(this.dataset.tab).style.display = 'block';
    });
});

// Toggle course field based on role
function toggleCourseField() {
    const role = document.querySelector('input[name="role"]:checked').value;
    const courseField = document.getElementById('courseField');
    const companyField = document.getElementById('companyField');
    const courseSelect = document.getElementById('courseSelect');
    const companySelect = document.getElementById('companySelect');
    
    if (role === 'student') {
        courseField.style.display = 'block';
        companyField.style.display = 'none';
        courseSelect.required = true;
        companySelect.required = false;
    } else if (role === 'coordinator') {
        courseField.style.display = 'none';
        companyField.style.display = 'block';
        courseSelect.required = false;
        companySelect.required = true;
        loadAvailableCompanies();
    } else {
        courseField.style.display = 'none';
        companyField.style.display = 'none';
        courseSelect.required = false;
        companySelect.required = false;
    }
}

// Load available companies from the server
async function loadAvailableCompanies() {
    const companySelect = document.getElementById('companySelect');
    try {
        const response = await fetch('{{ route("admin.users.available-companies") }}');
        const companies = await response.json();
        
        // Clear existing options except the default one
        while (companySelect.options.length > 1) {
            companySelect.remove(1);
        }
        
        // Add new options
        companies.forEach(company => {
            const option = document.createElement('option');
            option.value = company.company_email;
            option.textContent = `${company.company_name} (${company.company_email})`;
            companySelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading companies:', error);
        alert('Error loading available companies');
    }
}

// Filter table
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const course = document.getElementById('courseFilter').value;
    
    document.querySelectorAll('.student-row').forEach(row => {
        const name = row.dataset.name;
        const email = row.dataset.email;
        const rowCourse = row.dataset.course;
        
        const matchSearch = name.includes(search) || email.includes(search);
        const matchCourse = !course || rowCourse === course;
        
        row.style.display = (matchSearch && matchCourse) ? '' : 'none';
    });
}

// Edit user modal
function openEditUserModal(id, fname, lname, email, role, course = '', companyEmail = '') {
    const form = document.getElementById('editUserForm');
    form.action = `/admin/users/${id}`;
    form.fname.value = fname;
    form.lname.value = lname;
    form.email.value = email;
    
    const editCourseField = document.getElementById('editCourseField');
    const editCompanyField = document.getElementById('editCompanyField');
    const editCompanySelect = document.getElementById('editCompanySelect');
    
    if (role === 'student') {
        editCourseField.style.display = 'block';
        editCompanyField.style.display = 'none';
        form.course.value = course;
        form.course.required = true;
        editCompanySelect.required = false;
    } else if (role === 'coordinator') {
        editCourseField.style.display = 'none';
        editCompanyField.style.display = 'block';
        form.course.required = false;
        editCompanySelect.required = true;
        
        // Load companies and set current value
        loadEditAvailableCompanies(companyEmail);
    } else {
        editCourseField.style.display = 'none';
        editCompanyField.style.display = 'none';
    }
    
    openModal('editUserModal');
}

// Load available companies for edit form
async function loadEditAvailableCompanies(currentCompanyEmail = '') {
    const editCompanySelect = document.getElementById('editCompanySelect');
    try {
        const response = await fetch('{{ route("admin.users.available-companies") }}');
        const companies = await response.json();
        
        // Clear existing options except the default one
        while (editCompanySelect.options.length > 1) {
            editCompanySelect.remove(1);
        }
        
        // Add new options
        companies.forEach(company => {
            const option = document.createElement('option');
            option.value = company.company_email;
            option.textContent = `${company.company_name} (${company.company_email})`;
            editCompanySelect.appendChild(option);
        });
        
        // Set current value if provided
        if (currentCompanyEmail) {
            editCompanySelect.value = currentCompanyEmail;
        }
    } catch (error) {
        console.error('Error loading companies:', error);
        alert('Error loading available companies');
    }
}

function validateNewUserForm() {
    const role = document.querySelector('input[name="role"]:checked').value;
    const password = document.querySelector('input[name="password"]').value;
    const passwordConfirm = document.querySelector('input[name="password_confirmation"]').value;
    
    if (password.length < 8) {
        alert('Password must be at least 8 characters');
        return false;
    }
    
    if (password !== passwordConfirm) {
        alert('Passwords do not match');
        return false;
    }
    
    if (role === 'student') {
        const course = document.getElementById('courseSelect').value;
        if (!course) {
            alert('Please select a course');
            return false;
        }
    } else if (role === 'coordinator') {
        const company = document.getElementById('companySelect').value;
        if (!company) {
            alert('Please select a company');
            return false;
        }
    }
    
    return true;
}

function validateEditUserForm() {
    return true;
}
</script>
@endpush
