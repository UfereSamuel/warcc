@php
    $statusAnalytics = app(\App\Services\StaffStatusAnalyticsService::class);
    $complianceService = app(\App\Services\MissionComplianceService::class);
    $counts = $staffStatusData;
    $statusConfig = [
        'at_duty_station' => ['color' => '#28a745', 'icon' => 'fa-building', 'accent' => 'success'],
        'on_mission' => ['color' => '#ffc107', 'icon' => 'fa-plane', 'accent' => 'warning'],
        'on_leave' => ['color' => '#17a2b8', 'icon' => 'fa-calendar-times', 'accent' => 'info'],
        'not_submitted' => ['color' => '#6c757d', 'icon' => 'fa-clock', 'accent' => 'secondary'],
    ];
    $activeStatus = request('status');
@endphp

<div class="row mb-4" id="staff-status-hub">
    <div class="col-12">
        <div class="chart-card staff-status-hub-card">
            <div class="chart-header flex-wrap">
                <div class="mb-2 mb-md-0">
                    <h3 class="chart-title mb-1">
                        <i class="fas fa-users-cog mr-2"></i>Staff Status — Decision View
                    </h3>
                    <small class="text-muted">
                        From submitted weekly trackers · Week starts Monday · {{ $staffStatusData['week_label'] }}
                    </small>
                </div>
                <div class="d-flex flex-wrap align-items-center">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="form-inline mr-2 mb-2 mb-md-0" id="staffStatusWeekForm">
                        @if($activeStatus)
                            <input type="hidden" name="status" value="{{ $activeStatus }}">
                        @endif
                        <label for="dashboard_week" class="sr-only">Week</label>
                        <select name="week" id="dashboard_week" class="form-control form-control-sm" onchange="this.form.submit()">
                            @foreach($weekOptions as $weekOption)
                                <option value="{{ $weekOption->toDateString() }}"
                                    @selected($weekOption->toDateString() === $selectedWeekStart->toDateString())>
                                    {{ $weekOption->format('M d') }} – {{ $weekOption->copy()->endOfWeek()->format('M d, Y') }}
                                    @if($weekOption->isSameDay(now()->startOfWeek()))
                                        (Current week)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('admin.staff-roster.index', ['week' => $selectedWeekStart->toDateString()]) }}"
                       class="btn btn-sm btn-outline-primary mb-2 mb-md-0">
                        <i class="fas fa-external-link-alt mr-1"></i>Full roster
                    </a>
                </div>
            </div>

            <div class="card-body border-bottom pb-3">
                <div class="row">
                    @foreach($statusConfig as $statusKey => $config)
                        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
                            <button type="button"
                                    class="staff-status-kpi w-100 text-left {{ $activeStatus === $statusKey ? 'is-active' : '' }}"
                                    data-status="{{ $statusKey }}"
                                    data-label="{{ $statusAnalytics->statusLabel($statusKey) }}"
                                    style="--kpi-color: {{ $config['color'] }};">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="kpi-value">{{ $counts[$statusKey] ?? 0 }}</div>
                                        <div class="kpi-label">{{ $statusAnalytics->statusLabel($statusKey) }}</div>
                                    </div>
                                    <i class="fas {{ $config['icon'] }} kpi-icon"></i>
                                </div>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="row no-gutters">
                <div class="col-lg-4 border-right">
                    <div class="chart-body pb-2">
                        <canvas id="staffStatusChart" height="220"></canvas>
                    </div>
                    <div class="status-legend border-top-0 pt-0">
                        @foreach($statusConfig as $statusKey => $config)
                            <button type="button"
                                    class="legend-item legend-button w-100 text-left border-0 bg-transparent p-0 mb-2"
                                    data-status="{{ $statusKey }}"
                                    data-label="{{ $statusAnalytics->statusLabel($statusKey) }}">
                                <span class="legend-color" style="background: {{ $config['color'] }};"></span>
                                <span class="legend-text">
                                    {{ $statusAnalytics->statusLabel($statusKey) }} ({{ $counts[$statusKey] ?? 0 }})
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-8">
                    <div id="staffStatusDrilldown" class="drilldown-panel-wrap {{ $activeStatus ? '' : 'is-empty' }}">
                        <div class="drilldown-placeholder text-muted text-center py-5 px-3" id="staffStatusDrilldownPlaceholder">
                            <i class="fas fa-hand-pointer fa-2x mb-3 d-block"></i>
                            Click a status card or chart segment to see staff in that category.
                        </div>
                        @foreach($staffRoster['groups'] as $statusKey => $entries)
                            <div class="drilldown-content {{ $activeStatus === $statusKey ? '' : 'd-none' }}"
                                 data-status="{{ $statusKey }}"
                                 id="drilldown-{{ $statusKey }}">
                                <div class="drilldown-header d-flex flex-wrap justify-content-between align-items-center px-3 py-3 border-bottom">
                                    <h5 class="mb-0 drilldown-title">
                                        <i class="fas {{ $statusConfig[$statusKey]['icon'] ?? 'fa-user' }} mr-2"></i>
                                        <span>{{ $statusAnalytics->statusLabel($statusKey) }}</span>
                                        <span class="badge badge-{{ $statusAnalytics->statusBadgeClass($statusKey) }} ml-2">{{ $entries->count() }}</span>
                                    </h5>
                                    <div>
                                        <a href="{{ route('admin.staff-roster.index', ['week' => $selectedWeekStart->toDateString(), 'status' => $statusKey]) }}"
                                           class="btn btn-xs btn-outline-primary">
                                            Open in roster
                                        </a>
                                        @if(in_array($statusKey, ['at_duty_station', 'on_mission', 'on_leave'], true))
                                            <a href="{{ route('admin.weekly-trackers.index', ['week' => $selectedWeekStart->toDateString(), 'status' => $statusKey]) }}"
                                               class="btn btn-xs btn-outline-secondary ml-1">
                                                Weekly trackers
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @if($entries->isEmpty())
                                    <p class="text-muted text-center py-4 mb-0">No staff in this category for the selected week.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0 drilldown-table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Staff</th>
                                                    <th>Position</th>
                                                    <th>Details</th>
                                                    @if($selectedWeekStart->isSameDay(now()->startOfWeek()))
                                                        <th class="text-center">Today</th>
                                                    @endif
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($entries as $entry)
                                                    <tr>
                                                        <td>
                                                            <div class="media align-items-center">
                                                                <img src="{{ $entry['staff']->profile_picture_url }}"
                                                                     alt=""
                                                                     class="img-circle mr-2"
                                                                     style="width: 30px; height: 30px;">
                                                                <div>
                                                                    <strong>{{ $entry['staff']->full_name }}</strong>
                                                                    <small class="text-muted d-block">{{ $entry['staff']->staff_id }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $entry['position_title'] ?? 'Unassigned' }}</td>
                                                        <td>
                                                            @if($entry['mission_title'])
                                                                <strong>{{ $entry['mission_title'] }}</strong>
                                                                @if($entry['mission_range'])
                                                                    <small class="text-muted d-block">{{ $entry['mission_range'] }}</small>
                                                                @endif
                                                                @if($entry['mission_report_status'])
                                                                    <span class="badge badge-{{ $complianceService->reportStatusBadgeClass($entry['mission_report_status']) }}">
                                                                        {{ $complianceService->reportStatusLabel($entry['mission_report_status']) }}
                                                                    </span>
                                                                @endif
                                                            @elseif($entry['leave_type'])
                                                                <strong>{{ $entry['leave_type'] }}</strong>
                                                                @if($entry['leave_range'])
                                                                    <small class="text-muted d-block">{{ $entry['leave_range'] }}</small>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        @if($selectedWeekStart->isSameDay(now()->startOfWeek()))
                                                            <td class="text-center">
                                                                @if($entry['clocked_in_today'])
                                                                    <span class="badge badge-success">In</span>
                                                                @else
                                                                    <span class="badge badge-light">—</span>
                                                                @endif
                                                            </td>
                                                        @endif
                                                        <td class="text-right text-nowrap">
                                                            <a href="{{ route('admin.staff.show', $entry['staff']) }}" class="btn btn-xs btn-outline-primary">Profile</a>
                                                            @if($entry['tracker'])
                                                                <a href="{{ route('admin.weekly-trackers.show', $entry['tracker']) }}" class="btn btn-xs btn-outline-secondary ml-1">Tracker</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
