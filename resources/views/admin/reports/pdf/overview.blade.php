@extends('admin.reports.pdf.layout')

@section('title', 'Overview Report')

@section('content')
    <div class="section">
        <div class="section-title">Summary</div>
        <table class="stat-grid">
            <tr>
                <td>
                    <div class="stat-value">{{ $totalStaff }}</div>
                    Active Staff
                </td>
                <td>
                    <div class="stat-value">{{ $totalPositions }}</div>
                    Positions
                </td>
                <td>
                    <div class="stat-value">{{ $attendanceStats['attendance_rate'] }}%</div>
                    Attendance Rate
                </td>
                <td>
                    <div class="stat-value">{{ $trackerStats['submission_rate'] }}%</div>
                    Submission Rate
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Attendance</div>
        <table>
            <tr><th>Metric</th><th>Value</th></tr>
            <tr><td>Working Days in Period</td><td>{{ $attendanceStats['total_working_days'] }}</td></tr>
            <tr><td>Total Attendance Records</td><td>{{ $attendanceStats['total_attendance'] }}</td></tr>
            <tr><td>Present (On Time)</td><td>{{ $attendanceStats['present_days'] }}</td></tr>
            <tr><td>Late Arrivals</td><td>{{ $attendanceStats['late_days'] }}</td></tr>
            <tr><td>Average Hours per Day</td><td>{{ $attendanceStats['average_hours'] }}h</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Weekly Trackers</div>
        <table>
            <tr><th>Metric</th><th>Value</th></tr>
            <tr><td>Weeks in Period</td><td>{{ $trackerStats['total_weeks'] }}</td></tr>
            <tr><td>Submitted Trackers</td><td>{{ $trackerStats['submitted_trackers'] }}</td></tr>
            <tr><td>Pending Review</td><td>{{ $trackerStats['pending_trackers'] }}</td></tr>
            <tr><td>Approved</td><td>{{ $trackerStats['approved_trackers'] }}</td></tr>
        </table>
    </div>

    @if($positionStats->count() > 0)
    <div class="section">
        <div class="section-title">Position Breakdown</div>
        <table>
            <tr>
                <th>Position</th>
                <th class="text-center">Staff</th>
                <th class="text-center">Attendance Records</th>
                <th class="text-center">Avg / Staff</th>
            </tr>
            @foreach($positionStats as $row)
            <tr>
                <td>{{ $row['position'] }}</td>
                <td class="text-center">{{ $row['staff_count'] }}</td>
                <td class="text-center">{{ $row['total_attendance'] }}</td>
                <td class="text-center">{{ $row['avg_attendance_per_staff'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
@endsection
