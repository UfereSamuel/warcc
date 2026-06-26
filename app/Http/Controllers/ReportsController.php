<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\WeeklyTracker;
use App\Models\Position;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $totalStaff = Staff::where('status', 'active')->count();
        $totalPositions = Position::where('is_active', true)->count();

        $attendanceStats = [
            'total_days' => Attendance::whereBetween('date', [$startDate, $endDate])->count(),
            'present_days' => Attendance::whereBetween('date', [$startDate, $endDate])->where('status', 'present')->count(),
            'late_days' => Attendance::whereBetween('date', [$startDate, $endDate])->where('status', 'late')->count(),
            'avg_hours' => round(Attendance::whereBetween('date', [$startDate, $endDate])->avg('total_hours') ?? 0, 1),
        ];

        $trackerStats = [
            'submitted' => WeeklyTracker::whereBetween('week_start_date', [$startDate, $endDate])->where('status', '!=', 'draft')->count(),
            'pending' => WeeklyTracker::whereBetween('week_start_date', [$startDate, $endDate])->where('status', 'submitted')->count(),
            'approved' => WeeklyTracker::whereBetween('week_start_date', [$startDate, $endDate])->where('status', 'approved')->count(),
        ];

        $positionStats = Position::withCount(['staff' => fn ($query) => $query->where('status', 'active')])
            ->where('is_active', true)
            ->orderBy('title')
            ->get()
            ->map(fn ($position) => [
                'position' => $position->title,
                'staff_count' => $position->staff_count,
            ]);

        return view('admin.reports.index', compact(
            'totalStaff',
            'totalPositions',
            'attendanceStats',
            'trackerStats',
            'positionStats',
            'startDate',
            'endDate'
        ));
    }

    public function weeklyTrackers(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $positionId = $request->get('position_id');
        $status = $request->get('status');

        $query = WeeklyTracker::with(['staff.position'])
            ->whereBetween('week_start_date', [$startDate, $endDate]);

        if ($positionId) {
            $query->whereHas('staff', fn ($q) => $q->where('position_id', $positionId));
        }

        if ($status) {
            $weeklyStatuses = ['at_duty_station', 'on_mission', 'on_leave'];

            if (in_array($status, $weeklyStatuses, true)) {
                $query->where('status', $status);
            } else {
                $query->where('submission_status', $status);
            }
        }

        $allTrackers = (clone $query)->get();
        $trackerStats = [
            'total' => $allTrackers->count(),
            'draft' => $allTrackers->where('status', 'draft')->count(),
            'submitted' => $allTrackers->where('status', 'submitted')->count(),
            'reviewed' => $allTrackers->where('status', 'reviewed')->count(),
            'approved' => $allTrackers->where('status', 'approved')->count(),
        ];

        $trackers = $query->orderBy('week_start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $positions = Position::where('is_active', true)->orderBy('title')->get();
        $statuses = ['draft', 'submitted', 'reviewed', 'approved'];

        return view('admin.reports.weekly-trackers', compact(
            'trackers',
            'trackerStats',
            'positions',
            'statuses',
            'startDate',
            'endDate',
            'status'
        ) + ['position_id' => $positionId]);
    }

    public function attendance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $positionId = $request->get('position_id');

        $dailyAttendance = $this->getDailyAttendanceTrends($startDate, $endDate, $positionId);
        $staffRankings = $this->getStaffAttendanceRankings($startDate, $endDate, $positionId);
        $positions = Position::where('is_active', true)->orderBy('title')->get();

        return view('admin.reports.attendance', compact(
            'dailyAttendance',
            'staffRankings',
            'positions',
            'startDate',
            'endDate'
        ) + ['position_id' => $positionId]);
    }

    public function exportPDF(Request $request)
    {
        $type = $request->get('type', 'overview');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $positionId = $request->get('position_id');

        switch ($type) {
            case 'weekly-trackers':
                $data = $this->getWeeklyTrackersData($startDate, $endDate, $positionId);
                $view = 'admin.reports.pdf.weekly-trackers';
                $filename = 'weekly-trackers-report.pdf';
                break;

            case 'attendance':
                $data = $this->getAttendanceData($startDate, $endDate, $positionId);
                $view = 'admin.reports.pdf.attendance';
                $filename = 'attendance-report.pdf';
                break;

            default:
                $data = $this->getOverviewData($startDate, $endDate);
                $view = 'admin.reports.pdf.overview';
                $filename = 'overview-report.pdf';
                break;
        }

        $pdf = Pdf::loadView($view, array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now()->format('M d, Y g:i A'),
        ]));

        return $pdf->download($filename);
    }

    private function getAttendanceStats(string $startDate, string $endDate): array
    {
        $totalWorkingDays = $this->getWorkingDaysBetween($startDate, $endDate);
        $activeStaff = Staff::where('status', 'active')->count();
        $totalAttendance = Attendance::whereBetween('date', [$startDate, $endDate])->count();
        $presentDays = Attendance::whereBetween('date', [$startDate, $endDate])->where('status', 'present')->count();
        $lateDays = Attendance::whereBetween('date', [$startDate, $endDate])->where('status', 'late')->count();
        $avgHours = Attendance::whereBetween('date', [$startDate, $endDate])->avg('total_hours') ?? 0;

        return [
            'total_working_days' => $totalWorkingDays,
            'total_attendance' => $totalAttendance,
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'average_hours' => round($avgHours, 1),
            'attendance_rate' => ($totalWorkingDays > 0 && $activeStaff > 0)
                ? round(($totalAttendance / ($totalWorkingDays * $activeStaff)) * 100, 1)
                : 0,
        ];
    }

    private function getWeeklyTrackerStats(string $startDate, string $endDate): array
    {
        $totalWeeks = $this->getWeeksBetween($startDate, $endDate);
        $activeStaff = Staff::where('status', 'active')->count();
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
            'submission_rate' => ($totalWeeks > 0 && $activeStaff > 0)
                ? round(($submittedTrackers / ($totalWeeks * $activeStaff)) * 100, 1)
                : 0,
        ];
    }

    private function getPositionStats(string $startDate, string $endDate)
    {
        return Position::withCount(['staff' => fn ($query) => $query->where('status', 'active')])
            ->where('is_active', true)
            ->orderBy('title')
            ->get()
            ->map(function ($position) use ($startDate, $endDate) {
                $staffIds = Staff::where('position_id', $position->id)
                    ->where('status', 'active')
                    ->pluck('id');

                $totalAttendance = Attendance::whereIn('staff_id', $staffIds)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->count();

                $staffCount = $staffIds->count();

                return [
                    'position' => $position->title,
                    'staff_count' => $staffCount,
                    'total_attendance' => $totalAttendance,
                    'avg_attendance_per_staff' => $staffCount > 0 ? round($totalAttendance / $staffCount, 1) : 0,
                ];
            });
    }

    private function getWorkingDaysBetween(string $startDate, string $endDate): int
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

    private function getWeeksBetween(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfWeek();
        $end = Carbon::parse($endDate)->endOfWeek();

        return $start->diffInWeeks($end) + 1;
    }

    private function getDailyAttendanceTrends(string $startDate, string $endDate, ?int $positionId = null)
    {
        $query = Attendance::selectRaw('DATE(date) as attendance_date, COUNT(*) as total_attendance')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('attendance_date')
            ->orderBy('attendance_date');

        if ($positionId) {
            $query->whereHas('staff', fn ($q) => $q->where('position_id', $positionId));
        }

        return $query->get();
    }

    private function getStaffAttendanceRankings(string $startDate, string $endDate, ?int $positionId = null)
    {
        $query = Staff::with(['attendances' => fn ($q) => $q->whereBetween('date', [$startDate, $endDate]), 'position'])
            ->where('status', 'active');

        if ($positionId) {
            $query->where('position_id', $positionId);
        }

        return $query->get()
            ->map(function ($staff) {
                $attendanceCount = $staff->attendances->count();
                $avgHours = $staff->attendances->avg('total_hours') ?? 0;
                $lateCount = $staff->attendances->where('status', 'late')->count();

                return [
                    'staff' => $staff,
                    'attendance_count' => $attendanceCount,
                    'average_hours' => round($avgHours, 1),
                    'late_count' => $lateCount,
                    'punctuality_rate' => $attendanceCount > 0
                        ? round((($attendanceCount - $lateCount) / $attendanceCount) * 100, 1)
                        : 100,
                ];
            })
            ->sortByDesc('attendance_count')
            ->take(10)
            ->values();
    }

    private function getAttendancePatterns(string $startDate, string $endDate, ?int $positionId = null)
    {
        $query = Attendance::whereBetween('date', [$startDate, $endDate]);

        if ($positionId) {
            $query->whereHas('staff', fn ($q) => $q->where('position_id', $positionId));
        }

        return $query->get()
            ->groupBy(fn ($attendance) => Carbon::parse($attendance->date)->format('l'))
            ->map(fn ($records, $day) => ['day' => $day, 'total' => $records->count()])
            ->sortBy(fn ($item) => Carbon::parse('next ' . $item['day'])->dayOfWeek)
            ->values();
    }

    private function getOverviewData(string $startDate, string $endDate): array
    {
        return [
            'attendanceStats' => $this->getAttendanceStats($startDate, $endDate),
            'trackerStats' => $this->getWeeklyTrackerStats($startDate, $endDate),
            'positionStats' => $this->getPositionStats($startDate, $endDate),
            'totalStaff' => Staff::where('status', 'active')->count(),
            'totalPositions' => Position::where('is_active', true)->count(),
        ];
    }

    private function getWeeklyTrackersData(string $startDate, string $endDate, ?int $positionId = null): array
    {
        $query = WeeklyTracker::with(['staff.position'])
            ->whereBetween('week_start_date', [$startDate, $endDate]);

        if ($positionId) {
            $query->whereHas('staff', fn ($q) => $q->where('position_id', $positionId));
        }

        $trackers = $query->orderBy('week_start_date', 'desc')->get();

        return [
            'trackers' => $trackers,
            'trackerStats' => [
                'total' => $trackers->count(),
                'draft' => $trackers->where('status', 'draft')->count(),
                'submitted' => $trackers->where('status', 'submitted')->count(),
                'reviewed' => $trackers->where('status', 'reviewed')->count(),
                'approved' => $trackers->where('status', 'approved')->count(),
            ],
        ];
    }

    private function getAttendanceData(string $startDate, string $endDate, ?int $positionId = null): array
    {
        return [
            'dailyAttendance' => $this->getDailyAttendanceTrends($startDate, $endDate, $positionId),
            'staffRankings' => $this->getStaffAttendanceRankings($startDate, $endDate, $positionId),
            'attendancePatterns' => $this->getAttendancePatterns($startDate, $endDate, $positionId),
            'attendanceStats' => $this->getAttendanceStats($startDate, $endDate),
        ];
    }
}
