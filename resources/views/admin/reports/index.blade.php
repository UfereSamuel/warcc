@extends('adminlte::page')

@section('title', 'Reports & Analytics')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Reports & Analytics</h1>
            <p class="text-muted">Comprehensive staff performance and submission analytics</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Reports & Analytics</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Date Range Filter -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Filter Date Range
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="start_date" class="mr-2">From:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                               value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group mr-3">
                        <label for="end_date" class="mr-2">To:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                               value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-1"></i> Apply Filter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Key Statistics Overview -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Active Staff</span>
                <span class="info-box-number">{{ $totalStaff }}</span>
                <span class="progress-description">Across {{ $totalDepartments }} departments</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Attendance</span>
                <span class="info-box-number">{{ $attendanceStats['total_days'] }}</span>
                <span class="progress-description">{{ $attendanceStats['present_days'] }} on time, {{ $attendanceStats['late_days'] }} late</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Weekly Submissions</span>
                <span class="info-box-number">{{ $trackerStats['submitted'] }}</span>
                <span class="progress-description">{{ $trackerStats['pending'] }} pending approval</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Average Hours</span>
                <span class="info-box-number">{{ $attendanceStats['avg_hours'] }}</span>
                <span class="progress-description">Per day worked</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access to Detailed Reports -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Staff Performance Analytics
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Comprehensive performance scoring based on attendance, submissions, and punctuality.</p>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header text-primary">{{ $totalStaff }}</h5>
                            <span class="description-text">Staff Evaluated</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header text-success">{{ $attendanceStats['avg_hours'] }}h</h5>
                            <span class="description-text">Avg Daily Hours</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reports.staff-performance', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   class="btn btn-primary btn-block">
                    <i class="fas fa-eye mr-1"></i> View Detailed Report
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>
                    Weekly Tracker Analysis
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Monitor weekly status submissions, approval rates, and compliance tracking.</p>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header text-info">{{ $trackerStats['submitted'] }}</h5>
                            <span class="description-text">Submissions</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header text-success">{{ $trackerStats['approved'] }}</h5>
                            <span class="description-text">Approved</span>
                        </div>
                    </div>
                </div>
                @if($trackerStats['submitted'] > 0)
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success" style="width: {{ round(($trackerStats['approved'] / $trackerStats['submitted']) * 100) }}%"></div>
                    </div>
                    <small class="text-muted">{{ round(($trackerStats['approved'] / $trackerStats['submitted']) * 100) }}% approval rate</small>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reports.weekly-trackers', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   class="btn btn-info btn-block">
                    <i class="fas fa-eye mr-1"></i> View Submission Details
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Attendance Analytics
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Daily attendance trends, punctuality analysis, and department comparisons.</p>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header text-success">{{ $attendanceStats['present_days'] }}</h5>
                            <span class="description-text">Present Days</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header text-warning">{{ $attendanceStats['late_days'] }}</h5>
                            <span class="description-text">Late Days</span>
                        </div>
                    </div>
                </div>
                @if($attendanceStats['total_days'] > 0)
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success" style="width: {{ round(($attendanceStats['present_days'] / $attendanceStats['total_days']) * 100) }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ round(($attendanceStats['late_days'] / $attendanceStats['total_days']) * 100) }}%"></div>
                    </div>
                    <small class="text-muted">{{ round(($attendanceStats['present_days'] / $attendanceStats['total_days']) * 100) }}% punctuality rate</small>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reports.attendance', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   class="btn btn-success btn-block">
                    <i class="fas fa-eye mr-1"></i> View Attendance Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Department Overview -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-building mr-2"></i>
                    Department Overview
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $position_idStats->count() }} Departments</span>
                </div>
            </div>
            <div class="card-body">
                @if($position_idStats->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th class="text-center">Staff Count</th>
                                    <th class="text-center">Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($position_idStats as $position)
                                    <tr>
                                        <td>
                                            <strong>{{ $position->department }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary">{{ $position->staff_count }}</span>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-primary"
                                                     style="width: {{ round(($position->staff_count / $totalStaff) * 100) }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ round(($position->staff_count / $totalStaff) * 100) }}%</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No department data available for selected period.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-download mr-2"></i>
                    Export Reports
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Download comprehensive reports in PDF format</p>

                <div class="btn-group-vertical w-100">
                    <a href="{{ route('admin.reports.export-pdf', ['type' => 'overview', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="btn btn-outline-primary mb-2">
                        <i class="fas fa-file-pdf mr-1"></i> Overview Report
                    </a>

                    <a href="{{ route('admin.reports.export-pdf', ['type' => 'staff-performance', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="btn btn-outline-info mb-2">
                        <i class="fas fa-file-pdf mr-1"></i> Performance Report
                    </a>

                    <a href="{{ route('admin.reports.export-pdf', ['type' => 'weekly-trackers', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="btn btn-outline-success mb-2">
                        <i class="fas fa-file-pdf mr-1"></i> Submissions Report
                    </a>

                    <a href="{{ route('admin.reports.export-pdf', ['type' => 'attendance', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="btn btn-outline-warning">
                        <i class="fas fa-file-pdf mr-1"></i> Attendance Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .info-box:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .description-block {
            border-right: 1px solid #dee2e6;
        }

        .description-block:last-child {
            border-right: none;
        }

        .progress-sm {
            height: 0.5rem;
        }

        .btn-group-vertical .btn {
            border-radius: 0.375rem !important;
            margin-bottom: 0.5rem;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-submit form when dates change
        $('#start_date, #end_date').change(function() {
            $(this).closest('form').submit();
        });

        // Hover effects for info boxes
        $('.info-box').hover(
            function() {
                $(this).addClass('elevation-2');
            },
            function() {
                $(this).removeClass('elevation-2');
            }
        );
    </script>
@stop
