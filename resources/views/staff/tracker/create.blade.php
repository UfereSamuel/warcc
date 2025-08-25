@extends('layouts.staff')

@section('title', 'Create Weekly Tracker')
@section('page-title', 'Create Weekly Tracker')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.tracker.index') }}">Weekly Tracker</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Create Weekly Tracker
                </h3>
            </div>
            <form method="POST" action="{{ route('staff.tracker.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <!-- Week Dates -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="week_start_date">Week Start Date</label>
                                <input type="date"
                                       class="form-control @error('week_start_date') is-invalid @enderror"
                                       id="week_start_date"
                                       name="week_start_date"
                                       value="{{ old('week_start_date', $currentWeekStart->format('Y-m-d')) }}"
                                       readonly>
                                @error('week_start_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="week_end_date">Week End Date</label>
                                <input type="date"
                                       class="form-control @error('week_end_date') is-invalid @enderror"
                                       id="week_end_date"
                                       name="week_end_date"
                                       value="{{ old('week_end_date', $currentWeekEnd->format('Y-m-d')) }}"
                                       readonly>
                                @error('week_end_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <div class="form-group">
                        <label for="status">Weekly Status</label>
                        <select class="form-control @error('status') is-invalid @enderror"
                                id="status"
                                name="status"
                                onchange="updateStatusFields()">
                            <option value="">Select your status for this week</option>
                            <option value="at_duty_station" {{ old('status') === 'at_duty_station' ? 'selected' : '' }}>
                                At Duty Station
                            </option>
                            <option value="on_mission" {{ old('status') === 'on_mission' ? 'selected' : '' }}>
                                On Mission
                            </option>
                            <option value="on_leave" {{ old('status') === 'on_leave' ? 'selected' : '' }}>
                                On Leave
                            </option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Mission Fields (shown when on_mission is selected) -->
                    <div id="mission_fields" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-plane mr-2"></i>Mission Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="mission_title">Mission Title <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('mission_title') is-invalid @enderror"
                                           id="mission_title"
                                           name="mission_title"
                                           value="{{ old('mission_title') }}"
                                           placeholder="Enter mission title">
                                    @error('mission_title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mission_type">Mission Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('mission_type') is-invalid @enderror"
                                            id="mission_type"
                                            name="mission_type">
                                        <option value="">Select mission type</option>
                                        <option value="regional" {{ old('mission_type') === 'regional' ? 'selected' : '' }}>Regional</option>
                                        <option value="continental" {{ old('mission_type') === 'continental' ? 'selected' : '' }}>Continental</option>
                                        <option value="incountry" {{ old('mission_type') === 'incountry' ? 'selected' : '' }}>In-Country</option>
                                    </select>
                                    @error('mission_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mission_start_date">Mission Start Date <span class="text-danger">*</span></label>
                                            <input type="date"
                                                   class="form-control @error('mission_start_date') is-invalid @enderror"
                                                   id="mission_start_date"
                                                   name="mission_start_date"
                                                   value="{{ old('mission_start_date') }}"
                                                   onchange="updateMissionEndDateMin()">
                                            @error('mission_start_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mission_end_date">Mission End Date <span class="text-danger">*</span></label>
                                            <input type="date"
                                                   class="form-control @error('mission_end_date') is-invalid @enderror"
                                                   id="mission_end_date"
                                                   name="mission_end_date"
                                                   value="{{ old('mission_end_date') }}">
                                            @error('mission_end_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">Must be on or after start date</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="mission_purpose">Purpose of Mission <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('mission_purpose') is-invalid @enderror"
                                              id="mission_purpose"
                                              name="mission_purpose"
                                              rows="3"
                                              placeholder="Describe the purpose of the mission">{{ old('mission_purpose') }}</textarea>
                                    @error('mission_purpose')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mission_documents">Mission Documents</label>
                                    <input type="file"
                                           class="form-control-file @error('mission_documents.*') is-invalid @enderror"
                                           id="mission_documents"
                                           name="mission_documents[]"
                                           multiple
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload up to 3 documents (Mission approval memo, Note verbale, Invitation letter). Max 5MB each. Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
                                    @error('mission_documents.*')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Fields (shown when on_leave is selected) -->
                    <div id="leave_fields" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-calendar-times mr-2"></i>Leave Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="leave_type_id">Leave Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('leave_type_id') is-invalid @enderror"
                                            id="leave_type_id"
                                            name="leave_type_id">
                                        <option value="">Select leave type</option>
                                        @foreach($leaveTypes as $leaveType)
                                            <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                                {{ $leaveType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('leave_type_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="leave_start_date">Leave Start Date <span class="text-danger">*</span></label>
                                            <input type="date"
                                                   class="form-control @error('leave_start_date') is-invalid @enderror"
                                                   id="leave_start_date"
                                                   name="leave_start_date"
                                                   value="{{ old('leave_start_date') }}"
                                                   onchange="updateLeaveEndDateMin()">
                                            @error('leave_start_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="leave_end_date">Leave End Date <span class="text-danger">*</span></label>
                                            <input type="date"
                                                   class="form-control @error('leave_end_date') is-invalid @enderror"
                                                   id="leave_end_date"
                                                   name="leave_end_date"
                                                   value="{{ old('leave_end_date') }}">
                                            @error('leave_end_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">Must be on or after start date</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="leave_approval_document">Leave Approval Document <span class="text-danger">*</span></label>
                                    <input type="file"
                                           class="form-control-file @error('leave_approval_document') is-invalid @enderror"
                                           id="leave_approval_document"
                                           name="leave_approval_document"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload leave approval document. Max 5MB. Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
                                    @error('leave_approval_document')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="form-group">
                        <label for="remarks">Remarks <span id="remarks_required" class="text-danger" style="display: none;">*</span></label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror"
                                  id="remarks"
                                  name="remarks"
                                  rows="4"
                                  placeholder="Add any additional notes about your week...">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">Maximum 1000 characters</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Create Tracker
                    </button>
                    <a href="{{ route('staff.tracker.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    About Weekly Tracker
                </h3>
            </div>
            <div class="card-body">
                <p><strong>Weekly Tracker</strong> helps you record your primary activity for each week.</p>

                <h6>Status Options:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-building text-success mr-2"></i><strong>At Duty Station:</strong> Regular office work</li>
                    <li><i class="fas fa-plane text-primary mr-2"></i><strong>On Mission:</strong> Travel or field work</li>
                    <li><i class="fas fa-calendar-times text-danger mr-2"></i><strong>On Leave:</strong> Taking approved leave</li>
                </ul>

                <div class="alert alert-info">
                    <i class="fas fa-lightbulb mr-1"></i>
                    <strong>Tip:</strong> Select the status that best describes your primary activity for this week. Additional fields will appear based on your selection.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Validation Modal -->
<div class="modal fade" id="validationModal" tabindex="-1" role="dialog" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="validationModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Validation Error
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="validationModalMessage">
                    <!-- Message will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatusFields() {
    const status = document.getElementById('status').value;
    const missionFields = document.getElementById('mission_fields');
    const leaveFields = document.getElementById('leave_fields');
    const remarksRequired = document.getElementById('remarks_required');

    // Hide all conditional fields
    missionFields.style.display = 'none';
    leaveFields.style.display = 'none';
    remarksRequired.style.display = 'none';

    // Show relevant field based on status
    if (status === 'on_mission') {
        missionFields.style.display = 'block';
    } else if (status === 'on_leave') {
        leaveFields.style.display = 'block';
    } else if (status === 'at_duty_station') {
        remarksRequired.style.display = 'inline';
    }
}

function updateMissionEndDateMin() {
    const startDate = document.getElementById('mission_start_date').value;
    const endDateField = document.getElementById('mission_end_date');
    
    if (startDate) {
        endDateField.min = startDate;
        
        // Clear end date if it's before the new start date
        if (endDateField.value && endDateField.value < startDate) {
            endDateField.value = '';
        }
    }
}

function updateLeaveEndDateMin() {
    const startDate = document.getElementById('leave_start_date').value;
    const endDateField = document.getElementById('leave_end_date');
    
    if (startDate) {
        endDateField.min = startDate;
        
        // Clear end date if it's before the new start date
        if (endDateField.value && endDateField.value < startDate) {
            endDateField.value = '';
        }
    }
}

function showValidationModal(message) {
    $('#validationModalMessage').html(message);
    $('#validationModal').modal('show');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStatusFields();

    // Limit mission documents to 3 files
    document.getElementById('mission_documents').addEventListener('change', function() {
        if (this.files.length > 3) {
            showValidationModal(
                '<i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i><br>' +
                '<strong>Too Many Files</strong><br>' +
                'You can only upload up to 3 documents for mission evidence.<br>' +
                'Please select 3 or fewer files.'
            );
            this.value = '';
        }
    });
});
</script>
@endpush
