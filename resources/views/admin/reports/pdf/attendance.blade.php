@extends('admin.reports.pdf.layout')

@section('title', 'Attendance Report')

@section('content')
    <div class="section">
        <div class="section-title">Attendance Summary</div>
        <table>
            <tr><th>Metric</th><th>Value</th></tr>
            <tr><td>Total Records</td><td>{{ $attendanceStats['total_attendance'] }}</td></tr>
            <tr><td>Present (On Time)</td><td>{{ $attendanceStats['present_days'] }}</td></tr>
            <tr><td>Late Arrivals</td><td>{{ $attendanceStats['late_days'] }}</td></tr>
            <tr><td>Average Hours</td><td>{{ $attendanceStats['average_hours'] }}h</td></tr>
            <tr><td>Attendance Rate</td><td>{{ $attendanceStats['attendance_rate'] }}%</td></tr>
        </table>
    </div>

    @if($dailyAttendance->count() > 0)
    <div class="section">
        <div class="section-title">Daily Trends</div>
        <table>
            <tr><th>Date</th><th class="text-center">Attendance Count</th></tr>
            @foreach($dailyAttendance as $day)
            <tr>
                <td>{{ \Carbon\Carbon::parse($day->attendance_date)->format('M d, Y') }}</td>
                <td class="text-center">{{ $day->total_attendance }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if($staffRankings->count() > 0)
    <div class="section">
        <div class="section-title">Top Attendance Leaders</div>
        <table>
            <tr>
                <th>#</th>
                <th>Staff</th>
                <th>Position</th>
                <th class="text-center">Days</th>
                <th class="text-center">Late</th>
                <th class="text-center">Punctuality</th>
            </tr>
            @foreach($staffRankings as $index => $ranking)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $ranking['staff']->full_name }}</td>
                <td>{{ $ranking['staff']->position?->title ?? 'Unassigned' }}</td>
                <td class="text-center">{{ $ranking['attendance_count'] }}</td>
                <td class="text-center">{{ $ranking['late_count'] }}</td>
                <td class="text-center">{{ $ranking['punctuality_rate'] }}%</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
@endsection
