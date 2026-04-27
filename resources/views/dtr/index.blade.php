@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Daily Time Record (DTR)</h2>
                <a href="{{ route('dtr.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Entry
                </a>
            </div>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('dtr.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="filterDate" class="mr-2">Date:</label>
                            <input type="date" id="filterDate" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="form-group mr-3">
                            <label for="filterStatus" class="mr-2">Status:</label>
                            <select id="filterStatus" name="status" class="form-control">
                                <option value="">All</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>
                </div>
            </div>

            <!-- DTR List -->
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Hours Worked</th>
                                <th>Status</th>
                                <th>Verified By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dtrs as $dtr)
                            <tr>
                                <td>
                                    <strong>{{ $dtr->record_date->format('M d, Y') }}</strong>
                                </td>
                                <td>
                                    {{ $dtr->time_in ? \Carbon\Carbon::parse($dtr->time_in)->format('h:i A') : '-' }}
                                </td>
                                <td>
                                    {{ $dtr->time_out ? \Carbon\Carbon::parse($dtr->time_out)->format('h:i A') : '-' }}
                                </td>
                                <td>
                                    <strong>{{ $dtr->hours_worked ?? 'N/A' }}h</strong>
                                </td>
                                <td>
                                    @if($dtr->status === 'verified')
                                        <span class="badge badge-success">Verified</span>
                                    @elseif($dtr->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $dtr->verified_by ? $dtr->verifier->short_name : '-' }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('dtr.show', $dtr) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($dtr->status === 'pending' && auth()->user()->isSupervisor())
                                        <a href="{{ route('dtr.verify', $dtr) }}" class="btn btn-success" title="Verify">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        @endif
                                        @if(auth()->user()->isStudent() && $dtr->status === 'pending')
                                        <a href="{{ route('dtr.edit', $dtr) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('dtr.destroy', $dtr) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete entry?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox"></i> No DTR entries found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($dtrs->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $dtrs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
