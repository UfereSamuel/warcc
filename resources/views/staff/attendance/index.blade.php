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
                        <div class="col-md-8">
                                @if($todayAttendance && $todayAttendance->clock_in_time && !$todayAttendance->clock_out_time)
                                <!-- Currently Clocked In - Show Clock Out -->
                                <div class="alert alert-success">
                                    <h4><i class="fas fa-check-circle mr-2"></i>You're Currently Clocked In</h4>
                                    <p class="mb-2">Started work at: <strong>{{ \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A') }}</strong></p>
                                    <p class="mb-3">Working for: <span id="work-duration">0h 0m</span></p>
                                </div>
                                
                                <div class="text-center">
                                    <button id="clock-out-btn" class="btn btn-danger btn-lg px-5" disabled>
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Clock Out
                                    </button>
                                    <p class="text-muted mt-2 small" id="location-wait-message-out">
                                        <i class="fas fa-spinner fa-spin mr-1"></i>
                                        Waiting for location...
                                    </p>
                                    <p class="mt-2" id="location-override-out" style="display: none;">
                                        <button class="btn btn-sm btn-outline-secondary" onclick="useDefaultLocation()">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Use Default Location
                                        </button>
                                    </p>
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
                                    <button id="clock-in-btn" class="btn btn-success btn-lg px-5" disabled>
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        Clock In
                                    </button>
                                    <p class="text-muted mt-2 small" id="location-wait-message">
                                        <i class="fas fa-spinner fa-spin mr-1"></i>
                                        Waiting for location...
                                    </p>
                                    <p class="mt-2" id="location-override" style="display: none;">
                                        <button class="btn btn-sm btn-outline-secondary" onclick="useDefaultLocation()">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Use Default Location
                                        </button>
                                    </p>
                                </div>
                                @endif
                                </div>

                        <!-- Location Status Section -->
                        <div class="col-md-4">
                            @if(!$todayAttendance || !$todayAttendance->clock_out_time)
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Location Status
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="location-status">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary mb-2" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                            <p class="text-muted mb-0">Detecting location...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                            @endif
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
                <button type="button" class="btn btn-warning" id="default-location-btn" style="display: none;" onclick="useDefaultLocationFromModal()">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    Use Default Location
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

#location-status .card-body {
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    border: none;
    border-radius: 15px;
}
</style>
@endsection

@section('js')
<script>
// Global variables
let userLocation = null;
let locationReady = false;
let clockInTime = null;
let locationAttempts = 0;
let maxLocationAttempts = 3;

// Initialize everything when document is ready
$(document).ready(function() {
    console.log('Attendance page loaded');
    
    // Start real-time clock
    updateClock();
    setInterval(updateClock, 1000);
    
    // Initialize location if needed
    @if(!$todayAttendance || !$todayAttendance->clock_out_time)
        initializeLocation();
    @endif
    
    // Initialize work duration counter if clocked in
    @if($todayAttendance && $todayAttendance->clock_in_time && !$todayAttendance->clock_out_time)
        clockInTime = '{{ $todayAttendance->clock_in_time }}';
        updateWorkDuration();
        setInterval(updateWorkDuration, 60000); // Update every minute
    @endif
    
    // Bind event handlers
    bindEventHandlers();
});

// Update current time display
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

// Update work duration counter
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

// Initialize location detection with fast fallback
function initializeLocation() {
    locationAttempts++;
    console.log(`Location detection attempt ${locationAttempts}/${maxLocationAttempts}`);
    
    updateLocationStatus('loading', 'Getting location...');
    disableAttendanceButtons();
    
    if (!navigator.geolocation) {
        console.error('Geolocation not supported');
        updateLocationStatus('warning', 'Browser location not supported - using default');
        useDefaultLocation();
        return;
    }
    
    // Shorter timeouts to prevent getting stuck
    const fastOptions = {
        enableHighAccuracy: false,  // Start with network-based location (faster)
        timeout: 5000,              // Reduced timeout for first attempt
        maximumAge: 300000          // 5 minutes cache
    };
    
    const slowOptions = {
        enableHighAccuracy: true,   // GPS-based for better accuracy
        timeout: 8000,              // Timeout for GPS
        maximumAge: 60000           // 1 minute cache
    };
    
    console.log('Trying fast location detection...');
    updateLocationStatus('loading', 'Quick location check...');
    
    // Global timeout to prevent getting stuck
    const globalTimeout = setTimeout(() => {
        if (!locationReady) {
            console.warn('Global timeout reached - forcing default location');
            clearLocationTimeouts();
            useDefaultLocation();
        }
    }, 12000); // 12 seconds max wait time
    
    // Store timeout ID for cleanup
    window.locationGlobalTimeout = globalTimeout;
    
    // First attempt: Fast network-based location
            navigator.geolocation.getCurrentPosition(
                function(position) {
            console.log('Fast location success:', position.coords);
            clearLocationTimeouts();
            handleLocationSuccess(position);
        },
        function(error) {
            console.log('Fast location failed, trying accurate location...', error);
            
            // Only try second attempt if not a permission error
            if (error.code === error.PERMISSION_DENIED) {
                clearLocationTimeouts();
                handleLocationError(error);
                return;
            }
            
            updateLocationStatus('loading', 'Getting precise location...');
            
            // Second attempt: More accurate GPS-based location
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    console.log('Accurate location success:', position.coords);
                    clearLocationTimeouts();
                    handleLocationSuccess(position);
                },
                function(error) {
                    console.error('Both location attempts failed:', error);
                    clearLocationTimeouts();
                    handleLocationError(error);
                },
                slowOptions
            );
        },
        fastOptions
    );
}

// Clear all location timeouts
function clearLocationTimeouts() {
    if (window.locationGlobalTimeout) {
        clearTimeout(window.locationGlobalTimeout);
        window.locationGlobalTimeout = null;
    }
    if (window.manualLocationTimer) {
        clearTimeout(window.manualLocationTimer);
        window.manualLocationTimer = null;
    }
}

// Show manual location buttons
function showManualLocationButtons() {
    $('.btn[onclick="useDefaultLocation()"]').show();
    $('#default-location-btn').show();
}

// Hide manual location buttons  
function hideManualLocationButtons() {
    $('.btn[onclick="useDefaultLocation()"]').hide();
    $('#default-location-btn').hide();
}

// Handle successful location detection
function handleLocationSuccess(position) {
    console.log('Location detected successfully:', position.coords);
    
    // Validate coordinates
    if (!position.coords.latitude || !position.coords.longitude) {
        console.error('Invalid coordinates received');
        handleLocationError({ code: 'INVALID_COORDS', message: 'Invalid coordinates' });
        return;
    }

                    userLocation = {
        latitude: parseFloat(position.coords.latitude),
        longitude: parseFloat(position.coords.longitude),
        accuracy: position.coords.accuracy
    };
    
    // Additional validation for suspicious coordinates
    if (userLocation.latitude === 0 && userLocation.longitude === 0) {
        console.error('Suspicious coordinates (0,0) detected');
        if (locationAttempts < maxLocationAttempts) {
            setTimeout(() => {
                initializeLocation();
            }, 2000);
        } else {
            useDefaultLocation();
        }
        return;
    }
    
    locationReady = true;
    
    // Immediately enable buttons and show basic location
    const basicLocation = `Location: ${userLocation.latitude.toFixed(4)}, ${userLocation.longitude.toFixed(4)}`;
    updateLocationStatus('success', basicLocation);
    enableAttendanceButtons();
    
    // Optional: Try to get a better address in the background (non-blocking)
    getLocationAddress(userLocation.latitude, userLocation.longitude)
        .then(address => {
            // Only update if we got a better address
            if (address && address !== basicLocation) {
                updateLocationStatus('success', address);
            }
        })
        .catch(error => {
            console.log('Address lookup failed (non-critical):', error);
            // Keep the basic location display - don't change anything
        });
}

// Handle location detection errors
function handleLocationError(error) {
    console.error('Location error:', error);
    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
            showLocationPermissionDialog();
                            break;
                        case error.POSITION_UNAVAILABLE:
            updateLocationStatus('warning', 'Location unavailable - using default location');
            setTimeout(() => useDefaultLocation(), 1000);
                            break;
                        case error.TIMEOUT:
            updateLocationStatus('warning', 'Location timeout - using default location');
            setTimeout(() => useDefaultLocation(), 1000);
                            break;
                        default:
            updateLocationStatus('warning', 'Location failed - using default location');
            setTimeout(() => useDefaultLocation(), 1000);
                            break;
    }
}

// Handle location timeout - just use default immediately
function handleLocationTimeout() {
    console.error('Location detection timed out - using default');
    updateLocationStatus('warning', 'Location timeout - using default location');
    setTimeout(() => {
        useDefaultLocation();
    }, 500);
}

// Show location permission dialog
function showLocationPermissionDialog() {
    updateLocationStatus('error', 'Location permission required');
    
    // Detect browser type for specific instructions
    const isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    const isFirefox = /Firefox/.test(navigator.userAgent);
    const isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
    const isEdge = /Edg/.test(navigator.userAgent);
    
    let browserInstructions = '';
    if (isChrome || isEdge) {
        browserInstructions = `
            <li>Look for a location/GPS icon <i class="fas fa-map-marker-alt text-warning"></i> in the left side of your address bar</li>
            <li>Click on it and select "Allow" or "Always allow on this site"</li>
            <li>If blocked, click the icon and change it to "Allow"</li>
        `;
    } else if (isFirefox) {
        browserInstructions = `
            <li>Look for a shield icon or notification in the address bar</li>
            <li>Click on it and select "Allow Location Access"</li>
            <li>You may need to refresh the page after allowing</li>
        `;
    } else if (isSafari) {
        browserInstructions = `
            <li>Go to Safari menu → Preferences → Websites → Location Services</li>
            <li>Find this website and set it to "Allow"</li>
            <li>Or refresh the page and click "Allow" when prompted</li>
        `;
    } else {
        browserInstructions = `
            <li>Look for a location/GPS icon in your browser's address bar</li>
            <li>Click on it and select "Allow" or "Always allow"</li>
            <li>If you don't see it, refresh this page and click "Allow" when prompted</li>
        `;
    }
    
    showErrorModal(
        'Location Permission Required',
        `<div class="text-center">
            <i class="fas fa-map-marker-alt fa-3x text-warning mb-3"></i>
            <p><strong>We need your location to record accurate attendance.</strong></p>
            <p>Please follow these steps:</p>
            <ol class="text-left">${browserInstructions}</ol>
            <div class="alert alert-info mt-3 mb-0">
                <small><i class="fas fa-info-circle mr-1"></i>
                If location is still blocked, try refreshing the page or use the default location below.</small>
            </div>
        </div>`,
        true,
        'permission'
    );
}

// Use default location as fallback
function useDefaultLocation() {
    // Clear any existing timeouts
    clearLocationTimeouts();
    
            userLocation = {
                latitude: 5.6037,
        longitude: -0.1870,
        accuracy: null
    };
    locationReady = true;
    
    console.log('Using default location (Accra, Ghana)');
    updateLocationStatus('warning', 'Using default location (Accra, Ghana)');
    enableAttendanceButtons();
    
    // Hide any manual override buttons since location is now ready
    hideManualLocationButtons();
}

// Get human-readable address from coordinates (fast, non-blocking)
function getLocationAddress(lat, lng) {
    return new Promise((resolve, reject) => {
        // Return immediately with coordinates - can be enhanced with actual geocoding API later
        resolve(`Location: ${lat.toFixed(4)}, ${lng.toFixed(4)}`);
    });
}

// Update location status display
function updateLocationStatus(type, message) {
    const statusDiv = $('#location-status');
    
    let icon, bgClass, textClass;
        switch(type) {
        case 'loading':
            icon = '<div class="spinner-border text-primary mb-2" role="status"><span class="sr-only">Loading...</span></div>';
            bgClass = 'alert-info';
            textClass = 'text-info';
                break;
            case 'success':
            icon = '<i class="fas fa-check-circle fa-2x text-success mb-2"></i>';
            bgClass = 'alert-success';
            textClass = 'text-success';
                break;
            case 'warning':
            icon = '<i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>';
            bgClass = 'alert-warning';
            textClass = 'text-warning';
                break;
            case 'error':
            icon = '<i class="fas fa-times-circle fa-2x text-danger mb-2"></i>';
            bgClass = 'alert-danger';
            textClass = 'text-danger';
                break;
    }
    
    statusDiv.html(`
        <div class="alert ${bgClass} text-center mb-0">
            ${icon}
            <p class="mb-0 ${textClass} small">${message}</p>
        </div>
    `);
}

// Disable attendance buttons while location is loading
function disableAttendanceButtons() {
    $('#clock-in-btn, #clock-out-btn').prop('disabled', true);
    $('#location-wait-message, #location-wait-message-out').show();
    $('#location-override, #location-override-out').hide();
    
    // Show manual override option after 5 seconds
    setTimeout(() => {
        if (!locationReady) {
            $('#location-override, #location-override-out').show();
        }
    }, 5000);
}

// Enable attendance buttons when location is ready
function enableAttendanceButtons() {
    $('#clock-in-btn, #clock-out-btn').prop('disabled', false);
    $('#location-wait-message, #location-wait-message-out').hide();
    $('#location-override, #location-override-out').hide();
}

// Bind event handlers
function bindEventHandlers() {
    // Clock In button
    $(document).on('click', '#clock-in-btn', function(e) {
        e.preventDefault();
        if (locationReady) {
            handleClockIn();
        } else {
            showErrorModal('Location Required', 'Please wait for location detection to complete.');
        }
    });
    
    // Clock Out button
    $(document).on('click', '#clock-out-btn', function(e) {
        e.preventDefault();
        if (locationReady) {
            handleClockOut();
        } else {
            showErrorModal('Location Required', 'Please wait for location detection to complete.');
        }
    });
    
    // Retry button in error modal
    $('#retry-btn').on('click', function() {
        $('#errorModal').modal('hide');
        const action = $(this).data('action');
        if (action === 'clock-in') {
            handleClockIn();
        } else if (action === 'clock-out') {
            handleClockOut();
        } else if (action === 'permission') {
            // Retry location detection after permission grant
            locationAttempts = 0; // Reset attempts
            initializeLocation();
        }
    });
}

// Handle clock in
function handleClockIn() {
    console.log('Clock in requested');
    
    if (!locationReady || !userLocation) {
        showErrorModal('Location Required', 'Location is not ready. Please wait or refresh the page.');
        return;
    }
    
    // Final validation of location
    if (!userLocation.latitude || !userLocation.longitude) {
        showErrorModal('Invalid Location', 'Invalid location data. Please refresh the page and try again.');
        return;
    }
    
    performClockAction('in');
}

// Handle clock out
function handleClockOut() {
    console.log('Clock out requested');
    
    if (!locationReady || !userLocation) {
        showErrorModal('Location Required', 'Location is not ready. Please wait or refresh the page.');
            return;
        }

    // Final validation of location
    if (!userLocation.latitude || !userLocation.longitude) {
        showErrorModal('Invalid Location', 'Invalid location data. Please refresh the page and try again.');
            return;
        }

    performClockAction('out');
}

// Perform the actual clock in/out action
function performClockAction(action) {
    const isClockIn = action === 'in';
    const url = isClockIn ? '{{ route("staff.attendance.clock-in") }}' : '{{ route("staff.attendance.clock-out") }}';
    const title = isClockIn ? 'Clocking In...' : 'Clocking Out...';
    const message = isClockIn ? 'Recording your arrival' : 'Recording your departure';
    
    // Show loading modal
    showLoadingModal(title, message);
    
    // Disable button
    const button = isClockIn ? $('#clock-in-btn') : $('#clock-out-btn');
    button.prop('disabled', true);
    
    // Prepare data
    const data = {
        latitude: userLocation.latitude,
        longitude: userLocation.longitude,
        address: $('#location-status .alert p').text() || 'Location detected',
            _token: $('meta[name="csrf-token"]').attr('content')
        };

    console.log('Sending request:', { url, data });

    // Make AJAX request
        $.ajax({
        url: url,
            method: 'POST',
        data: data,
            dataType: 'json',
        timeout: 30000,
            success: function(response) {
            console.log('Success response:', response);
                hideLoadingModal();

                if (response.success) {
                const successTitle = isClockIn ? 'Clocked In Successfully!' : 'Clocked Out Successfully!';
                showSuccessModal(successTitle, response.message);
                } else {
                showErrorModal('Error', response.message || 'Operation failed. Please try again.', true, action);
                }
            },
            error: function(xhr, status, error) {
            console.error('AJAX error:', { xhr, status, error });
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
                errorMessage = 'Please check your location and try again.';
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
            // Only re-enable button if location is still ready
            if (locationReady) {
                button.prop('disabled', false);
            }
        }
    });
}

// Show success modal
function showSuccessModal(title, message) {
    $('#success-title').text(title);
    $('#success-message').text(message);
    $('#successModal').modal('show');
}

// Show error modal
function showErrorModal(title, message, showRetry = false, retryAction = null) {
    $('#error-title').text(title);
    
    // Check if message contains HTML
    if (message.includes('<')) {
        $('#error-message').html(message);
    } else {
        $('#error-message').text(message);
    }
    
    const retryBtn = $('#retry-btn');
    const defaultLocationBtn = $('#default-location-btn');
    
    if (showRetry && retryAction) {
        retryBtn.show().data('action', retryAction);
        
        // Customize retry button text based on action
        if (retryAction === 'permission') {
            retryBtn.text('Try Again');
            defaultLocationBtn.show(); // Show default location option for permission issues
        } else {
            retryBtn.text('Retry');
            defaultLocationBtn.hide();
        }
    } else {
        retryBtn.hide();
        defaultLocationBtn.hide();
    }
    
    $('#errorModal').modal('show');
}

// Use default location from modal and close it
function useDefaultLocationFromModal() {
    $('#errorModal').modal('hide');
    useDefaultLocation();
}

// Show loading modal
function showLoadingModal(title, message) {
    $('#loading-title').text(title);
    $('#loading-message').text(message);
    $('#loadingModal').modal('show');
}

// Hide loading modal
function hideLoadingModal() {
    $('#loadingModal').modal('hide');
}
</script>
@endsection
