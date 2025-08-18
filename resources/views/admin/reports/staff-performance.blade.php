@extends('adminlte::page')

@section('title', 'Staff Performance Report')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Staff Performance Report</h1>
            <p class="text-muted">Comprehensive performance analytics and scoring</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Staff Performance</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Filter Controls -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Options
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.staff-performance') }}" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('admin.reports.staff-performance') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-times mr-1"></i> Clear
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

<!-- Performance Overview Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Top Performers</span>
                <span class="info-box-number">{{ $staff->where('performance_score', '>=', 80)->count() }}</span>
                <span class="progress-description">Score ≥ 80</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Average Score</span>
                <span class="info-box-number">{{ round($staff->avg('performance_score')) }}</span>
                <span class="progress-description">Out of 100</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Avg Attendance</span>
                <span class="info-box-number">{{ round($staff->avg('attendance_days')) }}</span>
                <span class="progress-description">Days worked</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Avg Submissions</span>
                <span class="info-box-number">{{ round($staff->avg('weekly_submissions')) }}</span>
                <span class="progress-description">Weekly trackers</span>
            </div>
        </div>
    </div>
</div>

<!-- Performance Distribution Chart -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Performance Distribution
                </h3>
            </div>
            <div class="card-body">
                @php
                    $excellent = $staff->where('performance_score', '>=', 90)->count();
                    $good = $staff->where('performance_score', '>=', 70)->where('performance_score', '<', 90)->count();
                    $average = $staff->where('performance_score', '>=', 50)->where('performance_score', '<', 70)->count();
                    $needsImprovement = $staff->where('performance_score', '<', 50)->count();
                    $total = $staff->count();
                @endphp

                <div class="row text-center">
                    <div class="col-3">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                {{ $total > 0 ? round(($excellent / $total) * 100) : 0 }}%
                            </span>
                            <h5 class="description-header">{{ $excellent }}</h5>
                            <span class="description-text">Excellent (90+)</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="description-block border-right">
                            <span class="description-percentage text-primary">
                                {{ $total > 0 ? round(($good / $total) * 100) : 0 }}%
                            </span>
                            <h5 class="description-header">{{ $good }}</h5>
                            <span class="description-text">Good (70-89)</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="description-block border-right">
                            <span class="description-percentage text-warning">
                                {{ $total > 0 ? round(($average / $total) * 100) : 0 }}%
                            </span>
                            <h5 class="description-header">{{ $average }}</h5>
                            <span class="description-text">Average (50-69)</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="description-block">
                            <span class="description-percentage text-danger">
                                {{ $total > 0 ? round(($needsImprovement / $total) * 100) : 0 }}%
                            </span>
                            <h5 class="description-header">{{ $needsImprovement }}</h5>
                            <span class="description-text">Needs Work (<50)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Performance Scoring Guide
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-calculator mr-1"></i> <strong>How Performance Score is Calculated:</strong></h6>
                    <ul class="mb-0">
                        <li><strong>Attendance (40%):</strong> 2 points per day attended (max 40)</li>
                        <li><strong>Weekly Submissions (40%):</strong> 5 points per submission (max 40)</li>
                        <li><strong>Punctuality (20%):</strong> 20 points minus 2 per late day (max 20)</li>
                    </ul>
                </div>

                <div class="mt-3">
                    <h6>Performance Categories:</h6>
                    <ul class="list-unstyled">
                        <li><span class="badge badge-success mr-2">90-100</span> Excellent Performance</li>
                        <li><span class="badge badge-primary mr-2">70-89</span> Good Performance</li>
                        <li><span class="badge badge-warning mr-2">50-69</span> Average Performance</li>
                        <li><span class="badge badge-danger mr-2">0-49</span> Needs Improvement</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Staff Performance Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list mr-2"></i>
            Individual Performance Rankings
        </h3>
        <div class="card-tools">
            <span class="badge badge-primary">{{ $staff->count() }} Staff Members</span>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        @if($staff->count() > 0)
            <table class="table table-hover text-nowrap">
                <thead class="thead-light">
                    <tr>
                        <th>Rank</th>
                        <th>Staff Member</th>
                        <th>Department</th>
                        <th class="text-center">Performance Score</th>
                        <th class="text-center">Attendance Days</th>
                        <th class="text-center">Weekly Submissions</th>
                        <th class="text-center">Avg Hours</th>
                        <th class="text-center">Late Days</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $index => $member)
                        <tr>
                            <td>
                                <span class="badge
                                    @if($index < 3) badge-warning
                                    @elseif($index < 10) badge-primary
                                    @else badge-secondary
                                    @endif">
                                    #{{ $index + 1 }}
                                </span>
                            </td>
                            <td>
                                <div class="media align-items-center">
                                    <img src="{{ $member['staff']->profile_picture_url }}"
                                         alt="{{ $member['staff']->full_name }}"
                                         class="img-circle mr-3"
                                         style="width: 35px; height: 35px;">
                                    <div class="media-body">
                                        <h6 class="mb-0">{{ $member['staff']->full_name }}</h6>
                                        <small class="text-muted">{{ $member['staff']->staff_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $member['staff']->department }}</span>
                            </td>
                            <td class="text-center">
                                <div class="progress mb-1" style="height: 20px;">
                                    <div class="progress-bar
                                        @if($member['performance_score'] >= 90) bg-success
                                        @elseif($member['performance_score'] >= 70) bg-primary
                                        @elseif($member['performance_score'] >= 50) bg-warning
                                        @else bg-danger
                                        @endif"
                                        style="width: {{ $member['performance_score'] }}%">
                                        {{ $member['performance_score'] }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <strong>{{ $member['attendance_days'] }}</strong>
                            </td>
                            <td class="text-center">
                                <strong>{{ $member['weekly_submissions'] }}</strong>
                            </td>
                            <td class="text-center">
                                {{ $member['average_hours'] }}h
                            </td>
                            <td class="text-center">
                                @if($member['late_days'] > 0)
                                    <span class="badge badge-warning">{{ $member['late_days'] }}</span>
                                @else
                                    <span class="badge badge-success">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.staff.show', $member['staff']) }}"
                                   class="btn btn-info btn-sm" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Performance Data Available</h4>
                <p class="text-muted">No staff performance data found for the selected criteria.</p>
            </div>
        @endif
    </div>
</div>
@stop

@section('css')
    <style>
        .description-block {
            border-right: 1px solid #dee2e6;
        }

        .description-block:last-child {
            border-right: none;
        }

        .progress {
            background-color: #e9ecef;
        }

        .table th {
            font-weight: 600;
            color: #2c3e50;
            border-top: none;
        }

        .media {
            align-items: center;
        }

        .badge {
            font-size: 0.85rem;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-submit on filter change
        $('#department').change(function() {
            $(this).closest('form').submit();
        });

        // Tooltip for performance scores
        $('[data-toggle="tooltip"]').tooltip();
    </script>
@stop
