@extends('adminlte::page')

@section('title', 'Analytics Dashboard')

@section('content_header')
    <div class="dashboard-header">
        <div class="header-content">
            <div class="header-info">
                <h1 class="dashboard-title">
                    <i class="fas fa-chart-line text-primary mr-3"></i>
                    Analytics Dashboard
                </h1>
                <p class="dashboard-subtitle">
                    <i class="fas fa-user-circle mr-2"></i>
                    Welcome back, <strong>{{ auth()->guard('staff')->user()->full_name }}</strong>
                    <span class="text-muted ml-2">• {{ now()->format('F j, Y') }}</span>
                </p>
            </div>
            <div class="header-actions">
                <div class="export-dropdown dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown">
                        <i class="fas fa-download mr-2"></i>Export Data
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <h6 class="dropdown-header">
                            <i class="fas fa-file-export mr-1"></i>Export Options
                        </h6>
                        <a class="dropdown-item" href="{{ route('admin.export.dashboard-analytics', ['format' => 'csv']) }}">
                            <i class="fas fa-file-csv mr-2 text-success"></i>Dashboard Analytics (CSV)
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.export.attendance', ['format' => 'csv']) }}">
                            <i class="fas fa-clock mr-2 text-info"></i>Attendance Data (CSV)
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.export.weekly-trackers', ['format' => 'csv']) }}">
                            <i class="fas fa-calendar-week mr-2 text-warning"></i>Weekly Trackers (CSV)
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="openExportModal()">
                            <i class="fas fa-cog mr-2 text-secondary"></i>Custom Export
                        </a>
                    </div>
                </div>
                <button class="btn btn-outline-secondary ml-2" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
            </div>
        </div>
    </div>
@stop

@section('content')
<!-- Key Performance Indicators -->
<div class="analytics-grid">
    <!-- Staff Overview -->
    <div class="analytics-card primary">
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-title">Total Staff</div>
        </div>
        <div class="card-body">
            <div class="metric-value">{{ $staffStats['total'] }}</div>
            <div class="metric-breakdown">
                <span class="breakdown-item">
                    <i class="fas fa-user-tie"></i> {{ $staffStats['admins'] }} Admins
                </span>
                <span class="breakdown-item">
                    <i class="fas fa-users"></i> {{ $staffStats['regular'] }} Staff
                </span>
            </div>
        </div>
    </div>

    <!-- Attendance Today -->
    <div class="analytics-card success">
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="card-title">Today's Attendance</div>
        </div>
        <div class="card-body">
            <div class="metric-value">{{ $attendanceStats['today_present'] }}</div>
            <div class="metric-subtitle">
                of {{ $attendanceStats['today_total'] }} active staff
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $attendanceStats['today_total'] > 0 ? round(($attendanceStats['today_present'] / $attendanceStats['today_total']) * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Weekly Trackers -->
    <div class="analytics-card warning">
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="card-title">Weekly Trackers</div>
        </div>
        <div class="card-body">
            <div class="metric-value">{{ $weeklyTrackerStats['completion_rate'] }}%</div>
            <div class="metric-subtitle">Completion Rate</div>
            <div class="metric-detail">
                {{ $weeklyTrackerStats['this_week_submitted'] }} submitted, 
                {{ $weeklyTrackerStats['this_week_pending'] }} pending
            </div>
        </div>
    </div>

    <!-- Attendance Rate -->
    <div class="analytics-card info">
        <div class="card-header">
            <div class="card-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="card-title">Attendance Rate</div>
        </div>
        <div class="card-body">
            <div class="metric-value">{{ $attendanceStats['week_completion'] }}%</div>
            <div class="metric-subtitle">This Week</div>
            <div class="metric-trend">
                <i class="fas fa-arrow-up text-success"></i>
                Month avg: {{ $attendanceStats['month_average'] }}%
            </div>
        </div>
    </div>
</div>

<!-- Staff Status Distribution -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-chart-bar mr-2"></i>Attendance Trends (Last 7 Days)
                </h3>
                <div class="chart-actions">
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('attendanceChart')">
                        <i class="fas fa-download mr-1"></i>Download
                    </button>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="attendanceChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-chart-pie mr-2"></i>Staff Status
                </h3>
            </div>
            <div class="chart-body">
                <canvas id="staffStatusChart" height="300"></canvas>
            </div>
            <div class="status-legend">
                <div class="legend-item">
                    <span class="legend-color" style="background: #28a745;"></span>
                    <span class="legend-text">At Office ({{ $staffStatusData['at_office'] }})</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color" style="background: #ffc107;"></span>
                    <span class="legend-text">On Mission ({{ $staffStatusData['on_mission'] }})</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color" style="background: #17a2b8;"></span>
                    <span class="legend-text">On Leave ({{ $staffStatusData['on_leave'] }})</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Tracker & Gender Analytics -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title text-bold">Weekly Tracker Submissions</h3>
                    <a href="{{ route('admin.staff.index') }}">View Details</a>
                </div>
            </div>
            <div class="card-body">
                <div class="position-relative mb-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="description-block border-right">
                                @php
                                    $startOfWeek = now()->startOfWeek();
                                    $submittedCount = \App\Models\WeeklyTracker::whereDate('week_start_date', $startOfWeek)
                                        ->where('status', '!=', 'draft')->count();
                                    $totalStaff = \App\Models\Staff::where('status', 'active')->count();
                                    $submissionRate = $totalStaff > 0 ? round(($submittedCount / $totalStaff) * 100) : 0;
                                @endphp
                                <span class="description-percentage text-success">
                                    <i class="fas fa-caret-up"></i> {{ $submissionRate }}%
                                </span>
                                <h5 class="description-header text-bold">{{ $submittedCount }}</h5>
                                <span class="description-text">Submitted This Week</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <span class="description-percentage text-warning">
                                    <i class="fas fa-caret-down"></i> {{ 100 - $submissionRate }}%
                                </span>
                                <h5 class="description-header text-bold">{{ $totalStaff - $submittedCount }}</h5>
                                <span class="description-text">Pending Submission</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-success" style="width: {{ $submissionRate }}%"></div>
                </div>
                <span class="progress-description">
                    Week of {{ $startOfWeek->format('M d') }} - {{ $startOfWeek->copy()->endOfWeek()->format('M d, Y') }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title text-bold">Staff Gender Distribution</h3>
                    <a href="{{ route('admin.staff.index') }}">View All Staff</a>
                </div>
            </div>
            <div class="card-body">
                <div class="position-relative mb-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="description-block border-right">
                                @php
                                    $maleCount = \App\Models\Staff::where('gender', 'male')->count();
                                    $malePercentage = $totalStaff > 0 ? round(($maleCount / $totalStaff) * 100) : 0;
                                @endphp
                                <span class="description-percentage text-primary">
                                    <i class="fas fa-male"></i> {{ $malePercentage }}%
                                </span>
                                <h5 class="description-header text-bold">{{ $maleCount }}</h5>
                                <span class="description-text">Male Staff</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                @php
                                    $femaleCount = \App\Models\Staff::where('gender', 'female')->count();
                                    $femalePercentage = $totalStaff > 0 ? round(($femaleCount / $totalStaff) * 100) : 0;
                                @endphp
                                <span class="description-percentage text-danger">
                                    <i class="fas fa-female"></i> {{ $femalePercentage }}%
                                </span>
                                <h5 class="description-header text-bold">{{ $femaleCount }}</h5>
                                <span class="description-text">Female Staff</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-primary" style="width: {{ $malePercentage }}%"></div>
                    <div class="progress-bar bg-danger" style="width: {{ $femalePercentage }}%"></div>
                </div>
                <span class="progress-description">
                    Gender diversity across all departments
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Today's Activity & Pending Requests -->
<div class="row mb-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title text-bold">Today's Attendance</h3>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12">
                        @php
                            $todayAttendance = \App\Models\Attendance::whereDate('date', today())->count();
                            $attendanceRate = $totalStaff > 0 ? round(($todayAttendance / $totalStaff) * 100) : 0;
                        @endphp
                        <div class="text-center">
                            <h2 class="text-bold text-primary">{{ $todayAttendance }}/{{ $totalStaff }}</h2>
                            <p class="text-muted">Staff checked in today</p>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: {{ $attendanceRate }}%"></div>
                            </div>
                            <p class="text-sm text-muted mt-2">{{ $attendanceRate }}% attendance rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title text-bold">Pending Leave Requests</h3>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12">
                        @php $pendingLeaves = \App\Models\LeaveRequest::where('status', 'pending')->count(); @endphp
                        <div class="text-center">
                            <h2 class="text-bold text-warning">{{ $pendingLeaves }}</h2>
                            <p class="text-muted">Requests awaiting approval</p>
                            @if($pendingLeaves > 0)
                                <a href="{{ route('admin.leaves.index') }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-eye mr-1"></i> Review Requests
                                </a>
                            @else
                                <p class="text-success"><i class="fas fa-check"></i> All caught up!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title text-bold">Active Hero Slides</h3>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12">
                        @php $activeSlides = \App\Models\HeroSlide::active()->count(); @endphp
                        <div class="text-center">
                            <h2 class="text-bold text-success">{{ $activeSlides }}</h2>
                            <p class="text-muted">Currently published</p>
                            <a href="{{ route('admin.content.hero-slides.index') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-images mr-1"></i> Manage Slides
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Requests Section -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title text-bold">Activity Requests Overview</h3>
                    <a href="{{ route('admin.activity-requests.index') }}">View All Requests</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-info">
                                <i class="fas fa-list"></i>
                            </span>
                            <h5 class="description-header text-bold">{{ $activityRequestStats['total'] }}</h5>
                            <span class="description-text">Total Requests</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-warning">
                                <i class="fas fa-clock"></i>
                            </span>
                            <h5 class="description-header text-bold">{{ $activityRequestStats['pending'] }}</h5>
                            <span class="description-text">Pending Review</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <h5 class="description-header text-bold">{{ $activityRequestStats['approved'] }}</h5>
                            <span class="description-text">Approved</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="description-block">
                            <span class="description-percentage text-danger">
                                <i class="fas fa-times-circle"></i>
                            </span>
                            <h5 class="description-header text-bold">{{ $activityRequestStats['rejected'] }}</h5>
                            <span class="description-text">Rejected</span>
                        </div>
                    </div>
                </div>

                @if($activityRequestStats['pending'] > 0)
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle mr-2"></i>{{ $activityRequestStats['pending'] }} Pending Request{{ $activityRequestStats['pending'] > 1 ? 's' : '' }}</h5>
                        <p class="mb-0">Review and approve or reject staff activity proposals to keep the workflow moving.</p>
                    </div>
                @else
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle mr-2"></i>All Caught Up!</h5>
                        <p class="mb-0">No pending activity requests to review at this time.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title text-bold">Recent Activity Requests</h3>
            </div>
            <div class="card-body">
                @if($recentActivityRequests->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentActivityRequests as $request)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($request->title, 30) }}</h6>
                                    <p class="mb-1 text-muted small">
                                        <i class="fas fa-user me-1"></i>{{ $request->requester->full_name }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d') }}
                                    </small>
                                </div>
                                <span class="badge bg-warning">Pending</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.activity-requests.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list me-1"></i>Review All
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-plus fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No recent requests</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-light border-0">
                <h3 class="card-title mb-0">
                    <i class="fas fa-rocket mr-2 text-primary"></i>
                    <strong>Quick Actions</strong>
                </h3>
                <small class="text-muted d-block mt-1">Fast access to common tasks</small>
            </div>
            <div class="card-body bg-light">
                <div class="btn-group-vertical w-100" role="group">
                    <a href="{{ route('admin.content.hero-slides.create') }}" class="btn btn-primary btn-lg mb-3 shadow-sm">
                        <i class="fas fa-plus mr-3"></i>
                        <strong>Add New Hero Slide</strong>
                        <small class="d-block mt-1 opacity-75">Create homepage banner content</small>
                    </a>
                    <a href="{{ route('admin.staff.create') }}" class="btn btn-info btn-lg mb-3 shadow-sm">
                        <i class="fas fa-user-plus mr-3"></i>
                        <strong>Add New Staff Member</strong>
                        <small class="d-block mt-1 opacity-75">Register new team member</small>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-success btn-lg mb-3 shadow-sm">
                        <i class="fas fa-chart-line mr-3"></i>
                        <strong>Generate Reports</strong>
                        <small class="d-block mt-1 opacity-75">View analytics and statistics</small>
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-warning btn-lg shadow-sm" target="_blank">
                        <i class="fas fa-external-link-alt mr-3"></i>
                        <strong>View Public Website</strong>
                        <small class="d-block mt-1 opacity-75">Open in new tab</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-light border-0">
                <h3 class="card-title mb-0">
                    <i class="fas fa-cogs mr-2 text-primary"></i>
                    <strong>System Overview</strong>
                </h3>
                <small class="text-muted d-block mt-1">Recent system activity</small>
            </div>
            <div class="card-body bg-light">
                <div class="timeline timeline-inverse">
                    <div class="time-label">
                        <span class="bg-success">Today</span>
                    </div>

                    <div>
                        <i class="fas fa-clock bg-primary"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Attendance Tracking</h3>
                            <div class="timeline-body">
                                {{ $todayAttendance }} staff members have checked in today
                            </div>
                        </div>
                    </div>

                    @if($pendingLeaves > 0)
                    <div>
                        <i class="fas fa-calendar bg-warning"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Leave Requests</h3>
                            <div class="timeline-body">
                                {{ $pendingLeaves }} leave request{{ $pendingLeaves > 1 ? 's' : '' }} pending review
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <i class="fas fa-check bg-success"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Weekly Trackers</h3>
                            <div class="timeline-body">
                                {{ $submittedCount }} out of {{ $totalStaff }} staff submitted this week's tracker
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        :root {
            --africa-green: #348F41;
            --africa-gold: #B4A269;
            --africa-green-light: rgba(52, 143, 65, 0.1);
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --card-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .small-box {
            border-radius: 8px;
        }
        .card {
            border-radius: 8px;
        }
        .list-group-item-action:hover {
            background-color: #f8f9fa;
        }

        /* Statistics cards Africa CDC theme */
        .small-box.bg-info {
            background-color: var(--africa-gold) !important;
        }

        .small-box.bg-success {
            background-color: var(--africa-green) !important;
        }

        /* Enhanced Quick Actions Styling */
        .btn-group-vertical .btn {
            position: relative;
            overflow: hidden;
            text-align: left;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 8px !important;
            transform: translateY(0);
            transition: all 0.3s ease;
            color: white !important;
        }

        .btn-group-vertical .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
            color: white !important;
        }

        .btn-group-vertical .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: rgba(255,255,255,0.3);
            transition: width 0.3s ease;
        }

        .btn-group-vertical .btn:hover::before {
            width: 8px;
        }

        .btn-group-vertical .btn i {
            font-size: 1.2em;
            opacity: 0.9;
            color: white !important;
        }

        .btn-group-vertical .btn strong {
            font-size: 1.1em;
            display: block;
            margin-bottom: 2px;
            color: white !important;
        }

        .btn-group-vertical .btn small {
            font-size: 0.85em;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Button specific styling with distinct colors */
        .btn-primary {
            background: linear-gradient(135deg, var(--africa-green) 0%, #2d7a36 100%) !important;
            border: none !important;
            color: white !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background: linear-gradient(135deg, #2d7a36 0%, #1e5e29 100%) !important;
            color: white !important;
        }

        .btn-info {
            background: linear-gradient(135deg, var(--africa-gold) 0%, #a09558 100%) !important;
            border: none !important;
            color: white !important;
        }

        .btn-info:hover,
        .btn-info:focus,
        .btn-info:active {
            background: linear-gradient(135deg, #a09558 0%, #8d8247 100%) !important;
            color: white !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;
            border: none !important;
            color: white !important;
        }

        .btn-success:hover,
        .btn-success:focus,
        .btn-success:active {
            background: linear-gradient(135deg, #1e7e34 0%, #155724 100%) !important;
            color: white !important;
        }

        .btn-warning {
            background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%) !important;
            border: none !important;
            color: white !important;
        }

        .btn-warning:hover,
        .btn-warning:focus,
        .btn-warning:active {
            background: linear-gradient(135deg, #e55a00 0%, #cc4f00 100%) !important;
            color: white !important;
        }

        /* Card enhancements */
        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            border-bottom: 2px solid var(--africa-green) !important;
        }

        .card-title {
            color: var(--africa-green) !important;
            font-weight: 600 !important;
        }

        /* Enhanced card styling for quick actions */
        .bg-gradient-light {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
            border-bottom: 3px solid var(--africa-green) !important;
        }

        .card.shadow-sm {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
            border-radius: 12px !important;
            overflow: hidden;
        }

        .card-body.bg-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
            padding: 2rem !important;
        }

        .text-primary {
            color: var(--africa-green) !important;
        }

        /* Icon enhancements */
        .fas.fa-rocket {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Content management card styling */
        .list-group-item {
            border: none !important;
            border-radius: 6px !important;
            margin-bottom: 8px !important;
            transition: all 0.3s ease !important;
        }

        .list-group-item:hover {
            background-color: var(--africa-green-light) !important;
            transform: translateX(5px);
        }

        /* Small box hover effects */
        .small-box:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Enhanced Readability & Color Balance */
        .content-wrapper {
            background-color: #f8f9fa !important;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--text-dark) !important;
            font-weight: 600 !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .text-bold {
            font-weight: 700 !important;
        }

        /* Enhanced Info Boxes */
        .info-box {
            border-radius: 10px !important;
            box-shadow: var(--card-shadow) !important;
            border: none !important;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .info-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15) !important;
        }

        .info-box-icon {
            border-radius: 10px 0 0 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 2rem !important;
        }

        .info-box-content {
            padding: 1.5rem !important;
        }

        .info-box-text {
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: rgba(255,255,255,0.9) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .info-box-number {
            font-size: 2.2rem !important;
            font-weight: 700 !important;
            color: white !important;
            line-height: 1.2 !important;
        }

        .progress-description {
            font-size: 0.8rem !important;
            color: rgba(255,255,255,0.8) !important;
            margin-top: 0.5rem !important;
        }

        /* Card Improvements */
        .card {
            border-radius: 12px !important;
            box-shadow: var(--card-shadow) !important;
            border: none !important;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }

        .card-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
            border-bottom: 3px solid var(--africa-green) !important;
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem !important;
        }

        .card-title {
            color: var(--text-dark) !important;
            font-weight: 700 !important;
            margin-bottom: 0 !important;
            font-size: 1.1rem !important;
        }

        .card-body {
            padding: 1.5rem !important;
        }

        /* Enhanced Progress Bars */
        .progress {
            height: 8px !important;
            border-radius: 4px !important;
            background-color: rgba(0,0,0,0.1) !important;
        }

        .progress-bar {
            border-radius: 4px !important;
            transition: width 0.6s ease !important;
        }

        /* Description Blocks */
        .description-block {
            text-align: center !important;
            padding: 1rem !important;
        }

        .description-percentage {
            font-size: 1rem !important;
            font-weight: 600 !important;
            margin-bottom: 0.5rem !important;
            display: block !important;
        }

        .description-header {
            font-size: 2rem !important;
            font-weight: 700 !important;
            color: var(--text-dark) !important;
            margin: 0.5rem 0 !important;
        }

        .description-text {
            font-size: 0.9rem !important;
            color: var(--text-muted) !important;
            font-weight: 500 !important;
        }

        /* Timeline Styling */
        .timeline {
            margin: 0 !important;
        }

        .timeline-item {
            margin-bottom: 1rem !important;
            background: white !important;
            border-radius: 8px !important;
            padding: 1rem !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        }

        .timeline-header {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: var(--text-dark) !important;
            margin-bottom: 0.5rem !important;
        }

        .timeline-body {
            color: var(--text-muted) !important;
            font-size: 0.9rem !important;
        }

        /* Breadcrumb Styling */
        .breadcrumb {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--text-muted) !important;
        }

        .breadcrumb-item a {
            color: var(--africa-green) !important;
            text-decoration: none !important;
        }

        .breadcrumb-item.active {
            color: var(--text-muted) !important;
        }

        /* Content Header */
        .content-header {
            padding: 1.5rem 0 !important;
            margin-bottom: 1.5rem !important;
        }

        .content-header h1 {
            font-size: 2rem !important;
            color: var(--text-dark) !important;
            margin-bottom: 0.25rem !important;
        }

        /* Icon animations */
        .fas.fa-cogs {
            animation: rotate 3s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .info-box-number {
                font-size: 1.8rem !important;
            }

            .description-header {
                font-size: 1.5rem !important;
            }

            .card-body {
                padding: 1rem !important;
            }
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Admin Dashboard loaded successfully!');
    </script>
@stop
