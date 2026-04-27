@extends('layouts.app')

@section('title', 'Time Record - OJT System')
@section('page-title', 'Time Record Details')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock"></i> 
                        {{ $dtr->record_date->format('M d, Y') }}
                    </h5>
                    <a href="{{ route('student.dtr.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <!-- Clock In Display -->
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 60px; height: 60px; background: #e8f0ff; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-clock-history" style="font-size: 1.8rem; color: #2563eb;"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Clocked In At</div>
                                <div style="font-size: 1.8rem; font-weight: bold;">{{ $dtr->time_in->format('H:i') }}</div>
                                <div class="text-muted small">{{ $dtr->time_in->format('l, F d, Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Clock Out Section -->
                    @if($dtr->time_out)
                        <!-- Already Clocked Out -->
                        <div class="mb-4 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 60px; height: 60px; background: #f0fdf4; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-check-circle" style="font-size: 1.8rem; color: #059669;"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Clocked Out At</div>
                                    <div style="font-size: 1.8rem; font-weight: bold;">{{ $dtr->time_out->format('H:i') }}</div>
                                    <div class="text-muted small">{{ $dtr->time_out->format('l, F d, Y') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Hours Display -->
                        <div class="mb-4 p-3" style="background: #f8f9fa; border-radius: 8px;">
                            <div class="text-muted small mb-2">Total Hours Worked</div>
                            <div style="font-size: 2.5rem; font-weight: bold; color: #059669;">
                                {{ $dtr->hours_worked ?? 0 }} hrs
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="mb-4">
                            @if($dtr->status === 'verified')
                                <span class="badge bg-success" style="padding: 8px 16px; font-size: 0.9rem;">
                                    <i class="bi bi-check-circle"></i> Verified by Supervisor
                                </span>
                            @elseif($dtr->status === 'rejected')
                                <span class="badge bg-danger" style="padding: 8px 16px; font-size: 0.9rem;">
                                    <i class="bi bi-x-circle"></i> Rejected
                                </span>
                            @else
                                <span class="badge bg-warning" style="padding: 8px 16px; font-size: 0.9rem;">
                                    <i class="bi bi-clock"></i> Pending Verification
                                </span>
                            @endif
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Your time record has been saved and is pending supervisor verification.
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('student.dtr.index') }}" class="btn btn-primary">
                                <i class="bi bi-list-ul"></i> View All Records
                            </a>
                        </div>
                    @else
                        <!-- Clock Out Form -->
                        <form action="{{ route('student.dtr.update', $dtr->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label for="time_out" class="form-label"><strong>Clock Out Time</strong></label>
                                <input type="time" class="form-control @error('time_out') is-invalid @enderror" 
                                       id="time_out" name="time_out" value="{{ date('H:i') }}" required>
                                @error('time_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle"></i> <strong>Ready to Clock Out?</strong><br>
                                Set your clock out time above and save.
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Clock Out
                                </button>
                                <a href="{{ route('student.dtr.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
