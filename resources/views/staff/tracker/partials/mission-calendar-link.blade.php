<div class="form-group">
    <label for="activity_calendar_id">Link to Calendar Activity <span class="text-muted">(recommended)</span></label>
    <select class="form-control @error('activity_calendar_id') is-invalid @enderror"
            id="activity_calendar_id"
            name="activity_calendar_id"
            onchange="prefillMissionFromActivity(this.value)">
        <option value="">— Ad-hoc mission (not on calendar) —</option>
        @foreach($linkableActivities as $activity)
            <option value="{{ $activity->id }}"
                    @selected(old('activity_calendar_id', $selectedActivityId ?? null) == $activity->id)>
                {{ $activity->title }}
                ({{ $activity->type_label }}, {{ $activity->start_date->format('M d') }}–{{ $activity->end_date->format('M d') }})
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">
        Link this week's tracker to an approved calendar activity (mission, training, workshop, etc.).
        Mission fields will prefill when you select an activity.
    </small>
    @error('activity_calendar_id')
        <span class="invalid-feedback d-block">{{ $message }}</span>
    @enderror
</div>
