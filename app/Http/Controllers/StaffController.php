<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\WeeklyTracker;
use App\Models\Mission;
use App\Models\LeaveRequest;
use App\Models\Document;
use App\Models\ActivityCalendar;
use App\Models\ActivityRequest;

class StaffController extends Controller
{
    /**
     * Show the staff dashboard
     */
    public function dashboard()
    {
        $staff = Auth::guard('staff')->user();
        $staff->load('position');

        // Get today's attendance
        $todayAttendance = $staff->getTodayAttendance();

        // Get current week tracker
        $currentWeekTracker = $staff->getCurrentWeekTracker();

        // Get pending items
        $pendingMissions = $staff->missions()->pending()->count();
        $pendingLeaves = $staff->leaveRequests()->pending()->count();

        // Get activity request statistics
        $activityRequestStats = [
            'total' => ActivityRequest::byRequester($staff->id)->count(),
            'pending' => ActivityRequest::byRequester($staff->id)->pending()->count(),
            'approved' => ActivityRequest::byRequester($staff->id)->approved()->count(),
            'rejected' => ActivityRequest::byRequester($staff->id)->rejected()->count(),
        ];

        // Get recent activities
        $recentActivities = ActivityCalendar::active()
            ->orWhere('status', 'ongoing')
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();

        // Get attendance summary for current month
        $attendanceSummary = [
            'present_days' => $staff->attendances()
                ->thisMonth()
                ->where('status', 'present')
                ->count(),
            'total_days' => now()->day, // Days passed in current month
            'total_hours' => $staff->attendances()
                ->thisMonth()
                ->whereNotNull('total_hours')
                ->sum('total_hours'),
        ];

        // Get leave balance
        $leaveBalance = $staff->annual_leave_balance;
        $usedLeave = $staff->leaveRequests()
            ->approved()
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        return view('staff.dashboard', compact(
            'staff',
            'todayAttendance',
            'currentWeekTracker',
            'pendingMissions',
            'pendingLeaves',
            'activityRequestStats',
            'recentActivities',
            'attendanceSummary',
            'leaveBalance',
            'usedLeave'
        ));
    }

    /**
     * Show staff profile
     */
    public function profile()
    {
        $staff = Auth::guard('staff')->user();
        $staff->load('position');
        return view('staff.profile', compact('staff'));
    }

    /**
     * Update staff profile
     */
    public function updateProfile(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $staff->load('position');

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $staff->first_name = $request->first_name;
        $staff->last_name = $request->last_name;
        $staff->phone = $request->phone;

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($staff->profile_picture) {
                Storage::disk('public')->delete('images/uploads/' . $staff->profile_picture);
            }

            $image = $request->file('profile_picture');
            $filename = 'profile_' . $staff->id . '_' . time() . '.' . $image->getClientOriginalExtension();

            $image->storeAs('images/uploads', $filename, 'public');
            $staff->profile_picture = $filename;
        }

        $staff->save();

        return redirect()->route('profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Download document
     */
    public function downloadDocument(Document $document)
    {
        $staff = Auth::guard('staff')->user();

        // Check if staff has permission to download this document
        if (!$this->canAccessDocument($staff, $document)) {
            abort(403, 'You do not have permission to access this document.');
        }

        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $document->original_filename);
    }

    /**
     * Check if staff can access a document
     */
    private function canAccessDocument(Staff $staff, Document $document): bool
    {
        // Staff can access their own documents
        if ($document->uploaded_by === $staff->id) {
            return true;
        }

        // Staff can access documents related to their missions
        if ($document->documentable_type === 'App\Models\Mission') {
            $mission = $document->documentable;
            if ($mission && $mission->staff_id === $staff->id) {
                return true;
            }
        }

        // Staff can access documents related to their leave requests
        if ($document->documentable_type === 'App\Models\LeaveRequest') {
            $leaveRequest = $document->documentable;
            if ($leaveRequest && $leaveRequest->staff_id === $staff->id) {
                return true;
            }
        }

        // Admins can access all documents
        if ($staff->is_admin) {
            return true;
        }

        return false;
    }

    /**
     * Show profile completion form for new SSO users
     */
    public function showProfileCompletion()
    {
        $staff = Auth::guard('staff')->user();
        $staff->load('position');
        
        // If profile is already complete, redirect to dashboard
        if (!$this->requiresProfileCompletion($staff)) {
            if ($staff->is_admin) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('staff.dashboard');
            }
        }

        $positions = \App\Models\Position::active()->orderBy('title')->get();
        return view('staff.profile-complete', compact('staff', 'positions'));
    }

    /**
     * Handle profile completion form submission
     */
    public function completeProfile(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $request->validate([
            'position_id' => 'required|exists:positions,id',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update staff profile
        $staff->position_id = $request->position_id;
        $staff->gender = $request->gender;
        $staff->phone = $request->phone;

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $filename = 'profile_' . $staff->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('images/uploads', $filename, 'public');
            $staff->profile_picture = $filename;
        }

        $staff->save();

        // Log profile completion
        \Log::info('Staff profile completed after SSO registration', [
            'staff_id' => $staff->staff_id,
            'email' => $staff->email,
        ]);

        // Redirect to appropriate dashboard
        if ($staff->is_admin) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome! Your profile has been completed successfully.');
        } else {
            return redirect()->route('staff.dashboard')
                ->with('success', 'Welcome! Your profile has been completed successfully.');
        }
    }

    /**
     * Check if staff profile requires completion (same logic as AuthController)
     */
    private function requiresProfileCompletion(Staff $staff)
    {
        $requiredFields = [
            'position_id' => [null, ''],
            'phone' => [null, ''],
            'gender' => [null, ''],
        ];

        foreach ($requiredFields as $field => $invalidValues) {
            if (in_array($staff->$field, $invalidValues)) {
                return true;
            }
        }

        return false;
    }
}
