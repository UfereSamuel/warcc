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

<!-- Charts Section -->
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

<!-- Additional Analytics -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-chart-line mr-2"></i>Weekly Tracker Completion
                </h3>
            </div>
            <div class="chart-body">
                <canvas id="trackerChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-venus-mars mr-2"></i>Gender Distribution
                </h3>
            </div>
            <div class="chart-body">
                <canvas id="genderChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Department Performance Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="department-performance-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>Position Performance Overview
                </h3>
                <div class="card-actions">
                    <button class="btn btn-sm btn-outline-success" onclick="exportPositionData()">
                        <i class="fas fa-file-excel mr-1"></i>Export
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Total Staff</th>
                                <th>Active Staff</th>
                                <th>Today's Attendance</th>
                                <th>Weekly Tracker Rate</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positionStats as $position)
                            <tr>
                                <td>
                                    <strong>{{ $position->department }}</strong>
                                </td>
                                <td>{{ $position->total }}</td>
                                <td>{{ $position->active }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2">{{ $position->attendance_rate }}%</span>
                                        <div class="mini-progress">
                                            <div class="mini-progress-bar" style="width: {{ $position->attendance_rate }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2">{{ $position->tracker_rate }}%</span>
                                        <div class="mini-progress">
                                            <div class="mini-progress-bar tracker" style="width: {{ $position->tracker_rate }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $avgPerformance = ($position->attendance_rate + $position->tracker_rate) / 2;
                                    @endphp
                                    @if($avgPerformance >= 80)
                                        <span class="badge badge-success">Excellent</span>
                                    @elseif($avgPerformance >= 60)
                                        <span class="badge badge-warning">Good</span>
                                    @else
                                        <span class="badge badge-danger">Needs Attention</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download mr-2"></i>Custom Export
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="form-group">
                        <label>Export Type</label>
                        <select class="form-control" name="export_type" required>
                            <option value="attendance">Attendance Data</option>
                            <option value="weekly-trackers">Weekly Trackers</option>
                            <option value="dashboard-analytics">Dashboard Analytics</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Format</label>
                        <select class="form-control" name="format">
                            <option value="csv">CSV (Excel Compatible)</option>
                            <option value="pdf">PDF Report</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="performCustomExport()">
                    <i class="fas fa-download mr-1"></i>Export
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Dashboard Header */
.dashboard-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 25px 30px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dashboard-title {
    font-size: 2.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.dashboard-subtitle {
    font-size: 1.1rem;
    color: #7f8c8d;
    margin-bottom: 0;
}

.header-actions {
    display: flex;
    gap: 10px;
}

/* Analytics Grid */
.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.analytics-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.analytics-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.analytics-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
}

.analytics-card.primary { --accent-color: #007bff; --accent-light: #4dabf7; }
.analytics-card.success { --accent-color: #28a745; --accent-light: #51cf66; }
.analytics-card.warning { --accent-color: #ffc107; --accent-light: #ffd43b; }
.analytics-card.info { --accent-color: #17a2b8; --accent-light: #3bc9db; }

.analytics-card .card-header {
    padding: 20px 25px 15px;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    gap: 15px;
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #34495e;
}

.analytics-card .card-body {
    padding: 0 25px 25px;
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
    margin-bottom: 8px;
}

.metric-subtitle {
    font-size: 0.95rem;
    color: #7f8c8d;
    margin-bottom: 12px;
}

.metric-breakdown {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.breakdown-item {
    font-size: 0.9rem;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 8px;
}

.metric-detail {
    font-size: 0.85rem;
    color: #6c757d;
}

.metric-trend {
    font-size: 0.9rem;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 5px;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 10px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
    border-radius: 3px;
    transition: width 0.8s ease;
}

/* Chart Cards */
.chart-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 25px;
}

.chart-header {
    padding: 20px 25px;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chart-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.chart-actions {
    display: flex;
    gap: 10px;
}

.chart-body {
    padding: 25px;
}

.status-legend {
    padding: 20px 25px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    margin-right: 10px;
}

.legend-text {
    font-size: 0.9rem;
    color: #495057;
}

/* Department Performance */
.department-performance-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.department-performance-card .card-header {
    padding: 20px 25px;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.department-performance-card .card-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.department-performance-card .card-body {
    padding: 0;
}

.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background: #f8f9fa;
    padding: 15px;
}

.table td {
    padding: 15px;
    vertical-align: middle;
}

.mini-progress {
    width: 80px;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.mini-progress-bar {
    height: 100%;
    background: linear-gradient(135deg, #28a745, #51cf66);
    border-radius: 3px;
    transition: width 0.5s ease;
}

.mini-progress-bar.tracker {
    background: linear-gradient(135deg, #ffc107, #ffd43b);
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .dashboard-title {
        font-size: 1.8rem;
    }
    
    .analytics-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Data from Backend
const chartData = @json($chartData);

// Attendance Trend Chart
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
new Chart(attendanceCtx, {
    type: 'line',
    data: {
        labels: chartData.attendance_trend.map(item => item.date),
        datasets: [{
            label: 'Daily Attendance',
            data: chartData.attendance_trend.map(item => item.count),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true },
            x: { grid: { display: false } }
        }
    }
});

// Staff Status Pie Chart
const statusCtx = document.getElementById('staffStatusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['At Office', 'On Mission', 'On Leave'],
        datasets: [{
            data: [
                {{ $staffStatusData['at_office'] }},
                {{ $staffStatusData['on_mission'] }},
                {{ $staffStatusData['on_leave'] }}
            ],
            backgroundColor: ['#28a745', '#ffc107', '#17a2b8'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});

// Weekly Tracker Chart
const trackerCtx = document.getElementById('trackerChart').getContext('2d');
new Chart(trackerCtx, {
    type: 'bar',
    data: {
        labels: chartData.tracker_completion.map(item => item.week),
        datasets: [{
            label: 'Completed Trackers',
            data: chartData.tracker_completion.map(item => item.count),
            backgroundColor: '#ffc107',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Gender Chart
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'doughnut',
    data: {
        labels: chartData.gender_breakdown.map(item => item.gender),
        datasets: [{
            data: chartData.gender_breakdown.map(item => item.count),
            backgroundColor: ['#007bff', '#e91e63', '#9c27b0'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { 
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        }
    }
});

// Dashboard Functions
function refreshDashboard() { location.reload(); }
function openExportModal() { $('#exportModal').modal('show'); }

function performCustomExport() {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    const exportType = formData.get('export_type');
    const startDate = formData.get('start_date');
    const endDate = formData.get('end_date');
    const format = formData.get('format');
    
    const baseUrl = '{{ route("admin.export.attendance") }}';
    let url = baseUrl.replace('attendance', exportType);
    url += `?start_date=${startDate}&end_date=${endDate}&format=${format}`;
    
    window.location.href = url;
    $('#exportModal').modal('hide');
}

function downloadChart(chartId) {
    const canvas = document.getElementById(chartId);
    const url = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.download = `${chartId}_${new Date().getTime()}.png`;
    link.href = url;
    link.click();
}

function exportPositionData() {
    window.location.href = '{{ route("admin.export.dashboard-analytics", ["format" => "csv"]) }}';
}

console.log('Analytics Dashboard loaded successfully!');
</script>
@stop
