@extends('adminlte::page')

@section('title', 'Daily Attendance Report')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Daily Attendance Report</h1>
            <p class="text-muted">Detailed attendance analysis for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Attendance</a></li>
                <li class="breadcrumb-item active">Daily Report</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Filter Controls -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Report Filters
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.daily-report') }}" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date">Report Date</label>
                                <input type="date" class="form-control" id="date" name="date"
                                       value="{{ $date }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <select class="form-control" id="department" name="department">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept }}" {{ $department == $dept ? 'selected' : '' }}>
                                            {{ $dept }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-chart-bar mr-1"></i> Generate Report
                                    </button>
                                    <a href="{{ route('admin.attendance.export', ['date' => $date, 'department' => $department]) }}"
                                       class="btn btn-success ml-2">
                                        <i class="fas fa-download mr-1"></i> Export CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Department Summary -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-building mr-2"></i>
                    Department Attendance Summary
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                @if($departmentSummary->count() > 0)
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Department</th>
                                <th class="text-center">Total Staff</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Late</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Attendance Rate</th>
                                <th class="text-center">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departmentSummary as $dept)
                                <tr>
                                    <td>
                                        <strong>{{ $dept['department'] }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $dept['total_staff'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $dept['present'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">{{ $dept['late'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-danger">{{ $dept['absent'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar
                                                @if($dept['attendance_rate'] >= 90) bg-success
                                                @elseif($dept['attendance_rate'] >= 80) bg-info
                                                @elseif($dept['attendance_rate'] >= 70) bg-warning
                                                @else bg-danger
                                                @endif"
                                                style="width: {{ $dept['attendance_rate'] }}%">
                                                {{ $dept['attendance_rate'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($dept['attendance_rate'] >= 90)
                                            <span class="badge badge-success">Excellent</span>
                                        @elseif($dept['attendance_rate'] >= 80)
                                            <span class="badge badge-info">Good</span>
                                        @elseif($dept['attendance_rate'] >= 70)
                                            <span class="badge badge-warning">Fair</span>
                                        @else
                                            <span class="badge badge-danger">Poor</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-building fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Department Data</h4>
                        <p class="text-muted">No department attendance data found for this date.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Detailed Attendance Records -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>
                    Detailed Attendance Records
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $attendances->count() }} Records</span>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($attendances->count() > 0)
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Staff Member</th>
                                <th>Department</th>
                                <th class="text-center">Clock In</th>
                                <th class="text-center">Clock Out</th>
                                <th class="text-center">Break Duration</th>
                                <th class="text-center">Total Hours</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Overtime</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $index => $attendance)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="media align-items-center">
                                            <img src="{{ $attendance->staff->profile_picture_url }}"
                                                 alt="{{ $attendance->staff->full_name }}"
                                                 class="img-circle mr-2"
                                                 style="width: 30px; height: 30px;">
                                            <div class="media-body">
                                                <h6 class="mb-0">{{ $attendance->staff->full_name }}</h6>
                                                <small class="text-muted">{{ $attendance->staff->staff_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $attendance->staff->department }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($attendance->clock_in_time)
                                            <strong class="text-success">
                                                {{ \Carbon\Carbon::parse($attendance->clock_in_time)->format('h:i A') }}
                                            </strong>
                                            @if(\Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') > '08:30')
                                                <br><small class="text-warning">(Late)</small>
                                            @endif
                                        @else
                                            <span class="text-muted">--:--</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($attendance->clock_out_time)
                                            <strong class="text-danger">
                                                {{ \Carbon\Carbon::parse($attendance->clock_out_time)->format('h:i A') }}
                                            </strong>
                                        @else
                                            <span class="text-warning">Still In</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($attendance->break_duration)
                                            {{ $attendance->break_duration }} min
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($attendance->total_hours)
                                            <span class="badge
                                                @if($attendance->total_hours >= 8) badge-success
                                                @elseif($attendance->total_hours >= 6) badge-info
                                                @elseif($attendance->total_hours >= 4) badge-warning
                                                @else badge-danger
                                                @endif">
                                                {{ number_format($attendance->total_hours, 1) }}h
                                            </span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @switch($attendance->status)
                                            @case('present')
                                                <span class="badge badge-success">Present</span>
                                                @break
                                            @case('late')
                                                <span class="badge badge-warning">Late</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        @if($attendance->total_hours && $attendance->total_hours > 8)
                                            <span class="badge badge-primary">
                                                +{{ number_format($attendance->total_hours - 8, 1) }}h
                                            </span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->clock_in_address)
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ Str::limit($attendance->clock_in_address, 30) }}
                                            </small>
                                        @else
                                            <span class="text-muted">No location</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clock fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Attendance Records</h4>
                        <p class="text-muted">No attendance records found for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .media {
            align-items: center;
        }
        .badge {
            font-size: 0.85rem;
        }
        .table th {
            font-weight: 600;
            color: white;
            border-top: none;
        }
        .progress {
            background-color: #e9ecef;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.02);
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-submit on date/department change
        $('#date, #department').change(function() {
            $(this).closest('form').submit();
        });
    </script>
@stop
