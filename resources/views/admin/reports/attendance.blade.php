@extends('adminlte::page')

@section('title', 'Attendance Analytics Report')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Attendance Analytics</h1>
            <p class="text-muted">Daily trends and punctuality analysis</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Attendance Analytics</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Filter Controls -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Attendance Data
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.attendance') }}" class="form-horizontal">
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
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}" {{ $position_id == $position->id ? 'selected' : '' }}>
                                            {{ $position->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-search mr-1"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('admin.reports.attendance') }}" class="btn btn-secondary ml-2">
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

<!-- Daily Attendance Trends -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Daily Attendance Trends
                </h3>
                <div class="card-tools">
                    <span class="badge badge-success">{{ $dailyAttendance->count() }} Days</span>
                </div>
            </div>
            <div class="card-body">
                @if($dailyAttendance->count() > 0)
                    <div class="chart">
                        <canvas id="attendanceChart" style="height: 300px;"></canvas>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Attendance Data</h4>
                        <p class="text-muted">No attendance records found for the selected period.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Top Performers -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-2"></i>
                    Top 10 Attendance Leaders
                </h3>
                <div class="card-tools">
                    <span class="badge badge-primary">{{ $staffRankings->count() }} Staff</span>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($staffRankings->count() > 0)
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Rank</th>
                                <th>Staff Member</th>
                                <th>Department</th>
                                <th class="text-center">Attendance Days</th>
                                <th class="text-center">Avg Hours</th>
                                <th class="text-center">Late Days</th>
                                <th class="text-center">Punctuality</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffRankings as $index => $ranking)
                                <tr>
                                    <td>
                                        <span class="badge
                                            @if($index < 3) badge-warning
                                            @else badge-primary
                                            @endif">
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="media align-items-center">
                                            <img src="{{ $ranking['staff']->profile_picture_url }}"
                                                 alt="{{ $ranking['staff']->full_name }}"
                                                 class="img-circle mr-3"
                                                 style="width: 35px; height: 35px;">
                                            <div class="media-body">
                                                <h6 class="mb-0">{{ $ranking['staff']->full_name }}</h6>
                                                <small class="text-muted">{{ $ranking['staff']->staff_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $ranking['staff']->department }}</span>
                                    </td>
                                    <td class="text-center">
                                        <strong class="text-primary">{{ $ranking['attendance_count'] }}</strong>
                                    </td>
                                    <td class="text-center">
                                        {{ $ranking['average_hours'] }}h
                                    </td>
                                    <td class="text-center">
                                        @if($ranking['late_count'] > 0)
                                            <span class="badge badge-warning">{{ $ranking['late_count'] }}</span>
                                        @else
                                            <span class="badge badge-success">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="progress mb-1" style="height: 20px;">
                                            <div class="progress-bar
                                                @if($ranking['punctuality_rate'] >= 90) bg-success
                                                @elseif($ranking['punctuality_rate'] >= 75) bg-primary
                                                @elseif($ranking['punctuality_rate'] >= 60) bg-warning
                                                @else bg-danger
                                                @endif"
                                                style="width: {{ $ranking['punctuality_rate'] }}%">
                                                {{ $ranking['punctuality_rate'] }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Attendance Rankings</h4>
                        <p class="text-muted">No staff attendance data found for the selected criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Attendance Statistics -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Quick Statistics
                </h3>
            </div>
            <div class="card-body">
                @php
                    $totalAttendance = $staffRankings->sum('attendance_count');
                    $totalLate = $staffRankings->sum('late_count');
                    $avgHours = $staffRankings->avg('average_hours');
                    $avgPunctuality = $staffRankings->avg('punctuality_rate');
                @endphp

                <div class="row text-center">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <h5 class="description-header text-primary">{{ $totalAttendance }}</h5>
                            <span class="description-text">Total Attendance</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header text-warning">{{ $totalLate }}</h5>
                            <span class="description-text">Late Arrivals</span>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row text-center">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <h5 class="description-header text-success">{{ round($avgHours, 1) }}h</h5>
                            <span class="description-text">Avg Hours</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header text-info">{{ round($avgPunctuality) }}%</h5>
                            <span class="description-text">Punctuality</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Categories -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Performance Guide
                </h3>
            </div>
            <div class="card-body">
                <h6>Punctuality Ratings:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <span class="badge badge-success mr-2">90-100%</span>
                        Excellent
                    </li>
                    <li class="mb-2">
                        <span class="badge badge-primary mr-2">75-89%</span>
                        Good
                    </li>
                    <li class="mb-2">
                        <span class="badge badge-warning mr-2">60-74%</span>
                        Needs Improvement
                    </li>
                    <li class="mb-2">
                        <span class="badge badge-danger mr-2">&lt;60%</span>
                        Poor
                    </li>
                </ul>

                @if($totalAttendance > 0)
                    <div class="mt-3 text-center">
                        <strong class="text-success">
                            {{ $totalLate > 0 ? round((($totalAttendance - $totalLate) / $totalAttendance) * 100) : 100 }}%
                        </strong>
                        <br>
                        <small class="text-muted">Overall Punctuality</small>
                    </div>
                @endif
            </div>
        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Auto-submit on filter change
        $('#department').change(function() {
            $(this).closest('form').submit();
        });

        // Attendance Chart
        @if($dailyAttendance->count() > 0)
            var ctx = document.getElementById('attendanceChart').getContext('2d');
            var attendanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach($dailyAttendance as $day)
                            '{{ \Carbon\Carbon::parse($day->attendance_date)->format("M d") }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Daily Attendance',
                        data: [
                            @foreach($dailyAttendance as $day)
                                {{ $day->total_attendance }},
                            @endforeach
                        ],
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        @endif
    </script>
@stop
