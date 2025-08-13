<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\WeeklyTracker;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    /**
     * Main reports dashboard
     */
    public function index(Request $request)
    {
        // Date range setup
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Key statistics
        $totalStaff = Staff::where('status', 'active')->count();
        $totalDepartments = Staff::distinct('department')->count();

        // Attendance overview
        $attendanceStats = [
            'total_days' => Attendance::whereBetween('date', [$startDate, $endDate])->count(),
            'present_days' => Attendance::whereBetween('date', [$startDate, $endDate])->where('status', 'present')->count(),
            'late_days' => Attendance::whereBetween('date', [$startDate, $endDate])->where('status', 'late')->count(),
            'avg_hours' => round(Attendance::whereBetween('date', [$startDate, $endDate])->avg('total_hours') ?? 0, 1)
        ];

        // Weekly tracker stats
        $trackerStats = [
            'submitted' => WeeklyTracker::whereBetween('week_start_date', [$startDate, $endDate])->where('status', '!=', 'draft')->count(),
            'pending' => WeeklyTracker::whereBetween('week_start_date', [$startDate, $endDate])->where('status', 'submitted')->count(),
            'approved' => WeeklyTracker::whereBetween('week_start_date', [$startDate, $endDate])->where('status', 'approved')->count()
        ];

        // Department performance
        $departmentStats = Staff::select('department')
            ->selectRaw('COUNT(*) as staff_count')
            ->where('status', 'active')
            ->groupBy('department')
            ->get();

        return view('admin.reports.index', compact(
            'totalStaff', 'totalDepartments', 'attendanceStats', 'trackerStats',
            'departmentStats', 'startDate', 'endDate'
        ));
    }

    /**
     * Staff performance analytics
     */
    public function staffPerformance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $department = $request->get('department');

        $query = Staff::where('status', 'active');

        if ($department) {
            $query->where('department', $department);
        }

        $staff = $query->get()->map(function ($member) use ($startDate, $endDate) {
            $attendanceCount = $member->attendances()
                ->whereBetween('date', [$startDate, $endDate])
                ->count();

            $weeklySubmissions = $member->weeklyTrackers()
                ->whereBetween('week_start_date', [$startDate, $endDate])
                ->where('status', '!=', 'draft')
                ->count();

            $avgHours = $member->attendances()
                ->whereBetween('date', [$startDate, $endDate])
                ->avg('total_hours') ?? 0;

            $lateCount = $member->attendances()
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'late')
                ->count();

            // Performance scoring
            $attendanceScore = min($attendanceCount * 2, 40);
            $submissionScore = min($weeklySubmissions * 5, 40);
            $punctualityScore = max(20 - ($lateCount * 2), 0);
            $performanceScore = min($attendanceScore + $submissionScore + $punctualityScore, 100);

            return [
                'staff' => $member,
                'attendance_days' => $attendanceCount,
                'weekly_submissions' => $weeklySubmissions,
                'average_hours' => round($avgHours, 1),
                'late_days' => $lateCount,
                'performance_score' => $performanceScore
            ];
        })->sortByDesc('performance_score');

        $departments = Staff::distinct()->pluck('department')->filter()->sort();

        return view('admin.reports.staff-performance', compact(
            'staff', 'departments', 'startDate', 'endDate', 'department'
        ));
    }

    /**
     * Weekly tracker submissions analysis
     */
    public function weeklyTrackers(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $department = $request->get('department');
        $status = $request->get('status');

        $query = WeeklyTracker::with('staff')
            ->whereBetween('week_start_date', [$startDate, $endDate]);

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Get statistics before pagination
        $allTrackers = $query->get();
        $trackerStats = [
            'total' => $allTrackers->count(),
            'draft' => $allTrackers->where('status', 'draft')->count(),
            'submitted' => $allTrackers->where('status', 'submitted')->count(),
            'reviewed' => $allTrackers->where('status', 'reviewed')->count(),
            'approved' => $allTrackers->where('status', 'approved')->count()
        ];

        // Get paginated results
        $trackers = $query->orderBy('week_start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $departments = Staff::distinct()->pluck('department')->filter()->sort();
        $statuses = ['draft', 'submitted', 'reviewed', 'approved'];

        return view('admin.reports.weekly-trackers', compact(
            'trackers', 'trackerStats', 'departments', 'statuses',
            'startDate', 'endDate', 'department', 'status'
        ));
    }

    /**
     * Attendance analytics
     */
    public function attendance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $department = $request->get('department');

        // Daily attendance trends
        $query = Attendance::selectRaw('DATE(date) as attendance_date, COUNT(*) as total_attendance')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('attendance_date')
            ->orderBy('attendance_date');

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $dailyAttendance = $query->get();

        // Staff rankings
        $staffQuery = Staff::with(['attendances' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        }])->where('status', 'active');

        if ($department) {
            $staffQuery->where('department', $department);
        }

        $staffRankings = $staffQuery->get()
            ->map(function($staff) {
                $attendanceCount = $staff->attendances->count();
                $avgHours = $staff->attendances->avg('total_hours') ?? 0;
                $lateCount = $staff->attendances->where('status', 'late')->count();

                return [
                    'staff' => $staff,
                    'attendance_count' => $attendanceCount,
                    'average_hours' => round($avgHours, 1),
                    'late_count' => $lateCount,
                    'punctuality_rate' => $attendanceCount > 0 ? round((($attendanceCount - $lateCount) / $attendanceCount) * 100, 1) : 100
                ];
            })
            ->sortByDesc('attendance_count')
            ->take(10);

        $departments = Staff::distinct()->pluck('department')->filter()->sort();

        return view('admin.reports.attendance', compact(
            'dailyAttendance', 'staffRankings', 'departments', 'startDate', 'endDate', 'department'
        ));
    }

    /**
     * Export report to PDF
     */
    public function exportPDF(Request $request)
    {
        $type = $request->get('type', 'overview');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        switch ($type) {
            case 'staff-performance':
                $data = $this->getStaffPerformanceData($startDate, $endDate, $request->get('department'));
                $view = 'admin.reports.pdf.staff-performance';
                $filename = 'staff-performance-report.pdf';
                break;

            case 'weekly-trackers':
                $data = $this->getWeeklyTrackersData($startDate, $endDate, $request->get('department'));
                $view = 'admin.reports.pdf.weekly-trackers';
                $filename = 'weekly-trackers-report.pdf';
                break;

            case 'attendance':
                $data = $this->getAttendanceData($startDate, $endDate, $request->get('department'));
                $view = 'admin.reports.pdf.attendance';
                $filename = 'attendance-report.pdf';
                break;

            default:
                $data = $this->getOverviewData($startDate, $endDate);
                $view = 'admin.reports.pdf.overview';
                $filename = 'overview-report.pdf';
                break;
        }

        $pdf = PDF::loadView($view, array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now()->format('M d, Y g:i A')
        ]));

        return $pdf->download($filename);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    private function getAttendanceStats($startDate, $endDate)
    {
        $totalWorkingDays = $this->getWorkingDaysBetween($startDate, $endDate);
        $totalAttendance = Attendance::whereBetween('date', [$startDate, $endDate])->count();
        $presentDays = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')->count();
        $lateDays = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'late')->count();
        $avgHours = Attendance::whereBetween('date', [$startDate, $endDate])
            ->avg('total_hours') ?? 0;

        return [
            'total_working_days' => $totalWorkingDays,
            'total_attendance' => $totalAttendance,
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'average_hours' => round($avgHours, 1),
            'attendance_rate' => $totalWorkingDays > 0 ? round(($totalAttendance / ($totalWorkingDays * Staff::where('status', 'active')->count())) * 100, 1) : 0
        ];
    }

    private function getWeeklyTrackerStats($startDate, $endDate)
    {
        $totalWeeks = $this->getWeeksBetween($startDate, $endDate);
        $submittedTrackers = WeeklyTracker::whereBetween('week_start_date', [$startDate, $endDate])
            ->where('status', '!=', 'draft')->count();
        $approvedTrackers = WeeklyTracker::whereBetween('week_start_date', [$startDate, $endDate])
            ->where('status', 'approved')->count();
        $pendingTrackers = WeeklyTracker::whereBetween('week_start_date', [$startDate, $endDate])
            ->where('status', 'submitted')->count();

        return [
            'total_weeks' => $totalWeeks,
            'submitted_trackers' => $submittedTrackers,
            'approved_trackers' => $approvedTrackers,
            'pending_trackers' => $pendingTrackers,
            'submission_rate' => $totalWeeks > 0 ? round(($submittedTrackers / ($totalWeeks * Staff::where('status', 'active')->count())) * 100, 1) : 0
        ];
    }

    private function getDepartmentStats($startDate, $endDate)
    {
        return Staff::select('department')
            ->selectRaw('COUNT(*) as staff_count')
            ->selectRaw('AVG(annual_leave_balance) as avg_leave_balance')
            ->with(['attendances' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])
            ->where('status', 'active')
            ->groupBy('department')
            ->get()
            ->map(function($dept) {
                $totalAttendance = $dept->attendances->count();
                $avgAttendance = $dept->staff_count > 0 ? round($totalAttendance / $dept->staff_count, 1) : 0;

                return [
                    'department' => $dept->department,
                    'staff_count' => $dept->staff_count,
                    'avg_leave_balance' => round($dept->avg_leave_balance, 1),
                    'total_attendance' => $totalAttendance,
                    'avg_attendance_per_staff' => $avgAttendance
                ];
            });
    }

    private function getRecentActivity()
    {
        $recentAttendance = Attendance::with('staff')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentTrackers = WeeklyTracker::with('staff')
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'recent_attendance' => $recentAttendance,
            'recent_trackers' => $recentTrackers
        ];
    }

    private function calculatePerformanceScore($attendance, $weeklySubmissions, $lateCount)
    {
        $attendanceScore = min($attendance * 2, 40); // Max 40 points
        $submissionScore = min($weeklySubmissions * 5, 40); // Max 40 points
        $punctualityScore = max(20 - ($lateCount * 2), 0); // Max 20 points, -2 per late

        return min($attendanceScore + $submissionScore + $punctualityScore, 100);
    }

    private function getWorkingDaysBetween($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $workingDays = 0;

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    private function getWeeksBetween($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfWeek();
        $end = Carbon::parse($endDate)->endOfWeek();
        return $start->diffInWeeks($end) + 1;
    }

    private function getDailyAttendanceTrends($startDate, $endDate, $department = null)
    {
        $query = Attendance::selectRaw('DATE(date) as attendance_date, COUNT(*) as total_attendance')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('attendance_date')
            ->orderBy('attendance_date');

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        return $query->get();
    }

    private function getDepartmentAttendanceComparison($startDate, $endDate)
    {
        return DB::table('staff')
            ->leftJoin('attendances', 'staff.id', '=', 'attendances.staff_id')
            ->select('staff.department')
            ->selectRaw('COUNT(DISTINCT staff.id) as staff_count')
            ->selectRaw('COUNT(attendances.id) as total_attendance')
            ->selectRaw('ROUND(COUNT(attendances.id) / COUNT(DISTINCT staff.id), 1) as avg_attendance')
            ->where('staff.status', 'active')
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->groupBy('staff.department')
            ->get();
    }

    private function getStaffAttendanceRankings($startDate, $endDate, $department = null)
    {
        $query = Staff::with(['attendances' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }])
            ->where('status', 'active');

        if ($department) {
            $query->where('department', $department);
        }

        return $query->get()
            ->map(function($staff) {
                $attendanceCount = $staff->attendances->count();
                $avgHours = $staff->attendances->avg('total_hours') ?? 0;
                $lateCount = $staff->attendances->where('status', 'late')->count();

                return [
                    'staff' => $staff,
                    'attendance_count' => $attendanceCount,
                    'average_hours' => round($avgHours, 1),
                    'late_count' => $lateCount,
                    'punctuality_rate' => $attendanceCount > 0 ? round((($attendanceCount - $lateCount) / $attendanceCount) * 100, 1) : 100
                ];
            })
            ->sortByDesc('attendance_count')
            ->take(10);
    }

    private function getAttendancePatterns($startDate, $endDate, $department = null)
    {
        $query = Attendance::selectRaw('DAYOFWEEK(date) as day_of_week, COUNT(*) as total')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('day_of_week')
            ->orderBy('day_of_week');

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $patterns = $query->get();

        $dayNames = ['', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return $patterns->map(function($pattern) use ($dayNames) {
            return [
                'day' => $dayNames[$pattern->day_of_week],
                'total' => $pattern->total
            ];
        });
    }

    private function getWeeklySubmissionSummary($startDate, $endDate, $department = null)
    {
        $query = WeeklyTracker::selectRaw('week_start_date, status, COUNT(*) as count')
            ->whereBetween('week_start_date', [$startDate, $endDate])
            ->groupBy('week_start_date', 'status')
            ->orderBy('week_start_date', 'desc');

        if ($department) {
            $query->whereHas('staff', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        return $query->get()->groupBy('week_start_date');
    }

    // Data methods for PDF exports
    private function getOverviewData($startDate, $endDate)
    {
        return [
            'attendanceStats' => $this->getAttendanceStats($startDate, $endDate),
            'trackerStats' => $this->getWeeklyTrackerStats($startDate, $endDate),
            'departmentStats' => $this->getDepartmentStats($startDate, $endDate),
            'totalStaff' => Staff::where('status', 'active')->count()
        ];
    }

    private function getStaffPerformanceData($startDate, $endDate, $department = null)
    {
        // Implementation for staff performance PDF data
        return [];
    }

    private function getWeeklyTrackersData($startDate, $endDate, $department = null)
    {
        // Implementation for weekly trackers PDF data
        return [];
    }

    private function getAttendanceData($startDate, $endDate, $department = null)
    {
        // Implementation for attendance PDF data
        return [];
    }
}
