@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-briefcase"></i> Placement Details</h4>
                    <a href="{{ route('placements.index') }}" class="btn btn-sm btn-light">Back</a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Student:</strong><br>
                                {{ $placement->student->full_name }} ({{ $placement->student->id_number }})
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Course:</strong><br>
                                {{ $placement->student->course }} - Year {{ $placement->student->year_level }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Company:</strong><br>
                                {{ $placement->company->company_name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Supervisor:</strong><br>
                                {{ $placement->supervisor->full_name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Placement Period:</strong><br>
                                {{ $placement->start_date->format('M d, Y') }} - {{ $placement->end_date->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Required Hours:</strong><br>
                                {{ $placement->total_required_hours }} hours
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Progress -->
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Progress</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <p>
                                    <strong>Duration:</strong><br>
                                    {{ $placement->getDaysElapsed() }} / {{ $placement->getDaysElapsed() + $placement->getDaysRemaining() }} days
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p>
                                    <strong>Days Remaining:</strong><br>
                                    {{ $placement->getDaysRemaining() }} days
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p>
                                    <strong>Completion:</strong><br>
                                    {{ round($placement->getProgressPercentage(), 1) }}%
                                </p>
                            </div>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ round($placement->getProgressPercentage(), 1) }}%;">
                                {{ round($placement->getProgressPercentage(), 1) }}%
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Status -->
                    <div class="mb-4">
                        <p>
                            <strong>Status:</strong><br>
                            @if($placement->status === 'active')
                                <span class="badge badge-success" style="font-size: 1rem;">Active</span>
                            @elseif($placement->status === 'pending')
                                <span class="badge badge-warning" style="font-size: 1rem;">Pending</span>
                            @else
                                <span class="badge badge-secondary" style="font-size: 1rem;">Completed</span>
                            @endif
                        </p>
                    </div>

                    <!-- Coordinator Info -->
                    @if($placement->coordinator)
                    <div class="mb-4">
                        <p>
                            <strong>School Coordinator:</strong><br>
                            {{ $placement->coordinator->full_name }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Certifications Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-certificate"></i> Certification Status</h5>
                        </div>
                        <div class="card-body">
                            @if($placement->certifications->count() > 0)
                                @foreach($placement->certifications as $cert)
                                <div class="mb-3 pb-3 border-bottom">
                                    <h6 class="mb-1">{{ $cert->student->short_name }}</h6>
                                    <p class="mb-1"><small>Submitted: {{ $cert->created_at->format('M d, Y') }}</small></p>
                                    <p class="mb-1"><small>Hours: {{ $cert->actual_hours_worked }}</small></p>
                                    <p class="mb-1"><small>Rating: {{ $cert->final_rating ?? 'N/A' }}/5</small></p>
                                    <span class="badge badge-{{ $cert->status === 'approved' ? 'success' : ($cert->status === 'verified' ? 'info' : 'warning') }}">
                                        {{ ucfirst($cert->status) }}
                                    </span>
                                    <a href="{{ route('certifications.show', $cert) }}" class="btn btn-sm btn-info float-right">
                                        View
                                    </a>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">No certifications yet</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Completion Section -->
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-tasks"></i> Completion Status</h5>
                        </div>
                        <div class="card-body">
                            @if($placement->completionRecords->count() > 0)
                                @foreach($placement->completionRecords as $record)
                                <div class="mb-3 pb-3 border-bottom">
                                    <h6 class="mb-1">{{ $record->student->short_name }}</h6>
                                    <p class="mb-1"><small>Total Hours: {{ $record->total_hours_completed }}</small></p>
                                    <p class="mb-1"><small>Final Grade: {{ $record->final_grade ?? 'N/A' }}</small></p>
                                    <p class="mb-1"><small>Requirements Met: {{ $record->met_requirements ? 'Yes' : 'No' }}</small></p>
                                    <span class="badge badge-{{ $record->is_completed ? 'success' : 'secondary' }}">
                                        {{ $record->is_completed ? 'Completed' : 'Pending' }}
                                    </span>
                                    @if($record->certificate_number)
                                    <p class="mt-2"><small><strong>Cert #:</strong> {{ $record->certificate_number }}</small></p>
                                    @endif
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">No completion records yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
