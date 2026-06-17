@extends('layouts.staff')

@section('title', 'Edit Weekly Tracker')
@section('page-title', 'Edit Weekly Tracker')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.tracker.index') }}">Weekly Tracker</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Weekly Tracker
                </h3>
            </div>
            
            <!-- Main Edit Form -->
            <form method="POST" action="{{ route('staff.tracker.update', $tracker) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    <!-- Week Display -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-calendar mr-2"></i>
                                <strong>Week:</strong> {{ $tracker->week_range }}
                            </div>
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <div class="form-group">
                        <label for="status" class="required">Weekly Status</label>
                        <select class="form-control @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                onchange="updateStatusFields()" 
                                required>
                            <option value="">Select your status for this week</option>
                            <option value="at_duty_station" {{ old('status', $tracker->status) === 'at_duty_station' ? 'selected' : '' }}>
                                At Duty Station
                            </option>
                            <option value="on_mission" {{ old('status', $tracker->status) === 'on_mission' ? 'selected' : '' }}>
                                On Mission
                            </option>
                            <option value="on_leave" {{ old('status', $tracker->status) === 'on_leave' ? 'selected' : '' }}>
                                On Leave
                            </option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Mission Fields -->
                    <div id="mission_fields" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-plane mr-2"></i>Mission Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mission_title">Mission Title</label>
                                            <input type="text" 
                                                   class="form-control @error('mission_title') is-invalid @enderror" 
                                                   id="mission_title" 
                                                   name="mission_title" 
                                                   value="{{ old('mission_title', $tracker->mission_title) }}"
                                                   placeholder="Enter mission title">
                                            @error('mission_title')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mission_type">Mission Type</label>
                                            <select class="form-control @error('mission_type') is-invalid @enderror" 
                                                    id="mission_type" 
                                                    name="mission_type">
                                                <option value="">Select mission type</option>
                                                <option value="regional" {{ old('mission_type', $tracker->mission_type) === 'regional' ? 'selected' : '' }}>Regional</option>
                                                <option value="continental" {{ old('mission_type', $tracker->mission_type) === 'continental' ? 'selected' : '' }}>Continental</option>
                                                <option value="incountry" {{ old('mission_type', $tracker->mission_type) === 'incountry' ? 'selected' : '' }}>In-Country</option>
                                            </select>
                                            @error('mission_type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mission_start_date">Start Date</label>
                                            <input type="date" 
                                                   class="form-control @error('mission_start_date') is-invalid @enderror" 
                                                   id="mission_start_date" 
                                                   name="mission_start_date" 
                                                   value="{{ old('mission_start_date', $tracker->mission_start_date?->format('Y-m-d')) }}"
                                                   onchange="updateMissionEndDateMin()">
                                            @error('mission_start_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mission_end_date">End Date</label>
                                            <input type="date" 
                                                   class="form-control @error('mission_end_date') is-invalid @enderror" 
                                                   id="mission_end_date" 
                                                   name="mission_end_date" 
                                                   value="{{ old('mission_end_date', $tracker->mission_end_date?->format('Y-m-d')) }}">
                                            @error('mission_end_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="mission_purpose">Mission Purpose</label>
                                    <textarea class="form-control @error('mission_purpose') is-invalid @enderror" 
                                              id="mission_purpose" 
                                              name="mission_purpose" 
                                              rows="3" 
                                              placeholder="Describe the purpose and objectives of this mission">{{ old('mission_purpose', $tracker->mission_purpose) }}</textarea>
                                    @error('mission_purpose')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mission_documents">Additional Documents (Optional)</label>
                                    <input type="file" 
                                           class="form-control-file @error('mission_documents.*') is-invalid @enderror" 
                                           id="mission_documents" 
                                           name="mission_documents[]" 
                                           multiple 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">
                                        Upload up to 3 files (PDF, DOC, DOCX, JPG, PNG). Max 5MB each.
                                    </small>
                                    @error('mission_documents.*')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Fields -->
                    <div id="leave_fields" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-calendar-times mr-2"></i>Leave Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="leave_type_id">Leave Type</label>
                                            <select class="form-control @error('leave_type_id') is-invalid @enderror" 
                                                    id="leave_type_id" 
                                                    name="leave_type_id">
                                                <option value="">Select leave type</option>
                                                @foreach($leaveTypes as $leaveType)
                                                    <option value="{{ $leaveType->id }}" 
                                                            {{ old('leave_type_id', $tracker->leave_type_id) == $leaveType->id ? 'selected' : '' }}>
                                                        {{ $leaveType->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('leave_type_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="leave_start_date">Start Date</label>
                                            <input type="date" 
                                                   class="form-control @error('leave_start_date') is-invalid @enderror" 
                                                   id="leave_start_date" 
                                                   name="leave_start_date" 
                                                   value="{{ old('leave_start_date', $tracker->leave_start_date?->format('Y-m-d')) }}"
                                                   onchange="updateLeaveEndDateMin()">
                                            @error('leave_start_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="leave_end_date">End Date</label>
                                            <input type="date" 
                                                   class="form-control @error('leave_end_date') is-invalid @enderror" 
                                                   id="leave_end_date" 
                                                   name="leave_end_date" 
                                                   value="{{ old('leave_end_date', $tracker->leave_end_date?->format('Y-m-d')) }}">
                                            @error('leave_end_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="leave_approval_document">Leave Approval Document (Optional)</label>
                                    <input type="file" 
                                           class="form-control-file @error('leave_approval_document') is-invalid @enderror" 
                                           id="leave_approval_document" 
                                           name="leave_approval_document" 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">
                                        Upload approval document (PDF, DOC, DOCX, JPG, PNG). Max 5MB.
                                    </small>
                                    @error('leave_approval_document')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="form-group">
                        <label for="remarks">Additional Notes (Optional)</label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                  id="remarks" 
                                  name="remarks" 
                                  rows="4" 
                                  placeholder="Add any additional notes about your week...">{{ old('remarks', $tracker->remarks) }}</textarea>
                        @error('remarks')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">Maximum 1000 characters</small>
                    </div>
                </div>

                <!-- Form Footer with Actions -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                Update Tracker
                            </button>
                            <a href="{{ route('staff.tracker.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Back to Tracker
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            @if($tracker->submission_status === 'draft')
                                <button type="button" class="btn btn-success" id="submit-tracker-btn" onclick="submitTracker()">
                                    <i class="fas fa-paper-plane mr-1"></i>
                                    Submit Tracker
                                </button>
                            @else
                                <span class="badge badge-{{ $tracker->submission_status === 'submitted' ? 'warning' : ($tracker->submission_status === 'approved' ? 'success' : 'danger') }} p-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Status: {{ ucfirst($tracker->submission_status) }}
                                </span>
                                @if($tracker->submission_status === 'submitted')
                                    <button type="button" class="btn btn-outline-warning btn-sm ml-2" onclick="requestEdit()">
                                        <i class="fas fa-edit mr-1"></i>
                                        Request Edit
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Tracker Information
                </h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">Week:</dt>
                    <dd class="col-sm-7">{{ $tracker->week_range }}</dd>

                    <dt class="col-sm-5">Status:</dt>
                    <dd class="col-sm-7">
                        <span class="badge badge-{{ $tracker->submission_status === 'submitted' ? 'warning' : ($tracker->submission_status === 'approved' ? 'success' : ($tracker->submission_status === 'rejected' ? 'danger' : 'secondary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $tracker->submission_status)) }}
                        </span>
                    </dd>

                    @if($tracker->submitted_at)
                        <dt class="col-sm-5">Submitted:</dt>
                        <dd class="col-sm-7">{{ $tracker->submitted_at->format('M d, Y \a\t h:i A') }}</dd>
                    @endif
                </dl>

                @if($tracker->submission_status === 'submitted')
                    <div class="alert alert-warning">
                        <i class="fas fa-lock mr-1"></i>
                        <strong>Locked:</strong> This tracker has been submitted. Request admin approval to make changes.
                    </div>
                @elseif($tracker->submission_status === 'approved')
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-1"></i>
                        <strong>Approved:</strong> This tracker has been approved by admin.
                    </div>
                @elseif($tracker->submission_status === 'rejected')
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle mr-1"></i>
                        <strong>Rejected:</strong> This tracker was rejected. You can edit and resubmit.
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-edit mr-1"></i>
                        <strong>Draft:</strong> You can still edit this tracker before submitting it.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Submit Tracker Form (Separate) -->
@if($tracker->submission_status === 'draft')
<form method="POST" action="{{ route('staff.tracker.submit', $tracker) }}" id="submit-form" style="display: none;">
    @csrf
</form>
@endif

<!-- Edit Request Form (Separate) -->
@if($tracker->submission_status === 'submitted')
<form method="POST" action="{{ route('staff.tracker.request-edit', $tracker) }}" id="edit-request-form" style="display: none;">
    @csrf
</form>
@endif

@endsection

@push('scripts')
<script>
// Update status-dependent fields
function updateStatusFields() {
    const status = document.getElementById('status').value;
    const missionFields = document.getElementById('mission_fields');
    const leaveFields = document.getElementById('leave_fields');

    // Hide all conditional fields first
    missionFields.style.display = 'none';
    leaveFields.style.display = 'none';

    // Show relevant fields based on status
    if (status === 'on_mission') {
        missionFields.style.display = 'block';
    } else if (status === 'on_leave') {
        leaveFields.style.display = 'block';
    }
}

// Update mission end date minimum
function updateMissionEndDateMin() {
    const startDate = document.getElementById('mission_start_date').value;
    const endDateInput = document.getElementById('mission_end_date');
    if (startDate) {
        endDateInput.min = startDate;
    }
}

// Update leave end date minimum
function updateLeaveEndDateMin() {
    const startDate = document.getElementById('leave_start_date').value;
    const endDateInput = document.getElementById('leave_end_date');
    if (startDate) {
        endDateInput.min = startDate;
    }
}

// Submit tracker function
function submitTracker() {
    if (confirm('Are you sure you want to submit this tracker?\n\nNote: You won\'t be able to edit it after submission without admin approval.')) {
        const submitBtn = document.getElementById('submit-tracker-btn');
        const submitForm = document.getElementById('submit-form');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Submitting...';
        }
        
        if (submitForm) {
            submitForm.submit();
        } else {
            alert('Error: Could not submit tracker. Please refresh the page and try again.');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i>Submit Tracker';
            }
        }
    }
}

// Request edit function
function requestEdit() {
    if (confirm('Request permission to edit this submitted tracker?\n\nAn admin will need to approve your request before you can make changes.')) {
        const editForm = document.getElementById('edit-request-form');
        if (editForm) {
            editForm.submit();
        } else {
            alert('Error: Could not submit edit request. Please refresh the page and try again.');
        }
    }
}

// File upload validation
function validateFileUpload(input, maxFiles = 1) {
    if (input.files.length > maxFiles) {
        alert(`You can only upload up to ${maxFiles} file(s).`);
        input.value = '';
        return false;
    }
    
    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (file.size > maxSize) {
            alert(`File "${file.name}" is too large. Maximum file size is 5MB.`);
            input.value = '';
            return false;
        }
    }
    
    return true;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set up initial status fields
    updateStatusFields();
    
    // File upload validation for mission documents
    const missionDocsInput = document.getElementById('mission_documents');
    if (missionDocsInput) {
        missionDocsInput.addEventListener('change', function() {
            validateFileUpload(this, 3);
        });
    }
    
    // File upload validation for leave approval document
    const leaveDocInput = document.getElementById('leave_approval_document');
    if (leaveDocInput) {
        leaveDocInput.addEventListener('change', function() {
            validateFileUpload(this, 1);
        });
    }
});
</script>
@endpush