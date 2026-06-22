@extends('layouts.staff')

@section('title', 'Attendance')
@section('page-title', 'Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@php
    $isClockedIn = $todayAttendance && $todayAttendance->clock_in_time && ! $todayAttendance->clock_out_time;
    $isComplete = $todayAttendance && $todayAttendance->clock_out_time;
    $statusLabel = $isComplete
        ? 'Completed'
        : ($isClockedIn ? 'On duty' : 'Not started');
    $statusClass = $isComplete
        ? 'success'
        : ($isClockedIn ? 'primary' : 'secondary');
@endphp

@push('styles')
<style>
    .attendance-hero {
        background: linear-gradient(135deg, #348F41 0%, #2a7334 100%);
        border-radius: 12px;
        color: #fff;
        padding: 1.75rem 2rem;
    }

    .attendance-hero .live-clock {
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        line-height: 1.1;
    }

    .attendance-hero .live-date {
        opacity: 0.9;
        font-size: 1rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.2);
    }

    .action-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .action-card .card-body {
        padding: 2rem;
    }

    .clock-action-btn {
        min-width: 220px;
        padding: 0.85rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 10px;
    }

    .clock-action-btn:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }

    .today-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .today-meta-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.85rem 1rem;
    }

    .today-meta-item .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .today-meta-item .value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
    }

    .stat-tile {
        border: none;
        border-radius: 12px;
        color: #fff;
        overflow: hidden;
        position: relative;
        min-height: 110px;
    }

    .stat-tile .tile-body {
        padding: 1.25rem;
        position: relative;
        z-index: 1;
    }

    .stat-tile .tile-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .stat-tile .tile-label {
        font-size: 0.9rem;
        opacity: 0.95;
    }

    .stat-tile .tile-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 3rem;
        opacity: 0.2;
    }

    .stat-tile.tile-green { background: #348F41; }
    .stat-tile.tile-teal { background: #17a2b8; }
    .stat-tile.tile-gold { background: #B4A269; }

    .attendance-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6c757d;
        border-top: none;
    }

    #attendance-feedback {
        display: none;
    }
</style>
@endpush

@section('content')
<div id="attendance-feedback" class="alert mb-3" role="alert"></div>

<div class="row mb-4">
    <div class="col-12">
        <div class="attendance-hero d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <div class="live-clock" id="live-clock">{{ now()->format('h:i:s A') }}</div>
                <div class="live-date" id="live-date">{{ now()->format('l, F j, Y') }}</div>
            </div>
            <div class="text-right mt-3 mt-md-0">
                <span class="status-pill" id="duty-status-pill">
                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                    {{ $statusLabel }}
                </span>
                <div class="mt-2 small" style="opacity: 0.85;">
                    {{ $staff->full_name }} · {{ $staff->position_title ?? 'Staff' }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-5 mb-4 mb-lg-0">
        <div class="card action-card h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                @if($isClockedIn)
                    <div class="mb-3">
                        <span class="badge badge-success badge-lg px-3 py-2" style="font-size: 1rem;">
                            <i class="fas fa-user-check mr-1"></i> You are clocked in
                        </span>
                    </div>
                    <p class="text-muted mb-1">Started at</p>
                    <h3 class="text-success mb-3">
                        {{ \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A') }}
                    </h3>
                    <p class="text-muted mb-4">
                        Elapsed: <strong id="elapsed-time">—</strong>
                    </p>
                    <button type="button"
                            class="btn btn-danger clock-action-btn"
                            id="btn-clock-out"
                            data-action="out">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        <span class="btn-label">Clock Out</span>
                    </button>
                @elseif($isComplete)
                    <div class="mb-3">
                        <span class="badge badge-info badge-lg px-3 py-2" style="font-size: 1rem;">
                            <i class="fas fa-check-double mr-1"></i> Day complete
                        </span>
                    </div>
                    <p class="text-muted mb-4">You have already recorded attendance for today.</p>
                    <button type="button" class="btn btn-secondary clock-action-btn" disabled>
                        <i class="fas fa-check mr-2"></i> Attendance Recorded
                    </button>
                @else
                    <div class="mb-3">
                        <i class="fas fa-fingerprint fa-3x text-muted mb-2"></i>
                    </div>
                    <h5 class="mb-2">Ready to start your day?</h5>
                    <p class="text-muted mb-4">Tap the button below to record your arrival.</p>
                    <button type="button"
                            class="btn btn-success clock-action-btn"
                            id="btn-clock-in"
                            data-action="in">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        <span class="btn-label">Clock In</span>
                    </button>
                @endif

                <div class="today-meta text-left">
                    <div class="today-meta-item">
                        <div class="label">Clock in</div>
                        <div class="value" id="meta-clock-in">
                            {{ $todayAttendance?->clock_in_time
                                ? \Carbon\Carbon::parse($todayAttendance->clock_in_time)->format('h:i A')
                                : '—' }}
                        </div>
                    </div>
                    <div class="today-meta-item">
                        <div class="label">Clock out</div>
                        <div class="value" id="meta-clock-out">
                            {{ $todayAttendance?->clock_out_time
                                ? \Carbon\Carbon::parse($todayAttendance->clock_out_time)->format('h:i A')
                                : '—' }}
                        </div>
                    </div>
                    <div class="today-meta-item">
                        <div class="label">Hours today</div>
                        <div class="value" id="meta-hours">
                            {{ $todayAttendance?->total_hours
                                ? number_format($todayAttendance->total_hours, 1) . 'h'
                                : ($isClockedIn ? 'In progress' : '—') }}
                        </div>
                    </div>
                    <div class="today-meta-item">
                        <div class="label">Status</div>
                        <div class="value text-capitalize" id="meta-status">
                            {{ $todayAttendance?->status ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="stat-tile tile-green">
                    <div class="tile-body">
                        <div class="tile-value">{{ $weekSummary['present_days'] }}/{{ $weekSummary['working_days'] }}</div>
                        <div class="tile-label">Days this week</div>
                    </div>
                    <i class="fas fa-calendar-week tile-icon"></i>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-tile tile-teal">
                    <div class="tile-body">
                        <div class="tile-value">{{ number_format($weekSummary['total_hours'], 1) }}h</div>
                        <div class="tile-label">Hours this week</div>
                    </div>
                    <i class="fas fa-clock tile-icon"></i>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-tile tile-gold">
                    <div class="tile-body">
                        <div class="tile-value">{{ number_format($monthSummary['average_hours'], 1) }}h</div>
                        <div class="tile-label">Daily average (month)</div>
                    </div>
                    <i class="fas fa-chart-line tile-icon"></i>
                </div>
            </div>
        </div>

        <div class="card action-card">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history text-muted mr-2"></i>
                    Recent attendance
                </h5>
                <a href="{{ route('staff.attendance.history') }}" class="btn btn-sm btn-outline-success">
                    View all
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentAttendance->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover attendance-table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>In</th>
                                    <th>Out</th>
                                    <th>Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttendance as $record)
                                    <tr>
                                        <td>
                                            <strong>{{ $record->date->format('M d') }}</strong>
                                            <span class="text-muted small d-block">{{ $record->date->format('D') }}</span>
                                        </td>
                                        <td>
                                            @if($record->clock_in_time)
                                                {{ \Carbon\Carbon::parse($record->clock_in_time)->format('h:i A') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($record->clock_out_time)
                                                {{ \Carbon\Carbon::parse($record->clock_out_time)->format('h:i A') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($record->total_hours)
                                                {{ number_format($record->total_hours, 1) }}h
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badge = match ($record->status) {
                                                    'present' => 'success',
                                                    'late' => 'warning',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge badge-{{ $badge }}">{{ ucfirst($record->status) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 px-3">
                        <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No attendance records yet. Clock in to start your history.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const routes = {
        in: @json(route('staff.attendance.clock-in')),
        out: @json(route('staff.attendance.clock-out')),
    };

    const clockInTime = @json($isClockedIn ? $todayAttendance->clock_in_time : null);
    let elapsedTimer = null;

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function updateLiveClock() {
        const now = new Date();
        const hours = now.getHours();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const h12 = hours % 12 || 12;
        const time = `${pad(h12)}:${pad(now.getMinutes())}:${pad(now.getSeconds())} ${ampm}`;
        $('#live-clock').text(time);
    }

    function updateElapsed() {
        if (!clockInTime) {
            return;
        }

        const now = new Date();
        const parts = clockInTime.split(':');
        const start = new Date();
        start.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), parseInt(parts[2] || 0, 10), 0);

        const diffMs = Math.max(0, now - start);
        const hrs = Math.floor(diffMs / 3600000);
        const mins = Math.floor((diffMs % 3600000) / 60000);
        $('#elapsed-time').text(`${hrs}h ${mins}m`);
    }

    function showFeedback(type, message) {
        const el = $('#attendance-feedback');
        el.removeClass('alert-success alert-danger alert-warning alert-info')
            .addClass('alert-' + type)
            .html(message)
            .fadeIn(200);

        $('html, body').animate({ scrollTop: el.offset().top - 80 }, 250);
    }

    function setButtonLoading(button, loading, defaultLabel) {
        if (loading) {
            button.prop('disabled', true);
            button.find('.btn-label').text('Please wait…');
            button.find('i').removeClass().addClass('fas fa-spinner fa-spin mr-2');
        } else {
            button.prop('disabled', false);
            button.find('.btn-label').text(defaultLabel);
            const icon = button.data('action') === 'out' ? 'fa-sign-out-alt' : 'fa-sign-in-alt';
            button.find('i').removeClass().addClass('fas ' + icon + ' mr-2');
        }
    }

    function tryGetLocation() {
        return new Promise(function (resolve) {
            if (!navigator.geolocation) {
                resolve({});
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    resolve({
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude,
                    });
                },
                function () {
                    resolve({});
                },
                { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 }
            );
        });
    }

    async function submitClock(action) {
        const isIn = action === 'in';
        const button = isIn ? $('#btn-clock-in') : $('#btn-clock-out');

        if (!button.length) {
            return;
        }

        setButtonLoading(button, true, isIn ? 'Clock In' : 'Clock Out');

        const location = await tryGetLocation();
        const payload = Object.assign({ _token: $('meta[name="csrf-token"]').attr('content') }, location);

        $.ajax({
            url: routes[action],
            method: 'POST',
            data: payload,
            dataType: 'json',
            timeout: 30000,
        })
        .done(function (response) {
            if (response.success) {
                showFeedback('success', '<i class="fas fa-check-circle mr-1"></i> ' + response.message);
                setTimeout(function () {
                    window.location.reload();
                }, 1200);
            } else {
                showFeedback('danger', response.message || 'Unable to record attendance.');
            }
        })
        .fail(function (xhr) {
            let message = 'Something went wrong. Please try again.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.status === 419) {
                message = 'Your session expired. Please refresh the page and sign in again.';
            } else if (xhr.status === 429) {
                message = 'Please wait a moment before trying again.';
            }

            showFeedback('danger', '<i class="fas fa-exclamation-circle mr-1"></i> ' + message);
        })
        .always(function () {
            setButtonLoading(button, false, isIn ? 'Clock In' : 'Clock Out');
        });
    }

    $(document).ready(function () {
        updateLiveClock();
        setInterval(updateLiveClock, 1000);

        if (clockInTime) {
            updateElapsed();
            elapsedTimer = setInterval(updateElapsed, 60000);
        }

        $(document).on('click', '#btn-clock-in, #btn-clock-out', function (e) {
            e.preventDefault();
            const action = $(this).data('action');
            if (action) {
                submitClock(action);
            }
        });
    });
})();
</script>
@endpush
