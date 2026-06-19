@extends('layouts.staff')

@section('title', 'Attendance Management')
@section('page-title', 'Attendance Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Current Time Display -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body text-center">
                    <h2 id="current-time" class="mb-1">{{ now()->format('h:i:s A') }}</h2>
                    <p class="mb-0">{{ now()->format('l, F j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Action Card -->
<div class="row mb-4">
    <div class="col-12">
            <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                        <i class="fas fa-clock mr-2"></i>
                        Today's Attendance
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                        <!-- Clock In/Out Section -->
                        <div class="col-12">
                                @if($todayAttendance && $todayAttendance->clock_in_time && !$todayAttendance->clock_out_time)
                                <!-- Currently Clocked In - Show Clock Out -->
                                <div class="alert alert-success">
                                    <h4><i class="fas fa-check-circle mr-2"></i>You're Currently Clocked In</h4>
                                    <p class="mb-2">Started work at: <strong>{{ \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A') }}</strong></p>
                                    <p class="mb-3">Working for: <span id="work-duration">0h 0m</span></p>
                                </div>
                                
                                <div class="text-center">
                                    <button id="clock-out-btn" class="btn btn-danger btn-lg px-5">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Clock Out
                                    </button>
                                </div>

                                @elseif($todayAttendance && $todayAttendance->clock_out_time)
                                <!-- Already Completed Today -->
                                <div class="alert alert-info">
                                    <h4><i class="fas fa-check-double mr-2"></i>Attendance Complete for Today</h4>
                                    <p class="mb-1"><strong>Clock In:</strong> {{ \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A') }}</p>
                                    <p class="mb-1"><strong>Clock Out:</strong> {{ \Carbon\Carbon::parse($todayAttendance->clock_out_time)->format('h:i A') }}</p>
                                    <p class="mb-0"><strong>Total Hours:</strong> {{ number_format($todayAttendance->total_hours, 2) }} hours</p>
                                    </div>

                                @else
                                <!-- Not Clocked In Yet - Show Clock In -->
                                <div class="alert alert-warning">
                                    <h4><i class="fas fa-clock mr-2"></i>Ready to Start Your Day?</h4>
                                    <p class="mb-0">Click the button below to clock in and start your workday.</p>
                                </div>
                                
                                <div class="text-center">
                                    <button id="clock-in-btn" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        Clock In
                                    </button>
                                </div>
                                @endif
                        </div>
                    </div>
                            </div>
                                            </div>
                                        </div>
                                            </div>
                                        </div>

    <!-- Weekly Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $weekSummary['present_days'] }}/{{ $weekSummary['working_days'] }}</h3>
                    <p>Days This Week</p>
                                    </div>
                <div class="icon">
                    <i class="fas fa-calendar-week"></i>
                                            </div>
                                        </div>
                                    </div>
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($weekSummary['total_hours'], 1) }}h</h3>
                    <p>Hours This Week</p>
                                    </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($monthSummary['average_hours'], 1) }}h</h3>
                    <p>Daily Average</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

    <!-- Recent Attendance -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>
                        Recent Attendance
                </h3>
                <div class="card-tools">
                    <a href="{{ route('staff.attendance.history') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list mr-1"></i>
                            View All
                    </a>
                </div>
            </div>
            <div class="card-body">
                    @if($recentAttendance->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                        <th>Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @foreach($recentAttendance as $attendance)
                                    <tr>
                                        <td>
                                            <strong>{{ $attendance->date->format('M d, Y') }}</strong><br>
                                            <small class="text-muted">{{ $attendance->date->format('l') }}</small>
                                        </td>
                                    <td>
                                        @if($attendance->clock_in_time)
                                            <span class="text-success">
                                                {{ \Carbon\Carbon::parse($attendance->clock_in_time)->format('h:i A') }}
                                            </span>
                                        @else
                                                <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->clock_out_time)
                                                <span class="text-warning">
                                                {{ \Carbon\Carbon::parse($attendance->clock_out_time)->format('h:i A') }}
                                            </span>
                                        @else
                                                <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->total_hours)
                                                <span class="badge badge-info">
                                                    {{ number_format($attendance->total_hours, 1) }}h
                                                </span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                            <span class="badge badge-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Recent Attendance</h5>
                            <p class="text-muted">Your attendance history will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h4 id="success-title">Success!</h4>
                <p id="success-message">Operation completed successfully.</p>
                <button type="button" class="btn btn-success btn-lg" onclick="location.reload()">
                    Continue
                </button>
            </div>
                </div>
            </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                <h4 id="error-title">Error</h4>
                <p id="error-message">An error occurred.</p>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="retry-btn" style="display: none;">
                    Retry
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <h5 id="loading-title">Processing...</h5>
                <p class="text-muted mb-0" id="loading-message">Please wait</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border: none;
}

.btn-lg {
    padding: 12px 40px;
    font-size: 1.2rem;
    font-weight: 600;
    border-radius: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.small-box {
    border-radius: 10px;
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.small-box > .inner {
    padding: 10px;
}

.small-box .icon {
    color: rgba(0,0,0,0.15);
    z-index: 0;
}

.small-box .icon > i {
    font-size: 70px;
    position: absolute;
    right: 15px;
    top: 15px;
    transition: all .3s linear;
}

.small-box:hover .icon > i {
    font-size: 95px;
}

.small-box h3 {
    font-size: 2.2rem;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}

.small-box p {
    font-size: 1rem;
}

.small-box .inner h3, .small-box .inner p {
    color: #fff;
}

.bg-success {
    background-color: #28a745 !important;
}

.bg-info {
    background-color: #17a2b8 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.modal-content {
    border: none;
    border-radius: 15px;
}
</style>
@endsection

@section('js')
<script>
let clockInTime = null;

$(document).ready(function() {
    updateClock();
    setInterval(updateClock, 1000);

    @if($todayAttendance && $todayAttendance->clock_in_time && !$todayAttendance->clock_out_time)
        clockInTime = '{{ $todayAttendance->clock_in_time }}';
        updateWorkDuration();
        setInterval(updateWorkDuration, 60000);
    @endif

    bindEventHandlers();
});

function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    $('#current-time').text(timeString);
}

function updateWorkDuration() {
    if (!clockInTime) return;

    const now = new Date();
    const clockIn = new Date();
    const [hours, minutes] = clockInTime.split(':');
    clockIn.setHours(parseInt(hours), parseInt(minutes), 0, 0);

    const diffMs = now - clockIn;
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

    $('#work-duration').text(`${diffHours}h ${diffMinutes}m`);
}

function bindEventHandlers() {
    $(document).on('click', '#clock-in-btn', function(e) {
        e.preventDefault();
        performClockAction('in');
    });

    $(document).on('click', '#clock-out-btn', function(e) {
        e.preventDefault();
        performClockAction('out');
    });

    $('#retry-btn').on('click', function() {
        $('#errorModal').modal('hide');
        const action = $(this).data('action');
        if (action === 'clock-in') {
            performClockAction('in');
        } else if (action === 'clock-out') {
            performClockAction('out');
        }
    });
}

function performClockAction(action) {
    const isClockIn = action === 'in';
    const url = isClockIn ? '{{ route("staff.attendance.clock-in") }}' : '{{ route("staff.attendance.clock-out") }}';
    const title = isClockIn ? 'Clocking In...' : 'Clocking Out...';
    const message = isClockIn ? 'Recording your arrival' : 'Recording your departure';
    const button = isClockIn ? $('#clock-in-btn') : $('#clock-out-btn');

    showLoadingModal(title, message);
    button.prop('disabled', true);

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        timeout: 30000,
        success: function(response) {
            hideLoadingModal();

            if (response.success) {
                const successTitle = isClockIn ? 'Clocked In Successfully!' : 'Clocked Out Successfully!';
                showSuccessModal(successTitle, response.message);
            } else {
                showErrorModal('Error', response.message || 'Operation failed. Please try again.', true, action);
            }
        },
        error: function(xhr, status) {
            hideLoadingModal();

            let errorTitle = 'Error';
            let errorMessage = 'An unexpected error occurred. Please try again.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 419) {
                errorTitle = 'Session Expired';
                errorMessage = 'Your session has expired. Please refresh the page and try again.';
            } else if (xhr.status === 422) {
                errorTitle = 'Validation Error';
                errorMessage = 'Unable to record attendance. Please try again.';
            } else if (xhr.status === 429) {
                errorTitle = 'Too Many Requests';
                errorMessage = 'Please wait a moment before trying again.';
            } else if (status === 'timeout') {
                errorTitle = 'Request Timeout';
                errorMessage = 'The request timed out. Please check your connection and try again.';
            }

            showErrorModal(errorTitle, errorMessage, true, action);
        },
        complete: function() {
            button.prop('disabled', false);
        }
    });
}

function showSuccessModal(title, message) {
    $('#success-title').text(title);
    $('#success-message').text(message);
    $('#successModal').modal('show');
}

function showErrorModal(title, message, showRetry = false, retryAction = null) {
    $('#error-title').text(title);

    if (message.includes('<')) {
        $('#error-message').html(message);
    } else {
        $('#error-message').text(message);
    }

    const retryBtn = $('#retry-btn');

    if (showRetry && retryAction) {
        retryBtn.show().data('action', retryAction).text('Retry');
    } else {
        retryBtn.hide();
    }

    $('#errorModal').modal('show');
}

function showLoadingModal(title, message) {
    $('#loading-title').text(title);
    $('#loading-message').text(message);
    $('#loadingModal').modal('show');
}

function hideLoadingModal() {
    $('#loadingModal').modal('hide');
}
</script>
@endsection
