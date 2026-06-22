<div class="card-body">
    @if(isset($selectableMissionTrackers) && ($selectableMissionTrackers->count() || ($report?->weekly_tracker_id)))
    <div class="form-group">
        <label for="weekly_tracker_id">Mission from Weekly Tracker</label>
        <select class="form-control @error('weekly_tracker_id') is-invalid @enderror"
                id="weekly_tracker_id"
                name="weekly_tracker_id">
            <option value="">— No linked mission —</option>
            @foreach($selectableMissionTrackers ?? [] as $tracker)
                <option value="{{ $tracker->id }}"
                    data-prefill="{{ e(json_encode([
                        'title' => $tracker->mission_title,
                        'report_date' => optional($tracker->mission_end_date)->format('Y-m-d'),
                        'summary' => $tracker->mission_purpose ?? '',
                        'activity_calendar_id' => $tracker->activity_calendar_id,
                    ])) }}"
                    @selected(old('weekly_tracker_id', $report?->weekly_tracker_id ?? $selectedTracker?->id) == $tracker->id)>
                    {{ $tracker->mission_title }}
                    ({{ $tracker->week_range }}, {{ ucfirst(str_replace('_', ' ', $tracker->mission_type ?? 'mission')) }})
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">
            Select a submitted on-mission weekly tracker that does not yet have a filed report. Mission details will prefill below.
        </small>
        @error('weekly_tracker_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    @endif

    <div class="form-group">
        <label for="activity_calendar_id">Link to Calendar Activity <span class="text-muted">(optional)</span></label>
        <select class="form-control @error('activity_calendar_id') is-invalid @enderror" id="activity_calendar_id" name="activity_calendar_id">
            <option value="">— Standalone report (not linked) —</option>
            @foreach($calendarActivities as $activity)
                <option value="{{ $activity->id }}"
                    @selected(old('activity_calendar_id', $report?->activity_calendar_id ?? $selectedActivity?->id ?? $selectedTracker?->activity_calendar_id) == $activity->id)>
                    {{ $activity->title }} ({{ $activity->start_date->format('M d, Y') }} — {{ ucfirst($activity->type) }}@if(isset($activity->status)), {{ str_replace('_', ' ', $activity->status) }}@endif)
                </option>
            @endforeach
        </select>
        @error('activity_calendar_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="title">Report Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
               value="{{ old('title', $report?->title ?? $selectedActivity?->title ?? $selectedTracker?->mission_title) }}" required
               placeholder="e.g. Training workshop completion report">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="report_date">Report Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('report_date') is-invalid @enderror" id="report_date" name="report_date"
               value="{{ old('report_date', optional($report?->report_date ?? $selectedActivity?->end_date ?? $selectedTracker?->mission_end_date)->format('Y-m-d') ?? date('Y-m-d')) }}" required>
        @error('report_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="summary">Summary <span class="text-danger">*</span></label>
        <textarea class="form-control @error('summary') is-invalid @enderror" id="summary" name="summary" rows="5" required
                  placeholder="Describe what happened during the activity">{{ old('summary', $report?->summary ?? $selectedTracker?->mission_purpose ?? '') }}</textarea>
        @error('summary')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="outcomes">Outcomes / Results</label>
        <textarea class="form-control @error('outcomes') is-invalid @enderror" id="outcomes" name="outcomes" rows="3"
                  placeholder="Key results, deliverables, or achievements">{{ old('outcomes', $report?->outcomes ?? '') }}</textarea>
        @error('outcomes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="challenges">Challenges</label>
        <textarea class="form-control @error('challenges') is-invalid @enderror" id="challenges" name="challenges" rows="3"
                  placeholder="Any difficulties encountered">{{ old('challenges', $report?->challenges ?? '') }}</textarea>
        @error('challenges')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="recommendations">Recommendations</label>
        <textarea class="form-control @error('recommendations') is-invalid @enderror" id="recommendations" name="recommendations" rows="3"
                  placeholder="Suggestions for future activities">{{ old('recommendations', $report?->recommendations ?? '') }}</textarea>
        @error('recommendations')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="attachment">Supporting Document</label>
        @if($report?->attachment)
            <div class="mb-2">
                <a href="{{ route('staff.activity-reports.download', $report) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download mr-1"></i> {{ $report->attachment['original_name'] }}
                </a>
                <small class="text-muted d-block">Upload a new file to replace the current attachment.</small>
            </div>
        @endif
        <input type="file" class="form-control-file @error('attachment') is-invalid @enderror" id="attachment" name="attachment"
               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
        <small class="form-text text-muted">PDF, Word, or image files up to 5MB.</small>
        @error('attachment')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
    function applyMissionPrefill(option) {
        if (!option || !option.value || !option.dataset.prefill) {
            return;
        }

        let data;
        try {
            data = JSON.parse(option.dataset.prefill);
        } catch (e) {
            return;
        }

        const titleField = document.getElementById('title');
        const reportDateField = document.getElementById('report_date');
        const summaryField = document.getElementById('summary');
        const calendarField = document.getElementById('activity_calendar_id');

        if (titleField && data.title) {
            titleField.value = data.title;
        }

        if (reportDateField && data.report_date) {
            reportDateField.value = data.report_date;
        }

        if (summaryField && data.summary) {
            summaryField.value = data.summary;
        }

        if (calendarField && data.activity_calendar_id) {
            calendarField.value = String(data.activity_calendar_id);
        }
    }

    document.getElementById('weekly_tracker_id')?.addEventListener('change', function () {
        applyMissionPrefill(this.options[this.selectedIndex]);
    });

    document.getElementById('activity_calendar_id')?.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        if (!this.value || !selected) return;

        const titleField = document.getElementById('title');
        if (titleField && !titleField.value.trim()) {
            titleField.value = selected.text.split(' (')[0];
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const missionSelect = document.getElementById('weekly_tracker_id');
        if (missionSelect && missionSelect.value) {
            applyMissionPrefill(missionSelect.options[missionSelect.selectedIndex]);
        }
    });
</script>
@endpush
