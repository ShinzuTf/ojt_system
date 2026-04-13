<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs with filters
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user', 'targetUser')
            ->orderBy('created_at', 'desc');

        // Filter by activity type
        if ($request->has('activity') && $request->activity) {
            $query->where('activity', $request->activity);
        }

        // Filter by module
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter by recent days
        if ($request->has('days') && $request->days && !$request->has('from_date')) {
            $query->where('created_at', '>=', now()->subDays($request->days));
        }

        $activities = $query->paginate(50);
        $users = User::orderBy('fname')->get();
        
        // Get available activity types
        $activityTypes = ActivityLog::select('activity')
            ->distinct()
            ->orderBy('activity')
            ->pluck('activity');

        // Get available modules
        $modules = ActivityLog::select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('admin.activity-logs.index', compact(
            'activities',
            'users',
            'activityTypes',
            'modules'
        ));
    }

    /**
     * Show activity log details
     */
    public function show($id)
    {
        $activity = ActivityLog::with('user', 'targetUser')->findOrFail($id);
        return view('admin.activity-logs.show', compact('activity'));
    }

    /**
     * Get login activity report
     */
    public function loginReport(Request $request)
    {
        $query = ActivityLog::where('activity', 'user_login')
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logins = $query->paginate(100);
        $loginStats = ActivityLog::where('activity', 'user_login')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful')
            ->recent(30)
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.activity-logs.login-report', compact('logins', 'loginStats'));
    }

    /**
     * Get document generation report
     */
    public function documentReport(Request $request)
    {
        $query = ActivityLog::where('activity', 'document_generated')
            ->with('user', 'targetUser')
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $documents = $query->paginate(100);

        // Document generation stats
        $stats = ActivityLog::where('activity', 'like', 'document_%')
            ->selectRaw('activity, COUNT(*) as count')
            ->recent(30)
            ->groupBy('activity')
            ->get();

        return view('admin.activity-logs.document-report', compact('documents', 'stats'));
    }

    /**
     * Get evaluation report
     */
    public function evaluationReport(Request $request)
    {
        $query = ActivityLog::where('activity', 'like', 'evaluation_%')
            ->with('user', 'targetUser')
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $evaluations = $query->paginate(100);

        // Evaluation stats
        $stats = ActivityLog::where('activity', 'like', 'evaluation_%')
            ->selectRaw('activity, COUNT(*) as count')
            ->recent(30)
            ->groupBy('activity')
            ->get();

        return view('admin.activity-logs.evaluation-report', compact('evaluations', 'stats'));
    }

    /**
     * Get user management report
     */
    public function userManagementReport(Request $request)
    {
        $query = ActivityLog::where('module', 'admin')
            ->with('user', 'targetUser')
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $activities = $query->paginate(100);

        // Stats by activity
        $stats = ActivityLog::where('module', 'admin')
            ->selectRaw('activity, COUNT(*) as count')
            ->recent(30)
            ->groupBy('activity')
            ->get();

        return view('admin.activity-logs.user-management-report', compact('activities', 'stats'));
    }

    /**
     * Export activity logs to CSV
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user', 'targetUser');

        // Apply same filters as index
        if ($request->has('activity') && $request->activity) {
            $query->where('activity', $request->activity);
        }

        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }

        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($activities) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Date', 'User', 'Activity', 'Module', 'Action', 'Description', 'Target User', 'Status', 'IP Address'
            ]);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $activity->user?->email ?? 'N/A',
                    $activity->activity,
                    $activity->module,
                    $activity->action,
                    $activity->description,
                    $activity->targetUser?->email ?? 'N/A',
                    $activity->status,
                    $activity->ip_address,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
