@extends('layouts.app')

@section('title', 'Clock In - OJT System')
@section('page-title', 'Clock In')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-in"></i> Clock In</h5>
                    <a href="{{ route('student.dtr.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-list-ul"></i> View All
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.dtr.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="date" class="form-label"><strong>Date</strong></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" 
                                   value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="time_in" class="form-label"><strong>Time In</strong></label>
                            <input type="time" class="form-control @error('time_in') is-invalid @enderror" id="time_in" name="time_in" 
                                   value="{{ old('time_in', date('H:i')) }}" required>
                            @error('time_in')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle"></i> <strong>Clock In Only</strong><br>
                            You can add your Clock Out time after you've clocked in.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('student.dtr.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-clock"></i> Clock In
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
