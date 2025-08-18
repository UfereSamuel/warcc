<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Staff;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Show attendance dashboard
     */
    public function index()
    {
        /** @var Staff $staff */
        $staff = Auth::guard('staff')->user();

        // Fix any incomplete records from previous days
        $this->fixIncompleteRecords($staff);

        $todayAttendance = $staff->getTodayAttendance();

        // Get this week's attendance
        $weekAttendance = $staff->attendances()
            ->thisWeek()
            ->orderBy('date', 'desc')
            ->get();

        // Get attendance summary for current month
        $monthSummary = [
            'total_days' => now()->day,
            'present_days' => $staff->attendances()
                ->thisMonth()
                ->where('status', 'present')
                ->count(),
            'total_hours' => $staff->attendances()
                ->thisMonth()
                ->whereNotNull('total_hours')
                ->sum('total_hours'),
        ];

        return view('staff.attendance.index', compact(
            'staff',
            'todayAttendance',
            'weekAttendance',
            'monthSummary'
        ));
    }

    /**
     * Clock in
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('staff')->user();
        $now = now();
        $today = $now->toDateString();

        // Enhanced validation: check for suspicious coordinates (exact 0,0)
        if ($request->latitude == 0 && $request->longitude == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid location coordinates. Please ensure location services are enabled.'
            ], 400);
        }

        // Rate limiting check (prevent multiple requests within 1 minute)
        $recentAttempt = cache()->get("clock_in_attempt_{$staff->id}");
        if ($recentAttempt) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before attempting to clock in again.'
            ], 429);
        }

        // Set rate limit cache (1 minute)
        cache()->put("clock_in_attempt_{$staff->id}", true, 60);

        try {
            // Use database transaction for atomic operation
            return DB::transaction(function () use ($staff, $today, $request, $now) {
                // Check if already clocked in today (with lock to prevent race conditions)
                $existingAttendance = $staff->attendances()
                    ->whereDate('date', $today)
                    ->lockForUpdate()
                    ->first();

                if ($existingAttendance && $existingAttendance->clock_in_time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already clocked in today at ' .
                                   Carbon::parse($existingAttendance->clock_in_time)->format('h:i A') . '.'
                    ], 400);
                }

                // Check for incomplete record from previous day and fix it
                $incompleteRecord = $staff->attendances()
                    ->whereNotNull('clock_in_time')
                    ->whereNull('clock_out_time')
                    ->where('date', '<', $today)
                    ->orderBy('date', 'desc')
                    ->first();

                if ($incompleteRecord) {
                    // Auto-complete the previous incomplete record
                    $this->autoCompleteRecord($incompleteRecord);
                }

                $attendance = Attendance::updateOrCreate(
                    [
                        'staff_id' => $staff->id,
                        'date' => $today,
                    ],
                    [
                        'clock_in_time' => $now->format('H:i:s'),
                        'clock_in_latitude' => $request->latitude,
                        'clock_in_longitude' => $request->longitude,
                        'clock_in_address' => $request->address,
                        'status' => 'present',
                    ]
                );

                // Log the clock-in event
                Log::info('Staff clocked in', [
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->full_name,
                    'time' => $now->format('Y-m-d H:i:s'),
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'address' => $request->address
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Clocked in successfully at ' . $now->format('h:i A'),
                    'attendance' => $attendance->fresh()
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Clock in error: ' . $e->getMessage(), [
                'staff_id' => $staff->id,
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error clocking in. Please try again or contact IT support.'
            ], 500);
        }
    }

    /**
     * Clock out
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('staff')->user();
        $now = now();
        $today = $now->toDateString();

        // Enhanced validation: check for suspicious coordinates (exact 0,0)
        if ($request->latitude == 0 && $request->longitude == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid location coordinates. Please ensure location services are enabled.'
            ], 400);
        }

        // Rate limiting check (prevent multiple requests within 1 minute)
        $recentAttempt = cache()->get("clock_out_attempt_{$staff->id}");
        if ($recentAttempt) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before attempting to clock out again.'
            ], 429);
        }

        // Set rate limit cache (1 minute)
        cache()->put("clock_out_attempt_{$staff->id}", true, 60);

        try {
            // Use database transaction for atomic operation
            return DB::transaction(function () use ($staff, $today, $request, $now) {
                $attendance = $staff->attendances()
                    ->whereDate('date', $today)
                    ->lockForUpdate()
                    ->first();

                if (!$attendance || !$attendance->clock_in_time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You must clock in first before clocking out.'
                    ], 400);
                }

                if ($attendance->clock_out_time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already clocked out today at ' .
                                   Carbon::parse($attendance->clock_out_time)->format('h:i A') . '.'
                    ], 400);
                }

                // Create proper Carbon instances for calculation
                $clockInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date->format('Y-m-d') . ' ' . $attendance->clock_in_time);
                $clockOutDateTime = $now;

                // Validate minimum work duration (at least 30 minutes)
                $workMinutes = $clockInDateTime->diffInMinutes($clockOutDateTime);
                if ($workMinutes < 30) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum work duration is 30 minutes. You have worked for ' . $workMinutes . ' minutes.'
                    ], 400);
                }

                // Calculate total hours with precision
                $totalHours = $workMinutes / 60;
                $totalHours = round($totalHours, 2); // Round to 2 decimal places

                // Validate maximum daily hours (16 hours)
                if ($totalHours > 16) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum daily work hours exceeded. Please contact your supervisor.'
                    ], 400);
                }

                $attendance->update([
                    'clock_out_time' => $now->format('H:i:s'),
                    'clock_out_latitude' => $request->latitude,
                    'clock_out_longitude' => $request->longitude,
                    'clock_out_address' => $request->address,
                    'total_hours' => $totalHours,
                ]);

                // Log the clock-out event
                Log::info('Staff clocked out', [
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->full_name,
                    'clock_in_time' => $clockInDateTime->format('Y-m-d H:i:s'),
                    'clock_out_time' => $now->format('Y-m-d H:i:s'),
                    'total_hours' => $totalHours,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'address' => $request->address
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Clocked out successfully at ' . $now->format('h:i A') .
                               '. Total hours worked: ' . number_format($totalHours, 1) . 'h',
                    'total_hours' => $totalHours,
                    'attendance' => $attendance->fresh()
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Clock out error: ' . $e->getMessage(), [
                'staff_id' => $staff->id,
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error clocking out. Please try again or contact IT support.'
            ], 500);
        }
    }

    /**
     * Show attendance history
     */
    public function history(Request $request)
    {
        /** @var Staff $staff */
        $staff = Auth::guard('staff')->user();

        $query = $staff->attendances()->orderBy('date', 'desc');

        // Filter by month if provided
        if ($request->has('month') && $request->month) {
            $date = Carbon::createFromFormat('Y-m', $request->month);
            $query->whereMonth('date', $date->month)
                  ->whereYear('date', $date->year);
        }

        $attendances = $query->paginate(20);

        // Calculate summary statistics
        $summary = [
            'total_days' => $query->count(),
            'present_days' => $query->where('status', 'present')->count(),
            'total_hours' => $query->whereNotNull('total_hours')->sum('total_hours'),
            'average_hours' => $query->whereNotNull('total_hours')->avg('total_hours'),
        ];

        return view('staff.attendance.history', compact(
            'attendances',
            'summary',
            'staff'
        ));
    }

    /**
     * Fix incomplete attendance records from previous days
     */
    private function fixIncompleteRecords(Staff $staff)
    {
        try {
            $incompleteRecords = $staff->attendances()
                ->whereNotNull('clock_in_time')
                ->whereNull('clock_out_time')
                ->where('date', '<', today())
                ->get();

            foreach ($incompleteRecords as $record) {
                $this->autoCompleteRecord($record);
            }
        } catch (\Exception $e) {
            Log::error('Error fixing incomplete records: ' . $e->getMessage(), [
                'staff_id' => $staff->id,
                'exception' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Auto-complete a single incomplete attendance record
     */
    private function autoCompleteRecord(Attendance $record)
    {
        try {
            // Estimate clock-out time as 8 hours after clock-in (standard work day)
            $clockInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $record->date->format('Y-m-d') . ' ' . $record->clock_in_time);
            $estimatedClockOut = $clockInDateTime->copy()->addHours(8);

            // If that would be past end of day, set to end of business day (6 PM)
            if ($estimatedClockOut->hour > 18) {
                $estimatedClockOut = $clockInDateTime->copy()->setTime(18, 0, 0);
            }

            $totalHours = $clockInDateTime->diffInHours($estimatedClockOut, true);

            $record->update([
                'clock_out_time' => $estimatedClockOut->format('H:i:s'),
                'total_hours' => round($totalHours, 2),
                'remarks' => 'Auto-completed: Staff forgot to clock out'
            ]);

            Log::info('Fixed incomplete attendance record', [
                'staff_id' => $record->staff_id,
                'staff_name' => $record->staff->full_name,
                'date' => $record->date->format('Y-m-d'),
                'estimated_clock_out' => $estimatedClockOut->format('H:i:s'),
                'total_hours' => $totalHours
            ]);
        } catch (\Exception $e) {
            Log::error('Error auto-completing record: ' . $e->getMessage(), [
                'attendance_id' => $record->id,
                'exception' => $e->getTraceAsString()
            ]);
        }
    }
}
