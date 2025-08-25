<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityRequest;

class ActivityRequestController extends Controller
{
    /**
     * Display staff's activity requests
     */
    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = ActivityRequest::with(['reviewer', 'approvedActivity'])
            ->byRequester(Auth::guard('staff')->id())
            ->recentFirst();

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(15);

        // Get statistics
        $stats = [
            'total' => ActivityRequest::byRequester(Auth::guard('staff')->id())->count(),
            'pending' => ActivityRequest::byRequester(Auth::guard('staff')->id())->pending()->count(),
            'approved' => ActivityRequest::byRequester(Auth::guard('staff')->id())->approved()->count(),
            'rejected' => ActivityRequest::byRequester(Auth::guard('staff')->id())->rejected()->count(),
        ];

        return view('staff.activity-requests.index', compact('requests', 'stats', 'status'));
    }

    /**
     * Show the form for creating a new activity request
     */
    public function create()
    {
        return view('staff.activity-requests.create');
    }

    /**
     * Store a newly created activity request
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:meeting,training,event,holiday,deadline',
            'justification' => 'nullable|string|max:1000',
            'expected_participants' => 'nullable|integer|min:1|max:1000',
            'estimated_budget' => 'nullable|numeric|min:0|max:999999.99',
        ], [
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
        ]);

        ActivityRequest::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'type' => $request->type,
            'justification' => $request->justification,
            'expected_participants' => $request->expected_participants,
            'estimated_budget' => $request->estimated_budget,
            'requested_by' => Auth::guard('staff')->id(),
        ]);

        return redirect()->route('staff.activity-requests.index')
            ->with('success', 'Activity request submitted successfully. It will be reviewed by an administrator.');
    }

    /**
     * Display the specified activity request
     */
    public function show(ActivityRequest $activityRequest)
    {
        // Ensure staff can only view their own requests
        if ($activityRequest->requested_by !== Auth::guard('staff')->id()) {
            abort(403, 'Unauthorized to view this activity request.');
        }

        $activityRequest->load(['reviewer', 'approvedActivity']);

        return view('staff.activity-requests.show', compact('activityRequest'));
    }

    /**
     * Show the form for editing the specified activity request
     */
    public function edit(ActivityRequest $activityRequest)
    {
        // Ensure staff can only edit their own pending requests
        if ($activityRequest->requested_by !== Auth::guard('staff')->id()) {
            abort(403, 'Unauthorized to edit this activity request.');
        }

        if ($activityRequest->status !== 'pending') {
            return redirect()->route('staff.activity-requests.show', $activityRequest)
                ->with('error', 'Only pending activity requests can be edited.');
        }

        return view('staff.activity-requests.edit', compact('activityRequest'));
    }

    /**
     * Update the specified activity request
     */
    public function update(Request $request, ActivityRequest $activityRequest)
    {
        // Ensure staff can only edit their own pending requests
        if ($activityRequest->requested_by !== Auth::guard('staff')->id()) {
            abort(403, 'Unauthorized to edit this activity request.');
        }

        if ($activityRequest->status !== 'pending') {
            return redirect()->route('staff.activity-requests.show', $activityRequest)
                ->with('error', 'Only pending activity requests can be edited.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:meeting,training,event,holiday,deadline',
            'justification' => 'nullable|string|max:1000',
            'expected_participants' => 'nullable|integer|min:1|max:1000',
            'estimated_budget' => 'nullable|numeric|min:0|max:999999.99',
        ], [
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
        ]);

        $activityRequest->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'type' => $request->type,
            'justification' => $request->justification,
            'expected_participants' => $request->expected_participants,
            'estimated_budget' => $request->estimated_budget,
        ]);

        return redirect()->route('staff.activity-requests.show', $activityRequest)
            ->with('success', 'Activity request updated successfully.');
    }

    /**
     * Remove the specified activity request
     */
    public function destroy(ActivityRequest $activityRequest)
    {
        // Ensure staff can only delete their own pending requests
        if ($activityRequest->requested_by !== Auth::guard('staff')->id()) {
            abort(403, 'Unauthorized to delete this activity request.');
        }

        if ($activityRequest->status !== 'pending') {
            return redirect()->route('staff.activity-requests.index')
                ->with('error', 'Only pending activity requests can be deleted.');
        }

        $activityRequest->delete();

        return redirect()->route('staff.activity-requests.index')
            ->with('success', 'Activity request deleted successfully.');
    }
}
