@extends('layouts.staff')

@section('title', 'Create Activity Request')
@section('page-title', 'Create Activity Request')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.activity-requests.index') }}">Activity Requests</a></li>
    <li class="breadcrumb-item active">Create Request</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus mr-2"></i>
                    Activity Request Details
                </h3>
            </div>
            <form method="POST" action="{{ route('staff.activity-requests.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Activity Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required
                               placeholder="Enter a clear, descriptive title for your activity">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4"
                                  placeholder="Provide a detailed description of the activity">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Activity Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Activity Type</option>
                                    <option value="meeting" {{ old('type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="training" {{ old('type') == 'training' ? 'selected' : '' }}>Training</option>
                                    <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="holiday" {{ old('type') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                                    <option value="deadline" {{ old('type') == 'deadline' ? 'selected' : '' }}>Deadline</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expected_participants">Expected Participants</label>
                                <input type="number" class="form-control @error('expected_participants') is-invalid @enderror"
                                       id="expected_participants" name="expected_participants" value="{{ old('expected_participants') }}"
                                       min="1" max="1000" placeholder="Number of expected participants">
                                @error('expected_participants')
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

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror"
                               id="location" name="location" value="{{ old('location') }}"
                               placeholder="Enter venue or location">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hidden budget field - keeping for backend compatibility -->
                    <input type="hidden" id="estimated_budget" name="estimated_budget" value="0">

                    <div class="form-group">
                        <label for="justification">Remark</label>
                        <textarea class="form-control @error('justification') is-invalid @enderror"
                                  id="justification" name="justification" rows="4"
                                  placeholder="Add any additional remarks or notes about this activity (optional)">{{ old('justification') }}</textarea>
                        <small class="form-text text-muted">
                            Optional: Add any additional remarks, notes, or comments about this activity.
                        </small>
                        @error('justification')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Submit Request
                    </button>
                    <a href="{{ route('staff.activity-requests.index') }}" class="btn btn-secondary">
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
                    Activity Types
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <span class="badge badge-primary mr-2">Meeting</span>
                        <small class="text-muted">Formal meetings, conferences, and team sessions</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-info mr-2">Training</span>
                        <small class="text-muted">Professional development and skill-building sessions</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-success mr-2">Event</span>
                        <small class="text-muted">Organizational events, ceremonies, and celebrations</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-warning mr-2">Holiday</span>
                        <small class="text-muted">Public holidays and organizational observances</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-danger mr-2">Deadline</span>
                        <small class="text-muted">Important deadlines and due dates</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Submission Tips
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        <strong>Be specific</strong> about the activity's purpose and goals
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        <strong>Plan ahead</strong> - submit requests well in advance
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        <strong>Add remarks</strong> with any additional notes or details
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        <strong>Consider participants</strong> and logistics
                    </li>
                </ul>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Review Process:</strong> Your request will be reviewed by an administrator. You'll be notified of the decision and can track the status in your requests list.
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Set minimum date to today
    document.getElementById('start_date').min = new Date().toISOString().split('T')[0];
    document.getElementById('end_date').min = new Date().toISOString().split('T')[0];

    // Update end date minimum when start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
        if (document.getElementById('end_date').value < this.value) {
            document.getElementById('end_date').value = this.value;
        }
    });

    // Character counter for remark
    const remarkField = document.getElementById('justification');
    const maxLength = 1000;

    if (remarkField) {
        remarkField.addEventListener('input', function() {
            const remaining = maxLength - this.value.length;
            let feedbackElement = document.getElementById('remark-feedback');

            if (!feedbackElement) {
                feedbackElement = document.createElement('small');
                feedbackElement.id = 'remark-feedback';
                feedbackElement.className = 'form-text text-muted';
                this.parentNode.appendChild(feedbackElement);
            }

            feedbackElement.textContent = `${remaining} characters remaining`;

            if (remaining < 100) {
                feedbackElement.className = 'form-text text-warning';
            } else if (remaining < 0) {
                feedbackElement.className = 'form-text text-danger';
            } else {
                feedbackElement.className = 'form-text text-muted';
            }
        });
    }
</script>
@stop
