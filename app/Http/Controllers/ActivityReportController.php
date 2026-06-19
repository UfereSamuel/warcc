<?php

namespace App\Http\Controllers;

use App\Models\ActivityCalendar;
use App\Models\ActivityReport;
use App\Models\Staff;
use App\Services\ActivityWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ActivityReportController extends Controller
{
    public function __construct(
        private readonly ActivityWorkflowService $workflow
    ) {}

    public function index(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $status = $request->get('status');

        $query = ActivityReport::with('activity')
            ->byStaff($staffId)
            ->recentFirst();

        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->paginate(15);

        $stats = [
            'total' => ActivityReport::byStaff($staffId)->count(),
            'draft' => ActivityReport::byStaff($staffId)->where('status', 'draft')->count(),
            'submitted' => ActivityReport::byStaff($staffId)->where('status', 'submitted')->count(),
            'reviewed' => ActivityReport::byStaff($staffId)->where('status', 'reviewed')->count(),
        ];

        $pendingActivities = $this->workflow->getPendingReportsForStaff($staff);

        return view('staff.activity-reports.index', compact('reports', 'stats', 'status', 'pendingActivities'));
    }

    public function create(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $selectedActivity = null;

        if ($request->filled('activity_calendar_id')) {
            $selectedActivity = ActivityCalendar::find($request->activity_calendar_id);
        }

        $pendingActivities = $this->workflow->getPendingReportsForStaff($staff);
        $calendarActivities = $this->reportableCalendarActivities($staff, $selectedActivity);

        return view('staff.activity-reports.create', compact(
            'calendarActivities',
            'selectedActivity',
            'pendingActivities'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validateReport($request);
        $submit = $request->input('action') === 'submit';

        $report = ActivityReport::create([
            ...$data,
            'staff_id' => Auth::guard('staff')->id(),
            'status' => $submit ? 'submitted' : 'draft',
            'submitted_at' => $submit ? now() : null,
            'attachment' => $this->storeAttachment($request),
        ]);

        $message = $submit
            ? 'Activity report submitted successfully.'
            : 'Activity report saved as draft.';

        return redirect()->route('staff.activity-reports.show', $report)->with('success', $message);
    }

    public function show(ActivityReport $activityReport)
    {
        $this->authorizeStaff($activityReport);

        $activityReport->load(['activity', 'reviewer']);

        return view('staff.activity-reports.show', compact('activityReport'));
    }

    public function edit(ActivityReport $activityReport)
    {
        $this->authorizeStaff($activityReport);

        if (!$activityReport->isEditableByStaff()) {
            return redirect()->route('staff.activity-reports.show', $activityReport)
                ->with('error', 'Only draft reports can be edited.');
        }

        $calendarActivities = $this->reportableCalendarActivities(
            Auth::guard('staff')->user(),
            $activityReport->activity
        );

        return view('staff.activity-reports.edit', compact('activityReport', 'calendarActivities'));
    }

    public function update(Request $request, ActivityReport $activityReport)
    {
        $this->authorizeStaff($activityReport);

        if (!$activityReport->isEditableByStaff()) {
            return redirect()->route('staff.activity-reports.show', $activityReport)
                ->with('error', 'Only draft reports can be edited.');
        }

        $data = $this->validateReport($request);
        $submit = $request->input('action') === 'submit';

        $update = [
            ...$data,
            'status' => $submit ? 'submitted' : 'draft',
            'submitted_at' => $submit ? now() : $activityReport->submitted_at,
        ];

        if ($request->hasFile('attachment')) {
            $this->deleteAttachment($activityReport);
            $update['attachment'] = $this->storeAttachment($request);
        }

        $activityReport->update($update);

        $message = $submit
            ? 'Activity report submitted successfully.'
            : 'Activity report updated successfully.';

        return redirect()->route('staff.activity-reports.show', $activityReport)->with('success', $message);
    }

    public function destroy(ActivityReport $activityReport)
    {
        $this->authorizeStaff($activityReport);

        if (!$activityReport->isEditableByStaff()) {
            return redirect()->route('staff.activity-reports.index')
                ->with('error', 'Only draft reports can be deleted.');
        }

        $this->deleteAttachment($activityReport);
        $activityReport->delete();

        return redirect()->route('staff.activity-reports.index')
            ->with('success', 'Activity report deleted successfully.');
    }

    public function submit(ActivityReport $activityReport)
    {
        $this->authorizeStaff($activityReport);

        if ($activityReport->status !== 'draft') {
            return redirect()->route('staff.activity-reports.show', $activityReport)
                ->with('error', 'Only draft reports can be submitted.');
        }

        $activityReport->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('staff.activity-reports.show', $activityReport)
            ->with('success', 'Activity report submitted for review.');
    }

    public function downloadAttachment(ActivityReport $activityReport)
    {
        $this->authorizeStaff($activityReport);

        if (!$activityReport->attachment) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $activityReport->attachment['path'],
            $activityReport->attachment['original_name']
        );
    }

    private function authorizeStaff(ActivityReport $activityReport): void
    {
        if ($activityReport->staff_id !== Auth::guard('staff')->id()) {
            abort(403, 'Unauthorized to access this activity report.');
        }
    }

    private function validateReport(Request $request): array
    {
        $validated = $request->validate([
            'activity_calendar_id' => 'nullable|exists:activity_calendars,id',
            'title' => 'required|string|max:255',
            'report_date' => 'required|date',
            'summary' => 'required|string|max:10000',
            'outcomes' => 'nullable|string|max:5000',
            'challenges' => 'nullable|string|max:5000',
            'recommendations' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        return [
            'activity_calendar_id' => $validated['activity_calendar_id'] ?? null,
            'title' => $validated['title'],
            'report_date' => $validated['report_date'],
            'summary' => $validated['summary'],
            'outcomes' => $validated['outcomes'] ?? null,
            'challenges' => $validated['challenges'] ?? null,
            'recommendations' => $validated['recommendations'] ?? null,
        ];
    }

    private function storeAttachment(Request $request): ?array
    {
        if (!$request->hasFile('attachment')) {
            return null;
        }

        $file = $request->file('attachment');
        $path = $file->store('activity-reports', 'public');

        return [
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    private function deleteAttachment(ActivityReport $activityReport): void
    {
        if ($activityReport->attachment && isset($activityReport->attachment['path'])) {
            Storage::disk('public')->delete($activityReport->attachment['path']);
        }
    }

    /**
     * Calendar activities the staff member can link when filing a report.
     */
    private function reportableCalendarActivities(Staff $staff, ?ActivityCalendar $includeActivity = null)
    {
        $ids = $this->workflow->getParticipatedActivityIds($staff)
            ->merge($this->workflow->getPendingReportsForStaff($staff)->pluck('id'))
            ->unique()
            ->values();

        if ($includeActivity) {
            $ids = $ids->push($includeActivity->id)->unique()->values();
        }

        if ($ids->isEmpty()) {
            return collect();
        }

        return ActivityCalendar::query()
            ->whereIn('id', $ids)
            ->orderByDesc('end_date')
            ->get(['id', 'title', 'start_date', 'end_date', 'type', 'status']);
    }
}
