@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-clock"></i> Create Daily Time Record</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('dtr.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="recordDate">Date <span class="text-danger">*</span></label>
                            <input type="date" id="recordDate" name="record_date" class="form-control @error('record_date') is-invalid @enderror" 
                                   value="{{ old('record_date', \Carbon\Carbon::now()->toDateString()) }}" required>
                            @error('record_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="timeIn">Time In <span class="text-danger">*</span></label>
                                    <input type="time" id="timeIn" name="time_in" class="form-control @error('time_in') is-invalid @enderror" 
                                           value="{{ old('time_in') }}" required>
                                    @error('time_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="timeOut">Time Out <span class="text-danger">*</span></label>
                                    <input type="time" id="timeOut" name="time_out" class="form-control @error('time_out') is-invalid @enderror" 
                                           value="{{ old('time_out') }}" required>
                                    @error('time_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="breakTime">Break Time (minutes)</label>
                            <input type="number" id="breakTime" name="break_minutes" class="form-control" 
                                   value="{{ old('break_minutes', 0) }}" min="0" max="120">
                            <small class="form-text text-muted">Time to subtract from worked hours</small>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea id="remarks" name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                      rows="3" placeholder="Any notes or comments">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle"></i> This entry will be submitted for supervisor verification.</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-save"></i> Submit DTR Entry
                            </button>
                            <a href="{{ route('dtr.index') }}" class="btn btn-secondary btn-block mt-2">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
