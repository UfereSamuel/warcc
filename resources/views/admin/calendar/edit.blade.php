@extends('adminlte::page')

@section('title', 'Edit Activity')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Activity</h1>
            <p class="text-muted">Update activity information and settings</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.calendar.index') }}">Calendar</a></li>
                <li class="breadcrumb-item active">Edit Activity</li>
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
                    <i class="fas fa-edit mr-2"></i>
                    Activity Information
                </h3>
                <div class="card-tools">
                    <span class="badge badge-{{ $activity->type_color }}">{{ $activity->type_label }}</span>
                    <span class="badge badge-{{ $activity->status_color }}">{{ ucfirst(str_replace('_', ' ', $activity->status)) }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.calendar.update', $activity) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Activity Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $activity->title) }}" required
                               placeholder="Enter activity title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4"
                                  placeholder="Enter activity description">{{ old('description', $activity->description) }}</textarea>
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
                                    <option value="meeting" {{ old('type', $activity->type) == 'meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="training" {{ old('type', $activity->type) == 'training' ? 'selected' : '' }}>Training</option>
                                    <option value="event" {{ old('type', $activity->type) == 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="holiday" {{ old('type', $activity->type) == 'holiday' ? 'selected' : '' }}>Holiday</option>
                                    <option value="deadline" {{ old('type', $activity->type) == 'deadline' ? 'selected' : '' }}>Deadline</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="not_yet_started" {{ old('status', $activity->status) == 'not_yet_started' ? 'selected' : '' }}>Not Yet Started</option>
                                    <option value="ongoing" {{ old('status', $activity->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="done" {{ old('status', $activity->status) == 'done' ? 'selected' : '' }}>Done</option>
                                </select>
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
                                       id="start_date" name="start_date" value="{{ old('start_date', $activity->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date', $activity->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror"
                               id="location" name="location" value="{{ old('location', $activity->location) }}"
                               placeholder="Enter activity location">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Update Activity
                    </button>
                    <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <form method="POST" action="{{ route('admin.calendar.destroy', $activity) }}"
                          style="display: inline;" class="ml-2"
                          onsubmit="return confirm('Are you sure you want to delete this activity?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Activity Details
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-5"><strong>Created:</strong></div>
                    <div class="col-7">{{ $activity->created_at->format('M d, Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Created by:</strong></div>
                    <div class="col-7">{{ $activity->creator->full_name ?? 'Unknown' }}</div>
                </div>
                @if($activity->updated_at != $activity->created_at)
                <div class="row mb-3">
                    <div class="col-5"><strong>Last Updated:</strong></div>
                    <div class="col-7">{{ $activity->updated_at->format('M d, Y') }}</div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-5"><strong>Duration:</strong></div>
                    <div class="col-7">{{ $activity->duration_in_days }} day{{ $activity->duration_in_days > 1 ? 's' : '' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Current Status:</strong></div>
                    <div class="col-7">
                        <span class="badge badge-{{ $activity->status_color }}">
                            {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

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
                        <small class="text-muted">Formal meetings and conferences</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-info mr-2">Training</span>
                        <small class="text-muted">Training sessions and workshops</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-success mr-2">Event</span>
                        <small class="text-muted">Special events and activities</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-warning mr-2">Holiday</span>
                        <small class="text-muted">Public holidays and observances</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-danger mr-2">Deadline</span>
                        <small class="text-muted">Important deadlines and due dates</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Update end date minimum when start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
        if (document.getElementById('end_date').value < this.value) {
            document.getElementById('end_date').value = this.value;
        }
    });

    // Set initial minimum for end date
    document.getElementById('end_date').min = document.getElementById('start_date').value;
</script>
@stop
