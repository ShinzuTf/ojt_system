@extends('layouts.app')

@section('title', 'Student Records')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Student Records</span>
@endsection

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1 class="page-title">Student Records</h1>
        <p class="page-subtitle">Manage OJT trainees and view their OJT profile information</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-secondary" onclick="openModal('exportModal')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg>
            Export
        </button>
        <button class="btn btn-danger" onclick="openModal('endSemesterModal')" title="Deactivate all active OJT accounts at end of semester">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            End OJT Semester
        </button>
        <button class="btn btn-primary" onclick="openModal('addStudentModal')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v12m6-6H6"/></svg>
            Add Student
        </button>
    </div>
</div>

{{-- Toolbar --}}
<div class="card mb-3">
    <div class="card-body" style="padding: 14px 22px;">
        <div class="table-toolbar">
            <div class="table-search">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" placeholder="Search by name, student number, company..." id="searchInput" oninput="filterTable()">
            </div>
            <div class="table-filters">
                <select class="form-select" id="filterCourse" onchange="filterTable()" style="padding:8px 32px 8px 14px; font-size:0.84rem; width:auto;">
                    <option value="">All Courses</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
                </select>
                <select class="form-select" id="filterYear" onchange="filterTable()" style="padding:8px 32px 8px 14px; font-size:0.84rem; width:auto;">
                    <option value="">All Year Levels</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
                <select class="form-select" id="filterStatus" onchange="filterTable()" style="padding:8px 32px 8px 14px; font-size:0.84rem; width:auto;">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- Student Table --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table" id="studentTable">
                <thead>
                    <tr>
                        <th>Student No.</th>
                        <th>Full Name</th>
                        <th>Course / Year</th>
                        <th>Company</th>
                        <th>OJT Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    @php
                        $ojt      = $student->ojtInfo;
                        $docCount = $student->documents->count();
                        $reqDocs  = $student->requiredDocuments;
                    @endphp
                    <tr data-name="{{ strtolower($student->full_name) }}"
                        data-student-no="{{ strtolower($ojt?->student_number ?? '') }}"
                        data-company="{{ strtolower($ojt?->company_name ?? '') }}"
                        data-course="{{ $ojt?->course ?? '' }}"
                        data-year="{{ $ojt?->year_level ?? '' }}"
                        data-status="{{ $student->status }}">
                        <td>{{ $ojt?->student_number ?? '—' }}</td>
                        <td><strong>{{ $student->full_name }}</strong></td>
                        <td>
                            @if($ojt && $ojt->course)
                                <div>{{ $ojt->course }}</div>
                                <div class="text-small" style="color:var(--gray-400);">{{ $ojt->year_level ? ordinal($ojt->year_level).' Year' : '' }}</div>
                            @else
                                <span style="color:var(--gray-300); font-style:italic;">Not set</span>
                            @endif
                        </td>
                        <td>
                            @if($ojt && $ojt->company_name)
                                <div style="font-size:0.86rem; font-weight:600;">{{ $ojt->company_name }}</div>
                                @if($ojt->company_email)
                                <div class="text-small" style="color:var(--gray-400);">{{ $ojt->company_email }}</div>
                                @endif
                            @else
                                <span style="color:var(--gray-300); font-style:italic;">Not set</span>
                            @endif
                        </td>
                        <td style="font-size:0.84rem;">{{ $student->email }}</td>
                        <td>
                            @if($student->status === 'active')
                                <span class="badge badge-approved"><span class="badge-dot"></span> Active</span>
                            @else
                                <span class="badge badge-rejected"><span class="badge-dot"></span> Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn view" title="View Record"
                                    onclick="openViewModal(
                                        '{{ $student->id }}',
                                        '{{ addslashes($student->full_name) }}',
                                        '{{ addslashes($ojt?->student_number ?? '—') }}',
                                        '{{ addslashes($ojt?->course_full_name ?? '—') }}',
                                        '{{ addslashes(($ojt?->year_level ?? '').' Year') }}',
                                        '{{ addslashes($ojt?->company_name ?? '—') }}',
                                        '{{ addslashes($ojt?->company_email ?? '—') }}',
                                        '{{ addslashes($ojt?->company_address ?? '—') }}',
                                        '{{ addslashes($ojt?->supervisor_name ?? '—') }}',
                                        '{{ addslashes($ojt?->ojt_start?->format('M d, Y') ?? '—') }}',
                                        '{{ addslashes($ojt?->ojt_end?->format('M d, Y') ?? '—') }}',
                                        '{{ $student->email }}'
                                    )">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:40px; color:var(--gray-400);">
                            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:.3; margin-bottom:8px;"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <div>No students found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="pagination">
        <span>Showing {{ $students->count() }} student(s)</span>
    </div>
</div>
@endsection

@section('modals')

{{-- ================================================== --}}
{{-- View Student Modal --}}
{{-- ================================================== --}}
<div class="modal-overlay" id="viewStudentModal">
    <div class="modal" style="max-width:680px;">
        <div class="modal-header">
            <h3 class="modal-title">Student OJT Record</h3>
            <button class="modal-close" onclick="closeModal('viewStudentModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            {{-- Info Grid --}}
            <div class="form-grid" style="margin-bottom:18px;">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-name">—</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Student Number</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-student-no">—</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Course</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-course">—</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Year Level</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-year">—</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-email">—</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Supervisor</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-supervisor">—</div>
                </div>
            </div>

            <hr style="border:none; border-top:1px solid var(--gray-200); margin:4px 0 18px;">

            <h4 style="font-size:0.88rem; font-weight:700; margin-bottom:12px; color:var(--gray-600); text-transform:uppercase; letter-spacing:.04em;">Company / OJT Details</h4>
            <div class="form-grid" style="margin-bottom:18px;">
                <div class="form-group">
                    <label class="form-label">Company Name</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-company">—</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Company Email</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-company-email">—</div>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Company Address</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-company-address">—</div>
                </div>
                <div class="form-group">
                    <label class="form-label">OJT End</label>
                    <div style="padding:7px 0; font-weight:600;" id="vm-ojt-end">—</div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="justify-content: flex-end; gap: 10px;">
            <button class="btn btn-secondary" onclick="closeModal('viewStudentModal')">Close</button>
        </div>
    </div>
</div>

{{-- ================================================== --}}
{{-- Add Student Modal --}}
{{-- ================================================== --}}
<div class="modal-overlay" id="addStudentModal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <h3 class="modal-title">Add New Student Account</h3>
            <button class="modal-close" onclick="closeModal('addStudentModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf
            <div class="modal-body">
                <div class="alert" style="background:var(--info-light,#eff6ff); border-left:3px solid var(--info,#3b82f6); margin-bottom:18px; padding:12px 16px; border-radius:8px; font-size:0.83rem;">
                    A default password of <strong>philcst2024</strong> will be assigned. The student can change it after logging in. OJT details (course, year, company) are filled in by the student on their OJT Profile page.
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="required">*</span></label>
                        <input type="text" name="fname" class="form-input" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="mname" class="form-input" placeholder="Middle Name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name <span class="required">*</span></label>
                        <input type="text" name="lname" class="form-input" placeholder="Last Name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Suffix</label>
                        <select name="suffix" class="form-select">
                            <option value="">None</option>
                            <option>Jr.</option><option>Sr.</option><option>II</option><option>III</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="form-input" placeholder="Enter email address" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addStudentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Student Account</button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================== --}}
{{-- Export Modal --}}
{{-- ================================================== --}}
<div class="modal-overlay" id="exportModal">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <h3 class="modal-title">Export Records</h3>
            <button class="modal-close" onclick="closeModal('exportModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Export Format</label>
                <select class="form-select">
                    <option value="pdf">PDF Report</option>
                    <option value="excel">Excel Spreadsheet (.xlsx)</option>
                    <option value="csv">CSV File</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Filter</label>
                <select class="form-select">
                    <option value="all">All Students</option>
                    <option value="completed">Completed Only</option>
                    <option value="in-progress">In Progress Only</option>
                    <option value="pending">Pending Only</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('exportModal')">Cancel</button>
            <button class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg>
                Export
            </button>
        </div>
    </div>
</div>

{{-- ================================================== --}}
{{-- End OJT Semester - Deactivation Modal --}}
{{-- ================================================== --}}
<div class="modal-overlay" id="endSemesterModal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <h3 class="modal-title">End OJT Semester</h3>
            <button class="modal-close" onclick="closeModal('endSemesterModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.students.deactivate-all-ojt') }}">
            @csrf
            <div class="modal-body">
                <div class="alert" style="background:var(--danger-light,#fee2e2); border-left:3px solid var(--danger,#ef4444); margin-bottom:18px; padding:12px 16px; border-radius:8px; font-size:0.83rem;">
                    <strong>⚠ Warning:</strong> This action will:
                    <ul style="margin:8px 0 0 0; padding-left:20px;">
                        <li>Deactivate <strong>all active student OJT accounts</strong></li>
                        <li>Archive their current OJT records</li>
                        <li>Students will <strong>NOT be able to log in</strong> until reactivated</li>
                    </ul>
                </div>

                <div id="deactivationSummary" style="background:var(--gray-50); padding:16px; border-radius:8px; margin-bottom:18px; display:none;">
                    <h4 style="margin:0 0 12px 0; font-size:0.9rem; font-weight:600; color:var(--gray-700);">Impact Summary</h4>
                    <div class="form-grid">
                        <div>
                            <div style="font-size:2rem; font-weight:700; color:var(--primary);" id="summaryCount">0</div>
                            <div style="font-size:0.8rem; color:var(--gray-600);">Students to Deactivate</div>
                        </div>
                        <div>
                            <div style="font-size:2rem; font-weight:700; color:var(--warning);" id="summaryWithOjt">0</div>
                            <div style="font-size:0.8rem; color:var(--gray-600);">OJT Records to Archive</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Add Notes (Optional)</label>
                    <textarea name="notes" class="form-input" placeholder="e.g., End of 2nd semester 2024, re-enrollment expected next semester..." rows="3"></textarea>
                </div>

                <div class="form-check" style="margin-bottom:16px;">
                    <input type="checkbox" id="confirmCheckbox" class="form-check-input">
                    <label for="confirmCheckbox" class="form-check-label">
                        I understand this action will deactivate all active OJT student accounts and cannot be immediately undone.
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('endSemesterModal')">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmDeactivateBtn" disabled>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Deactivate All OJT Accounts
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Modal Deactivation Summary ────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('endSemesterModal');
    const confirmCheckbox = document.getElementById('confirmCheckbox');
    const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');
    const summaryDiv = document.getElementById('deactivationSummary');

    // Show modal trigger
    const originalOpenModal = window.openModal || function() {};
    window.openModal = function(modalId) {
        if (modalId === 'endSemesterModal') {
            // Load summary
            fetch('{{ route("admin.students.deactivation-summary") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('summaryCount').textContent = data.students_to_deactivate;
                    document.getElementById('summaryWithOjt').textContent = data.students_with_ojt_info;
                    summaryDiv.style.display = 'block';
                })
                .catch(error => console.error('Error loading summary:', error));
        }
        const overlayEl = document.getElementById(modalId);
        if (overlayEl) {
            overlayEl.style.display = 'flex';
        }
    };

    // Enable submit button only if checkbox is checked
    confirmCheckbox.addEventListener('change', function() {
        confirmDeactivateBtn.disabled = !this.checked;
    });
});

// ── View Modal ──────────────────────────────────────────
function openViewModal(id, name, studentNo, course, year, company, companyEmail, address, supervisor, ojtStart, ojtEnd, email) {
    document.getElementById('vm-name').textContent         = name;
    document.getElementById('vm-student-no').textContent   = studentNo;
    document.getElementById('vm-course').textContent       = course;
    document.getElementById('vm-year').textContent         = year;
    document.getElementById('vm-email').textContent        = email;
    document.getElementById('vm-supervisor').textContent   = supervisor;
    document.getElementById('vm-company').textContent      = company;
    document.getElementById('vm-company-email').textContent = companyEmail;
    document.getElementById('vm-company-address').textContent = address;
    document.getElementById('vm-ojt-start').textContent   = ojtStart;
    document.getElementById('vm-ojt-end').textContent     = ojtEnd;

    openModal('viewStudentModal');
}

// ── Table Search & Filter ────────────────────────────────
function filterTable() {
    const search  = document.getElementById('searchInput').value.toLowerCase();
    const course  = document.getElementById('filterCourse').value.toLowerCase();
    const year    = document.getElementById('filterYear').value;
    const status  = document.getElementById('filterStatus').value.toLowerCase();

    document.querySelectorAll('#studentTable tbody tr[data-name]').forEach(function(row) {
        const name      = row.dataset.name || '';
        const studentNo = row.dataset.studentNo || '';
        const company   = row.dataset.company || '';
        const rowCourse = row.dataset.course || '';
        const rowYear   = row.dataset.year || '';
        const rowStatus = row.dataset.status || '';

        const matchSearch  = !search  || name.includes(search) || studentNo.includes(search) || company.includes(search);
        const matchCourse  = !course  || rowCourse.toLowerCase() === course;
        const matchYear    = !year    || rowYear === year;
        const matchStatus  = !status  || rowStatus === status;

        row.style.display = (matchSearch && matchCourse && matchYear && matchStatus) ? '' : 'none';
    });
}
</script>
@endpush
