@php
    $complianceService = app(\App\Services\MissionComplianceService::class);
    $statusAnalytics = app(\App\Services\StaffStatusAnalyticsService::class);
    $compact = $compact ?? false;
    $groups = $staffRoster['groups'];
    $counts = $staffRoster['counts'];
    $totalListed = collect($groups)->sum(fn ($group) => count($group));
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="chart-card">
            <div class="chart-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h3 class="chart-title mb-0">
                        <i class="fas fa-users mr-2"></i>Staff Roster
                    </h3>
                    <small class="text-muted">
                        From submitted weekly trackers · {{ $staffRoster['week_label'] }}
                    </small>
                </div>
                <div class="mt-2 mt-md-0">
                    @if(!$compact && !request()->routeIs('admin.staff-roster.index'))
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary mr-1">
                            Dashboard
                        </a>
                    @endif
                    @unless(request()->routeIs('admin.staff-roster.index'))
                        <a href="{{ route('admin.staff-roster.index', ['week' => $staffRoster['week_start']]) }}"
                           class="btn btn-sm btn-outline-primary mr-1">
                            Full roster
                        </a>
                    @endunless
                    <a href="{{ route('admin.weekly-trackers.index', ['week' => $staffRoster['week_start']]) }}"
                       class="btn btn-sm btn-outline-success">
                        Weekly trackers
                    </a>
                </div>
            </div>

            <div class="card-body border-bottom py-3">
                <div class="row text-center">
                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                        <div class="h5 mb-0 text-success">{{ $counts['at_duty_station'] }}</div>
                        <small class="text-muted">At Duty Station</small>
                    </div>
                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                        <div class="h5 mb-0 text-warning">{{ $counts['on_mission'] }}</div>
                        <small class="text-muted">On Mission</small>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="h5 mb-0 text-info">{{ $counts['on_leave'] }}</div>
                        <small class="text-muted">On Leave</small>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="h5 mb-0 text-secondary">{{ $counts['not_submitted'] }}</div>
                        <small class="text-muted">Not Submitted</small>
                    </div>
                </div>
            </div>

            @if($totalListed === 0)
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-0">No active staff found for this week.</p>
                </div>
            @elseif($compact)
                <div class="card-body">
                    <div class="row">
                        @foreach($statusAnalytics->statusLabels() as $statusKey => $statusLabel)
                            <div class="col-md-3 mb-3">
                                <h6 class="text-{{ $statusAnalytics->statusBadgeClass($statusKey) }}">
                                    @switch($statusKey)
                                        @case('at_duty_station')<i class="fas fa-building mr-1"></i>@break
                                        @case('on_mission')<i class="fas fa-plane mr-1"></i>@break
                                        @case('on_leave')<i class="fas fa-calendar-times mr-1"></i>@break
                                        @default<i class="fas fa-clock mr-1"></i>
                                    @endswitch
                                    {{ $statusLabel }}
                                </h6>
                                @forelse($groups[$statusKey] as $entry)
                                    @include('admin.partials.staff-roster-entry', ['entry' => $entry, 'compact' => true])
                                @empty
                                    <div class="small text-muted">None</div>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Staff</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Details</th>
                                <th class="text-center">Today</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries ?? $statusAnalytics->flattenRosterGroups($groups) as $entry)
                                <tr>
                                    <td>
                                        @include('admin.partials.staff-roster-entry', ['entry' => $entry, 'compact' => false, 'table' => true])
                                    </td>
                                    <td>{{ $entry['position_title'] ?? 'Unassigned' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $entry['status_badge_class'] }}">
                                            {{ $entry['status_label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($entry['mission_title'])
                                            <strong>{{ $entry['mission_title'] }}</strong>
                                            @if($entry['mission_range'])
                                                <small class="text-muted d-block">{{ $entry['mission_range'] }}</small>
                                            @endif
                                            @if($entry['mission_report_status'])
                                                <span class="badge badge-{{ $complianceService->reportStatusBadgeClass($entry['mission_report_status']) }} mt-1">
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
                                    <td class="text-center">
                                        @if($entry['clocked_in_today'])
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>In</span>
                                        @else
                                            <span class="badge badge-light">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right text-nowrap">
                                        <a href="{{ route('admin.staff.show', $entry['staff']) }}" class="btn btn-xs btn-outline-primary">
                                            Profile
                                        </a>
                                        @if($entry['tracker'])
                                            <a href="{{ route('admin.weekly-trackers.show', $entry['tracker']) }}" class="btn btn-xs btn-outline-secondary ml-1">
                                                Tracker
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No staff match the current filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
