<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use App\Models\Staff;
use App\Services\ActivityReportAiService;
use App\Services\ActivityReportAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ActivityReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityReport::with(['staff', 'activity', 'weeklyTracker'])->recentFirst();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('linked')) {
            if ($request->linked === 'yes') {
                $query->whereNotNull('activity_calendar_id');
            } elseif ($request->linked === 'no') {
                $query->whereNull('activity_calendar_id');
            }
        }

        if ($request->filled('mission')) {
            if ($request->mission === 'yes') {
                $query->whereNotNull('weekly_tracker_id');
            } elseif ($request->mission === 'no') {
                $query->whereNull('weekly_tracker_id');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhereHas('staff', function ($staffQuery) use ($search) {
                        $staffQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('staff_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('weeklyTracker', function ($trackerQuery) use ($search) {
                        $trackerQuery->where('mission_title', 'like', "%{$search}%")
                            ->orWhere('mission_purpose', 'like', "%{$search}%");
                    })
                    ->orWhereHas('activity', function ($activityQuery) use ($search) {
                        $activityQuery->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $reports = $query->paginate(20)->withQueryString();

        $stats = app(ActivityReportAnalyticsService::class)->getIndexStats();

        $staffMembers = Staff::where('status', 'active')->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'staff_id']);

        $aiConfigured = app(ActivityReportAiService::class)->isConfigured();

        return view('admin.activity-reports.index', compact('reports', 'stats', 'staffMembers', 'aiConfigured'));
    }

    public function show(ActivityReport $activityReport)
    {
        $activityReport->load(['staff', 'activity', 'reviewer', 'weeklyTracker']);

        $aiConfigured = app(ActivityReportAiService::class)->isConfigured();

        return view('admin.activity-reports.show', compact('activityReport', 'aiConfigured'));
    }

    public function review(Request $request, ActivityReport $activityReport)
    {
        if ($activityReport->status === 'draft') {
            return redirect()->route('admin.activity-reports.show', $activityReport)
                ->with('error', 'Draft reports cannot be reviewed until submitted by staff.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $activityReport->update([
            'status' => 'reviewed',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::guard('staff')->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.activity-reports.show', $activityReport)
            ->with('success', 'Activity report marked as reviewed.');
    }

    public function downloadAttachment(ActivityReport $activityReport)
    {
        if (!$activityReport->attachment) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $activityReport->attachment['path'],
            $activityReport->attachment['original_name']
        );
    }
}
