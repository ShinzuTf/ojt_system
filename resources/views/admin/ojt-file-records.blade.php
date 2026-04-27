@extends('layouts.app')

@section('title', 'Past OJTs')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Past OJTs</span>
@endsection

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1 class="page-title">Past OJTs</h1>
        <p class="page-subtitle">View archived company and student pairings from previous OJT records.</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-secondary" onclick="exportToCsv()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg>
            Export CSV
        </button>
        <button class="btn btn-secondary" onclick="exportToExcel()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg>
            Export Excel
        </button>
    </div>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:16px; margin-bottom:22px;">
    <div class="card">
        <div class="card-body">
            <div style="font-size:0.8rem; color:var(--gray-500); margin-bottom:4px;">Unique Company/Student Pairs</div>
            <div style="font-size:1.8rem; font-weight:800; color:var(--gray-800);">{{ $totalPairs }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div style="font-size:0.8rem; color:var(--gray-500); margin-bottom:4px;">Companies Found</div>
            <div style="font-size:1.8rem; font-weight:800; color:var(--gray-800);">{{ $totalCompanies }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div style="font-size:0.8rem; color:var(--gray-500); margin-bottom:4px;">Students Found</div>
            <div style="font-size:1.8rem; font-weight:800; color:var(--gray-800);">{{ $totalStudents }}</div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:18px;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.ojt-file-records') }}" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:14px; align-items:end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">School Year</label>
                <select name="schoolyear" class="form-select">
                    <option value="">All School Years</option>
                    @foreach($availableSchoolYears as $sy)
                        <option value="{{ $sy }}" {{ $schoolYearFilter === $sy ? 'selected' : '' }}>{{ $sy }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Company</label>
                <input type="text" name="company" value="{{ $companyFilter }}" class="form-input" placeholder="Search company">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Student</label>
                <input type="text" name="student" value="{{ $studentFilter }}" class="form-input" placeholder="Search student">
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.ojt-file-records') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Student Name</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td><strong>{{ $record['company_name'] }}</strong></td>
                            <td>{{ $record['student_name'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align:center; padding: 24px; color:var(--gray-400);">No matching records found in OJT FILE.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-wrapper { overflow-x:auto; }
</style>

@push('scripts')
<script>
function exportToCsv() {
    const rows = [];
    const table = document.querySelector('.data-table');
    
    // Header
    rows.push('Company Name,Student Name');
    
    // Body
    table.querySelectorAll('tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 2) {
            const company = cells[0].textContent.trim();
            const student = cells[1].textContent.trim();
            rows.push(`"${company}","${student}"`);
        }
    });
    
    const csv = rows.join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `past-ojts-${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

function exportToExcel() {
    const rows = [];
    const table = document.querySelector('.data-table');
    
    // Header
    rows.push(['Company Name', 'Student Name']);
    
    // Body
    table.querySelectorAll('tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 2) {
            const company = cells[0].textContent.trim();
            const student = cells[1].textContent.trim();
            rows.push([company, student]);
        }
    });
    
    // Create workbook data
    let worksheet = 'Company Name\tStudent Name\n';
    rows.forEach((row, index) => {
        if (index > 0) { // Skip header since we already added it
            worksheet += row.join('\t') + '\n';
        }
    });
    
    const blob = new Blob([worksheet], { type: 'application/vnd.ms-excel' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `past-ojts-${new Date().toISOString().split('T')[0]}.xlsx`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}
</script>
@endpush
@endsection