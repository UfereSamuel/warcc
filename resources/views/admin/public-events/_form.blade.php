@php
    $event = $event ?? null;
    $isEdit = $isEdit ?? false;
    $tagsValue = old('tags', $event && is_array($event->tags) ? implode(', ', $event->tags) : '');
    $startDateValue = old('start_date', $event?->start_date?->format('Y-m-d') ?? '');
    $endDateValue = old('end_date', $event?->end_date?->format('Y-m-d') ?? '');
    $startTimeValue = old('start_time', $event?->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '');
    $endTimeValue = old('end_time', $event?->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '');
    $registrationDeadlineValue = old('registration_deadline', $event?->registration_deadline?->format('Y-m-d') ?? '');
@endphp

<div class="form-group">
    <label for="title">Event Title <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('title') is-invalid @enderror"
           id="title" name="title" value="{{ old('title', $event?->title ?? '') }}" required
           placeholder="Enter event title">
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="summary">Summary</label>
    <textarea class="form-control @error('summary') is-invalid @enderror"
              id="summary" name="summary" rows="3" maxlength="500"
              placeholder="Brief summary for event listings (optional)">{{ old('summary', $event?->summary ?? '') }}</textarea>
    <small class="form-text text-muted">
        Short description that appears in event listings. Maximum 500 characters.
    </small>
    @error('summary')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="description">Full Description <span class="text-danger">*</span></label>
    <textarea class="form-control @error('description') is-invalid @enderror"
              id="description" name="description" rows="6" required
              placeholder="Detailed event description">{{ old('description', $event?->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="category">Category <span class="text-danger">*</span></label>
            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                <option value="">Select Category</option>
                @foreach(['conference' => 'Conference', 'workshop' => 'Workshop', 'training' => 'Training', 'seminar' => 'Seminar', 'meeting' => 'Meeting', 'announcement' => 'Announcement', 'celebration' => 'Celebration'] as $value => $label)
                    <option value="{{ $value }}" {{ old('category', $event?->category ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('category')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="draft" {{ old('status', $event?->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $event?->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                @if($isEdit)
                    <option value="archived" {{ old('status', $event?->status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                @endif
            </select>
            <small class="form-text text-muted">
                Draft events are not visible to the public.
            </small>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="start_date">Start Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                   id="start_date" name="start_date" value="{{ $startDateValue }}" required
                   @unless($isEdit) min="{{ date('Y-m-d') }}" @endunless>
            @error('start_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="end_date">End Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                   id="end_date" name="end_date" value="{{ $endDateValue }}" required
                   @unless($isEdit) min="{{ date('Y-m-d') }}" @endunless>
            @error('end_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                   id="start_time" name="start_time" value="{{ $startTimeValue }}">
            @error('start_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                   id="end_time" name="end_time" value="{{ $endTimeValue }}">
            @error('end_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" class="form-control @error('location') is-invalid @enderror"
                   id="location" name="location" value="{{ old('location', $event?->location ?? '') }}"
                   placeholder="Event venue or location">
            @error('location')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="venue_address">Full Address</label>
            <input type="text" class="form-control @error('venue_address') is-invalid @enderror"
                   id="venue_address" name="venue_address" value="{{ old('venue_address', $event?->venue_address ?? '') }}"
                   placeholder="Complete venue address">
            @error('venue_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label for="featured_image">Featured Image</label>
    @if($isEdit && $event?->featured_image)
        <div class="mb-2">
            <img src="{{ $event->featured_image_url }}" alt="Current featured image" class="img-thumbnail" style="max-width: 200px;">
            <small class="form-text text-muted d-block">Upload a new image to replace the current one.</small>
        </div>
    @endif
    <input type="file" class="form-control-file @error('featured_image') is-invalid @enderror"
           id="featured_image" name="featured_image" accept="image/*">
    <small class="form-text text-muted">
        Upload an event banner or poster (JPEG, PNG, WebP). Maximum 5MB.
    </small>
    @error('featured_image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="contact_email">Contact Email</label>
            <input type="email" class="form-control @error('contact_email') is-invalid @enderror"
                   id="contact_email" name="contact_email" value="{{ old('contact_email', $event?->contact_email ?? '') }}"
                   placeholder="Event contact email">
            @error('contact_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="contact_phone">Contact Phone</label>
            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror"
                   id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $event?->contact_phone ?? '') }}"
                   placeholder="Event contact phone">
            @error('contact_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="registration_required"
               name="registration_required" value="1"
               {{ old('registration_required', $event?->registration_required ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="registration_required">
            Registration Required
        </label>
    </div>
</div>

<div id="registration-fields" style="display: none;">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="max_participants">Maximum Participants</label>
                <input type="number" class="form-control @error('max_participants') is-invalid @enderror"
                       id="max_participants" name="max_participants"
                       value="{{ old('max_participants', $event?->max_participants ?? '') }}"
                       min="1" max="10000" placeholder="Leave empty for unlimited">
                @error('max_participants')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="registration_deadline">Registration Deadline</label>
                <input type="date" class="form-control @error('registration_deadline') is-invalid @enderror"
                       id="registration_deadline" name="registration_deadline" value="{{ $registrationDeadlineValue }}"
                       @unless($isEdit) min="{{ date('Y-m-d') }}" @endunless>
                @error('registration_deadline')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="fee">Event Fee (GHS)</label>
                <input type="number" step="0.01" class="form-control @error('fee') is-invalid @enderror"
                       id="fee" name="fee" value="{{ old('fee', $event?->fee ?? '') }}"
                       min="0" max="999999.99" placeholder="0.00 for free events">
                @error('fee')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="registration_link">Registration Link</label>
                <input type="url" class="form-control @error('registration_link') is-invalid @enderror"
                       id="registration_link" name="registration_link"
                       value="{{ old('registration_link', $event?->registration_link ?? '') }}"
                       placeholder="https://registration.example.com">
                @error('registration_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="tags">Tags</label>
    <input type="text" class="form-control @error('tags') is-invalid @enderror"
           id="tags" name="tags" value="{{ $tagsValue }}"
           placeholder="health, research, conference (separated by commas)">
    <small class="form-text text-muted">
        Separate tags with commas. Used for categorization and search.
    </small>
    @error('tags')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="additional_info">Additional Information</label>
    <textarea class="form-control @error('additional_info') is-invalid @enderror"
              id="additional_info" name="additional_info" rows="4"
              placeholder="Any additional details, requirements, or instructions">{{ old('additional_info', $event?->additional_info ?? '') }}</textarea>
    @error('additional_info')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="is_featured"
               name="is_featured" value="1"
               {{ old('is_featured', $event?->is_featured ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_featured">
            Feature this event
        </label>
        <small class="form-text text-muted">
            Featured events are highlighted on the homepage and event listings.
        </small>
    </div>
</div>
