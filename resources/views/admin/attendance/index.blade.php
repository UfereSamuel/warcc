@extends('adminlte::page')

@section('title', 'Daily Attendance Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Daily Attendance Management</h1>
            <p class="text-muted">Monitor and review staff daily attendance</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Attendance</li>
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
                    Filter Attendance
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.index') }}" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date">Date</label>
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.attendance.daily-report', ['date' => $date, 'department' => $department]) }}"
                                       class="btn btn-info ml-2">
                                        <i class="fas fa-chart-bar mr-1"></i> Report
                                    </a>
                                    <a href="{{ route('admin.attendance.export', ['date' => $date, 'department' => $department]) }}"
                                       class="btn btn-success ml-2">
                                        <i class="fas fa-download mr-1"></i> Export
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

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Present</span>
                <span class="info-box-number">{{ $stats['present'] }}</span>
                <span class="progress-description">
                    {{ $stats['total_staff'] > 0 ? round(($stats['present'] / $stats['total_staff']) * 100) : 0 }}% of total staff
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-user-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Late</span>
                <span class="info-box-number">{{ $stats['late'] }}</span>
                <span class="progress-description">
                    {{ $stats['total_staff'] > 0 ? round(($stats['late'] / $stats['total_staff']) * 100) : 0 }}% of total staff
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-danger">
            <span class="info-box-icon"><i class="fas fa-user-times"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Absent</span>
                <span class="info-box-number">{{ $stats['absent'] }}</span>
                <span class="progress-description">
                    {{ $stats['total_staff'] > 0 ? round(($stats['absent'] / $stats['total_staff']) * 100) : 0 }}% of total staff
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Staff</span>
                <span class="info-box-number">{{ $stats['total_staff'] }}</span>
                <span class="progress-description">Active employees</span>
            </div>
        </div>
    </div>
</div>

<!-- Checked In Staff -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-check mr-2"></i>
                    Staff Attendance - {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $attendances->count() }} Records</span>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($attendances->count() > 0)
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Staff Member</th>
                                <th>Department</th>
                                <th class="text-center">Clock In</th>
                                <th class="text-center">Clock Out</th>
                                <th class="text-center">Hours</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Location</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <img src="{{ $attendance->staff->profile_picture_url }}"
                                                 alt="{{ $attendance->staff->full_name }}"
                                                 class="img-circle mr-3"
                                                 style="width: 35px; height: 35px;">
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
                                            <span class="text-warning">Still Logged In</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($attendance->total_hours)
                                            <span class="badge badge-primary">{{ number_format($attendance->total_hours, 1) }}h</span>
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
                                        @if($attendance->clock_in_latitude && $attendance->clock_in_longitude)
                                            <button class="btn btn-xs btn-info"
                                                    onclick="showLocationModal({{ $attendance->clock_in_latitude }}, {{ $attendance->clock_in_longitude }}, '{{ $attendance->staff->full_name }}', '{{ $attendance->clock_in_address ?? 'Location not available' }}')"
                                                    title="View Location">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.staff.show', $attendance->staff) }}"
                                           class="btn btn-xs btn-primary" title="View Profile">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clock fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Attendance Records</h4>
                        <p class="text-muted">No staff have checked in for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Absent Staff -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-times mr-2"></i>
                    Absent Staff
                </h3>
                <div class="card-tools">
                    <span class="badge badge-danger">{{ $absentStaff->count() }} Staff</span>
                </div>
            </div>
            <div class="card-body">
                @if($absentStaff->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($absentStaff as $staff)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="media align-items-center">
                                    <img src="{{ $staff->profile_picture_url }}"
                                         alt="{{ $staff->full_name }}"
                                         class="img-circle mr-3"
                                         style="width: 30px; height: 30px;">
                                    <div class="media-body">
                                        <h6 class="mb-0">{{ $staff->full_name }}</h6>
                                        <small class="text-muted">{{ $staff->department }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('admin.staff.show', $staff) }}"
                                   class="btn btn-xs btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted mb-0">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i><br>
                        All staff are present!
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Clock-in Location
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="locationInfo" class="mb-3"></div>
                <div id="map" style="height: 300px;"></div>
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
            color: #2c3e50;
            border-top: none;
        }
        .list-group-item {
            border-left: none;
            border-right: none;
        }
        .list-group-item:first-child {
            border-top: none;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-submit on date/department change
        $('#date, #department').change(function() {
            $(this).closest('form').submit();
        });

        // Location modal functionality
        function showLocationModal(lat, lng, staffName, address) {
            $('#locationInfo').html(`
                <h6><strong>${staffName}</strong></h6>
                <p class="text-muted mb-0">${address}</p>
                <small class="text-muted">Coordinates: ${lat}, ${lng}</small>
            `);

            $('#locationModal').modal('show');

            // Initialize map (using a simple placeholder - integrate with your preferred map service)
            setTimeout(function() {
                const mapElement = document.getElementById('map');
                mapElement.innerHTML = `
                    <div class="alert alert-info mb-0">
                        <h6>Location Information</h6>
                        <p><strong>Latitude:</strong> ${lat}</p>
                        <p><strong>Longitude:</strong> ${lng}</p>
                        <p class="mb-0"><strong>Address:</strong> ${address}</p>
                        <hr>
                        <small class="text-muted">Integrate with Google Maps or OpenStreetMap for full map display</small>
                    </div>
                `;
            }, 300);
        }
    </script>
@stop
