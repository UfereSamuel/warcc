<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mission;

class MissionController extends Controller
{
    /**
     * Display missions index
     */
    public function index()
    {
        $staff = Auth::guard('staff')->user();

        $missions = Mission::where('staff_id', $staff->id)
            ->with(['approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = [
            'total' => Mission::where('staff_id', $staff->id)->count(),
            'pending' => Mission::where('staff_id', $staff->id)->where('status', 'pending')->count(),
            'approved' => Mission::where('staff_id', $staff->id)->where('status', 'approved')->count(),
            'rejected' => Mission::where('staff_id', $staff->id)->where('status', 'rejected')->count(),
            'active' => Mission::where('staff_id', $staff->id)->active()->count(),
        ];

        return view('staff.missions.index', compact('missions', 'stats'));
    }

    /**
     * Show the form for creating a new mission
     */
    public function create()
    {
        return view('staff.missions.create');
    }

    /**
     * Store a newly created mission
     */
    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'purpose' => 'required|string|max:1000',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $mission = Mission::create([
            'staff_id' => $staff->id,
            'title' => $request->title,
            'purpose' => $request->purpose,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'remarks' => $request->remarks,
            'status' => 'pending',
        ]);

        return redirect()->route('staff.missions.index')
            ->with('success', 'Mission request submitted successfully. Awaiting approval.');
    }

    /**
     * Display the specified mission
     */
    public function show(Mission $mission)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this mission
        if ($mission->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to mission.');
        }

        return view('staff.missions.show', compact('mission'));
    }

    /**
     * Show the form for editing the mission
     */
    public function edit(Mission $mission)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this mission
        if ($mission->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to mission.');
        }

        // Can't edit approved or rejected missions
        if (in_array($mission->status, ['approved', 'rejected'])) {
            return redirect()->route('staff.missions.show', $mission)
                ->with('error', 'Cannot edit ' . $mission->status . ' missions.');
        }

        return view('staff.missions.edit', compact('mission'));
    }

    /**
     * Update the mission
     */
    public function update(Request $request, Mission $mission)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this mission
        if ($mission->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to mission.');
        }

        // Can't edit approved or rejected missions
        if (in_array($mission->status, ['approved', 'rejected'])) {
            return redirect()->route('staff.missions.show', $mission)
                ->with('error', 'Cannot edit ' . $mission->status . ' missions.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'purpose' => 'required|string|max:1000',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $mission->update([
            'title' => $request->title,
            'purpose' => $request->purpose,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('staff.missions.show', $mission)
            ->with('success', 'Mission updated successfully.');
    }

    /**
     * Remove the mission
     */
    public function destroy(Mission $mission)
    {
        $staff = Auth::guard('staff')->user();

        // Check if user owns this mission
        if ($mission->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access to mission.');
        }

        // Can't delete approved missions
        if ($mission->status === 'approved') {
            return redirect()->route('staff.missions.index')
                ->with('error', 'Cannot delete approved missions.');
        }

        $mission->delete();

        return redirect()->route('staff.missions.index')
            ->with('success', 'Mission deleted successfully.');
    }
}
