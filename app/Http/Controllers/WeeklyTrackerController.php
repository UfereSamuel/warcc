<?php

namespace App\Http\Controllers;

use App\Models\ActivityCalendar;
use App\Models\LeaveType;
use App\Models\WeeklyTracker;
use App\Services\ActivityWorkflowService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyTrackerController extends Controller
{
    public function __construct(
        private readonly ActivityWorkflowService $workflow
    ) {}

    public function index()
    {
        $staff = Auth::guard('staff')->user();

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $currentTracker = WeeklyTracker::with('activity')
            ->where('staff_id', $staff->id)
            ->whereDate('week_start_date', $currentWeekStart)
            ->first();

        $recentTrackers = WeeklyTracker::with('activity')
            ->where('staff_id', $staff->id)
            ->orderBy('week_start_date', 'desc')
            ->take(8)
            ->get();

        return view('staff.tracker.index', compact(
            'currentTracker',
            'recentTrackers',
            'currentWeekStart',
            'currentWeekEnd'
        ));
    }

    public function create()
    {
        $staff = Auth::guard('staff')->user();

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $existingTracker = WeeklyTracker::where('staff_id', $staff->id)
            ->whereDate('week_start_date', $currentWeekStart)
            ->first();

        if ($existingTracker) {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Weekly tracker for this week already exists. You can only create one tracker per week.');
        }

        $leaveTypes = LeaveType::active()->get();
        $linkableActivities = $this->workflow->getLinkableActivitiesForStaff($staff, $currentWeekStart, $currentWeekEnd);

        return view('staff.tracker.create', compact(
            'currentWeekStart',
            'currentWeekEnd',
            'leaveTypes',
            'linkableActivities'
        ));
    }

    public function activityPrefill(ActivityCalendar $activity): JsonResponse
    {
        $staff = Auth::guard('staff')->user();

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $linkable = $this->workflow->getLinkableActivitiesForStaff($staff, $weekStart, $weekEnd);

        if (! $linkable->contains('id', $activity->id)) {
            return response()->json(['message' => 'Activity is not available for linking.'], 403);
        }

        return response()->json([
            'activity' => [
                'id' => $activity->id,
                'title' => $activity->title,
                'type' => $activity->type_label,
                'location' => $activity->location,
                'start_date' => $activity->start_date->format('Y-m-d'),
                'end_date' => $activity->end_date->format('Y-m-d'),
                'description' => $activity->description,
            ],
            'mission_fields' => $this->workflow->missionFieldsFromActivity($activity),
        ]);
    }

    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $rules = [
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'status' => 'required|in:at_duty_station,on_mission,on_leave',
            'remarks' => 'nullable|string|max:1000',
        ];

        if ($request->status === 'on_mission') {
            $rules = array_merge($rules, [
                'activity_calendar_id' => 'nullable|exists:activity_calendars,id',
                'mission_title' => 'required|string|max:255',
                'mission_type' => 'required|in:regional,continental,incountry',
                'mission_start_date' => 'required|date',
                'mission_end_date' => 'required|date|after_or_equal:mission_start_date',
                'mission_purpose' => 'required|string|max:1000',
                'mission_documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            ]);
        } elseif ($request->status === 'on_leave') {
            $rules = array_merge($rules, [
                'leave_type_id' => 'required|exists:leave_types,id',
                'leave_start_date' => 'required|date',
                'leave_end_date' => 'required|date|after_or_equal:leave_start_date',
                'leave_approval_document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            ]);
        }

        $request->validate($rules);

        $existingTracker = WeeklyTracker::where('staff_id', $staff->id)
            ->whereDate('week_start_date', $request->week_start_date)
            ->first();

        if ($existingTracker) {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Weekly tracker for this week already exists. You can only create one tracker per week.');
        }

        $trackerData = [
            'staff_id' => $staff->id,
            'week_start_date' => $request->week_start_date,
            'week_end_date' => $request->week_end_date,
            'status' => $request->status,
            'remarks' => $request->remarks,
            'submission_status' => 'draft',
            'activity_calendar_id' => null,
        ];

        if ($request->status === 'on_mission') {
            $trackerData = array_merge($trackerData, $this->missionPayload($request));
        } elseif ($request->status === 'on_leave') {
            $trackerData = array_merge($trackerData, $this->leavePayload($request));
        }

        WeeklyTracker::create($trackerData);

        return redirect()->route('staff.tracker.index')
            ->with('success', 'Weekly tracker created successfully.');
    }

    public function edit(WeeklyTracker $tracker)
    {
        $staff = Auth::guard('staff')->user();

        if ($tracker->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to weekly tracker.');
        }

        if ($tracker->submission_status === 'submitted' && $tracker->edit_request_status !== 'approved') {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Cannot edit submitted weekly tracker. Contact admin if changes are needed.');
        }

        $leaveTypes = LeaveType::active()->get();
        $tracker->load('activity');
        $linkableActivities = $this->workflow->getLinkableActivitiesForStaff(
            $staff,
            $tracker->week_start_date,
            $tracker->week_end_date
        );

        if ($tracker->activity_calendar_id && $tracker->activity && ! $linkableActivities->contains('id', $tracker->activity_calendar_id)) {
            $linkableActivities = $linkableActivities->push($tracker->activity);
        }

        return view('staff.tracker.edit', compact('tracker', 'leaveTypes', 'linkableActivities'));
    }

    public function update(Request $request, WeeklyTracker $tracker)
    {
        $staff = Auth::guard('staff')->user();

        if ($tracker->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to weekly tracker.');
        }

        if ($tracker->submission_status === 'submitted' && $tracker->edit_request_status !== 'approved') {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Cannot edit submitted weekly tracker. Contact admin if changes are needed.');
        }

        $rules = [
            'status' => 'required|in:at_duty_station,on_mission,on_leave',
            'remarks' => 'nullable|string|max:1000',
        ];

        if ($request->status === 'on_mission') {
            $rules = array_merge($rules, [
                'activity_calendar_id' => 'nullable|exists:activity_calendars,id',
                'mission_title' => 'required|string|max:255',
                'mission_type' => 'required|in:regional,continental,incountry',
                'mission_start_date' => 'required|date',
                'mission_end_date' => 'required|date|after_or_equal:mission_start_date',
                'mission_purpose' => 'required|string|max:1000',
                'mission_documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            ]);
        } elseif ($request->status === 'on_leave') {
            $rules = array_merge($rules, [
                'leave_type_id' => 'required|exists:leave_types,id',
                'leave_start_date' => 'required|date',
                'leave_end_date' => 'required|date|after_or_equal:leave_start_date',
                'leave_approval_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            ]);
        }

        $request->validate($rules);

        $updateData = [
            'status' => $request->status,
            'remarks' => $request->remarks,
            'activity_calendar_id' => null,
        ];

        if ($request->status === 'on_mission') {
            $updateData = array_merge($updateData, $this->missionPayload($request, $tracker));
        } else {
            $updateData = array_merge($updateData, [
                'mission_title' => null,
                'mission_type' => null,
                'mission_start_date' => null,
                'mission_end_date' => null,
                'mission_purpose' => null,
                'mission_documents' => null,
            ]);
        }

        if ($request->status === 'on_leave') {
            $updateData = array_merge($updateData, $this->leavePayload($request, $tracker));
        } else {
            $updateData = array_merge($updateData, [
                'leave_type_id' => null,
                'leave_start_date' => null,
                'leave_end_date' => null,
                'leave_approval_document' => null,
            ]);
        }

        if ($tracker->edit_request_status === 'approved') {
            $updateData['edit_request_status'] = 'none';
            $updateData['edit_requested_at'] = null;
            $updateData['edit_approved_at'] = null;
            $updateData['edit_approved_by'] = null;
            $updateData['edit_rejection_reason'] = null;
        }

        $tracker->update($updateData);

        return redirect()->route('staff.tracker.index')
            ->with('success', 'Weekly tracker updated successfully.');
    }

    public function submit(WeeklyTracker $tracker)
    {
        $staff = Auth::guard('staff')->user();

        if ($tracker->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to weekly tracker.');
        }

        if ($tracker->submission_status === 'submitted') {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Weekly tracker is already submitted.');
        }

        if ($message = $this->submissionReadinessError($tracker)) {
            return redirect()->route('staff.tracker.edit', $tracker)
                ->with('error', $message);
        }

        $tracker->update([
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('staff.tracker.index')
            ->with('success', 'Weekly tracker submitted successfully. Contact admin if you need to make changes.');
    }

    public function requestEditApproval(WeeklyTracker $tracker)
    {
        $staff = Auth::guard('staff')->user();

        if ($tracker->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to weekly tracker.');
        }

        if ($tracker->submission_status !== 'submitted') {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Can only request edit approval for submitted trackers.');
        }

        if ($tracker->edit_request_status === 'pending') {
            return redirect()->route('staff.tracker.index')
                ->with('info', 'Edit approval request is already pending.');
        }

        $tracker->update([
            'edit_request_status' => 'pending',
            'edit_requested_at' => now(),
        ]);

        return redirect()->route('staff.tracker.index')
            ->with('success', 'Edit approval request sent to admin. You will be notified once approved.');
    }

    public function downloadDocument(WeeklyTracker $tracker, $type, $index = 0, bool $allowAdmin = false)
    {
        $staff = Auth::guard('staff')->user();

        if ($tracker->staff_id !== $staff->id && ! ($allowAdmin && $staff?->is_admin)) {
            abort(403, 'Unauthorized access to document.');
        }

        if ($type === 'mission' && $tracker->mission_documents) {
            $documents = $tracker->mission_documents;

            if (isset($documents[$index])) {
                $document = $documents[$index];

                return response()->download(storage_path('app/public/' . $document['path']), $document['original_name']);
            }
        } elseif ($type === 'leave' && $tracker->leave_approval_document) {
            $document = $tracker->leave_approval_document;

            return response()->download(storage_path('app/public/' . $document['path']), $document['original_name']);
        }

        abort(404, 'Document not found.');
    }

    /**
     * @return array<string, mixed>
     */
    private function missionPayload(Request $request, ?WeeklyTracker $tracker = null): array
    {
        $data = [
            'activity_calendar_id' => $request->activity_calendar_id,
            'mission_title' => $request->mission_title,
            'mission_type' => $request->mission_type,
            'mission_start_date' => $request->mission_start_date,
            'mission_end_date' => $request->mission_end_date,
            'mission_purpose' => $request->mission_purpose,
        ];

        if ($request->hasFile('mission_documents')) {
            $documents = [];
            foreach ($request->file('mission_documents') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('weekly-tracker/mission-documents', 'public');
                    $documents[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ];
                }
            }

            if ($tracker && $tracker->mission_documents) {
                $existing = $tracker->mission_documents;
                $documents = array_merge($existing ?? [], $documents);
            }

            $data['mission_documents'] = $documents;
        } elseif ($tracker) {
            $data['mission_documents'] = $tracker->mission_documents;
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function leavePayload(Request $request, ?WeeklyTracker $tracker = null): array
    {
        $data = [
            'leave_type_id' => $request->leave_type_id,
            'leave_start_date' => $request->leave_start_date,
            'leave_end_date' => $request->leave_end_date,
        ];

        if ($request->hasFile('leave_approval_document')) {
            $file = $request->file('leave_approval_document');
            $path = $file->store('weekly-tracker/leave-documents', 'public');
            $data['leave_approval_document'] = [
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
        } elseif ($tracker) {
            $data['leave_approval_document'] = $tracker->leave_approval_document;
        }

        return $data;
    }

    private function submissionReadinessError(WeeklyTracker $tracker): ?string
    {
        if ($tracker->status === 'on_mission') {
            if (! $tracker->mission_title || ! $tracker->mission_type || ! $tracker->mission_purpose
                || ! $tracker->mission_start_date || ! $tracker->mission_end_date) {
                return 'Complete all mission details before submitting your weekly tracker.';
            }

            if (empty($tracker->mission_documents)) {
                return 'Upload at least one mission document before submitting your weekly tracker.';
            }
        }

        if ($tracker->status === 'on_leave') {
            if (! $tracker->leave_type_id || ! $tracker->leave_start_date || ! $tracker->leave_end_date) {
                return 'Complete all leave details before submitting your weekly tracker.';
            }

            if (empty($tracker->leave_approval_document)) {
                return 'Upload your approved leave form before submitting your weekly tracker.';
            }
        }

        return null;
    }
}
