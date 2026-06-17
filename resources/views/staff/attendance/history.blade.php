@extends('layouts.staff')

@section('title', 'Attendance History')
@section('page-title', 'Attendance History')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.attendance.index') }}">Attendance</a></li>
    <li class="breadcrumb-item active">History</li>
@endsection

@section('content')
<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success">
                <i class="fas fa-calendar-check"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Days</span>
                <span class="info-box-number">{{ $summary['total_days'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Present Days</span>
                <span class="info-box-number">{{ $summary['present_days'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning">
                <i class="fas fa-clock"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Hours</span>
                <span class="info-box-number">{{ number_format($summary['total_hours'], 1) }}h</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary">
                <i class="fas fa-chart-line"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Avg Hours/Day</span>
                <span class="info-box-number">{{ number_format($summary['average_hours'], 1) }}h</span>
            </div>
        </div>
    </div>
</div>

<!-- Filter and Search -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Attendance Records
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('staff.attendance.history') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="month">Filter by Month</label>
                                <input type="month"
                                       id="month"
                                       name="month"
                                       class="form-control"
                                       value="{{ request('month', now()->format('Y-m')) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i>
                                        Filter
                                    </button>
                                    <a href="{{ route('staff.attendance.history') }}" class="btn btn-secondary">
                                        <i class="fas fa-times mr-1"></i>
                                        Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-success" onclick="exportToCSV()">
                                        <i class="fas fa-download mr-1"></i>
                                        Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Records -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Attendance Records
                    @if(request('month'))
                        - {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y') }}
                    @endif
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $attendances->total() }} records</span>
                </div>
            </div>
            <div class="card-body">
                @if($attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="attendance-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Break Duration</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $attendance)
                                <tr>
                                    <td>
                                        <strong>{{ $attendance->date->format('M d, Y') }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-light">{{ $attendance->date->format('l') }}</span>
                                    </td>
                                    <td>
                                        @if($attendance->clock_in_time)
                                            <span class="text-success">
                                                <i class="fas fa-sign-in-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($attendance->clock_in_time)->format('h:i A') }}
                                            </span>
                                            @if($attendance->clock_in_address)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ Str::limit($attendance->clock_in_address, 30) }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">--:--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->clock_out_time)
                                            <span class="text-danger">
                                                <i class="fas fa-sign-out-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($attendance->clock_out_time)->format('h:i A') }}
                                            </span>
                                            @if($attendance->clock_out_address)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ Str::limit($attendance->clock_out_address, 30) }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">--:--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->break_duration)
                                            <span class="badge badge-warning">{{ $attendance->break_duration }}m</span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->total_hours)
                                            <span class="badge badge-info">{{ number_format($attendance->total_hours, 1) }}h</span>
                                            @if($attendance->total_hours >= 8)
                                                <i class="fas fa-check-circle text-success ml-1" title="Full day"></i>
                                            @elseif($attendance->total_hours >= 4)
                                                <i class="fas fa-exclamation-triangle text-warning ml-1" title="Partial day"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger ml-1" title="Insufficient hours"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button"
                                                    class="btn btn-sm btn-info"
                                                    onclick="viewAttendanceDetails({{ $attendance->id }})"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($attendance->clock_in_latitude && $attendance->clock_in_longitude)
                                                <button type="button"
                                                        class="btn btn-sm btn-primary"
                                                        onclick="showLocationMap({{ $attendance->clock_in_latitude }}, {{ $attendance->clock_in_longitude }}, '{{ $attendance->date->format('M d, Y') }}')"
                                                        title="View Location">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $attendances->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No attendance records found</h4>
                        <p class="text-muted">
                            @if(request('month'))
                                No records found for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y') }}
                            @else
                                Start tracking your attendance to see records here
                            @endif
                        </p>
                        <a href="{{ route('staff.attendance.index') }}" class="btn btn-primary">
                            <i class="fas fa-clock mr-1"></i>
                            Go to Attendance
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Attendance Details Modal -->
<div class="modal fade" id="attendanceDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Attendance Details
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="attendanceDetailsContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Map Modal -->
<div class="modal fade" id="locationMapModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Attendance Location
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="location-map" style="height: 400px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let locationMap;

function viewAttendanceDetails(attendanceId) {
    $('#attendanceDetailsModal').modal('show');

    // Simulate loading attendance details
    // In a real application, you would make an AJAX call to get detailed information
    setTimeout(function() {
        $('#attendanceDetailsContent').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6>Clock In Details</h6>
                    <p><strong>Time:</strong> 8:30 AM</p>
                    <p><strong>Location:</strong> Office Building, Accra</p>
                    <p><strong>Coordinates:</strong> 5.6037, -0.1870</p>
                </div>
                <div class="col-md-6">
                    <h6>Clock Out Details</h6>
                    <p><strong>Time:</strong> 5:45 PM</p>
                    <p><strong>Location:</strong> Office Building, Accra</p>
                    <p><strong>Coordinates:</strong> 5.6037, -0.1870</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <h6>Work Duration</h6>
                    <p><strong>Total Hours:</strong> 8.25 hours</p>
                    <p><strong>Break Time:</strong> 60 minutes</p>
                </div>
                <div class="col-md-4">
                    <h6>Status</h6>
                    <p><span class="badge badge-success">Present</span></p>
                    <p><strong>Overtime:</strong> 0.25 hours</p>
                </div>
                <div class="col-md-4">
                    <h6>Notes</h6>
                    <p class="text-muted">Regular working day</p>
                </div>
            </div>
        `);
    }, 1000);
}

function showLocationMap(lat, lng, date) {
    $('#locationMapModal').modal('show');

    // Initialize map when modal is shown
    $('#locationMapModal').on('shown.bs.modal', function() {
        if (!locationMap) {
            locationMap = L.map('location-map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(locationMap);
        } else {
            locationMap.setView([lat, lng], 15);
        }

        // Clear existing markers
        locationMap.eachLayer(function(layer) {
            if (layer instanceof L.Marker) {
                locationMap.removeLayer(layer);
            }
        });

        // Add marker for the location
        L.marker([lat, lng])
            .addTo(locationMap)
            .bindPopup(`Attendance location for ${date}`)
            .openPopup();

        // Refresh map size
        setTimeout(function() {
            locationMap.invalidateSize();
        }, 100);
    });
}

function exportToCSV() {
    // Get table data
    const table = document.getElementById('attendance-table');
    const rows = table.querySelectorAll('tr');

    let csvContent = '';

    // Add headers
    const headers = ['Date', 'Day', 'Clock In', 'Clock Out', 'Total Hours', 'Status'];
    csvContent += headers.join(',') + '\n';

    // Add data rows (skip header row)
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].querySelectorAll('td');
        const rowData = [];

        // Extract text content from each cell (first 6 columns)
        for (let j = 0; j < 6; j++) {
            if (cells[j]) {
                let cellText = cells[j].textContent.trim();
                // Clean up the text
                cellText = cellText.replace(/\s+/g, ' ').replace(/,/g, ';');
                rowData.push(cellText);
            }
        }

        csvContent += rowData.join(',') + '\n';
    }

    // Create and download file
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `attendance_history_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

$(document).ready(function() {
    // Initialize tooltips
    $('[title]').tooltip();

    // Clear modal content when closed
    $('#attendanceDetailsModal').on('hidden.bs.modal', function() {
        $('#attendanceDetailsContent').html(`
            <div class="text-center">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Loading details...</p>
            </div>
        `);
    });
});
</script>
@endpush
