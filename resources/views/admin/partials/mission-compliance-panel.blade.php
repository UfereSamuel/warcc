@php
    $complianceService = app(\App\Services\MissionComplianceService::class);
    $summary = $missionCompliance['summary'];
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="chart-card">
            <div class="chart-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h3 class="chart-title mb-0">
                        <i class="fas fa-clipboard-check mr-2"></i>Mission Report Compliance
                    </h3>
                    <small class="text-muted">On-mission weekly trackers · {{ $missionCompliance['week_label'] }}</small>
                </div>
                <a href="{{ route('admin.weekly-trackers.index', ['week' => $missionCompliance['week_start'], 'status' => 'on_mission']) }}"
                   class="btn btn-sm btn-outline-primary mt-2 mt-md-0">
                    View mission trackers
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-6 col-md-3 mb-2">
                        <div class="border rounded p-3 text-center h-100">
                            <div class="h4 mb-0">{{ $summary['total'] }}</div>
                            <small class="text-muted">On Mission</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="border rounded p-3 text-center h-100 border-danger">
                            <div class="h4 mb-0 text-danger">{{ $summary['missing'] }}</div>
                            <small class="text-muted">Report Missing</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="border rounded p-3 text-center h-100 border-warning">
                            <div class="h4 mb-0 text-warning">{{ $summary['submitted'] }}</div>
                            <small class="text-muted">Awaiting Review</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="border rounded p-3 text-center h-100 border-success">
                            <div class="h4 mb-0 text-success">{{ $summary['reviewed'] }}</div>
                            <small class="text-muted">Report Reviewed</small>
                        </div>
                    </div>
                </div>

                @if($missionCompliance['items']->isEmpty())
                    <p class="text-muted mb-0">No submitted on-mission weekly trackers for this week.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Staff</th>
                                    <th>Mission</th>
                                    <th>Mission Dates</th>
                                    <th>Report Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($missionCompliance['items'] as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item['staff']->full_name }}</strong>
                                            <span class="text-muted d-block small">{{ $item['staff']->staff_id }}</span>
                                        </td>
                                        <td>{{ $item['mission_title'] ?? '—' }}</td>
                                        <td>{{ $item['mission_range'] ?? '—' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $complianceService->reportStatusBadgeClass($item['report_status']) }}">
                                                {{ $complianceService->reportStatusLabel($item['report_status']) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            @if($item['report'])
                                                <a href="{{ route('admin.activity-reports.show', $item['report']) }}"
                                                   class="btn btn-xs btn-info">
                                                    <i class="fas fa-eye"></i> View Report
                                                </a>
                                            @else
                                                <span class="text-muted small">No report filed</span>
                                            @endif
                                            <a href="{{ route('admin.weekly-trackers.show', $item['tracker']) }}"
                                               class="btn btn-xs btn-outline-secondary ml-1">
                                                Tracker
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
