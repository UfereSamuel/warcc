<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;
use App\Models\LeaveType;

class LeaveController extends Controller
{
    /**
     * Display leave requests index
     */
    public function index()
    {
        $staff = Auth::guard('staff')->user();

        $leaveRequests = LeaveRequest::where('staff_id', $staff->id)
            ->with(['leaveType', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = [
            'total' => LeaveRequest::where('staff_id', $staff->id)->count(),
            'pending' => LeaveRequest::where('staff_id', $staff->id)->where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('staff_id', $staff->id)->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('staff_id', $staff->id)->where('status', 'rejected')->count(),
            'active' => LeaveRequest::where('staff_id', $staff->id)->active()->count(),
        ];

        return view('staff.leaves.index', compact('leaveRequests', 'stats'));
    }

    /**
     * Show the form for creating a new leave request
     */
    public function create()
    {
        $leaveTypes = LeaveType::active()->get();
        return view('staff.leaves.create', compact('leaveTypes'));
    }

    /**
     * Store a newly created leave request
     */
    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // Check for overlapping leave requests
        $overlapping = LeaveRequest::where('staff_id', $staff->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                          ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['start_date' => 'You already have approved leave during this period.']);
        }

        $leaveRequest = LeaveRequest::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('staff.leaves.index')
            ->with('success', 'Leave request submitted successfully. Awaiting approval.');
    }

    /**
     * Display the specified leave request
     */
    public function show(LeaveRequest $leave)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this leave request
        if ($leave->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to leave request.');
        }

        return view('staff.leaves.show', compact('leave'));
    }

    /**
     * Show the form for editing the leave request
     */
    public function edit(LeaveRequest $leave)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this leave request
        if ($leave->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to leave request.');
        }

        // Can't edit approved or rejected leave requests
        if (in_array($leave->status, ['approved', 'rejected'])) {
            return redirect()->route('staff.leaves.show', $leave)
                ->with('error', 'Cannot edit ' . $leave->status . ' leave requests.');
        }

        $leaveTypes = LeaveType::active()->get();
        return view('staff.leaves.edit', compact('leave', 'leaveTypes'));
    }

    /**
     * Update the leave request
     */
    public function update(Request $request, LeaveRequest $leave)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this leave request
        if ($leave->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to leave request.');
        }

        // Can't edit approved or rejected leave requests
        if (in_array($leave->status, ['approved', 'rejected'])) {
            return redirect()->route('staff.leaves.show', $leave)
                ->with('error', 'Cannot edit ' . $leave->status . ' leave requests.');
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // Check for overlapping leave requests (excluding current one)
        $overlapping = LeaveRequest::where('staff_id', $staff->id)
            ->where('id', '!=', $leave->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                          ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['start_date' => 'You already have approved leave during this period.']);
        }

        $leave->update([
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
        ]);

        return redirect()->route('staff.leaves.show', $leave)
            ->with('success', 'Leave request updated successfully.');
    }

    /**
     * Remove the leave request
     */
    public function destroy(LeaveRequest $leave)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this leave request
        if ($leave->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to leave request.');
        }

        // Can't delete approved leave requests
        if ($leave->status === 'approved') {
            return redirect()->route('staff.leaves.index')
                ->with('error', 'Cannot delete approved leave requests.');
        }

        $leave->delete();

        return redirect()->route('staff.leaves.index')
            ->with('success', 'Leave request deleted successfully.');
    }

    /**
     * Show leave balance summary
     */
    public function balanceSummary()
    {
        $staff = Auth::guard('staff')->user();

        $leaveTypes = LeaveType::active()->get();
        $currentYear = now()->year;

        $balances = [];
        foreach ($leaveTypes as $leaveType) {
            $usedDays = LeaveRequest::where('staff_id', $staff->id)
                ->where('leave_type_id', $leaveType->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $currentYear)
                ->sum('total_days');

            $balances[] = [
                'leave_type' => $leaveType,
                'max_days' => $leaveType->max_days,
                'used_days' => $usedDays,
                'remaining_days' => $leaveType->max_days ? ($leaveType->max_days - $usedDays) : null,
            ];
        }

        return view('staff.leaves.balance', compact('balances', 'currentYear'));
    }
}
