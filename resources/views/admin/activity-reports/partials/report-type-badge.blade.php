<span class="badge badge-{{ $report->report_type_badge_class }}">
    @if($report->isMissionReport())
        <i class="fas fa-plane mr-1"></i>
    @elseif($report->activity_calendar_id)
        <i class="fas fa-calendar mr-1"></i>
    @else
        <i class="fas fa-file mr-1"></i>
    @endif
    {{ $report->report_type_label }}
</span>
