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
                <div class="card-tools">
                    <span class="badge badge-{{ $tracker->submission_status === 'submitted' ? 'success' : 'warning' }}">
                        {{ ucfirst(str_replace('_', ' ', $tracker->submission_status)) }}
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('staff.tracker.update', $tracker) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <!-- Week Information (Read-only) -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-calendar-week mr-1"></i>
                                <strong>Week:</strong> {{ $tracker->week_range }}
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
                            <option value="at_duty_station"
                                    {{ old('status', $tracker->status) === 'at_duty_station' ? 'selected' : '' }}>
                                At Duty Station
                            </option>
                            <option value="on_mission"
                                    {{ old('status', $tracker->status) === 'on_mission' ? 'selected' : '' }}>
                                On Mission
                            </option>
                            <option value="on_leave"
                                    {{ old('status', $tracker->status) === 'on_leave' ? 'selected' : '' }}>
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
                                           value="{{ old('mission_title', $tracker->mission_title) }}"
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
                                        <option value="regional" {{ old('mission_type', $tracker->mission_type) === 'regional' ? 'selected' : '' }}>Regional</option>
                                        <option value="continental" {{ old('mission_type', $tracker->mission_type) === 'continental' ? 'selected' : '' }}>Continental</option>
                                        <option value="incountry" {{ old('mission_type', $tracker->mission_type) === 'incountry' ? 'selected' : '' }}>In-Country</option>
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
                                                   value="{{ old('mission_start_date', $tracker->mission_start_date?->format('Y-m-d')) }}">
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
                                                   value="{{ old('mission_end_date', $tracker->mission_end_date?->format('Y-m-d')) }}">
                                            @error('mission_end_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="mission_purpose">Purpose of Mission <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('mission_purpose') is-invalid @enderror"
                                              id="mission_purpose"
                                              name="mission_purpose"
                                              rows="3"
                                              placeholder="Describe the purpose of the mission">{{ old('mission_purpose', $tracker->mission_purpose) }}</textarea>
                                    @error('mission_purpose')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if($tracker->mission_documents)
                                    <div class="form-group">
                                        <label>Existing Mission Documents</label>
                                        <div class="list-group">
                                            @foreach($tracker->mission_documents as $index => $document)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>{{ $document['original_name'] }}</span>
                                                    <a href="{{ route('staff.tracker.download', [$tracker, 'mission', $index]) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="mission_documents">Add More Mission Documents</label>
                                    <input type="file"
                                           class="form-control-file @error('mission_documents.*') is-invalid @enderror"
                                           id="mission_documents"
                                           name="mission_documents[]"
                                           multiple
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload additional documents. Max 5MB each. Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
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
                                            <option value="{{ $leaveType->id }}" {{ old('leave_type_id', $tracker->leave_type_id) == $leaveType->id ? 'selected' : '' }}>
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
                                                   value="{{ old('leave_start_date', $tracker->leave_start_date?->format('Y-m-d')) }}">
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
                                                   value="{{ old('leave_end_date', $tracker->leave_end_date?->format('Y-m-d')) }}">
                                            @error('leave_end_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @if($tracker->leave_approval_document)
                                    <div class="form-group">
                                        <label>Existing Leave Approval Document</label>
                                        <div class="list-group">
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>{{ $tracker->leave_approval_document['original_name'] }}</span>
                                                <a href="{{ route('staff.tracker.download', [$tracker, 'leave']) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="leave_approval_document">{{ $tracker->leave_approval_document ? 'Replace' : 'Upload' }} Leave Approval Document</label>
                                    <input type="file"
                                           class="form-control-file @error('leave_approval_document') is-invalid @enderror"
                                           id="leave_approval_document"
                                           name="leave_approval_document"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">{{ $tracker->leave_approval_document ? 'Upload a new document to replace the existing one.' : 'Upload leave approval document.' }} Max 5MB. Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
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
                                  placeholder="Add any additional notes about your week...">{{ old('remarks', $tracker->remarks) }}</textarea>
                        @error('remarks')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">Maximum 1000 characters</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Update Tracker
                    </button>
                    <a href="{{ route('staff.tracker.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Tracker
                    </a>

                    @if($tracker->submission_status === 'draft')
                        <div class="float-right">
                            <form method="POST" action="{{ route('staff.tracker.submit', $tracker) }}" style="display: inline;" id="submit-tracker-form">
                                @csrf
                                <button type="button" class="btn btn-success" onclick="showConfirmModal('submit-tracker', 'Submit Tracker', 'Are you sure you want to submit this tracker?<br><br><strong>Note:</strong> You won\'t be able to edit it after submission without admin approval.', function() { document.getElementById(\'submit-tracker-form\').submit(); })">
                                    <i class="fas fa-paper-plane mr-1"></i>
                                    Submit Tracker
                                </button>
                            </form>
                        </div>
                    @endif
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
                        <span class="badge badge-{{ $tracker->submission_status === 'submitted' ? 'success' : 'warning' }}">
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

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i id="confirmModalIcon" class="fas fa-question-circle mr-2"></i>
                    <span id="confirmModalTitle">Confirm Action</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="confirmModalMessage">
                    <!-- Message will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="confirmModalAction" class="btn btn-primary">
                    Confirm
                </button>
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

function showValidationModal(message) {
    $('#validationModalMessage').html(message);
    $('#validationModal').modal('show');
}

function showConfirmModal(type, title, message, actionCallback) {
    const modal = $('#confirmModal');
    const icon = $('#confirmModalIcon');
    const titleEl = $('#confirmModalTitle');
    const messageEl = $('#confirmModalMessage');
    const actionBtn = $('#confirmModalAction');

    // Set icon and colors based on type
    switch(type) {
        case 'submit-tracker':
            icon.removeClass().addClass('fas fa-paper-plane mr-2');
            modal.find('.modal-header').removeClass('bg-danger bg-warning').addClass('bg-success text-white');
            actionBtn.removeClass('btn-primary btn-danger').addClass('btn-success');
            break;
        case 'edit-request':
            icon.removeClass().addClass('fas fa-edit mr-2');
            modal.find('.modal-header').removeClass('bg-danger bg-success').addClass('bg-info text-white');
            actionBtn.removeClass('btn-primary btn-danger').addClass('btn-info');
            break;
        case 'delete':
            icon.removeClass().addClass('fas fa-trash mr-2');
            modal.find('.modal-header').removeClass('bg-success bg-info').addClass('bg-danger text-white');
            actionBtn.removeClass('btn-primary btn-success').addClass('btn-danger');
            break;
        default:
            icon.removeClass().addClass('fas fa-question-circle mr-2');
            modal.find('.modal-header').removeClass('bg-success bg-danger bg-info').addClass('bg-primary text-white');
            actionBtn.removeClass('btn-success btn-danger btn-info').addClass('btn-primary');
    }

    titleEl.text(title);
    messageEl.html(message);

    // Handle action button
    actionBtn.off('click').on('click', function() {
        modal.modal('hide');
        if (actionCallback) {
            actionCallback();
        }
    });

    modal.modal('show');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStatusFields();

    // Limit mission documents to 3 files
    const missionDocsInput = document.getElementById('mission_documents');
    if (missionDocsInput) {
        missionDocsInput.addEventListener('change', function() {
            if (this.files.length > 3) {
                showValidationModal(
                    '<i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i><br>' +
                    '<strong>Too Many Files</strong><br>' +
                    'You can only upload up to 3 additional documents.<br>' +
                    'Please select 3 or fewer files.'
                );
                this.value = '';
            }
        });
    }
});
</script>
@endpush
