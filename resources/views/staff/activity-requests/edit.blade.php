@extends('layouts.staff')

@section('title', 'Edit Activity Request')
@section('page-title', 'Edit Activity Request')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.activity-requests.index') }}">Activity Requests</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.activity-requests.show', $activityRequest) }}">{{ Str::limit($activityRequest->title, 30) }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Activity Request
                </h3>
            </div>
            <form method="POST" action="{{ route('staff.activity-requests.update', $activityRequest) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Activity Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title"
                               value="{{ old('title', $activityRequest->title) }}" required
                               placeholder="Enter a clear, descriptive title for your activity">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4"
                                  placeholder="Provide a detailed description of the activity">{{ old('description', $activityRequest->description) }}</textarea>
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
                                    @foreach(['meeting' => 'Meeting', 'training' => 'Training', 'workshop' => 'Workshop', 'mission' => 'Mission', 'event' => 'Event', 'holiday' => 'Holiday', 'deadline' => 'Deadline'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('type', $activityRequest->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
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
                                       id="expected_participants" name="expected_participants"
                                       value="{{ old('expected_participants', $activityRequest->expected_participants) }}"
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
                                       id="start_date" name="start_date"
                                       value="{{ old('start_date', $activityRequest->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date"
                                       value="{{ old('end_date', $activityRequest->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror"
                               id="location" name="location"
                               value="{{ old('location', $activityRequest->location) }}"
                               placeholder="Enter venue or location">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" id="estimated_budget" name="estimated_budget"
                           value="{{ old('estimated_budget', $activityRequest->estimated_budget ?? 0) }}">

                    <div class="form-group">
                        <label for="justification">Remark</label>
                        <textarea class="form-control @error('justification') is-invalid @enderror"
                                  id="justification" name="justification" rows="4"
                                  placeholder="Add any additional remarks or notes about this activity (optional)">{{ old('justification', $activityRequest->justification) }}</textarea>
                        @error('justification')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                    <a href="{{ route('staff.activity-requests.show', $activityRequest) }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Pending review:</strong> Changes will be visible to administrators when they review your request.
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Request Info
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Submitted:</strong> {{ $activityRequest->created_at->format('M d, Y') }}</p>
                <p class="mb-0"><strong>Status:</strong>
                    <span class="badge badge-{{ $activityRequest->status_color }}">{{ $activityRequest->status_label }}</span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
        if (document.getElementById('end_date').value < this.value) {
            document.getElementById('end_date').value = this.value;
        }
    });
</script>
@endsection
