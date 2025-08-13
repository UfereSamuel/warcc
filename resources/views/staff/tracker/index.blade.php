@extends('layouts.staff')

@section('title', 'Weekly Tracker')
@section('page-title', 'Weekly Tracker')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Weekly Tracker</li>
@endsection

@section('content')
<!-- Current Week Status -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-week mr-2"></i>
                    Current Week ({{ $currentWeekStart->format('M d') }} - {{ $currentWeekEnd->format('M d, Y') }})
                </h3>
                <div class="card-tools">
                    @if(!$currentTracker)
                        <a href="{{ route('staff.tracker.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Create This Week's Tracker
                        </a>
                    @elseif($currentTracker->submission_status === 'draft')
                        <a href="{{ route('staff.tracker.edit', $currentTracker) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit mr-1"></i>
                            Edit Tracker
                        </a>
                    @else
                        @if($currentTracker->edit_request_status === 'none')
                            <form method="POST" action="{{ route('staff.tracker.request-edit', $currentTracker) }}" style="display: inline;" id="request-edit-form">
                                @csrf
                                <button type="button" class="btn btn-info btn-sm" onclick="showConfirmModal('edit-request', 'Request Edit Approval', 'Are you sure you want to request admin approval to edit this submitted tracker?', function() { document.getElementById(\'request-edit-form\').submit(); })">
                                    <i class="fas fa-edit mr-1"></i>
                                    Request Edit
                                </button>
                            </form>
                        @elseif($currentTracker->edit_request_status === 'pending')
                            <span class="btn btn-secondary btn-sm disabled">
                                <i class="fas fa-clock mr-1"></i>
                                Edit Pending
                            </span>
                        @elseif($currentTracker->edit_request_status === 'approved')
                            <a href="{{ route('staff.tracker.edit', $currentTracker) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-edit mr-1"></i>
                                Edit Approved
                            </a>
                        @endif
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($currentTracker)
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-calendar-week"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Week Range</span>
                                    <span class="info-box-number">{{ $currentTracker->week_range }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-{{ $currentTracker->submission_status === 'submitted' ? 'success' : 'warning' }}">
                                    <i class="fas fa-{{ $currentTracker->submission_status === 'submitted' ? 'check' : 'clock' }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number">{{ ucfirst(str_replace('_', ' ', $currentTracker->submission_status)) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-{{ $currentTracker->status === 'at_duty_station' ? 'building' : ($currentTracker->status === 'on_mission' ? 'plane' : 'calendar-times') }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Activity</span>
                                    <span class="info-box-number">{{ ucfirst(str_replace('_', ' ', $currentTracker->status)) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($currentTracker->submission_status === 'draft')
                                <form method="POST" action="{{ route('staff.tracker.submit', $currentTracker) }}" style="display: inline;" id="submit-tracker-form">
                                    @csrf
                                    <button type="button" class="btn btn-success btn-block" onclick="showConfirmModal('submit-tracker', 'Submit Tracker', 'Are you sure you want to submit this tracker?<br><br><strong>Note:</strong> You won\'t be able to edit it after submission without admin approval.', function() { document.getElementById(\'submit-tracker-form\').submit(); })">
                                        <i class="fas fa-paper-plane mr-1"></i>
                                        Submit Tracker
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check mr-1"></i>
                                    Submitted on {{ $currentTracker->submitted_at->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Mission/Leave Details -->
                    @if($currentTracker->status === 'on_mission' && $currentTracker->mission_title)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-plane mr-2"></i>
                                            Mission Details
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Title:</strong> {{ $currentTracker->mission_title }}<br>
                                                <strong>Type:</strong> {{ ucfirst($currentTracker->mission_type) }}<br>
                                                <strong>Purpose:</strong> {{ Str::limit($currentTracker->mission_purpose, 100) }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Duration:</strong> {{ $currentTracker->mission_start_date->format('M d') }} - {{ $currentTracker->mission_end_date->format('M d, Y') }}<br>
                                                @if($currentTracker->mission_documents)
                                                    <strong>Documents:</strong> {{ count($currentTracker->mission_documents) }} uploaded
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($currentTracker->status === 'on_leave' && $currentTracker->leaveType)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-calendar-times mr-2"></i>
                                            Leave Details
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Type:</strong> {{ $currentTracker->leaveType->name }}<br>
                                                <strong>Duration:</strong> {{ $currentTracker->leave_start_date->format('M d') }} - {{ $currentTracker->leave_end_date->format('M d, Y') }}
                                            </div>
                                            <div class="col-md-6">
                                                @if($currentTracker->leave_approval_document)
                                                    <strong>Approval Document:</strong> Uploaded
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($currentTracker->remarks)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-comment mr-2"></i>
                                            Remarks
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $currentTracker->remarks }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($currentTracker->edit_request_status !== 'none')
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-{{ $currentTracker->edit_request_status === 'approved' ? 'success' : ($currentTracker->edit_request_status === 'rejected' ? 'danger' : 'info') }}">
                                    <h6><i class="fas fa-{{ $currentTracker->edit_request_status === 'approved' ? 'check' : ($currentTracker->edit_request_status === 'rejected' ? 'times' : 'clock') }} mr-1"></i> Edit Request Status: {{ ucfirst($currentTracker->edit_request_status) }}</h6>
                                    @if($currentTracker->edit_requested_at)
                                        <small>Requested on: {{ $currentTracker->edit_requested_at->format('M d, Y \a\t h:i A') }}</small><br>
                                    @endif
                                    @if($currentTracker->edit_approved_at)
                                        <small>Approved on: {{ $currentTracker->edit_approved_at->format('M d, Y \a\t h:i A') }}</small><br>
                                    @endif
                                    @if($currentTracker->edit_rejection_reason)
                                        <small><strong>Rejection Reason:</strong> {{ $currentTracker->edit_rejection_reason }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No tracker for current week</h5>
                        <p class="text-muted">Create your weekly tracker to track your activities</p>
                        <a href="{{ route('staff.tracker.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>
                            Create Weekly Tracker
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Weekly Trackers -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Recent Weekly Trackers
                </h3>
            </div>
            <div class="card-body">
                @if($recentTrackers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Week</th>
                                    <th>Status</th>
                                    <th>Activity</th>
                                    <th>Submission</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTrackers as $tracker)
                                <tr>
                                    <td>
                                        <strong>{{ $tracker->week_range }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $tracker->submission_status === 'submitted' ? 'success' : 'warning' }}">
                                            {{ ucfirst(str_replace('_', ' ', $tracker->submission_status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <i class="fas fa-{{ $tracker->status === 'at_duty_station' ? 'building' : ($tracker->status === 'on_mission' ? 'plane' : 'calendar-times') }} mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $tracker->status)) }}
                                    </td>
                                    <td>
                                        @if($tracker->submitted_at)
                                            <small class="text-success">
                                                <i class="fas fa-check mr-1"></i>
                                                {{ $tracker->submitted_at->format('M d, Y') }}
                                            </small>
                                        @else
                                            <small class="text-muted">Not submitted</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tracker->submission_status === 'draft')
                                            <a href="{{ route('staff.tracker.edit', $tracker) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No weekly trackers yet</h5>
                        <p class="text-muted">Start by creating your first weekly tracker</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

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
</script>
@endpush

@push('styles')
<style>
    .info-box-number {
        font-size: 14px !important;
        font-weight: normal !important;
    }
</style>
@endpush
