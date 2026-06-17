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
     * Show admin dashboard with comprehensive analytics
     */
    public function dashboard()
    {
        // Get current date ranges
        $today = now();
        $currentWeek = [
            'start' => $today->startOfWeek()->copy(),
            'end' => $today->endOfWeek()->copy()
        ];
        $currentMonth = [
            'start' => $today->startOfMonth()->copy(),
            'end' => $today->endOfMonth()->copy()
        ];

        // Staff overview statistics
        $staffStats = [
            'total' => Staff::count(),
            'regular' => Staff::where('is_admin', false)->count(),
            'admins' => Staff::where('is_admin', true)->count(),
            'active' => Staff::where('status', 'active')->count(),
        ];

        // Attendance analytics
        $attendanceStats = [
            'today_present' => Attendance::whereDate('date', $today)
                ->whereNotNull('clock_in_time')
                ->count(),
            'today_total' => Staff::where('status', 'active')->count(),
            'week_completion' => $this->getWeeklyAttendanceCompletion(),
            'month_average' => $this->getMonthlyAttendanceAverage(),
        ];

        // Weekly tracker analytics
        $weeklyTrackerStats = [
            'this_week_submitted' => WeeklyTracker::whereBetween('week_start_date', [$currentWeek['start'], $currentWeek['end']])
                ->where('status', 'submitted')
                ->count(),
            'this_week_pending' => WeeklyTracker::whereBetween('week_start_date', [$currentWeek['start'], $currentWeek['end']])
                ->whereIn('status', ['draft', 'pending'])
                ->count(),
            'completion_rate' => $this->getWeeklyTrackerCompletionRate(),
            'monthly_trends' => $this->getMonthlyTrackerTrends(),
        ];

        // Staff status distribution
        $staffStatusData = [
            'at_office' => Staff::whereDoesntHave('missions', function($q) {
                $q->where('status', 'approved')
                  ->where('start_date', '<=', today())
                  ->where('end_date', '>=', today());
            })->whereDoesntHave('leaveRequests', function($q) {
                $q->where('status', 'approved')
                  ->where('start_date', '<=', today())
                  ->where('end_date', '>=', today());
            })->count(),
            'on_mission' => Staff::whereHas('missions', function($q) {
                $q->where('status', 'approved')
                  ->where('start_date', '<=', today())
                  ->where('end_date', '>=', today());
            })->count(),
            'on_leave' => Staff::whereHas('leaveRequests', function($q) {
                $q->where('status', 'approved')
                  ->where('start_date', '<=', today())
                  ->where('end_date', '>=', today());
            })->count(),
        ];



        // Recent activity requests
        $recentActivityRequests = ActivityRequest::with(['requester'])
            ->pending()
            ->recentFirst()
            ->take(5)
            ->get();

        // Position-based statistics instead of department
        $positionStats = $this->getPositionStats();

        // Chart data for frontend
        $chartData = [
            'attendance_trend' => $this->getAttendanceTrendData(),
            'tracker_completion' => $this->getTrackerCompletionData(),
            'gender_breakdown' => $this->getGenderBreakdownData(),
            'monthly_comparison' => $this->getMonthlyComparisonData(),
        ];

        return view('admin.dashboard', compact(
            'staffStats',
            'attendanceStats', 
            'weeklyTrackerStats',
            'staffStatusData',
            'positionStats',
            'recentActivityRequests',
            'chartData'
        ));
    }

    /**
     * Get position-based statistics for dashboard
     */
    private function getPositionStats()
    {
        $today = now();
        $startOfWeek = $today->copy()->startOfWeek();
        
        return \App\Models\Position::with(['staff' => function($query) {
            $query->where('status', 'active');
        }])->get()->map(function($position) use ($today, $startOfWeek) {
            $activeStaff = $position->staff->where('status', 'active');
            $totalStaff = $activeStaff->count();
            
            if ($totalStaff === 0) {
                return (object)[
                    'department' => $position->title, // Using 'department' key for compatibility with view
                    'total' => 0,
                    'active' => 0,
                    'attendance_rate' => 0,
                    'tracker_rate' => 0,
                ];
            }
            
            // Calculate today's attendance rate
            $todayAttendance = Attendance::whereDate('date', $today)
                ->whereIn('staff_id', $activeStaff->pluck('id'))
                ->whereNotNull('clock_in_time')
                ->count();
            $attendanceRate = round(($todayAttendance / $totalStaff) * 100, 1);
            
            // Calculate weekly tracker submission rate
            $weeklyTrackers = WeeklyTracker::whereDate('week_start_date', $startOfWeek)
                ->whereIn('staff_id', $activeStaff->pluck('id'))
                ->where('status', '!=', 'draft')
                ->count();
            $trackerRate = round(($weeklyTrackers / $totalStaff) * 100, 1);
            
            return (object)[
                'department' => $position->title, // Using 'department' key for compatibility with view
                'total' => $totalStaff,
                'active' => $totalStaff, // All staff in this query are active
                'attendance_rate' => $attendanceRate,
                'tracker_rate' => $trackerRate,
            ];
        })->filter(function($stat) {
            return $stat->total > 0; // Only show positions that have staff
        });
    }

    /**
     * Get weekly attendance completion rate
     */
    private function getWeeklyAttendanceCompletion()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        $totalWorkDays = 5; // Monday to Friday
        $totalStaff = Staff::where('status', 'active')->count();
        $expectedAttendances = $totalStaff * $totalWorkDays;
        
        $actualAttendances = Attendance::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->whereNotNull('clock_in_time')
            ->count();
            
        return $expectedAttendances > 0 ? round(($actualAttendances / $expectedAttendances) * 100, 1) : 0;
    }

    /**
     * Get monthly attendance average
     */
    private function getMonthlyAttendanceAverage()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $workDaysInMonth = 22; // Approximate work days
        $totalStaff = Staff::where('status', 'active')->count();
        $expectedAttendances = $totalStaff * $workDaysInMonth;
        
        $actualAttendances = Attendance::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('clock_in_time')
            ->count();
            
        return $expectedAttendances > 0 ? round(($actualAttendances / $expectedAttendances) * 100, 1) : 0;
    }

    /**
     * Get weekly tracker completion rate
     */
    private function getWeeklyTrackerCompletionRate()
    {
        $currentWeekStart = now()->startOfWeek();
        $totalStaff = Staff::where('status', 'active')->count();
        
        $submittedTrackers = WeeklyTracker::where('week_start_date', $currentWeekStart)
            ->where('status', 'submitted')
            ->count();
            
        return $totalStaff > 0 ? round(($submittedTrackers / $totalStaff) * 100, 1) : 0;
    }

    /**
     * Get monthly tracker trends
     */
    private function getMonthlyTrackerTrends()
    {
        $trends = [];
        for ($i = 0; $i < 4; $i++) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $submitted = WeeklyTracker::where('week_start_date', $weekStart)
                ->where('status', 'submitted')
                ->count();
            $trends[] = $submitted;
        }
        return array_reverse($trends);
    }

    /**
     * Get department analytics
     */
    private function getDepartmentAnalytics()
    {
        return Staff::select('department')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(CASE WHEN status = "active" THEN 1 END) as active')
            ->groupBy('department')
            ->get()
            ->map(function($dept) {
                $dept->attendance_rate = $this->getDepartmentAttendanceRate($dept->department);
                $dept->tracker_rate = $this->getDepartmentTrackerRate($dept->department);
                return $dept;
            });
    }

    /**
     * Get attendance trend data for charts
     */
    private function getAttendanceTrendData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $attendance = Attendance::whereDate('date', $date)
                ->whereNotNull('clock_in_time')
                ->count();
            $data[] = [
                'date' => $date->format('M d'),
                'count' => $attendance
            ];
        }
        return $data;
    }

    /**
     * Get tracker completion data for charts
     */
    private function getTrackerCompletionData()
    {
        $data = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $submitted = WeeklyTracker::where('week_start_date', $weekStart)
                ->where('status', 'submitted')
                ->count();
            $data[] = [
                'week' => 'Week ' . ($i + 1),
                'count' => $submitted
            ];
        }
        return array_reverse($data);
    }

    /**
     * Get gender breakdown data
     */
    private function getGenderBreakdownData()
    {
        return Staff::select('gender')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('gender')
            ->get()
            ->map(function($item) {
                return [
                    'gender' => ucfirst($item->gender),
                    'count' => $item->count
                ];
            });
    }

    /**
     * Get monthly comparison data
     */
    private function getMonthlyComparisonData()
    {
        $thisMonth = Attendance::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->whereNotNull('clock_in_time')
            ->count();
            
        $lastMonth = Attendance::whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->whereNotNull('clock_in_time')
            ->count();
            
        return [
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'change' => $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0
        ];
    }

    /**
     * Get department attendance rate
     */
    private function getDepartmentAttendanceRate($department)
    {
        $staffCount = Staff::where('department', $department)
            ->where('status', 'active')
            ->count();
            
        if ($staffCount == 0) return 0;
        
        $attendanceCount = Attendance::whereHas('staff', function($q) use ($department) {
            $q->where('department', $department);
        })->whereDate('date', today())
        ->whereNotNull('clock_in_time')
        ->count();
        
        return round(($attendanceCount / $staffCount) * 100, 1);
    }

    /**
     * Get department tracker rate
     */
    private function getDepartmentTrackerRate($department)
    {
        $staffCount = Staff::where('department', $department)
            ->where('status', 'active')
            ->count();
            
        if ($staffCount == 0) return 0;
        
        $trackerCount = WeeklyTracker::whereHas('staff', function($q) use ($department) {
            $q->where('department', $department);
        })->where('week_start_date', now()->startOfWeek())
        ->where('status', 'submitted')
        ->count();
        
        return round(($trackerCount / $staffCount) * 100, 1);
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
     * Display admin listing with search and filters
     */
    public function adminIndex(Request $request)
    {
        $query = Staff::where('is_admin', true);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%")
                  ->orWhereHas('position', function($posQuery) use ($search) {
                      $posQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by position
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $admins = $query->with('position')->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $statuses = Staff::distinct()->pluck('status')->filter()->sort();
        $positions = \App\Models\Position::orderBy('title')->get();
        $genders = ['male', 'female', 'other'];

        return view('admin.admins.index', compact('admins', 'statuses', 'positions', 'genders'));
    }

    /**
     * Display staff listing with search and filters (non-admin only)
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
                  ->orWhereHas('position', function($posQuery) use ($search) {
                      $posQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Only show non-admin staff
                $query->where('is_admin', false);

        // Filter by position
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $staff = $query->with('position')->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $positions = \App\Models\Position::orderBy('title')->get();
        $statuses = ['active', 'inactive'];
        $genders = ['male', 'female', 'other'];

        return view('admin.staff.index', compact('staff', 'positions', 'statuses', 'genders'));
    }

    /**
     * Show create staff form
     */
    public function staffCreate()
    {
        // Only super admin can create new staff
        if (auth()->guard('staff')->user()->email !== 'admin@africacdc.org') {
            abort(403, 'Only super admin can create new staff members.');
        }
        
        $positions = \App\Models\Position::active()->orderBy('title')->get();
        return view('admin.staff.create', compact('positions'));
    }

    /**
     * Store new staff member
     */
    public function staffStore(Request $request)
    {
        // Only super admin can create new staff
        if (auth()->guard('staff')->user()->email !== 'admin@africacdc.org') {
            abort(403, 'Only super admin can create new staff members.');
        }
        
        $request->validate([
            'staff_id' => 'required|string|unique:staff,staff_id|max:20',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:staff,email|max:255',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'position_id' => 'required|exists:positions,id',
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
        // Load the position relationship
        $staff->load('position');
        
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
        $positions = \App\Models\Position::active()->orderBy('title')->get();
        return view('admin.staff.edit', compact('staff', 'positions'));
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
            'position_id' => 'required|exists:positions,id',
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
        // Check if current user is admin
        $currentUser = auth()->guard('staff')->user();
        if (!$currentUser->is_admin) {
            return redirect()->back()
                ->with('error', 'You do not have permission to promote staff.');
        }

        // Prevent promoting super admin email to regular admin
        if ($staff->email === 'admin@africacdc.org') {
            return redirect()->route('admin.staff.show', $staff)
                ->with('error', 'This is the super admin account and cannot be modified.');
        }

        // Update admin status
        $staff->update(['is_admin' => true]);

        // Assign Administrator role if Spatie roles are set up
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $adminRole = \Spatie\Permission\Models\Role::where('name', 'Administrator')
                ->where('guard_name', 'staff')
                ->first();
            
            if ($adminRole) {
                // Remove staff role and assign admin role
                $staff->removeRole('Staff');
                $staff->assignRole($adminRole);
            }
        }

        return redirect()->route('admin.staff.show', $staff)
            ->with('success', 'Staff member promoted to administrator.');
    }

    /**
     * Remove admin privileges
     */
    public function demoteStaff(Staff $staff)
    {
        // Check if current user is admin
        $currentUser = auth()->guard('staff')->user();
        if (!$currentUser->is_admin) {
            return redirect()->back()
                ->with('error', 'You do not have permission to demote administrators.');
        }

        // Prevent demoting super admin
        if ($staff->email === 'admin@africacdc.org') {
            return redirect()->route('admin.staff.show', $staff)
                ->with('error', 'Cannot demote the super administrator.');
        }

        // Prevent demoting the last admin
        $adminCount = Staff::where('is_admin', true)
            ->where('email', '!=', 'admin@africacdc.org') // Exclude super admin from count
            ->count();
        
        if ($adminCount <= 1) {
            return redirect()->route('admin.staff.show', $staff)
                ->with('error', 'Cannot demote the last administrator.');
        }

        // Update admin status
        $staff->update(['is_admin' => false]);

        // Assign Staff role if Spatie roles are set up
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $staffRole = \Spatie\Permission\Models\Role::where('name', 'Staff')
                ->where('guard_name', 'staff')
                ->first();
            
            if ($staffRole) {
                // Remove admin role and assign staff role
                $staff->removeRole('Administrator');
                $staff->assignRole($staffRole);
            }
        }

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
        $position_id = $request->get('position_id');

        // Get today's attendance with staff details
        $query = Attendance::with('staff')
            ->whereDate('date', $date)
            ->orderBy('clock_in_time', 'asc');

        if ($position_id) {
            $query->whereHas('staff', function($q) use ($position_id) {
                $q->where('position_id', $position_id);
            });
        }

        $attendances = $query->get();

        // Get staff who haven't checked in today
        $checkedInStaffIds = $attendances->pluck('staff_id')->toArray();
        $absentStaffQuery = Staff::where('status', 'active')
            ->whereNotIn('id', $checkedInStaffIds);

        if ($position_id) {
            $absentStaffQuery->where('position_id', $position_id);
        }

        $absentStaff = $absentStaffQuery->get();

        // Get positions for filter
        $positions = \App\Models\Position::orderBy('title')->get();

        // Calculate statistics
        $clockInTimes = $attendances->whereNotNull('clock_in_time')->pluck('clock_in_time');
        $averageClockIn = null;
        
        if ($clockInTimes->count() > 0) {
            // Convert time strings to minutes for averaging
            $totalMinutes = $clockInTimes->map(function ($time) {
                $parts = explode(':', $time);
                return ($parts[0] * 60) + $parts[1];
            })->avg();
            
            // Convert back to time format
            $hours = floor($totalMinutes / 60);
            $minutes = floor($totalMinutes % 60);
            $averageClockIn = sprintf('%02d:%02d', $hours, $minutes);
        }
        
        $stats = [
            'total_staff' => Staff::where('status', 'active')->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $absentStaff->count(),
            'average_clock_in' => $averageClockIn
        ];

        return view('admin.attendance.index', compact(
            'attendances', 'absentStaff', 'positions', 'stats', 'date', 'position_id'
        ));
    }

    /**
     * Show daily attendance report
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $position_id = $request->get('position_id');

        // Get attendance for the selected date
        $query = Attendance::with('staff')
            ->whereDate('date', $date);

        if ($position_id) {
            $query->whereHas('staff', function($q) use ($position_id) {
                $q->where('position_id', $position_id);
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

        $positions = \App\Models\Position::orderBy('title')->get();

        return view('admin.attendance.daily-report', compact(
            'attendances', 'departmentSummary', 'positions', 'date', 'position_id'
        ));
    }



    /**
     * Show weekly tracker management
     */
    public function weeklyTrackersIndex(Request $request)
    {
        $week = $request->get('week', now()->startOfWeek()->format('Y-m-d'));
        $position_id = $request->get('position_id');
        $status = $request->get('status');

        $weekStart = \Carbon\Carbon::parse($week)->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $query = WeeklyTracker::with('staff', 'leaveType')
            ->whereDate('week_start_date', $weekStart);

        if ($position_id) {
            $query->whereHas('staff', function($q) use ($position_id) {
                $q->where('position_id', $position_id);
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

        if ($position_id) {
            $missingStaffQuery->where('position_id', $position_id);
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

        $positions = \App\Models\Position::orderBy('title')->get();
        $statuses = ['at_duty_station', 'on_mission', 'on_leave'];

        return view('admin.weekly-trackers.index', compact(
            'trackers', 'missingStaff', 'stats', 'positions', 'statuses',
            'week', 'weekStart', 'weekEnd', 'position_id', 'status'
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
        $position_id = $request->get('position_id');

        $query = Mission::with('staff');

        if ($status) {
            $query->where('status', $status);
        }

        if ($position_id) {
            $query->whereHas('staff', function($q) use ($position_id) {
                $q->where('position_id', $position_id);
            });
        }

        $missions = $query->orderBy('created_at', 'desc')->paginate(20);

        $positions = \App\Models\Position::orderBy('title')->get();
        $statuses = ['pending', 'approved', 'rejected', 'completed'];

        // Statistics
        $stats = [
            'total' => Mission::count(),
            'pending' => Mission::where('status', 'pending')->count(),
            'approved' => Mission::where('status', 'approved')->count(),
            'rejected' => Mission::where('status', 'rejected')->count(),
            'completed' => Mission::where('status', 'completed')->count()
        ];

        return view('admin.missions.index', compact('missions', 'positions', 'statuses', 'stats', 'status', 'position_id'));
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
        $position_id = $request->get('position_id');
        $leaveType = $request->get('leave_type');

        $query = LeaveRequest::with('staff', 'leaveType');

        if ($status) {
            $query->where('status', $status);
        }

        if ($position_id) {
            $query->whereHas('staff', function($q) use ($position_id) {
                $q->where('position_id', $position_id);
            });
        }

        if ($leaveType) {
            $query->where('leave_type_id', $leaveType);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(20);

        $positions = \App\Models\Position::orderBy('title')->get();
        $leaveTypes = \App\Models\LeaveType::all();
        $statuses = ['pending', 'approved', 'rejected'];

        // Statistics
        $stats = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count()
        ];

        return view('admin.leaves.index', compact('leaveRequests', 'positions', 'leaveTypes', 'statuses', 'stats', 'status', 'position_id', 'leaveType'));
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
        
        // Get positions for filter
        $positions = \App\Models\Position::orderBy('title')->get();

        return view('admin.activity-requests.index', compact('requests', 'stats', 'types', 'statuses', 'status', 'type', 'positions'));
    }

    /**
     * Show individual activity request details
     */
    public function activityRequestShow(\App\Models\ActivityRequest $activityRequest)
    {
        $activityRequest->load(['requester.position', 'reviewer.position', 'approvedActivity']);

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

    // ================================================
    // ROLES & PERMISSIONS MANAGEMENT
    // ================================================

    /**
     * Display roles listing
     */
    public function rolesIndex()
    {
        $roles = \Spatie\Permission\Models\Role::where('guard_name', 'staff')
            ->with('permissions')
            ->get();

        $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'staff')
            ->get()
            ->groupBy(function($permission) {
                // Group permissions by category (prefix before underscore)
                $parts = explode('_', $permission->name);
                return ucfirst($parts[0]);
            });

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show create role form
     */
    public function rolesCreate()
    {
        $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'staff')
            ->get()
            ->groupBy(function($permission) {
                $parts = explode('_', $permission->name);
                return ucfirst($parts[0]);
            });

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store new role
     */
    public function rolesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        $role = \Spatie\Permission\Models\Role::create([
            'name' => $request->name,
            'guard_name' => 'staff'
        ]);

        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show edit role form
     */
    public function rolesEdit(\Spatie\Permission\Models\Role $role)
    {
        $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'staff')
            ->get()
            ->groupBy(function($permission) {
                $parts = explode('_', $permission->name);
                return ucfirst($parts[0]);
            });

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update role
     */
    public function rolesUpdate(Request $request, \Spatie\Permission\Models\Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        $role->update(['name' => $request->name]);

        // Sync permissions
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Delete role
     */
    public function rolesDestroy(\Spatie\Permission\Models\Role $role)
    {
        // Prevent deleting core roles
        if (in_array($role->name, ['Super Admin', 'Administrator', 'Staff'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete core system roles.');
        }

        // Check if role is assigned to any users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete role that is assigned to users.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    // ================================================
    // EXPORT FUNCTIONALITY
    // ================================================

    /**
     * Export attendance data
     */
    public function exportAttendance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $format = $request->get('format', 'csv');

        $attendances = Attendance::with(['staff'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->exportAttendanceCSV($attendances, $startDate, $endDate);
        } else {
            return $this->exportAttendancePDF($attendances, $startDate, $endDate);
        }
    }

    /**
     * Export weekly trackers data
     */
    public function exportWeeklyTrackers(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $format = $request->get('format', 'csv');

        $trackers = WeeklyTracker::with(['staff'])
            ->whereBetween('week_start_date', [$startDate, $endDate])
            ->orderBy('week_start_date', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->exportTrackersCSV($trackers, $startDate, $endDate);
        } else {
            return $this->exportTrackersPDF($trackers, $startDate, $endDate);
        }
    }

    /**
     * Export analytics dashboard data
     */
    public function exportDashboardAnalytics(Request $request)
    {
        $format = $request->get('format', 'pdf');
        
        // Get comprehensive analytics data
        $staffStats = [
            'total' => Staff::count(),
            'regular' => Staff::where('is_admin', false)->count(),
            'admins' => Staff::where('is_admin', true)->count(),
            'active' => Staff::where('status', 'active')->count(),
        ];

        $attendanceStats = [
            'today_present' => Attendance::whereDate('date', now())
                ->whereNotNull('clock_in_time')
                ->count(),
            'week_completion' => $this->getWeeklyAttendanceCompletion(),
            'month_average' => $this->getMonthlyAttendanceAverage(),
        ];

        $weeklyTrackerStats = [
            'completion_rate' => $this->getWeeklyTrackerCompletionRate(),
            'monthly_trends' => $this->getMonthlyTrackerTrends(),
        ];

        $departmentStats = $this->getDepartmentAnalytics();

        if ($format === 'pdf') {
            return $this->exportAnalyticsPDF($staffStats, $attendanceStats, $weeklyTrackerStats, $departmentStats);
        } else {
            return $this->exportAnalyticsCSV($staffStats, $attendanceStats, $weeklyTrackerStats, $departmentStats);
        }
    }

    /**
     * Export attendance data as CSV
     */
    private function exportAttendanceCSV($attendances, $startDate, $endDate)
    {
        $filename = "attendance_report_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function() use ($attendances) {
            $handle = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($handle, [
                'Date',
                'Staff ID',
                'Staff Name',
                'Department',
                'Clock In Time',
                'Clock Out Time',
                'Total Hours',
                'Status',
                'Clock In Location',
                'Clock Out Location'
            ]);

            // CSV data
            foreach ($attendances as $attendance) {
                fputcsv($handle, [
                    $attendance->date->format('Y-m-d'),
                    $attendance->staff->staff_id,
                    $attendance->staff->full_name,
                    $attendance->staff->department,
                    $attendance->clock_in_time ?? 'N/A',
                    $attendance->clock_out_time ?? 'N/A',
                    $attendance->total_hours ?? 'N/A',
                    ucfirst($attendance->status),
                    $attendance->clock_in_address ?? 'N/A',
                    $attendance->clock_out_address ?? 'N/A'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export weekly trackers data as CSV
     */
    private function exportTrackersCSV($trackers, $startDate, $endDate)
    {
        $filename = "weekly_trackers_report_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function() use ($trackers) {
            $handle = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($handle, [
                'Week Start Date',
                'Week End Date',
                'Staff ID',
                'Staff Name',
                'Department',
                'Monday Status',
                'Tuesday Status',
                'Wednesday Status',
                'Thursday Status',
                'Friday Status',
                'Submission Status',
                'Submitted At'
            ]);

            // CSV data
            foreach ($trackers as $tracker) {
                fputcsv($handle, [
                    $tracker->week_start_date->format('Y-m-d'),
                    $tracker->week_end_date->format('Y-m-d'),
                    $tracker->staff->staff_id,
                    $tracker->staff->full_name,
                    $tracker->staff->department,
                    $tracker->monday_status ?? 'N/A',
                    $tracker->tuesday_status ?? 'N/A',
                    $tracker->wednesday_status ?? 'N/A',
                    $tracker->thursday_status ?? 'N/A',
                    $tracker->friday_status ?? 'N/A',
                    ucfirst($tracker->status),
                    $tracker->submitted_at ? $tracker->submitted_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export analytics data as CSV
     */
    private function exportAnalyticsCSV($staffStats, $attendanceStats, $weeklyTrackerStats, $departmentStats)
    {
        $filename = "dashboard_analytics_" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function() use ($staffStats, $attendanceStats, $weeklyTrackerStats, $departmentStats) {
            $handle = fopen('php://output', 'w');
            
            // Staff Statistics
            fputcsv($handle, ['STAFF STATISTICS']);
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Total Staff', $staffStats['total']]);
            fputcsv($handle, ['Regular Staff', $staffStats['regular']]);
            fputcsv($handle, ['Administrators', $staffStats['admins']]);
            fputcsv($handle, ['Active Staff', $staffStats['active']]);
            fputcsv($handle, []);

            // Attendance Statistics
            fputcsv($handle, ['ATTENDANCE STATISTICS']);
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Today Present', $attendanceStats['today_present']]);
            fputcsv($handle, ['Week Completion Rate', $attendanceStats['week_completion'] . '%']);
            fputcsv($handle, ['Month Average', $attendanceStats['month_average'] . '%']);
            fputcsv($handle, []);

            // Weekly Tracker Statistics
            fputcsv($handle, ['WEEKLY TRACKER STATISTICS']);
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Completion Rate', $weeklyTrackerStats['completion_rate'] . '%']);
            fputcsv($handle, []);

            // Department Statistics
            fputcsv($handle, ['DEPARTMENT STATISTICS']);
            fputcsv($handle, ['Department', 'Total Staff', 'Active Staff', 'Attendance Rate', 'Tracker Rate']);
            foreach ($departmentStats as $dept) {
                fputcsv($handle, [
                    $dept->department,
                    $dept->total,
                    $dept->active,
                    $dept->attendance_rate . '%',
                    $dept->tracker_rate . '%'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    // ==================== SYSTEM SETTINGS ====================

    /**
     * Display system settings
     */
    public function settingsIndex()
    {
        $generalSettings = \App\Models\Setting::getByGroup('general');
        $contactSettings = \App\Models\Setting::getByGroup('contact');
        $socialSettings = \App\Models\Setting::getByGroup('social');
        $mediaSettings = \App\Models\Setting::getByGroup('media');
        $systemSettings = \App\Models\Setting::getByGroup('system');

        return view('admin.settings.index', compact(
            'generalSettings',
            'contactSettings',
            'socialSettings',
            'mediaSettings',
            'systemSettings'
        ));
    }

    /**
     * Update system settings
     */
    public function settingsUpdate(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string|max:1000',
        ]);

        try {
            foreach ($request->settings as $key => $value) {
                $setting = \App\Models\Setting::where('key', $key)->first();
                
                if ($setting) {
                    // Handle file uploads for image type settings
                    if ($setting->type === 'image' && $request->hasFile("files.{$key}")) {
                        $file = $request->file("files.{$key}");
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('settings', $filename, 'public');
                        $value = $path;
                    }
                    
                    // Handle boolean settings
                    if ($setting->type === 'boolean') {
                        $value = $request->has("settings.{$key}") ? '1' : '0';
                    }

                    $setting->update(['value' => $value]);
                }
            }

            // Clear cache
            \App\Models\Setting::clearCache();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Settings updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Reset settings to default
     */
    public function settingsReset()
    {
        try {
            // Run the settings seeder to reset to defaults
            \Artisan::call('db:seed', ['--class' => 'SettingsSeeder']);
            
            // Clear cache
            \App\Models\Setting::clearCache();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Settings have been reset to default values.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reset settings: ' . $e->getMessage());
        }
    }

    /**
     * Show email test form
     */
    public function emailTestForm()
    {
        $currentConfig = [
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'encryption' => config('mail.mailers.smtp.scheme'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        return view('admin.email.test', compact('currentConfig'));
    }

    /**
     * Configure email settings
     */
    public function configureEmail(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer|between:1,65535',
            'mail_username' => 'required|email',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        try {
            // Update .env file
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            $updates = [
                'MAIL_MAILER' => 'smtp',
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                'MAIL_FROM_NAME' => '"' . $request->mail_from_name . '"',
            ];

            foreach ($updates as $key => $value) {
                $pattern = "/^{$key}=.*$/m";
                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
                } else {
                    $envContent .= "\n{$key}={$value}";
                }
            }

            file_put_contents($envPath, $envContent);

            // Clear config cache
            \Artisan::call('config:clear');

            return redirect()->route('admin.email.test')
                ->with('success', 'Email configuration updated successfully! You can now test sending emails.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update email configuration: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Send test email
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
            'test_message' => 'nullable|string|max:500',
            'mailer_type' => 'required|in:laravel,microsoft-graph',
        ]);

        try {
            $testData = [
                'system_name' => 'WARCC Staff Management System',
                'test_time' => now()->format('Y-m-d H:i:s T'),
                'server_info' => php_uname('n'),
                'test_message' => $request->test_message,
                'tested_by' => auth()->guard('staff')->user()->full_name,
            ];

            if ($request->mailer_type === 'microsoft-graph') {
                // Use Microsoft Graph service
                $graphService = new \App\Services\MicrosoftGraphService();
                $response = $graphService->sendEmail(
                    $request->test_email,
                    '✅ Email Test - WARCC System (Microsoft Graph)',
                    view('emails.test', compact('testData'))->render(),
                    config('mail.from.address')
                );

                return redirect()->back()
                    ->with('success', 'Test email sent successfully via Microsoft Graph to ' . $request->test_email . '! Please check the inbox.');

            } else {
                // Use Laravel mailer
                \Mail::to($request->test_email)->send(new \App\Mail\TestEmail($testData));

                return redirect()->back()
                    ->with('success', 'Test email sent successfully via Laravel Mailer to ' . $request->test_email . '! Please check the inbox.');
            }

        } catch (\Exception $e) {
            \Log::error('Email test failed', [
                'error' => $e->getMessage(),
                'email' => $request->test_email,
                'mailer_type' => $request->mailer_type,
                'user' => auth()->guard('staff')->user()->email ?? 'unknown'
            ]);

            return redirect()->back()
                ->with('error', 'Failed to send test email: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Test Microsoft Graph connection
     */
    public function testMicrosoftGraph()
    {
        try {
            $graphService = new \App\Services\MicrosoftGraphService();
            $result = $graphService->testConnection();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'user' => $result['user']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Microsoft Graph test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== POSITIONS MANAGEMENT ====================

    /**
     * Show positions management
     */
    public function positionsIndex()
    {
        $positions = \App\Models\Position::withCount('staff')
            ->orderBy('title')
            ->paginate(20);

        $stats = [
            'total' => \App\Models\Position::count(),
            'active' => \App\Models\Position::active()->count(),
            'inactive' => \App\Models\Position::inactive()->count(),
            'with_staff' => \App\Models\Position::whereHas('staff')->count(),
        ];

        return view('admin.positions.index', compact('positions', 'stats'));
    }

    /**
     * Show form to create new position
     */
    public function positionsCreate()
    {
        return view('admin.positions.create');
    }

    /**
     * Store new position
     */
    public function positionsStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:positions,title',
        ]);

        \App\Models\Position::create([
            'title' => $request->title,
            'is_active' => true,
        ]);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position "' . $request->title . '" created successfully.');
    }

    /**
     * Show form to edit position
     */
    public function positionsEdit(\App\Models\Position $position)
    {
        return view('admin.positions.edit', compact('position'));
    }

    /**
     * Update position
     */
    public function positionsUpdate(Request $request, \App\Models\Position $position)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:positions,title,' . $position->id,
        ]);

        $position->update([
            'title' => $request->title,
        ]);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position updated successfully.');
    }

    /**
     * Delete position
     */
    public function positionsDestroy(\App\Models\Position $position)
    {
        // Check if position is being used by any staff
        if ($position->staff()->count() > 0) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Cannot delete position "' . $position->title . '" because it is assigned to ' . $position->staff()->count() . ' staff member(s).');
        }

        $positionTitle = $position->title;
        $position->delete();

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position "' . $positionTitle . '" deleted successfully.');
    }

    /**
     * Toggle position status
     */
    public function positionsToggleStatus(\App\Models\Position $position)
    {
        $position->update([
            'is_active' => !$position->is_active
        ]);

        $status = $position->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.positions.index')
            ->with('success', 'Position "' . $position->title . '" ' . $status . ' successfully.');
    }

    // ==================== END OF ADMIN CONTROLLER ====================
}
