@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-alt"></i> Create Report</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('reports.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="reportType">Report Type <span class="text-danger">*</span></label>
                            <select id="reportType" name="report_type" class="form-control @error('report_type') is-invalid @enderror" required>
                                <option value="">-- Select Type --</option>
                                <option value="weekly" {{ old('report_type') === 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                                <option value="monthly" {{ old('report_type') === 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                            </select>
                            @error('report_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periodStart">Period Start <span class="text-danger">*</span></label>
                                    <input type="date" id="periodStart" name="report_period_start" class="form-control @error('report_period_start') is-invalid @enderror" 
                                           value="{{ old('report_period_start') }}" required>
                                    @error('report_period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periodEnd">Period End <span class="text-danger">*</span></label>
                                    <input type="date" id="periodEnd" name="report_period_end" class="form-control @error('report_period_end') is-invalid @enderror" 
                                           value="{{ old('report_period_end') }}" required>
                                    @error('report_period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="accomplishments">Accomplishments <span class="text-danger">*</span></label>
                            <textarea id="accomplishments" name="accomplishments" class="form-control @error('accomplishments') is-invalid @enderror" 
                                      rows="4" placeholder="Describe what you accomplished during this period" required>{{ old('accomplishments') }}</textarea>
                            @error('accomplishments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="learnings">Key Learnings</label>
                            <textarea id="learnings" name="key_learnings" class="form-control @error('key_learnings') is-invalid @enderror" 
                                      rows="3" placeholder="What did you learn?">{{ old('key_learnings') }}</textarea>
                            @error('key_learnings')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="challenges">Challenges Encountered</label>
                            <textarea id="challenges" name="challenges_faced" class="form-control @error('challenges_faced') is-invalid @enderror" 
                                      rows="3" placeholder="Any challenges or issues?">{{ old('challenges_faced') }}</textarea>
                            @error('challenges_faced')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nextSteps">Next Steps / Goals</label>
                            <textarea id="nextSteps" name="next_steps" class="form-control @error('next_steps') is-invalid @enderror" 
                                      rows="3" placeholder="What are your goals for next period?">{{ old('next_steps') }}</textarea>
                            @error('next_steps')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" name="action" value="draft" class="btn btn-secondary btn-lg">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Submit Report
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-lg">
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
