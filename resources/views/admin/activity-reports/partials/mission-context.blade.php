@if($activityReport->weeklyTracker)
    @php $tracker = $activityReport->weeklyTracker; @endphp
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-plane mr-2"></i>Linked Mission
            </h3>
            <div class="card-tools">
                <a href="{{ route('admin.weekly-trackers.show', $tracker) }}" class="btn btn-xs btn-outline-secondary">
                    View tracker
                </a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">Mission</dt>
                <dd class="col-sm-8">{{ $tracker->mission_title }}</dd>

                <dt class="col-sm-4">Week</dt>
                <dd class="col-sm-8">{{ $tracker->week_range }}</dd>

                @if($tracker->mission_type)
                    <dt class="col-sm-4">Type</dt>
                    <dd class="col-sm-8">{{ ucfirst($tracker->mission_type) }}</dd>
                @endif

                @if($tracker->mission_start_date && $tracker->mission_end_date)
                    <dt class="col-sm-4">Mission Dates</dt>
                    <dd class="col-sm-8">
                        {{ $tracker->mission_start_date->format('M d, Y') }}
                        – {{ $tracker->mission_end_date->format('M d, Y') }}
                    </dd>
                @endif

                @if($tracker->mission_purpose)
                    <dt class="col-sm-4">Purpose</dt>
                    <dd class="col-sm-8">{{ $tracker->mission_purpose }}</dd>
                @endif
            </dl>
        </div>
    </div>
@endif
