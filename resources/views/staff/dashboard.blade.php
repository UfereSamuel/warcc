@extends('layouts.staff')

@section('title', 'Dashboard')
@section('page-title', 'Staff Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Admin Access Banner for promoted admins -->
@if(auth()->guard('staff')->user()->is_admin && auth()->guard('staff')->user()->email !== 'admin@africacdc.org')
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-cogs"></i> Admin Access Available</h5>
            You have administrative privileges. 
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-info ml-2">
                <i class="fas fa-tools mr-1"></i>
                Go to Admin Dashboard
            </a>
        </div>
    </div>
</div>
@endif

<!-- Quick Actions Row -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card attendance-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>
                    Today's Attendance
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ now()->format('l, F j, Y') }}</span>
                </div>
            </div>
            <div class="card-body">
                @if($todayAttendance)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-sign-in-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Clock In</span>
                                    <span class="info-box-number">
                                        {{ $todayAttendance->clock_in_time ? \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A') : 'Not clocked in' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-sign-out-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Clock Out</span>
                                    <span class="info-box-number">
                                        {{ $todayAttendance->clock_out_time ? \Carbon\Carbon::parse($todayAttendance->clock_out_time)->format('h:i A') : 'Not clocked out' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-hourglass-half"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Hours</span>
                                    <span class="info-box-number">
                                        {{ $todayAttendance->total_hours ? number_format($todayAttendance->total_hours, 1) . 'h' : 'In progress' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon {{ $todayAttendance->status === 'present' ? 'bg-success' : 'bg-danger' }}">
                                    <i class="fas {{ $todayAttendance->status === 'present' ? 'fa-check' : 'fa-times' }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number status-{{ $todayAttendance->status }}">
                                        {{ ucfirst($todayAttendance->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No attendance record for today</h4>
                        <p class="text-muted">Click the button below to start your day</p>
                    </div>
                @endif

                <div class="text-center mt-3">
                    <a href="{{ route('staff.attendance.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-clock mr-2"></i>
                        Manage Attendance
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Row -->
<div class="row">
    <!-- Attendance Summary -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $attendanceSummary['present_days'] }}<sup style="font-size: 20px">/ {{ $attendanceSummary['total_days'] }}</sup></h3>
                <p>Days Present This Month</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <a href="{{ route('staff.attendance.history') }}" class="small-box-footer">
                View History <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Total Hours -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($attendanceSummary['total_hours'], 1) }}<sup style="font-size: 20px">h</sup></h3>
                <p>Total Hours This Month</p>
            </div>
            <div class="icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <a href="{{ route('staff.attendance.history') }}" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Pending Missions -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pendingMissions }}</h3>
                <p>Pending Missions</p>
            </div>
            <div class="icon">
                <i class="fas fa-plane"></i>
            </div>
            <a href="{{ route('staff.tracker.index') }}" class="small-box-footer">
                View Missions <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Leave Balance -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $leaveBalance - $usedLeave }}<sup style="font-size: 20px">/ {{ $leaveBalance }}</sup></h3>
                <p>Leave Days Remaining</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <a href="{{ route('staff.tracker.index') }}" class="small-box-footer">
                Request Leave <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Activity Requests Overview -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Activity Requests
                </h3>
                <div class="card-tools">
                    <a href="{{ route('staff.activity-requests.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i>New Request
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-info">
                                <i class="fas fa-list"></i>
                            </span>
                            <h5 class="description-header">{{ $activityRequestStats['total'] }}</h5>
                            <span class="description-text">Total Requests</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-warning">
                                <i class="fas fa-clock"></i>
                            </span>
                            <h5 class="description-header">{{ $activityRequestStats['pending'] }}</h5>
                            <span class="description-text">Pending Review</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <h5 class="description-header">{{ $activityRequestStats['approved'] }}</h5>
                            <span class="description-text">Approved</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="description-block">
                            <span class="description-percentage text-danger">
                                <i class="fas fa-times-circle"></i>
                            </span>
                            <h5 class="description-header">{{ $activityRequestStats['rejected'] }}</h5>
                            <span class="description-text">Rejected</span>
                        </div>
                    </div>
                </div>
                @if($activityRequestStats['pending'] > 0)
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        You have {{ $activityRequestStats['pending'] }} pending activity request{{ $activityRequestStats['pending'] > 1 ? 's' : '' }}.
                        <a href="{{ route('staff.activity-requests.index') }}" class="alert-link">View all requests</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Weekly Tracker -->
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-week mr-2"></i>
                    Current Week Tracker
                </h3>
            </div>
            <div class="card-body">
                @if($currentWeekTracker)
                    <div class="row">
                        <div class="col-6">
                            <div class="description-block">
                                <h5 class="description-header">{{ $currentWeekTracker->total_activities }}</h5>
                                <span class="description-text">Activities Planned</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <h5 class="description-header">{{ $currentWeekTracker->completed_activities }}</h5>
                                <span class="description-text">Completed</span>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-success" style="width: {{ $currentWeekTracker->completion_percentage }}%"></div>
                    </div>
                    <p class="text-muted mt-2">
                        Week {{ $currentWeekTracker->week_number }}, {{ $currentWeekTracker->year }} -
                        {{ number_format($currentWeekTracker->completion_percentage, 1) }}% Complete
                    </p>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-plus fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No weekly tracker for this week</p>
                        <a href="{{ route('staff.tracker.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Create Weekly Tracker
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bell mr-2"></i>
                    Recent Activities & Announcements
                </h3>
            </div>
            <div class="card-body">
                @if($recentActivities->count() > 0)
                    <div class="timeline">
                        @foreach($recentActivities as $activity)
                        <div class="time-label">
                            <span class="bg-green">{{ $activity->start_date->format('M d') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-calendar bg-blue"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">{{ $activity->title }}</h3>
                                <div class="timeline-body">
                                    {{ Str::limit($activity->description, 100) }}
                                </div>
                                <div class="timeline-footer">
                                    <span class="badge badge-{{ $activity->status === 'active' ? 'success' : ($activity->status === 'ongoing' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($activity->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No recent activities</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-link mr-2"></i>
                    Quick Links
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <a href="{{ route('staff.attendance.index') }}" class="btn btn-app">
                            <i class="fas fa-clock"></i>
                            Clock In/Out
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('staff.attendance.history') }}" class="btn btn-app">
                            <i class="fas fa-history"></i>
                            Attendance History
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('staff.tracker.index') }}" class="btn btn-app">
                            <i class="fas fa-calendar-check"></i>
                            Weekly Tracker
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('staff.activity-requests.index') }}" class="btn btn-app">
                            <i class="fas fa-calendar-plus"></i>
                            Activity Requests
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('staff.calendar.index') }}" class="btn btn-app">
                            <i class="fas fa-calendar-alt"></i>
                            View Calendar
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('staff.profile') }}" class="btn btn-app">
                            <i class="fas fa-user-cog"></i>
                            Update Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
