@php $staff = $entry['staff']; @endphp

@if(!empty($table))
    <div class="media align-items-center">
        <img src="{{ $staff->profile_picture_url }}"
             alt="{{ $staff->full_name }}"
             class="img-circle mr-2"
             style="width: 32px; height: 32px;">
        <div class="media-body">
            <strong>{{ $staff->full_name }}</strong>
            <small class="text-muted d-block">{{ $staff->staff_id }}</small>
        </div>
    </div>
@else
    <div class="d-flex align-items-start mb-2">
        <img src="{{ $staff->profile_picture_url }}"
             alt="{{ $staff->full_name }}"
             class="img-circle mr-2 mt-1"
             style="width: 28px; height: 28px;">
        <div class="flex-grow-1" style="min-width: 0;">
            <div class="small font-weight-bold">{{ $staff->full_name }}</div>
            <div class="text-muted" style="font-size: 0.75rem;">{{ $staff->staff_id }}</div>
            @if($entry['position_title'])
                <div class="text-muted" style="font-size: 0.75rem;">{{ $entry['position_title'] }}</div>
            @endif
            @if($entry['mission_title'])
                <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($entry['mission_title'], 42) }}</div>
            @endif
            @if($entry['leave_type'])
                <div class="text-muted" style="font-size: 0.75rem;">{{ $entry['leave_type'] }}</div>
            @endif
            <div class="mt-1">
                @if($entry['clocked_in_today'])
                    <span class="badge badge-success badge-pill" style="font-size: 0.65rem;">Clocked in</span>
                @endif
                @if($entry['mission_report_status'])
                    <span class="badge badge-{{ app(\App\Services\MissionComplianceService::class)->reportStatusBadgeClass($entry['mission_report_status']) }} badge-pill" style="font-size: 0.65rem;">
                        {{ app(\App\Services\MissionComplianceService::class)->reportStatusLabel($entry['mission_report_status']) }}
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif
