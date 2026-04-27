@extends('layouts.app')

@section('title', 'Daily Time Records - OJT System')
@section('page-title', 'Daily Time Records')

@section('content')
<div class="container-fluid">
    <!-- Header with Actions -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="mb-0"><i class="bi bi-clock-history"></i> Daily Time Records</h4>
            <small class="text-muted">Complete record of all time entries with hours logged</small>
        </div>
        <div class="col-md-4 text-end">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-printer"></i> Print
            </button>
            <a href="{{ route('student.dtr.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus"></i> New Entry
            </a>
        </div>
    </div>

    @if($dtrs && $dtrs->count())
        <!-- Timesheet Summary Table -->
        <div class="card mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0"><i class="bi bi-table"></i> Timesheet Summary</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead style="background-color: #f8f9fa; position: sticky; top: 0;">
                            <tr>
                                <th style="width: 15%; padding: 12px 16px;"><strong>Date</strong></th>
                                <th style="width: 15%; padding: 12px 16px;"><strong>Day</strong></th>
                                <th style="width: 15%; padding: 12px 16px;"><strong>Time In</strong></th>
                                <th style="width: 15%; padding: 12px 16px;"><strong>Time Out</strong></th>
                                <th style="width: 12%; padding: 12px 16px;"><strong>Hours</strong></th>
                                <th style="width: 15%; padding: 12px 16px;"><strong>Status</strong></th>
                                <th style="width: 13%; padding: 12px 16px;"><strong>Action</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalHoursSummary = 0; @endphp
                            @foreach($dtrs as $dtr)
                                @php 
                                    $hours = $dtr->hours_worked ?? 0;
                                    $totalHoursSummary += $hours;
                                @endphp
                                <tr>
                                    <td style="padding: 12px 16px;">
                                        <strong>{{ $dtr->record_date->format('M d, Y') }}</strong>
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        {{ $dtr->record_date->format('l') }}
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        <code>{{ $dtr->time_in->format('H:i') }}</code>
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        <code>{{ $dtr->time_out ? $dtr->time_out->format('H:i') : '-' }}</code>
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        <strong style="font-size: 1.1em;">{{ $hours }}</strong>
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        @if($dtr->status === 'verified')
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Verified</span>
                                        @elseif($dtr->status === 'rejected')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                                        @else
                                            <span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        <a href="{{ route('student.dtr.show', $dtr->id) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-hourglass-split"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Hours</div>
                        <div class="stat-value">{{ $totalHours ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Verified</div>
                        <div class="stat-value">{{ $verifiedCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="bi bi-clock"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Pending</div>
                        <div class="stat-value">{{ $pendingCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-calendar-range"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Entries</div>
                        <div class="stat-value">{{ $dtrs->total() ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            {{ $dtrs->links() }}
        </nav>

        <!-- Print Stylesheet -->
        <style media="print">
            @page {
                size: A4;
                margin: 10mm;
            }
            body {
                font-size: 11pt;
                color: #000;
            }
            .btn, .row > div:not(.table-responsive), .card-header, .stat-card, [onclick] {
                display: none !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f0f0f0;
                font-weight: bold;
            }
            .badge {
                border: 1px solid #000;
                padding: 2px 4px;
                display: inline-block;
            }
        </style>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="empty-state">
                    <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3"><strong>No DTR entries yet</strong></p>
                    <small class="text-secondary">Start logging your time by creating your first entry</small>
                    <div class="mt-4">
                        <a href="{{ route('student.dtr.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create First Entry
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
