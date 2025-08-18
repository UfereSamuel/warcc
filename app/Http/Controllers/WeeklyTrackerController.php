<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\WeeklyTracker;
use App\Models\LeaveType;

class WeeklyTrackerController extends Controller
{
    /**
     * Display weekly tracker index
     */
    public function index()
    {
        $staff = Auth::guard('staff')->user();

        // Get current week dates
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        // Get current week tracker
        $currentTracker = WeeklyTracker::where('staff_id', $staff->id)
            ->whereDate('week_start_date', $currentWeekStart)
            ->first();

        // Get recent trackers (last 8 weeks)
        $recentTrackers = WeeklyTracker::where('staff_id', $staff->id)
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

    /**
     * Show the form for creating a new weekly tracker
     */
    public function create()
    {
        $staff = Auth::guard('staff')->user();

        // Get current week dates
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        // Check if tracker already exists for current week
        $existingTracker = WeeklyTracker::where('staff_id', $staff->id)
            ->whereDate('week_start_date', $currentWeekStart)
            ->first();

        if ($existingTracker) {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Weekly tracker for this week already exists. You can only create one tracker per week.');
        }

        // Get leave types for dropdown
        $leaveTypes = LeaveType::active()->get();

        return view('staff.tracker.create', compact(
            'currentWeekStart',
            'currentWeekEnd',
            'leaveTypes'
        ));
    }

    /**
     * Store a newly created weekly tracker
     */
    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        // Base validation rules
        $rules = [
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'status' => 'required|in:at_duty_station,on_mission,on_leave',
            'remarks' => 'nullable|string|max:1000',
        ];

        // Add conditional validation based on status
        if ($request->status === 'on_mission') {
            $rules = array_merge($rules, [
                'mission_title' => 'required|string|max:255',
                'mission_type' => 'required|in:regional,continental,incountry',
                'mission_start_date' => 'required|date|after_or_equal:today',
                'mission_end_date' => 'required|date|after_or_equal:mission_start_date',
                'mission_purpose' => 'required|string|max:1000',
                'mission_documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
            ]);
        } elseif ($request->status === 'on_leave') {
            $rules = array_merge($rules, [
                'leave_type_id' => 'required|exists:leave_types,id',
                'leave_start_date' => 'required|date|after_or_equal:today',
                'leave_end_date' => 'required|date|after_or_equal:leave_start_date',
                'leave_approval_document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
            ]);
        }

        $request->validate($rules);

        // Check if tracker already exists for this week
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
        ];

        // Handle mission-specific data
        if ($request->status === 'on_mission') {
            $trackerData = array_merge($trackerData, [
                'mission_title' => $request->mission_title,
                'mission_type' => $request->mission_type,
                'mission_start_date' => $request->mission_start_date,
                'mission_end_date' => $request->mission_end_date,
                'mission_purpose' => $request->mission_purpose,
            ]);

            // Handle mission document uploads
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
                $trackerData['mission_documents'] = json_encode($documents);
            }
        }

        // Handle leave-specific data
        if ($request->status === 'on_leave') {
            $trackerData = array_merge($trackerData, [
                'leave_type_id' => $request->leave_type_id,
                'leave_start_date' => $request->leave_start_date,
                'leave_end_date' => $request->leave_end_date,
            ]);

            // Handle leave approval document upload
            if ($request->hasFile('leave_approval_document')) {
                $file = $request->file('leave_approval_document');
                $path = $file->store('weekly-tracker/leave-documents', 'public');
                $trackerData['leave_approval_document'] = json_encode([
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        $tracker = WeeklyTracker::create($trackerData);

        return redirect()->route('staff.tracker.index')
            ->with('success', 'Weekly tracker created successfully.');
    }

    /**
     * Show the form for editing the weekly tracker
     */
    public function edit(WeeklyTracker $tracker)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this tracker
        if ($tracker->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to weekly tracker.');
        }

        // Can edit if it's draft OR if admin has approved edit request
        if ($tracker->submission_status === 'submitted' && $tracker->edit_request_status !== 'approved') {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Cannot edit submitted weekly tracker. Contact admin if changes are needed.');
        }

        // Get leave types for dropdown
        $leaveTypes = LeaveType::active()->get();

        return view('staff.tracker.edit', compact('tracker', 'leaveTypes'));
    }

    /**
     * Update the weekly tracker
     */
    public function update(Request $request, WeeklyTracker $tracker)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this tracker
        if ($tracker->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to weekly tracker.');
        }

        // Can edit if it's draft OR if admin has approved edit request
        if ($tracker->submission_status === 'submitted' && $tracker->edit_request_status !== 'approved') {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Cannot edit submitted weekly tracker. Contact admin if changes are needed.');
        }

        // Base validation rules
        $rules = [
            'status' => 'required|in:at_duty_station,on_mission,on_leave',
            'remarks' => 'nullable|string|max:1000',
        ];

        // Add conditional validation based on status
        if ($request->status === 'on_mission') {
            $rules = array_merge($rules, [
                'mission_title' => 'required|string|max:255',
                'mission_type' => 'required|in:regional,continental,incountry',
                'mission_start_date' => 'required|date|after_or_equal:today',
                'mission_end_date' => 'required|date|after_or_equal:mission_start_date',
                'mission_purpose' => 'required|string|max:1000',
                'mission_documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            ]);
        } elseif ($request->status === 'on_leave') {
            $rules = array_merge($rules, [
                'leave_type_id' => 'required|exists:leave_types,id',
                'leave_start_date' => 'required|date|after_or_equal:today',
                'leave_end_date' => 'required|date|after_or_equal:leave_start_date',
                'leave_approval_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            ]);
        }

        $request->validate($rules);

        $updateData = [
            'status' => $request->status,
            'remarks' => $request->remarks,
        ];

        // Handle mission-specific data
        if ($request->status === 'on_mission') {
            $updateData = array_merge($updateData, [
                'mission_title' => $request->mission_title,
                'mission_type' => $request->mission_type,
                'mission_start_date' => $request->mission_start_date,
                'mission_end_date' => $request->mission_end_date,
                'mission_purpose' => $request->mission_purpose,
            ]);

            // Handle new mission document uploads
            if ($request->hasFile('mission_documents')) {
                $existingDocuments = $tracker->mission_documents ? json_decode($tracker->mission_documents, true) : [];
                $newDocuments = [];

                foreach ($request->file('mission_documents') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('weekly-tracker/mission-documents', 'public');
                        $newDocuments[] = [
                            'original_name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ];
                    }
                }

                $allDocuments = array_merge($existingDocuments, $newDocuments);
                $updateData['mission_documents'] = json_encode($allDocuments);
            }
        } else {
            // Clear mission data if status changed
            $updateData = array_merge($updateData, [
                'mission_title' => null,
                'mission_type' => null,
                'mission_start_date' => null,
                'mission_end_date' => null,
                'mission_purpose' => null,
                'mission_documents' => null,
            ]);
        }

        // Handle leave-specific data
        if ($request->status === 'on_leave') {
            $updateData = array_merge($updateData, [
                'leave_type_id' => $request->leave_type_id,
                'leave_start_date' => $request->leave_start_date,
                'leave_end_date' => $request->leave_end_date,
            ]);

            // Handle new leave approval document upload
            if ($request->hasFile('leave_approval_document')) {
                $file = $request->file('leave_approval_document');
                $path = $file->store('weekly-tracker/leave-documents', 'public');
                $updateData['leave_approval_document'] = json_encode([
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        } else {
            // Clear leave data if status changed
            $updateData = array_merge($updateData, [
                'leave_type_id' => null,
                'leave_start_date' => null,
                'leave_end_date' => null,
                'leave_approval_document' => null,
            ]);
        }

        // If this was an approved edit request, reset the edit request status
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

    /**
     * Submit weekly tracker
     */
    public function submit(WeeklyTracker $tracker)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this tracker
        if ($tracker->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to weekly tracker.');
        }

        // Can't submit already submitted trackers
        if ($tracker->submission_status === 'submitted') {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Weekly tracker is already submitted.');
        }

        $tracker->update([
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('staff.tracker.index')
            ->with('success', 'Weekly tracker submitted successfully. Contact admin if you need to make changes.');
    }

    /**
     * Request edit approval from admin
     */
    public function requestEditApproval(WeeklyTracker $tracker)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this tracker
        if ($tracker->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to weekly tracker.');
        }

        // Can only request edit for submitted trackers
        if ($tracker->submission_status !== 'submitted') {
            return redirect()->route('staff.tracker.index')
                ->with('error', 'Can only request edit approval for submitted trackers.');
        }

        // Check if edit request already exists
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

    /**
     * Download document
     */
    public function downloadDocument(WeeklyTracker $tracker, $type, $index = 0)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this tracker
        if ($tracker->staff_id !== $staff->id) {
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
}
