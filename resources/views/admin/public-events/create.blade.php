@extends('adminlte::page')

@section('title', 'Create Public Event')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Create Public Event</h1>
            <p class="text-muted">Add a new event for public display</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.public-events.index') }}">Public Events</a></li>
                <li class="breadcrumb-item active">Create Event</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus mr-2"></i>
                    Event Details
                </h3>
            </div>
            <form method="POST" action="{{ route('admin.public-events.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Event Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required
                               placeholder="Enter event title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="summary">Summary</label>
                        <textarea class="form-control @error('summary') is-invalid @enderror"
                                  id="summary" name="summary" rows="3" maxlength="500"
                                  placeholder="Brief summary for event listings (optional)">{{ old('summary') }}</textarea>
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
                                  placeholder="Detailed event description">{{ old('description') }}</textarea>
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
                                    <option value="conference" {{ old('category') == 'conference' ? 'selected' : '' }}>Conference</option>
                                    <option value="workshop" {{ old('category') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                    <option value="training" {{ old('category') == 'training' ? 'selected' : '' }}>Training</option>
                                    <option value="seminar" {{ old('category') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                                    <option value="meeting" {{ old('category') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="announcement" {{ old('category') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                                    <option value="celebration" {{ old('category') == 'celebration' ? 'selected' : '' }}>Celebration</option>
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
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
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
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required
                                       min="{{ date('Y-m-d') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required
                                       min="{{ date('Y-m-d') }}">
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
                                       id="start_time" name="start_time" value="{{ old('start_time') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_time">End Time</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                       id="end_time" name="end_time" value="{{ old('end_time') }}">
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
                                       id="location" name="location" value="{{ old('location') }}"
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
                                       id="venue_address" name="venue_address" value="{{ old('venue_address') }}"
                                       placeholder="Complete venue address">
                                @error('venue_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="featured_image">Featured Image</label>
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
                                       id="contact_email" name="contact_email" value="{{ old('contact_email') }}"
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
                                       id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}"
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
                                   name="registration_required" value="1" {{ old('registration_required') ? 'checked' : '' }}>
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
                                           id="max_participants" name="max_participants" value="{{ old('max_participants') }}"
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
                                           id="registration_deadline" name="registration_deadline" value="{{ old('registration_deadline') }}"
                                           min="{{ date('Y-m-d') }}">
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
                                           id="fee" name="fee" value="{{ old('fee') }}"
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
                                           id="registration_link" name="registration_link" value="{{ old('registration_link') }}"
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
                               id="tags" name="tags" value="{{ old('tags') }}"
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
                                  placeholder="Any additional details, requirements, or instructions">{{ old('additional_info') }}</textarea>
                        @error('additional_info')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_featured"
                                   name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_featured">
                                Feature this event
                            </label>
                            <small class="form-text text-muted">
                                Featured events are highlighted on the homepage and event listings.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Create Event
                    </button>
                    <a href="{{ route('admin.public-events.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Event Categories
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge badge-primary mr-2">Conference</span>
                    <small class="text-muted">Large gatherings, symposiums</small>
                </div>
                <div class="mb-3">
                    <span class="badge badge-info mr-2">Workshop</span>
                    <small class="text-muted">Hands-on learning sessions</small>
                </div>
                <div class="mb-3">
                    <span class="badge badge-success mr-2">Training</span>
                    <small class="text-muted">Educational programs</small>
                </div>
                <div class="mb-3">
                    <span class="badge badge-warning mr-2">Seminar</span>
                    <small class="text-muted">Presentations and discussions</small>
                </div>
                <div class="mb-3">
                    <span class="badge badge-secondary mr-2">Meeting</span>
                    <small class="text-muted">Formal meetings and sessions</small>
                </div>
                <div class="mb-3">
                    <span class="badge badge-danger mr-2">Announcement</span>
                    <small class="text-muted">Important announcements</small>
                </div>
                <div class="mb-3">
                    <span class="badge badge-purple mr-2">Celebration</span>
                    <small class="text-muted">Ceremonies and celebrations</small>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Tips
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        <strong>Clear title</strong> - Make it descriptive and engaging
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        <strong>Compelling description</strong> - Include benefits and objectives
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        <strong>High-quality image</strong> - Use professional event graphics
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        <strong>Complete details</strong> - Include all relevant information
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        <strong>Clear contact</strong> - Provide easy ways to get in touch
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
// Show/hide registration fields based on checkbox
document.getElementById('registration_required').addEventListener('change', function() {
    const registrationFields = document.getElementById('registration-fields');
    if (this.checked) {
        registrationFields.style.display = 'block';
    } else {
        registrationFields.style.display = 'none';
    }
});

// Initialize on page load
if (document.getElementById('registration_required').checked) {
    document.getElementById('registration-fields').style.display = 'block';
}

// Set minimum date to today
document.getElementById('start_date').min = new Date().toISOString().split('T')[0];
document.getElementById('end_date').min = new Date().toISOString().split('T')[0];

// Update end date minimum when start date changes
document.getElementById('start_date').addEventListener('change', function() {
    document.getElementById('end_date').min = this.value;
    if (document.getElementById('end_date').value < this.value) {
        document.getElementById('end_date').value = this.value;
    }

    // Update registration deadline
    if (document.getElementById('registration_deadline').value >= this.value) {
        document.getElementById('registration_deadline').max = this.value;
    }
});

// Update registration deadline maximum when start date changes
document.getElementById('start_date').addEventListener('change', function() {
    document.getElementById('registration_deadline').max = this.value;
});

// Character counter for summary
const summaryField = document.getElementById('summary');
summaryField.addEventListener('input', function() {
    const remaining = 500 - this.value.length;
    let feedbackElement = document.getElementById('summary-feedback');

    if (!feedbackElement) {
        feedbackElement = document.createElement('small');
        feedbackElement.id = 'summary-feedback';
        feedbackElement.className = 'form-text text-muted';
        this.parentNode.appendChild(feedbackElement);
    }

    feedbackElement.textContent = `${remaining} characters remaining`;

    if (remaining < 50) {
        feedbackElement.className = 'form-text text-warning';
    } else if (remaining < 0) {
        feedbackElement.className = 'form-text text-danger';
    } else {
        feedbackElement.className = 'form-text text-muted';
    }
});

// Image preview
document.getElementById('featured_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('image-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'image-preview';
                preview.className = 'mt-2 img-thumbnail';
                preview.style.maxWidth = '200px';
                document.getElementById('featured_image').parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@stop

@section('css')
<style>
    .badge-purple {
        background-color: #6f42c1;
    }
</style>
@stop
