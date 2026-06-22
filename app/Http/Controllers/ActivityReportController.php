<?php

namespace App\Http\Controllers;

use App\Models\ActivityCalendar;
use App\Models\ActivityReport;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use App\Services\ActivityWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

        $query = ActivityReport::with(['activity', 'weeklyTracker'])
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
        $pendingMissionTrackers = $this->workflow->getUnreportedMissionTrackers($staff);

        return view('staff.activity-reports.index', compact(
            'reports',
            'stats',
            'status',
            'pendingActivities',
            'pendingMissionTrackers'
        ));
    }

    public function create(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $selectedActivity = null;
        $selectedTracker = null;

        if ($request->filled('activity_calendar_id')) {
            $selectedActivity = ActivityCalendar::find($request->activity_calendar_id);
        }

        if ($request->filled('weekly_tracker_id')) {
            $selectedTracker = WeeklyTracker::with('activity')
                ->where('staff_id', $staff->id)
                ->find($request->weekly_tracker_id);

            if ($selectedTracker && $this->workflow->trackerIsReportable($selectedTracker)) {
                $existingDraft = $selectedTracker->getMissionReport();
                if ($existingDraft && $existingDraft->status === 'draft') {
                    return redirect()->route('staff.activity-reports.edit', $existingDraft)
                        ->with('info', 'Continue your draft mission report for this tracker.');
                }
            } else {
                $selectedTracker = null;
            }
        }

        $pendingActivities = $this->workflow->getPendingReportsForStaff($staff);
        $selectableMissionTrackers = $this->workflow->getSelectableMissionTrackersForReport($staff);
        $calendarActivities = $this->reportableCalendarActivities($staff, $selectedActivity, $selectedTracker);

        return view('staff.activity-reports.create', compact(
            'calendarActivities',
            'selectedActivity',
            'selectedTracker',
            'selectableMissionTrackers',
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

        $activityReport->load(['activity', 'weeklyTracker', 'reviewer']);

        return view('staff.activity-reports.show', compact('activityReport'));
    }

    public function edit(ActivityReport $activityReport)
    {
        $this->authorizeStaff($activityReport);

        if (!$activityReport->isEditableByStaff()) {
            return redirect()->route('staff.activity-reports.show', $activityReport)
                ->with('error', 'Only draft reports can be edited.');
        }

        $activityReport->load(['activity', 'weeklyTracker']);

        $staff = Auth::guard('staff')->user();
        $calendarActivities = $this->reportableCalendarActivities(
            $staff,
            $activityReport->activity,
            $activityReport->weeklyTracker
        );
        $selectableMissionTrackers = $this->workflow->getSelectableMissionTrackersForReport($staff);

        if ($activityReport->weekly_tracker_id) {
            $selectableMissionTrackers = $selectableMissionTrackers
                ->push($activityReport->weeklyTracker)
                ->filter()
                ->unique('id')
                ->values();
        }

        return view('staff.activity-reports.edit', compact(
            'activityReport',
            'calendarActivities',
            'selectableMissionTrackers'
        ));
    }

    public function update(Request $request, ActivityReport $activityReport)
    {
        $this->authorizeStaff($activityReport);

        if (!$activityReport->isEditableByStaff()) {
            return redirect()->route('staff.activity-reports.show', $activityReport)
                ->with('error', 'Only draft reports can be edited.');
        }

        $data = $this->validateReport($request, $activityReport);
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

    private function validateReport(Request $request, ?ActivityReport $existingReport = null): array
    {
        $staff = Auth::guard('staff')->user();

        $validated = $request->validate([
            'weekly_tracker_id' => 'nullable|exists:weekly_trackers,id',
            'activity_calendar_id' => 'nullable|exists:activity_calendars,id',
            'title' => 'required|string|max:255',
            'report_date' => 'required|date',
            'summary' => 'required|string|max:10000',
            'outcomes' => 'nullable|string|max:5000',
            'challenges' => 'nullable|string|max:5000',
            'recommendations' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $weeklyTrackerId = $validated['weekly_tracker_id'] ?? null;
        $activityCalendarId = $validated['activity_calendar_id'] ?? null;

        if ($weeklyTrackerId) {
            $tracker = WeeklyTracker::findOrFail($weeklyTrackerId);

            if (! $this->workflow->trackerBelongsToStaff($tracker, $staff)) {
                throw ValidationException::withMessages([
                    'weekly_tracker_id' => 'You can only report on your own missions.',
                ]);
            }

            if (! $this->workflow->trackerIsReportable($tracker)
                && (! $existingReport || (int) $existingReport->weekly_tracker_id !== (int) $tracker->id)) {
                throw ValidationException::withMessages([
                    'weekly_tracker_id' => 'This mission already has a submitted report or is not eligible.',
                ]);
            }

            $duplicate = ActivityReport::query()
                ->where('weekly_tracker_id', $tracker->id)
                ->whereIn('status', ['submitted', 'reviewed'])
                ->when($existingReport, fn ($query) => $query->where('id', '!=', $existingReport->id))
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'weekly_tracker_id' => 'A report has already been filed for this mission.',
                ]);
            }

            if (! $activityCalendarId && $tracker->activity_calendar_id) {
                $activityCalendarId = $tracker->activity_calendar_id;
            }
        }

        return [
            'weekly_tracker_id' => $weeklyTrackerId,
            'activity_calendar_id' => $activityCalendarId,
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
    private function reportableCalendarActivities(
        Staff $staff,
        ?ActivityCalendar $includeActivity = null,
        ?WeeklyTracker $includeTracker = null
    ) {
        $ids = $this->workflow->getParticipatedActivityIds($staff)
            ->merge($this->workflow->getPendingReportsForStaff($staff)->pluck('id'))
            ->unique()
            ->values();

        if ($includeActivity) {
            $ids = $ids->push($includeActivity->id)->unique()->values();
        }

        if ($includeTracker?->activity_calendar_id) {
            $ids = $ids->push($includeTracker->activity_calendar_id)->unique()->values();
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
