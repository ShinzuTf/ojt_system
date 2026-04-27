@extends('layouts.app')

@section('title', 'Manage Trainees - OJT System')
@section('page-title', 'All Trainees')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h4"><i class="bi bi-people-fill"></i> Students This Semester</h2>
            <p class="text-muted">Manage and monitor all student trainees in the school this semester.</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('coordinator.trainees.index') }}" class="btn btn-sm btn-outline-secondary {{ request('status') ? '' : 'active' }}">All</a>
                <a href="{{ route('coordinator.trainees.index', ['status' => 'active']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') === 'active' ? 'active' : '' }}">Active</a>
                <a href="{{ route('coordinator.trainees.index', ['status' => 'completed']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') === 'completed' ? 'active' : '' }}">Completed</a>
            </div>
        </div>
    </div>

    @if($trainees->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No students found for this filter.
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trainees as $trainee)
                <tr>
                    <td>
                        <code class="text-danger">{{ $trainee->id }}</code>
                    </td>
                    <td>
                        <strong>{{ $trainee->fname }} {{ $trainee->lname }}</strong>
                        @if($trainee->suffix)
                            <small class="text-muted">{{ $trainee->suffix }}</small>
                        @endif
                    </td>
                    <td>
                        @if($trainee->currentPlacement && $trainee->currentPlacement->supervisor)
                            {{ $trainee->currentPlacement->supervisor->company_name ?? ($trainee->ojtInfo->company_name ?? '-') }}
                        @elseif($trainee->ojtInfo && $trainee->ojtInfo->company_name)
                            {{ $trainee->ojtInfo->company_name }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($trainee->currentPlacement)
                            <span class="badge badge-{{ $trainee->currentPlacement->status === 'active' ? 'success' : ($trainee->currentPlacement->status === 'completed' ? 'info' : 'warning') }}">
                                {{ ucfirst($trainee->currentPlacement->status) }}
                            </span>
                        @else
                            <span class="text-muted">No Active Placement</span>
                        @endif
                    </td>
                    <td>
                        @if($trainee->currentPlacement)
                            {{ $trainee->currentPlacement->start_date ? $trainee->currentPlacement->start_date->format('M d, Y') : '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($trainee->currentPlacement)
                            {{ $trainee->currentPlacement->end_date ? $trainee->currentPlacement->end_date->format('M d, Y') : '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('coordinator.trainees.show', $trainee) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $trainees->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<style>
    .badge-success { background-color: #198754; }
    .badge-warning { background-color: #ffc107; color: #333; }
    .badge-info { background-color: #0dcaf0; }
    .badge-danger { background-color: #dc3545; }
</style>
@endsection
