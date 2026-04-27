@extends('layouts.app')

@section('title', 'Certification Review - OJT System')
@section('page-title', 'Certification Review')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-award"></i> Certification Details</h5>
                        <span class="badge badge-{{ $certification->status === 'approved' ? 'success' : ($certification->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($certification->status ?? 'submitted') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Student</label>
                            <p class="h6 mb-0">{{ $certification->student?->fname ?? $certification->student?->name ?? 'N/A' }} {{ $certification->student?->lname ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Company</label>
                            <p class="h6 mb-0">{{ $certification->placement?->company_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Certification Date</label>
                            <p class="h6 mb-0">{{ $certification->certification_date ? $certification->certification_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Final Rating</label>
                            <p class="h6 mb-0">{{ $certification->final_rating ? number_format($certification->final_rating, 2) . ' / 5' : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Actual Hours Worked</label>
                            <p class="h6 mb-0">{{ $certification->actual_hours_worked ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Issued By</label>
                            <p class="h6 mb-0">{{ $certification->issuedBy?->fname ?? $certification->issuedBy?->name ?? 'N/A' }} {{ $certification->issuedBy?->lname ?? '' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted">Remarks</label>
                        <div class="p-3" style="background:#f8f9fa; border-radius:0.375rem;">
                            <p class="mb-0">{{ $certification->remarks ?? 'No remarks provided.' }}</p>
                        </div>
                    </div>

                    @if($certification->certificate_path)
                        <div class="mb-4">
                            <label class="form-label text-muted">Certificate File</label>
                            <div class="d-flex align-items-center justify-content-between p-3" style="background:#f8f9fa; border-radius:0.375rem;">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size:1.5rem; margin-right:12px;"></i>
                                    <div>
                                        <p class="mb-0"><strong>{{ $certification->certificate_file_name ?? basename($certification->certificate_path) }}</strong></p>
                                        <small class="text-muted">Uploaded certification file</small>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $certification->certificate_path) }}" class="btn btn-sm btn-outline-primary" download>
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($certification->status === 'submitted')
                        <div class="border-top pt-4 mt-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('coordinator.certifications.approve', $certification) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('coordinator.certifications.reject', $certification) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Status</small>
                        <p class="h6 mb-0">{{ ucfirst($certification->status ?? 'submitted') }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Rating Text</small>
                        <p class="h6 mb-0">{{ $certification->getRatingText() }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Verified By</small>
                        <p class="h6 mb-0">{{ $certification->verifiedBy?->fname ?? $certification->verifiedBy?->name ?? 'Pending' }} {{ $certification->verifiedBy?->lname ?? '' }}</p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Verified At</small>
                        <p class="h6 mb-0">{{ $certification->verified_at ? $certification->verified_at->format('M d, Y g:i A') : 'Pending' }}</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('coordinator.certifications.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="bi bi-arrow-left"></i> Back to Certifications
            </a>
        </div>
    </div>
</div>
@endsection