@extends('adminlte::page')

@section('title', 'Staff Profile - ' . $staff->full_name)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Staff Profile</h1>
                            <p class="text-muted">{{ $staff->full_name }} - {{ $staff->position_title }}</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staff</a></li>
                <li class="breadcrumb-item active">{{ $staff->full_name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Staff Profile Card -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ $staff->profile_picture_url }}"
                         alt="{{ $staff->full_name }}"
                         style="width: 120px; height: 120px; object-fit: cover;">
                </div>

                <h3 class="profile-username text-center">{{ $staff->full_name }}</h3>

                <p class="text-muted text-center">
                                                    {{ $staff->position_title }}
                    @if($staff->is_admin)
                        <span class="badge badge-warning ml-1">Administrator</span>
                    @endif
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Staff ID</b> <a class="float-right">{{ $staff->staff_id }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right">{{ $staff->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b> <a class="float-right">{{ $staff->phone ?? 'Not provided' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Gender</b> <a class="float-right">{{ ucfirst($staff->gender ?? 'Not specified') }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Department</b> <a class="float-right">{{ $staff->department }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Hire Date</b> <a class="float-right">{{ $staff->hire_date ? $staff->hire_date->format('M d, Y') : 'Not set' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b>
                        <span class="float-right">
                            @if($staff->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Leave Balance</b>
                        <span class="float-right">
                            <span class="text-bold text-primary">{{ $staff->annual_leave_balance }}</span> days
                        </span>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    </div>
                    <div class="col-6">
                        @if($staff->is_admin)
                            <form action="{{ route('admin.staff.demote', $staff) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-secondary btn-block"
                                        onclick="return confirm('Remove admin privileges?')">
                                    <i class="fas fa-user-minus mr-1"></i> Demote
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.staff.promote', $staff) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-block"
                                        onclick="return confirm('Promote to administrator?')">
                                    <i class="fas fa-user-plus mr-1"></i> Promote
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Balance Management -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Manage Leave Balance
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.staff.leave-balance', $staff) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="annual_leave_balance">Annual Leave Balance (days)</label>
                        <input type="number" class="form-control"
                               id="annual_leave_balance"
                               name="annual_leave_balance"
                               value="{{ $staff->annual_leave_balance }}"
                               min="0" max="50" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Update Balance
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance Statistics and Recent Activity -->
    <div class="col-md-8">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Days</span>
                        <span class="info-box-number">{{ $attendanceStats['total_days'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Present</span>
                        <span class="info-box-number">{{ $attendanceStats['present_days'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Late Days</span>
                        <span class="info-box-number">{{ $attendanceStats['late_days'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-stopwatch"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Avg Hours</span>
                        <span class="info-box-number">{{ number_format($attendanceStats['average_hours'], 1) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>
                    Recent Attendance (Last 30 Days)
                </h3>
            </div>
            <div class="card-body">
                @if($recentAttendance->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttendance as $attendance)
                                    <tr>
                                        <td>{{ $attendance->date->format('M d, Y') }}</td>
                                        <td>{{ $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('g:i A') : '-' }}</td>
                                        <td>{{ $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('g:i A') : '-' }}</td>
                                        <td>{{ $attendance->total_hours ?? '-' }}h</td>
                                        <td>
                                            @if($attendance->status === 'present')
                                                <span class="badge badge-success">Present</span>
                                            @elseif($attendance->status === 'late')
                                                <span class="badge badge-warning">Late</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No attendance records found.</p>
                @endif
            </div>
        </div>

        <!-- Active Missions and Pending Leaves -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plane mr-2"></i>
                            Active Missions
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($activeMissions->count() > 0)
                            @foreach($activeMissions as $mission)
                                <div class="callout callout-info">
                                    <h5>{{ $mission->title }}</h5>
                                    <p class="text-sm">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $mission->start_date->format('M d') }} - {{ $mission->end_date->format('M d, Y') }}
                                    </p>
                                    <p class="text-sm">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $mission->destination }}
                                    </p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No active missions.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-times mr-2"></i>
                            Pending Leave Requests
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($pendingLeaves->count() > 0)
                            @foreach($pendingLeaves as $leave)
                                <div class="callout callout-warning">
                                    <h5>{{ $leave->leaveType->name ?? 'Leave Request' }}</h5>
                                    <p class="text-sm">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                    </p>
                                    <p class="text-sm">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $leave->days_requested }} days requested
                                    </p>
                                    <div class="mt-2">
                                        <a href="{{ route('admin.leaves.approve', $leave) }}"
                                           class="btn btn-success btn-xs mr-1">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </a>
                                        <a href="{{ route('admin.leaves.reject', $leave) }}"
                                           class="btn btn-danger btn-xs">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No pending leave requests.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .profile-user-img {
            border: 3px solid #adb5bd;
            margin: 0 auto;
            padding: 3px;
        }

        .info-box {
            margin-bottom: 1rem;
        }

        .callout {
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
    </style>
@stop
