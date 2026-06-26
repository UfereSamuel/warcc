@php
    $complianceService = app(\App\Services\MissionComplianceService::class);
    $reportStatus = $complianceService->resolveMissionReportStatus($tracker);
    $report = $tracker->activityReport ?? $tracker->getMissionReport();
@endphp
<span class="badge badge-{{ $complianceService->reportStatusBadgeClass($reportStatus) }}">
    {{ $complianceService->reportStatusLabel($reportStatus) }}
</span>
@if($report)
    <a href="{{ route('admin.activity-reports.show', $report) }}" class="btn btn-xs btn-link p-0 ml-1">View</a>
@endif
