@extends('layouts.app')

@section('title', 'Manage Trainees')

@section('breadcrumb')
    <a href="{{ route('supervisor.dashboard') }}">Dashboard</a>
    <span class="separator">/</span>
    <span class="current">Trainees</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Trainees</h1>
    <p class="page-subtitle">View and manage all your assigned trainees</p>
</div>

{{-- Search & Filter --}}
<div class="card" style="margin-bottom:22px;">
    <div class="card-body" style="padding:18px 24px;">
        <form method="GET" action="{{ route('supervisor.trainees') }}" style="display:grid; grid-template-columns: 1fr auto; gap:12px;">
            <div style="display:flex; gap:12px;">
                <input type="text" name="search" placeholder="Search trainee..." value="{{ request('search') }}" 
                    style="flex:1; padding:10px 14px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem;">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('supervisor.trainees') }}" class="btn btn-ghost">Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Trainees Table --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table" style="width:100%;">
                <thead>
                    <tr>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Name</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Student #</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Course</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Company</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">OJT Period</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainees as $trainee)
                        @php $ojt = $trainee->ojtInfo; @endphp
                        <tr style="border-bottom:1px solid var(--gray-100);">
                            <td style="padding:14px 24px;">
                                <div style="font-weight:600; color:var(--gray-800);">{{ $trainee->full_name }}</div>
                                <div style="font-size:0.8rem; color:var(--gray-500); margin-top:2px;">{{ $trainee->email }}</div>
                            </td>
                            <td style="padding:14px 24px; font-size:0.9rem;">
                                {{ $ojt?->student_number ?? '—' }}
                            </td>
                            <td style="padding:14px 24px; font-size:0.9rem;">
                                {{ $ojt?->course ?? '—' }}
                            </td>
                            <td style="padding:14px 24px; font-size:0.9rem;">
                                {{ $ojt?->company_name ?? '—' }}
                            </td>
                            <td style="padding:14px 24px; font-size:0.85rem;">
                                @if($ojt)
                                    {{ $ojt->ojt_start?->format('M d') ?? '—' }} to {{ $ojt->ojt_end?->format('M d, Y') ?? '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="padding:14px 24px;">
                                <a href="{{ route('supervisor.trainees.show', $trainee->id) }}" class="btn btn-sm btn-primary" style="font-size:0.75rem;">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:40px 24px; text-align:center; color:var(--gray-400);">
                                No trainees found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($trainees->hasPages())
            <div style="padding:18px 24px; border-top:1px solid var(--gray-100); display:flex; justify-content:center;">
                {{ $trainees->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
