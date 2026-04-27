@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>OJT Placements</h2>
                @if(auth()->user()->isCoordinator())
                <a href="{{ route('placements.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Placement
                </a>
                @endif
            </div>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('placements.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="filterStatus" class="mr-2">Status:</label>
                            <select id="filterStatus" name="status" class="form-control">
                                <option value="">All</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="form-group mr-3">
                            <label for="filterCompany" class="mr-2">Company:</label>
                            <input type="text" id="filterCompany" name="company" class="form-control" placeholder="Search...">
                        </div>
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>
                </div>
            </div>

            <!-- Placements List -->
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Student</th>
                                <th>Company</th>
                                <th>Placement Period</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($placements as $placement)
                            <tr>
                                <td><strong>{{ $placement->student->short_name }}</strong></td>
                                <td>{{ $placement->company->company_name ?? 'N/A' }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $placement->start_date->format('M d, Y') }} - {{ $placement->end_date->format('M d, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px; width: 150px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ round($placement->getProgressPercentage(), 1) }}%;">
                                            {{ round($placement->getProgressPercentage(), 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($placement->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($placement->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-secondary">Completed</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('placements.show', $placement) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->isCoordinator() && $placement->status === 'active')
                                        <a href="{{ route('placements.edit', $placement) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-briefcase"></i> No placements found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($placements->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $placements->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
