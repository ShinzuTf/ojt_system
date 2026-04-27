@extends('layouts.app')

@section('title', 'Set Student Company - OJT System')
@section('page-title', 'Set Student Company')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-briefcase"></i> Students Needing Company Assignment</h5>
        </div>
        <div class="card-body">
            @if($students && $students->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Student Number</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td><strong>{{ $student->full_name ?? 'N/A' }}</strong></td>
                                    <td>{{ $student->ojtInfo?->student_number ?? 'N/A' }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        <a href="{{ route('coordinator.placements.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-briefcase"></i> Set Company
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <nav>
                    {{ $students->links() }}
                </nav>
            @else
                <div class="empty-state">
                    <i class="bi bi-check-circle"></i>
                    <p>All students have been assigned companies!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
