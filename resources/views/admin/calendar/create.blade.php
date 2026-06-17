@extends('adminlte::page')

@section('title', 'Create Activity')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Create Activity</h1>
            <p class="text-muted">Add a new organizational activity or event</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.calendar.index') }}">Calendar</a></li>
                <li class="breadcrumb-item active">Create Activity</li>
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
                    Activity Information
                </h3>
            </div>
            <form method="POST" action="{{ route('admin.calendar.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Activity Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required
                               placeholder="Enter activity title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4"
                                  placeholder="Enter activity description">{{ old('description') }}</textarea>
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
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="not_yet_started" {{ old('status') == 'not_yet_started' ? 'selected' : '' }}>Not Yet Started</option>
                                    <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
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
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
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
                               placeholder="Enter activity location">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Create Activity
                    </button>
                    <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary">
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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>
                    Status Guide
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <span class="badge badge-info mr-2">Not Yet Started</span>
                        <small class="text-muted">Activity is planned but not started</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-warning mr-2">Ongoing</span>
                        <small class="text-muted">Activity is currently in progress</small>
                    </div>
                    <div class="col-12 mb-3">
                        <span class="badge badge-success mr-2">Done</span>
                        <small class="text-muted">Activity has been completed</small>
                    </div>
                </div>
            </div>
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
</script>
@stop
