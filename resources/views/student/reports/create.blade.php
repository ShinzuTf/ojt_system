@extends('layouts.app')

@section('title', 'Submit Report - OJT System')
@section('page-title', 'Submit Weekly/Monthly Report')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-pdf"></i> Upload Progress Report</h5>
                    <a href="{{ route('student.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.reports.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="report_type" class="form-label"><strong>Report Type</strong></label>
                            <select class="form-select @error('report_type') is-invalid @enderror" id="report_type" name="report_type" required>
                                <option value="">-- Select Report Type --</option>
                                <option value="weekly" {{ old('report_type') === 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                                <option value="monthly" {{ old('report_type') === 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                            </select>
                            @error('report_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="report_period_start" class="form-label"><strong>Period Start</strong></label>
                                <input type="date" class="form-control @error('report_period_start') is-invalid @enderror" 
                                       id="report_period_start" name="report_period_start" value="{{ old('report_period_start') }}" required>
                                @error('report_period_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="report_period_end" class="form-label"><strong>Period End</strong></label>
                                <input type="date" class="form-control @error('report_period_end') is-invalid @enderror" 
                                       id="report_period_end" name="report_period_end" value="{{ old('report_period_end') }}" required>
                                @error('report_period_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="mb-4">
                            <label for="file_path" class="form-label"><strong>Report Document</strong></label>
                            <div class="upload-area border-2 border-dashed rounded p-4 text-center" 
                                 style="border-color: #ccc; background-color: #fafafa; cursor: pointer; transition: all 0.3s ease;">
                                <input type="file" class="form-control @error('file_path') is-invalid @enderror" 
                                       id="file_path" name="file_path" 
                                       accept=".pdf,.doc,.docx,.xlsx,.xls,.ppt,.pptx,.txt,.jpg,.jpeg,.png"
                                       required style="display: none;">
                                
                                <div id="upload-placeholder">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 3rem; color: #999;"></i>
                                    <p class="mt-3 mb-1"><strong>Click or drag file here</strong></p>
                                    <small class="text-muted">Supported: PDF, DOC, DOCX, XLSX, PPT, PNG, JPG (Max 10MB)</small>
                                </div>

                                <div id="file-selected" style="display: none;">
                                    <i class="bi bi-check-circle" style="font-size: 2rem; color: #059669;"></i>
                                    <p class="mt-2 mb-1"><strong id="file-name"></strong></p>
                                    <small class="text-muted" id="file-size"></small>
                                    <p class="mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('file_path').value = ''; document.getElementById('upload-placeholder').style.display = 'block'; document.getElementById('file-selected').style.display = 'none';">
                                            Choose Different File
                                        </button>
                                    </p>
                                </div>
                            </div>
                            @error('file_path')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle"></i> <strong>Report Guidelines:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Include your accomplishments, activities, and learnings</li>
                                <li>Mention any challenges faced</li>
                                <li>Can be in any format (Word, PDF, PowerPoint, etc.)</li>
                                <li>Once submitted, supervisors will review it</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('student.reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" name="action" value="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file_path');
    const uploadArea = document.querySelector('.upload-area');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const fileSelected = document.getElementById('file-selected');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');

    // Click to upload
    uploadArea.addEventListener('click', () => fileInput.click());

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#7c3aed';
        uploadArea.style.backgroundColor = '#f5f0ff';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '#ccc';
        uploadArea.style.backgroundColor = '#fafafa';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#ccc';
        uploadArea.style.backgroundColor = '#fafafa';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            updateFileDisplay();
        }
    });

    // File input change
    fileInput.addEventListener('change', updateFileDisplay);

    function updateFileDisplay() {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
            uploadPlaceholder.style.display = 'none';
            fileSelected.style.display = 'block';
        } else {
            uploadPlaceholder.style.display = 'block';
            fileSelected.style.display = 'none';
        }
    }
});
</script>
@endsection
