<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\HeroSlide;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Mission;
use App\Models\WeeklyTracker;
use App\Models\ActivityCalendar;
use App\Models\PublicEvent;
use App\Models\ActivityRequest;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        // Get activity request statistics
        $activityRequestStats = [
            'total' => ActivityRequest::count(),
            'pending' => ActivityRequest::pending()->count(),
            'approved' => ActivityRequest::approved()->count(),
            'rejected' => ActivityRequest::rejected()->count(),
        ];

        // Get recent activity requests
        $recentActivityRequests = ActivityRequest::with(['requester'])
            ->pending()
            ->recentFirst()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('activityRequestStats', 'recentActivityRequests'));
    }

    /**
     * Show hero slides management
     */
    public function heroSlidesIndex()
    {
        $heroSlides = HeroSlide::with(['creator', 'updater'])
            ->orderBy('order_index', 'asc')
            ->paginate(10);

        return view('admin.content.hero-slides.index', compact('heroSlides'));
    }

    /**
     * Show create hero slide form
     */
    public function heroSlidesCreate()
    {
        return view('admin.content.hero-slides.create');
    }

    /**
     * Store new hero slide
     */
    public function heroSlidesStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|url|max:255',
            'order_index' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'hero_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/hero-slides'), $filename);

            HeroSlide::create([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'image_path' => $filename,
                'button_text' => $request->button_text,
                'button_link' => $request->button_link,
                'order_index' => $request->order_index,
                'status' => $request->status,
                'created_by' => Auth::guard('staff')->id(),
            ]);

            return redirect()->route('admin.content.hero-slides.index')
                ->with('success', 'Hero slide created successfully.');
        }

        return back()->withErrors(['image' => 'Image upload failed.']);
    }

    /**
     * Show edit hero slide form
     */
    public function heroSlidesEdit(HeroSlide $heroSlide)
    {
        return view('admin.content.hero-slides.edit', compact('heroSlide'));
    }

    /**
     * Update hero slide
     */
    public function heroSlidesUpdate(Request $request, HeroSlide $heroSlide)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|url|max:255',
            'order_index' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'order_index' => $request->order_index,
            'status' => $request->status,
            'updated_by' => Auth::guard('staff')->id(),
        ];

        // Handle image upload if new image provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($heroSlide->image_path && file_exists(public_path('images/hero-slides/' . $heroSlide->image_path))) {
                unlink(public_path('images/hero-slides/' . $heroSlide->image_path));
            }

            $image = $request->file('image');
            $filename = 'hero_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/hero-slides'), $filename);
            $data['image_path'] = $filename;
        }

        $heroSlide->update($data);

        return redirect()->route('admin.content.hero-slides.index')
            ->with('success', 'Hero slide updated successfully.');
    }

    /**
     * Delete hero slide
     */
    public function heroSlidesDestroy(HeroSlide $heroSlide)
    {
        // Delete image file
        if ($heroSlide->image_path && file_exists(public_path('images/hero-slides/' . $heroSlide->image_path))) {
            unlink(public_path('images/hero-slides/' . $heroSlide->image_path));
        }

        $heroSlide->delete();

        return redirect()->route('admin.content.hero-slides.index')
            ->with('success', 'Hero slide deleted successfully.');
    }

    /**
     * Toggle hero slide status
     */
    public function heroSlidesToggleStatus(HeroSlide $heroSlide)
    {
        $heroSlide->update([
            'status' => $heroSlide->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::guard('staff')->id(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $heroSlide->status,
            'message' => 'Status updated successfully.'
        ]);
    }

    /**
     * Reorder hero slides
     */
    public function heroSlidesReorder(Request $request)
    {
        $request->validate([
            'slides' => 'required|array',
            'slides.*.id' => 'required|exists:hero_slides,id',
            'slides.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->slides as $slide) {
            HeroSlide::where('id', $slide['id'])->update([
                'order_index' => $slide['order'],
                'updated_by' => Auth::guard('staff')->id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Slides reordered successfully.'
        ]);
    }

    // ==================== STAFF MANAGEMENT ====================

    /**
     * Display staff listing with search and filters
     */
    public function staffIndex(Request $request)
    {
        $query = Staff::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'staff') {
                $query->where('is_admin', false);
            }
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $staff = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $departments = Staff::distinct()->pluck('department')->filter()->sort();
        $statuses = ['active', 'inactive'];
        $roles = ['admin', 'staff'];
        $genders = ['male', 'female', 'other'];

        return view('admin.staff.index', compact('staff', 'departments', 'statuses', 'roles', 'genders'));
    }

    /**
     * Show create staff form
     */
    public function staffCreate()
    {
        $departments = Staff::distinct()->pluck('department')->filter()->sort();
        return view('admin.staff.create', compact('departments'));
    }

    /**
     * Store new staff member
     */
    public function staffStore(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string|unique:staff,staff_id|max:20',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:staff,email|max:255',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:150',
            'department' => 'required|string|max:100',
            'hire_date' => 'required|date|before_or_equal:today',
            'annual_leave_balance' => 'required|integer|min:0|max:50',
            'status' => 'required|in:active,inactive',
            'is_admin' => 'boolean',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['profile_picture']);
        $data['is_admin'] = $request->boolean('is_admin');

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $filename = 'profile_' . $request->staff_id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/profiles'), $filename);
            $data['profile_picture'] = $filename;
        }

        Staff::create($data);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    /**
     * Show individual staff details
     */
    public function staffShow(Staff $staff)
    {
        // Get recent attendance (last 30 days)
        $recentAttendance = $staff->attendances()
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Get pending leave requests
        $pendingLeaves = $staff->leaveRequests()
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get active missions
        $activeMissions = $staff->missions()
            ->where('status', 'approved')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->get();

        // Calculate attendance statistics
        $attendanceStats = [
            'total_days' => $staff->attendances()->count(),
            'present_days' => $staff->attendances()->where('status', 'present')->count(),
            'late_days' => $staff->attendances()->where('status', 'late')->count(),
            'average_hours' => $staff->attendances()->avg('total_hours') ?? 0,
        ];

        return view('admin.staff.show', compact('staff', 'recentAttendance', 'pendingLeaves', 'activeMissions', 'attendanceStats'));
    }

    /**
     * Show edit staff form
     */
    public function staffEdit(Staff $staff)
    {
        $departments = Staff::distinct()->pluck('department')->filter()->sort();
        return view('admin.staff.edit', compact('staff', 'departments'));
    }

    /**
     * Update staff information
     */
    public function staffUpdate(Request $request, Staff $staff)
    {
        $request->validate([
            'staff_id' => ['required', 'string', 'max:20', Rule::unique('staff')->ignore($staff->id)],
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('staff')->ignore($staff->id)],
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:150',
            'department' => 'required|string|max:100',
            'hire_date' => 'required|date|before_or_equal:today',
            'annual_leave_balance' => 'required|integer|min:0|max:50',
            'status' => 'required|in:active,inactive',
            'is_admin' => 'boolean',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['profile_picture']);
        $data['is_admin'] = $request->boolean('is_admin');

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture
            if ($staff->profile_picture && file_exists(public_path('images/profiles/' . $staff->profile_picture))) {
                unlink(public_path('images/profiles/' . $staff->profile_picture));
            }

            $image = $request->file('profile_picture');
            $filename = 'profile_' . $request->staff_id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/profiles'), $filename);
            $data['profile_picture'] = $filename;
        }

        $staff->update($data);

        return redirect()->route('admin.staff.show', $staff)
            ->with('success', 'Staff information updated successfully.');
    }

    /**
     * Delete staff member
     */
    public function staffDestroy(Staff $staff)
    {
        // Check if staff has related records
        $hasAttendance = $staff->attendances()->exists();
        $hasLeaveRequests = $staff->leaveRequests()->exists();
        $hasMissions = $staff->missions()->exists();

        if ($hasAttendance || $hasLeaveRequests || $hasMissions) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Cannot delete staff member with existing attendance, leave, or mission records. Consider deactivating instead.');
        }

        // Delete profile picture
        if ($staff->profile_picture && file_exists(public_path('images/profiles/' . $staff->profile_picture))) {
            unlink(public_path('images/profiles/' . $staff->profile_picture));
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Promote staff to admin
     */
    public function promoteStaff(Staff $staff)
    {
        $staff->update(['is_admin' => true]);

        return redirect()->route('admin.staff.show', $staff)
            ->with('success', 'Staff member promoted to administrator.');
    }

    /**
     * Remove admin privileges
     */
    public function demoteStaff(Staff $staff)
    {
        // Prevent demoting the last admin
        $adminCount = Staff::where('is_admin', true)->count();
        if ($adminCount <= 1) {
            return redirect()->route('admin.staff.show', $staff)
                ->with('error', 'Cannot demote the last administrator.');
        }

        $staff->update(['is_admin' => false]);

        return redirect()->route('admin.staff.show', $staff)
            ->with('success', 'Administrator privileges removed.');
    }

    /**
     * Update staff leave balance
     */
    public function updateLeaveBalance(Request $request, Staff $staff)
    {
        $request->validate([
            'annual_leave_balance' => 'required|integer|min:0|max:50',
        ]);

        $staff->update(['annual_leave_balance' => $request->annual_leave_balance]);

        return redirect()->route('admin.staff.show', $staff)
            ->with('success', 'Leave balance updated successfully.');
    }

    /**
     * Show attendance management dashboard
     */
    public function attendanceIndex(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $department = $request->get('department');

        // Get today's attendance with staff details
        $query = Attendance::with('staff')
            ->whereDate('date', $date)
            ->orderBy('clock_in_time', 'asc');

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $attendances = $query->get();

        // Get staff who haven't checked in today
        $checkedInStaffIds = $attendances->pluck('staff_id')->toArray();
        $absentStaffQuery = Staff::where('status', 'active')
            ->whereNotIn('id', $checkedInStaffIds);

        if ($department) {
            $absentStaffQuery->where('department', $department);
        }

        $absentStaff = $absentStaffQuery->get();

        // Get departments for filter
        $departments = Staff::distinct()->pluck('department')->filter()->sort();

        // Calculate statistics
        $stats = [
            'total_staff' => Staff::where('status', 'active')->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $absentStaff->count(),
            'average_clock_in' => $attendances->whereNotNull('clock_in_time')->avg('clock_in_time')
        ];

        return view('admin.attendance.index', compact(
            'attendances', 'absentStaff', 'departments', 'stats', 'date', 'department'
        ));
    }

    /**
     * Show daily attendance report
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $department = $request->get('department');

        // Get attendance for the selected date
        $query = Attendance::with('staff')
            ->whereDate('date', $date);

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $attendances = $query->get();

        // Department summary
        $departmentSummary = Staff::select('department')
            ->selectRaw('COUNT(*) as total_staff')
            ->when($department, function($q) use ($department) {
                $q->where('department', $department);
            })
            ->where('status', 'active')
            ->groupBy('department')
            ->get()
            ->map(function($dept) use ($attendances) {
                $deptAttendances = $attendances->where('staff.department', $dept->department);
                return [
                    'department' => $dept->department,
                    'total_staff' => $dept->total_staff,
                    'present' => $deptAttendances->where('status', 'present')->count(),
                    'late' => $deptAttendances->where('status', 'late')->count(),
                    'absent' => $dept->total_staff - $deptAttendances->count(),
                    'attendance_rate' => $dept->total_staff > 0 ? round(($deptAttendances->count() / $dept->total_staff) * 100, 1) : 0
                ];
            });

        $departments = Staff::distinct()->pluck('department')->filter()->sort();

        return view('admin.attendance.daily-report', compact(
            'attendances', 'departmentSummary', 'departments', 'date', 'department'
        ));
    }

    /**
     * Export attendance data
     */
    public function exportAttendance(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $department = $request->get('department');

        $query = Attendance::with('staff')
            ->whereDate('date', $date);

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $attendances = $query->get();

        $filename = 'attendance-report-' . $date . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Staff ID', 'Full Name', 'Department', 'Date', 'Clock In', 'Clock Out', 'Total Hours', 'Status', 'Location']);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->staff->staff_id,
                    $attendance->staff->full_name,
                    $attendance->staff->department,
                    $attendance->date->format('Y-m-d'),
                    $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i:s') : '',
                    $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i:s') : '',
                    $attendance->total_hours ?? '',
                    $attendance->status,
                    $attendance->clock_in_address ?? ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show weekly tracker management
     */
    public function weeklyTrackersIndex(Request $request)
    {
        $week = $request->get('week', now()->startOfWeek()->format('Y-m-d'));
        $department = $request->get('department');
        $status = $request->get('status');

        $weekStart = \Carbon\Carbon::parse($week)->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $query = WeeklyTracker::with('staff', 'leaveType')
            ->whereDate('week_start_date', $weekStart);

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $trackers = $query->orderBy('created_at', 'desc')->get();

        // Get staff who haven't submitted trackers for this week
        $submittedStaffIds = $trackers->pluck('staff_id')->toArray();
        $missingStaffQuery = Staff::where('status', 'active')
            ->whereNotIn('id', $submittedStaffIds);

        if ($department) {
            $missingStaffQuery->where('department', $department);
        }

        $missingStaff = $missingStaffQuery->get();

        // Statistics
        $stats = [
            'at_duty_station' => $trackers->where('status', 'at_duty_station')->count(),
            'on_mission' => $trackers->where('status', 'on_mission')->count(),
            'on_leave' => $trackers->where('status', 'on_leave')->count(),
            'pending_review' => $trackers->where('submission_status', 'submitted')->count(),
            'not_submitted' => $missingStaff->count()
        ];

        $departments = Staff::distinct()->pluck('department')->filter()->sort();
        $statuses = ['at_duty_station', 'on_mission', 'on_leave'];

        return view('admin.weekly-trackers.index', compact(
            'trackers', 'missingStaff', 'stats', 'departments', 'statuses',
            'week', 'weekStart', 'weekEnd', 'department', 'status'
        ));
    }

    /**
     * Show individual weekly tracker details
     */
    public function weeklyTrackerShow(WeeklyTracker $tracker)
    {
        $tracker->load('staff', 'leaveType');

        return view('admin.weekly-trackers.show', compact('tracker'));
    }

    /**
     * Update weekly tracker status
     */
    public function weeklyTrackerUpdateStatus(Request $request, WeeklyTracker $tracker)
    {
        $request->validate([
            'submission_status' => 'required|in:submitted,reviewed,approved,rejected',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $tracker->update([
            'submission_status' => $request->submission_status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::guard('staff')->id(),
            'reviewed_at' => now()
        ]);

        $statusText = ucfirst($request->submission_status);

        return redirect()->back()->with('success', "Weekly tracker has been marked as {$statusText}.");
    }

    /**
     * Approve edit request for weekly tracker
     */
    public function weeklyTrackerApproveEdit(WeeklyTracker $tracker)
    {
        $tracker->update([
            'edit_request_status' => 'approved',
            'edit_approved_by' => Auth::guard('staff')->id(),
            'edit_approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Edit request approved. Staff can now modify the tracker.');
    }

    /**
     * Reject edit request for weekly tracker
     */
    public function weeklyTrackerRejectEdit(Request $request, WeeklyTracker $tracker)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $tracker->update([
            'edit_request_status' => 'rejected',
            'edit_rejection_reason' => $request->rejection_reason,
            'edit_approved_by' => Auth::guard('staff')->id(),
            'edit_approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Edit request rejected.');
    }

    // ==================== MISSION MANAGEMENT ====================

    /**
     * Show missions management dashboard
     */
    public function missionsIndex(Request $request)
    {
        $status = $request->get('status');
        $department = $request->get('department');

        $query = Mission::with('staff');

        if ($status) {
            $query->where('status', $status);
        }

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $missions = $query->orderBy('created_at', 'desc')->paginate(20);

        $departments = Staff::distinct()->pluck('department')->filter()->sort();
        $statuses = ['pending', 'approved', 'rejected', 'completed'];

        // Statistics
        $stats = [
            'total' => Mission::count(),
            'pending' => Mission::where('status', 'pending')->count(),
            'approved' => Mission::where('status', 'approved')->count(),
            'rejected' => Mission::where('status', 'rejected')->count(),
            'completed' => Mission::where('status', 'completed')->count()
        ];

        return view('admin.missions.index', compact('missions', 'departments', 'statuses', 'stats', 'status', 'department'));
    }

    /**
     * Approve a mission
     */
    public function approveMission(Mission $mission)
    {
        $mission->update([
            'status' => 'approved',
            'approved_by' => Auth::guard('staff')->id(),
            'approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Mission approved successfully.');
    }

    /**
     * Reject a mission
     */
    public function rejectMission(Request $request, Mission $mission)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $mission->update([
            'status' => 'rejected',
            'approved_by' => Auth::guard('staff')->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return redirect()->back()->with('success', 'Mission rejected.');
    }

    // ==================== LEAVE MANAGEMENT ====================

    /**
     * Show leave requests management
     */
    public function leavesIndex(Request $request)
    {
        $status = $request->get('status');
        $department = $request->get('department');
        $leaveType = $request->get('leave_type');

        $query = LeaveRequest::with('staff', 'leaveType');

        if ($status) {
            $query->where('status', $status);
        }

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($leaveType) {
            $query->where('leave_type_id', $leaveType);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(20);

        $departments = Staff::distinct()->pluck('department')->filter()->sort();
        $leaveTypes = \App\Models\LeaveType::all();
        $statuses = ['pending', 'approved', 'rejected'];

        // Statistics
        $stats = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count()
        ];

        return view('admin.leaves.index', compact('leaveRequests', 'departments', 'leaveTypes', 'statuses', 'stats', 'status', 'department', 'leaveType'));
    }

    /**
     * Approve a leave request
     */
    public function approveLeave(LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::guard('staff')->id(),
            'approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject a leave request
     */
    public function rejectLeave(Request $request, LeaveRequest $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::guard('staff')->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return redirect()->back()->with('success', 'Leave request rejected.');
    }

    // ==================== LEAVE TYPES MANAGEMENT ====================

    /**
     * Show leave types management
     */
    public function leaveTypesIndex()
    {
        $leaveTypes = \App\Models\LeaveType::orderBy('name')->paginate(20);

        return view('admin.leave-types.index', compact('leaveTypes'));
    }

    /**
     * Show create leave type form
     */
    public function leaveTypesCreate()
    {
        return view('admin.leave-types.create');
    }

    /**
     * Store new leave type
     */
    public function leaveTypesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:leave_types,name',
            'description' => 'nullable|string|max:500',
            'max_days' => 'nullable|integer|min:1|max:365',
            'requires_approval' => 'boolean'
        ]);

        \App\Models\LeaveType::create([
            'name' => $request->name,
            'description' => $request->description,
            'max_days' => $request->max_days,
            'requires_approval' => $request->boolean('requires_approval', true)
        ]);

        return redirect()->route('admin.leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    /**
     * Show edit leave type form
     */
    public function leaveTypesEdit(\App\Models\LeaveType $leaveType)
    {
        return view('admin.leave-types.edit', compact('leaveType'));
    }

    /**
     * Update leave type
     */
    public function leaveTypesUpdate(Request $request, \App\Models\LeaveType $leaveType)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:leave_types,name,' . $leaveType->id,
            'description' => 'nullable|string|max:500',
            'max_days' => 'nullable|integer|min:1|max:365',
            'requires_approval' => 'boolean'
        ]);

        $leaveType->update([
            'name' => $request->name,
            'description' => $request->description,
            'max_days' => $request->max_days,
            'requires_approval' => $request->boolean('requires_approval')
        ]);

        return redirect()->route('admin.leave-types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    /**
     * Delete leave type
     */
    public function leaveTypesDestroy(\App\Models\LeaveType $leaveType)
    {
        // Check if leave type is in use
        if ($leaveType->leaveRequests()->exists()) {
            return redirect()->route('admin.leave-types.index')
                ->with('error', 'Cannot delete leave type that is being used by leave requests.');
        }

        $leaveType->delete();

        return redirect()->route('admin.leave-types.index')
            ->with('success', 'Leave type deleted successfully.');
    }

    // ==================== ACTIVITY CALENDAR MANAGEMENT ====================

    /**
     * Show activity calendar management
     */
    public function calendarIndex()
    {
        $activities = \App\Models\ActivityCalendar::with('creator')
            ->orderBy('start_date', 'desc')
            ->paginate(20);

        return view('admin.calendar.index', compact('activities'));
    }

    /**
     * Show create activity form
     */
    public function calendarCreate()
    {
        return view('admin.calendar.create');
    }

    /**
     * Store new activity
     */
    public function calendarStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:meeting,training,event,holiday,deadline',
            'status' => 'required|in:done,ongoing,not_yet_started'
        ]);

        \App\Models\ActivityCalendar::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'type' => $request->type,
            'status' => $request->status,
            'created_by' => Auth::guard('staff')->id()
        ]);

        return redirect()->route('admin.calendar.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Show edit activity form
     */
    public function calendarEdit(\App\Models\ActivityCalendar $activity)
    {
        return view('admin.calendar.edit', compact('activity'));
    }

    /**
     * Update activity
     */
    public function calendarUpdate(Request $request, \App\Models\ActivityCalendar $activity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:meeting,training,event,holiday,deadline',
            'status' => 'required|in:done,ongoing,not_yet_started'
        ]);

        $activity->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'type' => $request->type,
            'status' => $request->status,
            'updated_by' => Auth::guard('staff')->id()
        ]);

        return redirect()->route('admin.calendar.index')
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Delete activity
     */
    public function calendarDestroy(\App\Models\ActivityCalendar $activity)
    {
        $activity->delete();

        return redirect()->route('admin.calendar.index')
            ->with('success', 'Activity deleted successfully.');
    }

    /**
     * Get calendar events for FullCalendar.js (API endpoint)
     */
    public function calendarEvents()
    {
        $activities = \App\Models\ActivityCalendar::all();

        $events = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'title' => $activity->title,
                'start' => $activity->start_date->format('Y-m-d'),
                'end' => $activity->end_date->addDay()->format('Y-m-d'), // FullCalendar end date is exclusive
                'description' => $activity->description,
                'location' => $activity->location,
                'type' => $activity->type,
                'type_label' => $activity->type_label,
                'status' => $activity->status,
                'backgroundColor' => $this->getEventColor($activity->type),
                'borderColor' => $this->getEventColor($activity->type),
                'textColor' => $activity->type === 'holiday' ? '#212529' : '#ffffff',
                'extendedProps' => [
                    'type' => $activity->type,
                    'type_label' => $activity->type_label,
                    'status' => $activity->status,
                    'description' => $activity->description,
                    'location' => $activity->location,
                    'creator' => $activity->creator->full_name ?? 'Unknown'
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Get event color based on activity type
     */
    private function getEventColor($type)
    {
        return match($type) {
            'meeting' => '#007bff',
            'training' => '#17a2b8',
            'event' => '#28a745',
            'holiday' => '#ffc107',
            'deadline' => '#dc3545',
            default => '#6c757d'
        };
    }

    // ==================== CONTENT MANAGEMENT ====================

    /**
     * Show content management dashboard
     */
    public function contentIndex()
    {
        $heroSlidesCount = HeroSlide::count();
        $activeHeroSlides = HeroSlide::where('status', 'active')->count();
        $activitiesCount = \App\Models\ActivityCalendar::count();

        return view('admin.content.index', compact('heroSlidesCount', 'activeHeroSlides', 'activitiesCount'));
    }

    /**
     * Show homepage edit form
     */
    public function homepageEdit()
    {
        // This would contain homepage content settings
        return view('admin.content.homepage');
    }

    /**
     * Update homepage content
     */
    public function homepageUpdate(Request $request)
    {
        // Homepage content update logic would go here
        return redirect()->route('admin.content.homepage')
            ->with('success', 'Homepage updated successfully.');
    }

    /**
     * Show about page edit form
     */
    public function aboutEdit()
    {
        // This would contain about page content settings
        return view('admin.content.about');
    }

    /**
     * Update about page content
     */
    public function aboutUpdate(Request $request)
    {
        // About page content update logic would go here
        return redirect()->route('admin.content.about')
            ->with('success', 'About page updated successfully.');
    }

    // ==================== ACTIVITY REQUEST MANAGEMENT ====================

    /**
     * Show activity requests management
     */
    public function activityRequestsIndex(Request $request)
    {
        $status = $request->get('status');
        $type = $request->get('type');

        $query = \App\Models\ActivityRequest::with(['requester', 'reviewer', 'approvedActivity'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $requests = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => \App\Models\ActivityRequest::count(),
            'pending' => \App\Models\ActivityRequest::pending()->count(),
            'approved' => \App\Models\ActivityRequest::approved()->count(),
            'rejected' => \App\Models\ActivityRequest::rejected()->count(),
        ];

        $types = ['meeting', 'training', 'event', 'holiday', 'deadline'];
        $statuses = ['pending', 'approved', 'rejected'];

        return view('admin.activity-requests.index', compact('requests', 'stats', 'types', 'statuses', 'status', 'type'));
    }

    /**
     * Show individual activity request details
     */
    public function activityRequestShow(\App\Models\ActivityRequest $activityRequest)
    {
        $activityRequest->load(['requester', 'reviewer', 'approvedActivity']);

        return view('admin.activity-requests.show', compact('activityRequest'));
    }

    /**
     * Approve an activity request
     */
    public function approveActivityRequest(Request $request, \App\Models\ActivityRequest $activityRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
            'create_activity' => 'boolean'
        ]);

        // Update the request status
        $activityRequest->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::guard('staff')->id(),
            'reviewed_at' => now()
        ]);

        // Optionally create the activity in the calendar
        if ($request->boolean('create_activity', true)) {
            $activity = \App\Models\ActivityCalendar::create([
                'title' => $activityRequest->title,
                'description' => $activityRequest->description,
                'start_date' => $activityRequest->start_date,
                'end_date' => $activityRequest->end_date,
                'location' => $activityRequest->location,
                'type' => $activityRequest->type,
                'status' => 'not_yet_started', // Default status for approved activities
                'created_by' => Auth::guard('staff')->id()
            ]);

            // Link the approved activity to the request
            $activityRequest->update([
                'approved_activity_id' => $activity->id
            ]);
        }

        return redirect()->route('admin.activity-requests.index')
            ->with('success', 'Activity request approved successfully.' .
                ($request->boolean('create_activity', true) ? ' Activity has been added to the calendar.' : ''));
    }

    /**
     * Reject an activity request
     */
    public function rejectActivityRequest(Request $request, \App\Models\ActivityRequest $activityRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $activityRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::guard('staff')->id(),
            'reviewed_at' => now()
        ]);

        return redirect()->route('admin.activity-requests.index')
            ->with('success', 'Activity request rejected.');
    }

    /**
     * Batch process activity requests
     */
    public function batchProcessActivityRequests(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:activity_requests,id',
            'batch_admin_notes' => 'nullable|string|max:1000',
            'batch_rejection_reason' => 'required_if:action,reject|string|max:1000'
        ]);

        $requestIds = $request->request_ids;
        $action = $request->action;
        $processed = 0;

        foreach ($requestIds as $requestId) {
            $activityRequest = \App\Models\ActivityRequest::find($requestId);

            if ($activityRequest && $activityRequest->status === 'pending') {
                if ($action === 'approve') {
                    $activityRequest->update([
                        'status' => 'approved',
                        'admin_notes' => $request->batch_admin_notes,
                        'reviewed_by' => Auth::guard('staff')->id(),
                        'reviewed_at' => now()
                    ]);

                    // Create activity in calendar
                    $activity = \App\Models\ActivityCalendar::create([
                        'title' => $activityRequest->title,
                        'description' => $activityRequest->description,
                        'start_date' => $activityRequest->start_date,
                        'end_date' => $activityRequest->end_date,
                        'location' => $activityRequest->location,
                        'type' => $activityRequest->type,
                        'status' => 'not_yet_started',
                        'created_by' => Auth::guard('staff')->id()
                    ]);

                    $activityRequest->update(['approved_activity_id' => $activity->id]);

                } elseif ($action === 'reject') {
                    $activityRequest->update([
                        'status' => 'rejected',
                        'rejection_reason' => $request->batch_rejection_reason,
                        'admin_notes' => $request->batch_admin_notes,
                        'reviewed_by' => Auth::guard('staff')->id(),
                        'reviewed_at' => now()
                    ]);
                }
                $processed++;
            }
        }

        $actionText = $action === 'approve' ? 'approved' : 'rejected';
        return redirect()->route('admin.activity-requests.index')
            ->with('success', "{$processed} activity requests have been {$actionText}.");
    }

    // ==================== PUBLIC EVENTS MANAGEMENT ====================

    /**
     * Show public events management
     */
    public function publicEventsIndex(Request $request)
    {
        $status = $request->get('status');
        $category = $request->get('category');

        $query = \App\Models\PublicEvent::with(['creator', 'updater']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $events = $query->orderBy('start_date', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => \App\Models\PublicEvent::count(),
            'published' => \App\Models\PublicEvent::where('status', 'published')->count(),
            'draft' => \App\Models\PublicEvent::where('status', 'draft')->count(),
            'featured' => \App\Models\PublicEvent::where('is_featured', true)->count(),
        ];

        $categories = ['conference', 'workshop', 'training', 'seminar', 'meeting', 'announcement', 'celebration'];
        $statuses = ['draft', 'published', 'archived'];

        return view('admin.public-events.index', compact('events', 'stats', 'categories', 'statuses', 'status', 'category'));
    }

    /**
     * Show create public event form
     */
    public function publicEventsCreate()
    {
        return view('admin.public-events.create');
    }

    /**
     * Store new public event
     */
    public function publicEventsStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'summary' => 'nullable|string|max:500',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string|max:500',
            'category' => 'required|in:conference,workshop,training,seminar,meeting,announcement,celebration',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'registration_link' => 'nullable|url|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'max_participants' => 'nullable|integer|min:1|max:10000',
            'is_featured' => 'boolean',
            'registration_required' => 'boolean',
            'registration_deadline' => 'nullable|date|after_or_equal:today|before_or_equal:start_date',
            'fee' => 'nullable|numeric|min:0|max:999999.99',
            'tags' => 'nullable|string|max:500',
            'additional_info' => 'nullable|string',
        ]);

        $data = $request->except(['featured_image', 'tags']);
        $data['created_by'] = Auth::guard('staff')->id();

        // Handle tags
        if ($request->tags) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // Set published_at if status is published
        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = 'event_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Create events directory if it doesn't exist
            if (!file_exists(public_path('images/events'))) {
                mkdir(public_path('images/events'), 0755, true);
            }

            $image->move(public_path('images/events'), $filename);
            $data['featured_image'] = $filename;
        }

        \App\Models\PublicEvent::create($data);

        return redirect()->route('admin.public-events.index')
            ->with('success', 'Public event created successfully.');
    }

    /**
     * Show individual public event details
     */
    public function publicEventsShow(\App\Models\PublicEvent $publicEvent)
    {
        $publicEvent->load(['creator', 'updater']);

        return view('admin.public-events.show', compact('publicEvent'));
    }

    /**
     * Show edit public event form
     */
    public function publicEventsEdit(\App\Models\PublicEvent $publicEvent)
    {
        return view('admin.public-events.edit', compact('publicEvent'));
    }

    /**
     * Update public event
     */
    public function publicEventsUpdate(Request $request, \App\Models\PublicEvent $publicEvent)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'summary' => 'nullable|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string|max:500',
            'category' => 'required|in:conference,workshop,training,seminar,meeting,announcement,celebration',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'registration_link' => 'nullable|url|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'max_participants' => 'nullable|integer|min:1|max:10000',
            'is_featured' => 'boolean',
            'registration_required' => 'boolean',
            'registration_deadline' => 'nullable|date|before_or_equal:start_date',
            'fee' => 'nullable|numeric|min:0|max:999999.99',
            'tags' => 'nullable|string|max:500',
            'additional_info' => 'nullable|string',
        ]);

        $data = $request->except(['featured_image', 'tags']);
        $data['updated_by'] = Auth::guard('staff')->id();

        // Handle tags
        if ($request->tags) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        } else {
            $data['tags'] = null;
        }

        // Set/unset published_at based on status change
        if ($request->status === 'published' && $publicEvent->status !== 'published') {
            $data['published_at'] = now();
        } elseif ($request->status !== 'published') {
            $data['published_at'] = null;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($publicEvent->featured_image && file_exists(public_path('images/events/' . $publicEvent->featured_image))) {
                unlink(public_path('images/events/' . $publicEvent->featured_image));
            }

            $image = $request->file('featured_image');
            $filename = 'event_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Create events directory if it doesn't exist
            if (!file_exists(public_path('images/events'))) {
                mkdir(public_path('images/events'), 0755, true);
            }

            $image->move(public_path('images/events'), $filename);
            $data['featured_image'] = $filename;
        }

        $publicEvent->update($data);

        return redirect()->route('admin.public-events.show', $publicEvent)
            ->with('success', 'Public event updated successfully.');
    }

    /**
     * Delete public event
     */
    public function publicEventsDestroy(\App\Models\PublicEvent $publicEvent)
    {
        // Delete featured image
        if ($publicEvent->featured_image && file_exists(public_path('images/events/' . $publicEvent->featured_image))) {
            unlink(public_path('images/events/' . $publicEvent->featured_image));
        }

        $publicEvent->delete();

        return redirect()->route('admin.public-events.index')
            ->with('success', 'Public event deleted successfully.');
    }

    /**
     * Toggle public event featured status
     */
    public function publicEventsToggleFeatured(\App\Models\PublicEvent $publicEvent)
    {
        $publicEvent->update([
            'is_featured' => !$publicEvent->is_featured,
            'updated_by' => Auth::guard('staff')->id(),
        ]);

        return response()->json([
            'success' => true,
            'is_featured' => $publicEvent->is_featured,
            'message' => 'Featured status updated successfully.'
        ]);
    }

    /**
     * Publish/unpublish public event
     */
    public function publicEventsToggleStatus(\App\Models\PublicEvent $publicEvent)
    {
        $newStatus = $publicEvent->status === 'published' ? 'draft' : 'published';

        $updateData = [
            'status' => $newStatus,
            'updated_by' => Auth::guard('staff')->id(),
        ];

        if ($newStatus === 'published') {
            $updateData['published_at'] = now();
        } else {
            $updateData['published_at'] = null;
        }

        $publicEvent->update($updateData);

        return response()->json([
            'success' => true,
            'status' => $publicEvent->status,
            'message' => 'Status updated successfully.'
        ]);
    }

    /**
     * Bulk action for public events
     */
    public function publicEventsBulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,unpublish,archive,delete,feature,unfeature',
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:public_events,id',
        ]);

        $eventIds = $request->event_ids;
        $action = $request->action;
        $processed = 0;

        foreach ($eventIds as $eventId) {
            $event = \App\Models\PublicEvent::find($eventId);

            if ($event) {
                switch ($action) {
                    case 'publish':
                        $event->update([
                            'status' => 'published',
                            'published_at' => now(),
                            'updated_by' => Auth::guard('staff')->id(),
                        ]);
                        break;
                    case 'unpublish':
                        $event->update([
                            'status' => 'draft',
                            'published_at' => null,
                            'updated_by' => Auth::guard('staff')->id(),
                        ]);
                        break;
                    case 'archive':
                        $event->update([
                            'status' => 'archived',
                            'updated_by' => Auth::guard('staff')->id(),
                        ]);
                        break;
                    case 'delete':
                        // Delete featured image
                        if ($event->featured_image && file_exists(public_path('images/events/' . $event->featured_image))) {
                            unlink(public_path('images/events/' . $event->featured_image));
                        }
                        $event->delete();
                        break;
                    case 'feature':
                        $event->update([
                            'is_featured' => true,
                            'updated_by' => Auth::guard('staff')->id(),
                        ]);
                        break;
                    case 'unfeature':
                        $event->update([
                            'is_featured' => false,
                            'updated_by' => Auth::guard('staff')->id(),
                        ]);
                        break;
                }
                $processed++;
            }
        }

        $actionText = ucfirst($action);
        return redirect()->route('admin.public-events.index')
            ->with('success', "{$processed} events have been {$actionText}d successfully.");
    }

    // ==================== END OF ADMIN CONTROLLER ====================
}
