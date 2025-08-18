@extends('layouts.staff')

@section('title', 'Attendance Management')
@section('page-title', 'Attendance Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<!-- Today's Attendance Status -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card attendance-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-day mr-2"></i>
                    Today's Attendance - {{ now()->format('l, F j, Y') }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info" id="current-time">{{ now()->format('h:i:s A') }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Clock In/Out Buttons -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-clock mr-2"></i>
                                    Clock In/Out
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                @if($todayAttendance && $todayAttendance->clock_in_time && !$todayAttendance->clock_out_time)
                                    <!-- Clock Out Button -->
                                    <button id="clock-out-btn" class="btn btn-danger clock-button clock-out-btn btn-lg" disabled>
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        <span class="loading-spinner">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                        <span class="button-text">Clock Out</span>
                                    </button>
                                    <p class="text-muted mt-2">
                                        You clocked in at {{ \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A') }}
                                    </p>
                                @elseif($todayAttendance && $todayAttendance->clock_out_time)
                                    <!-- Already clocked out -->
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        You have completed your attendance for today
                                    </div>
                                    <p class="text-muted">
                                        Worked: {{ \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($todayAttendance->clock_out_time)->format('h:i A') }}
                                        ({{ number_format($todayAttendance->total_hours, 1) }} hours)
                                    </p>
                                @else
                                    <!-- Clock In Button -->
                                    <button id="clock-in-btn" class="btn btn-success clock-button clock-in-btn btn-lg" disabled>
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        <span class="loading-spinner">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                        <span class="button-text">Clock In</span>
                                    </button>
                                    <p class="text-muted mt-2">Click to start your workday</p>
                                @endif

                                <!-- Button Status -->
                                <div id="button-status" class="mt-2" style="display: none;">
                                    <small class="text-info">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <span id="button-status-text">Buttons will be enabled once location is detected</span>
                                    </small>
                                </div>

                                <!-- Location Status -->
                                <div id="location-status" class="mt-3">
                                    <div class="alert alert-info" id="location-alert">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        <span id="location-text">Getting your location...</span>
                                        <div class="spinner-border spinner-border-sm ml-2" role="status" id="location-spinner">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Summary -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Today's Summary
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($todayAttendance)
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="description-block">
                                                <h5 class="description-header text-success">
                                                    {{ $todayAttendance->clock_in_time ? \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A') : '--:--' }}
                                                </h5>
                                                <span class="description-text">Clock In</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="description-block">
                                                <h5 class="description-header text-danger">
                                                    {{ $todayAttendance->clock_out_time ? \Carbon\Carbon::parse($todayAttendance->clock_out_time)->format('h:i A') : '--:--' }}
                                                </h5>
                                                <span class="description-text">Clock Out</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="description-block">
                                                <h5 class="description-header text-info">
                                                    {{ $todayAttendance->total_hours ? number_format($todayAttendance->total_hours, 1) . 'h' : 'In Progress' }}
                                                </h5>
                                                <span class="description-text">Total Hours</span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No attendance record yet</h5>
                                        <p class="text-muted">Clock in to start tracking your time</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- This Week's Attendance -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-week mr-2"></i>
                    This Week's Attendance
                </h3>
                <div class="card-tools">
                    <a href="{{ route('staff.attendance.history') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-history mr-1"></i>
                        View Full History
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($weekAttendance->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weekAttendance as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('M d, Y') }}</td>
                                    <td>{{ $attendance->date->format('l') }}</td>
                                    <td>
                                        @if($attendance->clock_in_time)
                                            <span class="text-success">
                                                <i class="fas fa-sign-in-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($attendance->clock_in_time)->format('h:i A') }}
                                            </span>
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
                                        @else
                                            <span class="text-muted">--:--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->total_hours)
                                            <span class="badge badge-info">{{ number_format($attendance->total_hours, 1) }}h</span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            Present
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Week Summary -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-calendar-check"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Days Present</span>
                                    <span class="info-box-number">{{ $monthSummary['present_days'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Hours</span>
                                    <span class="info-box-number">{{ number_format($monthSummary['total_hours'], 1) }}h</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Days</span>
                                    <span class="info-box-number">{{ $monthSummary['total_days'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-percentage"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Attendance Rate</span>
                                    <span class="info-box-number">
                                        {{ $monthSummary['total_days'] > 0 ? number_format(($monthSummary['present_days'] / $monthSummary['total_days']) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No attendance records this week</h5>
                        <p class="text-muted">Start by clocking in today!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceModalLabel">
                    <i id="attendanceModalIcon" class="fas fa-info-circle mr-2"></i>
                    <span id="attendanceModalTitle">Attendance</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="attendanceModalMessage" class="text-center">
                    <!-- Message will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="attendanceModalAction" class="btn btn-primary" style="display: none;">
                    Action
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <h6 id="loadingModalMessage">Processing...</h6>
                <p class="text-muted mb-0" id="loadingModalSubtext">Please wait</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Wait for both DOM and jQuery to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is loaded
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded!');
        // Fallback: try to load jQuery from CDN
        var script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        script.onload = function() {
            console.log('jQuery loaded from CDN');
            initializeAttendancePage();
        };
        document.head.appendChild(script);
        return;
    }

    // jQuery is available, initialize
    initializeAttendancePage();
});

function initializeAttendancePage() {
    console.log('Attendance page loaded');

    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Debug: Check if CSRF token is available
    console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));

    let userLocation = null;
    let locationDisplayText = null; // Store the successful location text
    let isLocationDetected = false; // Track if location has been successfully detected

    // Modal helper functions
    function showAttendanceModal(type, title, message, actionText = null, actionCallback = null) {
        // Force hide any existing modals first with a more thorough cleanup
        $('.modal').each(function() {
            if ($(this).hasClass('show')) {
                $(this).modal('hide');
            }
        });

        // Clean up any lingering modal artifacts
        setTimeout(() => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');
            $('body').css('overflow', '');
        }, 100);

        const modal = $('#attendanceModal');
        const headerEl = modal.find('.modal-header');
        const titleEl = modal.find('#attendanceModalTitle');
        const messageEl = modal.find('#attendanceModalMessage');
        const actionBtn = modal.find('#attendanceModalAction');

        // Reset header classes
        headerEl.removeClass('bg-success bg-danger bg-warning bg-info text-white text-dark');

        // Apply styling based on type
        switch(type) {
            case 'success':
                headerEl.addClass('bg-success text-white');
                break;
            case 'error':
                headerEl.addClass('bg-danger text-white');
                break;
            case 'warning':
                headerEl.addClass('bg-warning text-dark');
                break;
            default:
                headerEl.addClass('bg-info text-white');
        }

        titleEl.text(title);
        messageEl.html(message);

        // Handle action button
        if (actionText && actionCallback) {
            actionBtn.text(actionText).show().off('click').on('click', actionCallback);
        } else {
            actionBtn.hide();
        }

        // Delay showing the modal to ensure cleanup is complete
        setTimeout(() => {
            modal.modal('show');
        }, 150);
    }

    function showLoadingModal(message, subtext = 'Please wait') {
        // Clean up any existing modals first
        hideAllModals();

        $('#loadingModalMessage').text(message);
        $('#loadingModalSubtext').text(subtext);

        setTimeout(() => {
            $('#loadingModal').modal('show');
        }, 100);
    }

    function hideLoadingModal() {
        const loadingModal = $('#loadingModal');
        if (loadingModal.hasClass('show') || loadingModal.data('bs.modal')?._isShown) {
            loadingModal.modal('hide');
        }

        // Force cleanup after a delay
        setTimeout(() => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');
            $('body').css('overflow', '');
        }, 250);
    }

    function hideAllModals() {
        $('.modal').each(function() {
            if ($(this).hasClass('show') || $(this).data('bs.modal')?._isShown) {
                $(this).modal('hide');
            }
        });

        setTimeout(() => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');
            $('body').css('overflow', '');
        }, 100);
    }

    // Reverse geocoding function
    function reverseGeocode(lat, lng) {
        return new Promise((resolve, reject) => {
            // Use OpenStreetMap Nominatim instead of Google Maps for better compatibility
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        resolve(data.display_name);
                    } else {
                        reject('No address found');
                    }
                })
                .catch(error => {
                    console.log('Reverse geocoding failed:', error);
                    reject(error);
                });
        });
    }

    // Get current location
    function getCurrentLocation() {
        console.log('Getting current location...');

        // Disable buttons and show loading state
        disableClockButtons();
        updateLocationStatus('fetching', 'Getting your location...', true);

        if (navigator.geolocation) {
            console.log('Geolocation is supported');

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    console.log('Position obtained:', position);

                    userLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };

                    console.log('Location obtained:', userLocation);
                    updateLocationStatus('success', 'Location detected', false);

                    // Enable buttons now that location is available
                    enableClockButtons();

                    // Try to get address
                    reverseGeocode(userLocation.latitude, userLocation.longitude)
                        .then((address) => {
                            updateLocationStatus('success', address, false);
                            console.log('Address resolved:', address);
                        })
                        .catch((error) => {
                            console.log('Address resolution failed:', error);
                            updateLocationStatus('success', 'Location detected', false);
                        });
                },
                function(error) {
                    console.error('Error getting location:', error);

                    let errorMessage = 'Unable to get your location. ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += 'Location access was denied. Please enable location services and refresh the page.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage += 'Location request timed out.';
                            break;
                        default:
                            errorMessage += 'An unknown error occurred.';
                            break;
                    }

                    updateLocationStatus('warning', 'Location access denied', false);

                    showAttendanceModal('warning', 'Location Required', errorMessage + '<br><br>Using default location for now.', 'Retry', function() {
                        $('#attendanceModal').modal('hide');
                        getCurrentLocation();
                    });

                    // Set default location for testing and enable buttons
                    userLocation = {
                        latitude: 5.6037,
                        longitude: -0.1870
                    };
                    updateLocationStatus('warning', 'Default location (Accra, Ghana)', false);
                    enableClockButtons();
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000, // Increased timeout
                    maximumAge: 300000 // 5 minutes
                }
            );
        } else {
            console.log('Geolocation not supported');
            updateLocationStatus('error', 'Geolocation not supported', false);
            showAttendanceModal('error', 'Geolocation Not Supported', 'Your browser does not support location services. Using default location.');

            // Set default location for testing and enable buttons
            userLocation = {
                latitude: 5.6037,
                longitude: -0.1870
            };
            updateLocationStatus('warning', 'Default location (Accra, Ghana)', false);
            enableClockButtons();
        }
    }

    // Helper functions for button and location status management
    function disableClockButtons() {
        $('#clock-in-btn, #clock-out-btn')
            .prop('disabled', true)
            .addClass('btn-disabled location-fetching');

        // Show button status message
        $('#button-status').show();
        $('#button-status-text').text('Buttons will be enabled once location is detected');
    }

    function enableClockButtons() {
        $('#clock-in-btn, #clock-out-btn')
            .prop('disabled', false)
            .removeClass('btn-disabled location-fetching');

        // Hide button status message
        $('#button-status').hide();
    }

    function updateLocationStatus(type, message, showSpinner, forceUpdate = false) {
        const locationAlert = $('#location-alert');
        const locationText = $('#location-text');
        const locationSpinner = $('#location-spinner');

        // If location has been successfully detected and this isn't a forced update,
        // preserve the existing successful location display
        if (isLocationDetected && !forceUpdate && type !== 'success') {
            console.log('Location already detected, preserving display:', locationDisplayText);
            return;
        }

        // Update alert class based on type
        locationAlert.removeClass('alert-info alert-success alert-warning alert-danger');
        switch(type) {
            case 'fetching':
                locationAlert.addClass('alert-info');
                break;
            case 'success':
                locationAlert.addClass('alert-success');
                // Store successful location text and mark as detected
                locationDisplayText = message;
                isLocationDetected = true;
                break;
            case 'warning':
                locationAlert.addClass('alert-warning');
                // If this is a default location, still mark as detected
                if (message.includes('Default location')) {
                    locationDisplayText = message;
                    isLocationDetected = true;
                }
                break;
            case 'error':
                locationAlert.addClass('alert-danger');
                break;
            default:
                locationAlert.addClass('alert-info');
        }

        // Update message
        locationText.text(message);

        // Show/hide spinner
        if (showSpinner) {
            locationSpinner.show();
        } else {
            locationSpinner.hide();
        }
    }

    // Enhanced location preservation function
    function preserveLocationDisplay() {
        if (isLocationDetected && locationDisplayText) {
            updateLocationStatus('success', locationDisplayText, false, true);
        }
    }

    // Clock In
    $('#clock-in-btn').click(function(e) {
        e.preventDefault();

        console.log('Clock in button clicked');
        console.log('User location:', userLocation);
        console.log('Location detected status:', isLocationDetected);

        if (!userLocation) {
            // Only show fetching message if location hasn't been detected yet
            if (!isLocationDetected) {
                updateLocationStatus('fetching', 'Please wait while we get your location...', true);
                disableClockButtons();
            }

            showAttendanceModal('warning', 'Location Required', 'Please wait while we get your location...', 'Retry', function() {
                $('#attendanceModal').modal('hide');
                getCurrentLocation();
            });
            return;
        }

        // Basic location validation
        if (!isValidLocation(userLocation.latitude, userLocation.longitude)) {
            showAttendanceModal('error', 'Invalid Location', 'The location data appears to be invalid. Please refresh the page and try again.');
            return;
        }

        // Preserve location display during the operation
        preserveLocationDisplay();

        const button = $(this);
        const spinner = button.find('.loading-spinner');
        const text = button.find('.button-text');

        // Prevent double-clicking
        if (button.hasClass('processing')) {
            return;
        }

        button.addClass('processing').prop('disabled', true);
        spinner.addClass('show');
        text.text('Clocking In...');

        showLoadingModal('Clocking In...', 'Recording your attendance');

        // Prepare data with additional validation
        const requestData = {
            latitude: parseFloat(userLocation.latitude).toFixed(8),
            longitude: parseFloat(userLocation.longitude).toFixed(8),
            address: locationDisplayText || $('#location-text').text().substring(0, 500), // Use stored location text
            timestamp: new Date().toISOString(), // Add timestamp for additional validation
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        console.log('Sending clock-in request with data:', requestData);

        $.ajax({
            url: '{{ route("staff.attendance.clock-in") }}',
            method: 'POST',
            data: requestData,
            dataType: 'json',
            timeout: 30000, // 30 second timeout
            success: function(response) {
                hideLoadingModal();
                console.log('Clock-in success:', response);

                if (response.success) {
                    // Add a delay to ensure loading modal is completely hidden
                    setTimeout(() => {
                        showAttendanceModal('success', 'Clock In Successful',
                            '<i class="fas fa-check-circle fa-3x text-success mb-3"></i><br>' +
                            '<strong>Welcome to work!</strong><br>' +
                            'You have successfully clocked in at ' + new Date().toLocaleTimeString() + '.<br>' +
                            'Have a productive day!', 'Continue', function() {
                            location.reload();
                        });
                    }, 500);
                } else {
                    // Preserve location display even on error
                    preserveLocationDisplay();
                    setTimeout(() => {
                        showAttendanceModal('error', 'Clock In Failed', response.message || 'Clock-in failed. Please try again.');
                    }, 500);
                }
            },
            error: function(xhr, status, error) {
                hideLoadingModal();
                console.error('Clock-in error details:');
                console.error('XHR:', xhr);
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response text:', xhr.responseText);

                // Preserve location display even on error
                preserveLocationDisplay();

                let errorTitle = 'Clock In Error';
                let errorMessage = 'Unable to clock in. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Handle validation errors
                    const errors = xhr.responseJSON.errors;
                    errorMessage = 'Validation errors: ' + Object.values(errors).flat().join(', ');
                } else if (xhr.status === 419) {
                    errorTitle = 'Session Expired';
                    errorMessage = 'Your session has expired. Please refresh the page and try again.';
                } else if (xhr.status === 422) {
                    errorTitle = 'Invalid Data';
                    errorMessage = 'Invalid data submitted. Please check your location and try again.';
                } else if (xhr.status === 429) {
                    errorTitle = 'Too Many Requests';
                    errorMessage = 'Please wait a moment before trying again.';
                } else if (xhr.status === 500) {
                    errorTitle = 'Server Error';
                    errorMessage = 'A server error occurred. Please contact IT support if this persists.';
                } else if (status === 'timeout') {
                    errorTitle = 'Request Timeout';
                    errorMessage = 'The request timed out. Please check your internet connection and try again.';
                }

                setTimeout(() => {
                    showAttendanceModal('error', errorTitle, errorMessage, 'Retry', function() {
                        $('#attendanceModal').modal('hide');
                        setTimeout(() => {
                            $('#clock-in-btn').click();
                        }, 500);
                    });
                }, 500);
            },
            complete: function() {
                // Preserve location display when operation completes
                preserveLocationDisplay();
                button.removeClass('processing').prop('disabled', false);
                spinner.removeClass('show');
                text.text('Clock In');
            }
        });
    });

    // Clock Out
    $('#clock-out-btn').click(function(e) {
        e.preventDefault();

        console.log('Clock out button clicked');
        console.log('User location:', userLocation);
        console.log('Location detected status:', isLocationDetected);

        if (!userLocation) {
            // Only show fetching message if location hasn't been detected yet
            if (!isLocationDetected) {
                updateLocationStatus('fetching', 'Please wait while we get your location...', true);
                disableClockButtons();
            }

            showAttendanceModal('warning', 'Location Required', 'Please wait while we get your location...', 'Retry', function() {
                $('#attendanceModal').modal('hide');
                getCurrentLocation();
            });
            return;
        }

        // Basic location validation
        if (!isValidLocation(userLocation.latitude, userLocation.longitude)) {
            showAttendanceModal('error', 'Invalid Location', 'The location data appears to be invalid. Please refresh the page and try again.');
            return;
        }

        // Preserve location display during the operation
        preserveLocationDisplay();

        const button = $(this);
        const spinner = button.find('.loading-spinner');
        const text = button.find('.button-text');

        // Prevent double-clicking
        if (button.hasClass('processing')) {
            return;
        }

        button.addClass('processing').prop('disabled', true);
        spinner.addClass('show');
        text.text('Clocking Out...');

        showLoadingModal('Clocking Out...', 'Recording your departure');

        // Prepare data with additional validation
        const requestData = {
            latitude: parseFloat(userLocation.latitude).toFixed(8),
            longitude: parseFloat(userLocation.longitude).toFixed(8),
            address: locationDisplayText || $('#location-text').text().substring(0, 500), // Use stored location text
            timestamp: new Date().toISOString(), // Add timestamp for additional validation
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        console.log('Sending clock-out request with data:', requestData);

        $.ajax({
            url: '{{ route("staff.attendance.clock-out") }}',
            method: 'POST',
            data: requestData,
            dataType: 'json',
            timeout: 30000, // 30 second timeout
            success: function(response) {
                hideLoadingModal();
                console.log('Clock-out success:', response);

                if (response.success) {
                    // Add a delay to ensure loading modal is completely hidden
                    setTimeout(() => {
                        showAttendanceModal('success', 'Clock Out Successful',
                            '<i class="fas fa-sign-out-alt fa-3x text-success mb-3"></i><br>' +
                            '<strong>Have a great day!</strong><br>' +
                            'You have successfully clocked out at ' + new Date().toLocaleTimeString() + '.<br>' +
                            '<div class="alert alert-info mt-2">' +
                            '<i class="fas fa-clock mr-2"></i>Total hours worked: ' +
                            (response.total_hours ? response.total_hours.toFixed(1) + 'h' : 'Calculating...') +
                            '</div>' +
                            'Your attendance has been recorded.', 'Continue', function() {
                            location.reload();
                        });
                    }, 500);
                } else {
                    // Preserve location display even on error
                    preserveLocationDisplay();
                    setTimeout(() => {
                        showAttendanceModal('error', 'Clock Out Failed', response.message || 'Clock-out failed. Please try again.');
                    }, 500);
                }
            },
            error: function(xhr, status, error) {
                hideLoadingModal();
                console.error('Clock-out error details:');
                console.error('XHR:', xhr);
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response text:', xhr.responseText);

                // Preserve location display even on error
                preserveLocationDisplay();

                let errorTitle = 'Clock Out Error';
                let errorMessage = 'Unable to clock out. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Handle validation errors
                    const errors = xhr.responseJSON.errors;
                    errorMessage = 'Validation errors: ' + Object.values(errors).flat().join(', ');
                } else if (xhr.status === 419) {
                    errorTitle = 'Session Expired';
                    errorMessage = 'Your session has expired. Please refresh the page and try again.';
                } else if (xhr.status === 422) {
                    errorTitle = 'Invalid Data';
                    errorMessage = 'Invalid data submitted. Please check your location and try again.';
                } else if (xhr.status === 429) {
                    errorTitle = 'Too Many Requests';
                    errorMessage = 'Please wait a moment before trying again.';
                } else if (xhr.status === 500) {
                    errorTitle = 'Server Error';
                    errorMessage = 'A server error occurred. Please contact IT support if this persists.';
                } else if (status === 'timeout') {
                    errorTitle = 'Request Timeout';
                    errorMessage = 'The request timed out. Please check your internet connection and try again.';
                }

                setTimeout(() => {
                    showAttendanceModal('error', errorTitle, errorMessage, 'Retry', function() {
                        $('#attendanceModal').modal('hide');
                        setTimeout(() => {
                            $('#clock-out-btn').click();
                        }, 500);
                    });
                }, 500);
            },
            complete: function() {
                // Preserve location display when operation completes
                preserveLocationDisplay();
                button.removeClass('processing').prop('disabled', false);
                spinner.removeClass('show');
                text.text('Clock Out');
            }
        });
    });

    // Location validation helper function
    function isValidLocation(lat, lng) {
        // Basic validation for latitude and longitude ranges
        return lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180 &&
               !isNaN(lat) && !isNaN(lng) &&
               (lat !== 0 || lng !== 0); // Reject exact 0,0 coordinates as suspicious (wrap in parentheses to be explicit)
    }

    // Update current time every second
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: true,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        $('#current-time').text(timeString);
    }

    // Initialize
    try {
        getCurrentLocation();
        console.log('Location initialized');
    } catch (error) {
        console.error('Location initialization failed:', error);
        setTimeout(() => {
            showAttendanceModal('error', 'Initialization Error', 'Failed to initialize location services. Please refresh the page.');
        }, 300);
    }

    updateTime();

    // Update time every second
    setInterval(updateTime, 1000);

    console.log('Attendance page JavaScript initialized');

    // Enhanced modal cleanup on page events
    $(window).on('beforeunload', function() {
        hideAllModals();
    });

    // Handle modal cleanup when modals are hidden
    $('.modal').on('hidden.bs.modal', function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
        $('body').css('overflow', '');
    });

    // Prevent multiple modal instances
    $('.modal').on('show.bs.modal', function() {
        // Hide any other visible modals
        $('.modal').not(this).modal('hide');
    });

    // Add escape key handling for better UX
    $(document).on('keyup', function(e) {
        if (e.key === 'Escape') {
            if ($('#loadingModal').hasClass('show')) {
                // Don't allow escape to close loading modal during operations
                return false;
            }
        }
    });
}
</script>

@push('styles')
<style>
    .info-box-number {
        font-size: 14px !important;
        font-weight: normal !important;
    }

    .attendance-card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .clock-button {
        width: 150px;
        height: 60px;
        font-size: 16px;
        font-weight: bold;
        border-radius: 10px;
        transition: all 0.3s ease;
        position: relative;
    }

    .clock-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .clock-button:disabled,
    .clock-button.btn-disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    .clock-button:disabled:hover,
    .clock-button.btn-disabled:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    .clock-button .loading-spinner {
        display: none;
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
    }

    .clock-button .loading-spinner.show {
        display: inline-block;
    }

    .status-present {
        color: #28a745 !important;
    }

    .status-absent {
        color: #dc3545 !important;
    }

    .status-late {
        color: #ffc107 !important;
    }

    /* Modal Styles */
    .modal-header.bg-success {
        background-color: #28a745 !important;
        border-color: #1e7e34;
    }

    .modal-header.bg-danger {
        background-color: #dc3545 !important;
        border-color: #bd2130;
    }

    .modal-header.bg-warning {
        background-color: #ffc107 !important;
        border-color: #d39e00;
    }

    .modal-header.text-white .close {
        color: white;
        opacity: 0.8;
    }

    .modal-header.text-white .close:hover {
        opacity: 1;
    }

    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
    }

    #attendanceModal .modal-body {
        padding: 2rem;
    }

    #attendanceModal .modal-body i.fa-3x {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    #loadingModal .modal-content {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    #loadingModal .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* Enhanced modal transition and backdrop fixes */
    .modal {
        z-index: 1055;
    }

    .modal-backdrop {
        z-index: 1054;
    }

    /* Ensure modals don't interfere with each other */
    .modal.fade {
        transition: opacity 0.25s linear;
    }

    .modal.fade .modal-dialog {
        transition: transform 0.25s ease-out;
        transform: translate(0, -50px);
    }

    .modal.show .modal-dialog {
        transform: translate(0, 0);
    }

    /* Prevent modal backdrop conflicts */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    /* Loading modal specific styling */
    #loadingModal {
        z-index: 1060;
    }

    #loadingModal + .modal-backdrop {
        z-index: 1059;
    }

    /* Animation for modal entrance */
    .modal.fade .modal-dialog {
        transform: translate(0, -50px);
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: translate(0, 0);
    }

    /* Pulse animation for loading */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    #loadingModal .spinner-border {
        animation: pulse 2s infinite;
    }

    /* Location Status Styles */
    #location-alert {
        font-size: 14px;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    #location-alert .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.125em;
    }

    #location-alert.alert-info {
        background-color: #e3f2fd;
        color: #1976d2;
        border-left: 4px solid #2196f3;
    }

    #location-alert.alert-success {
        background-color: #e8f5e8;
        color: #2e7d32;
        border-left: 4px solid #4caf50;
    }

    #location-alert.alert-warning {
        background-color: #fff3e0;
        color: #f57c00;
        border-left: 4px solid #ff9800;
    }

    #location-alert.alert-danger {
        background-color: #ffebee;
        color: #d32f2f;
        border-left: 4px solid #f44336;
    }

    /* Button text update during location fetch */
    .location-fetching .button-text::after {
        content: " (Getting location...)";
        font-size: 11px;
        opacity: 0.8;
    }
</style>
@endpush
