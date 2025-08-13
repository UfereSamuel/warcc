@extends('adminlte::page')

@section('title', 'Weekly Tracker Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Weekly Tracker Details</h1>
            <p class="text-muted">Review {{ $tracker->staff->full_name }}'s weekly submission</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.weekly-trackers.index') }}">Weekly Trackers</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Main Tracker Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-week mr-2"></i>
                    Weekly Tracker Information
                </h3>
                <div class="card-tools">
                    @switch($tracker->submission_status)
                        @case('draft')
                            <span class="badge badge-secondary badge-lg">Draft</span>
                            @break
                        @case('submitted')
                            <span class="badge badge-warning badge-lg">Submitted</span>
                            @break
                        @case('reviewed')
                            <span class="badge badge-info badge-lg">Reviewed</span>
                            @break
                        @case('approved')
                            <span class="badge badge-success badge-lg">Approved</span>
                            @break
                        @case('rejected')
                            <span class="badge badge-danger badge-lg">Rejected</span>
                            @break
                    @endswitch
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Staff Member:</dt>
                            <dd class="col-sm-8">
                                <div class="media align-items-center">
                                    <img src="{{ $tracker->staff->profile_picture_url }}"
                                         alt="{{ $tracker->staff->full_name }}"
                                         class="img-circle mr-3"
                                         style="width: 40px; height: 40px;">
                                    <div class="media-body">
                                        <h6 class="mb-0">{{ $tracker->staff->full_name }}</h6>
                                        <small class="text-muted">{{ $tracker->staff->staff_id }}</small>
                                    </div>
                                </div>
                            </dd>

                            <dt class="col-sm-4">Department:</dt>
                            <dd class="col-sm-8">
                                <span class="badge badge-info">{{ $tracker->staff->department }}</span>
                            </dd>

                            <dt class="col-sm-4">Week Period:</dt>
                            <dd class="col-sm-8">
                                <strong>{{ $tracker->week_range }}</strong>
                            </dd>

                            <dt class="col-sm-4">Weekly Status:</dt>
                            <dd class="col-sm-8">
                                @switch($tracker->status)
                                    @case('at_duty_station')
                                        <span class="badge badge-primary badge-lg">
                                            <i class="fas fa-building mr-1"></i>At Duty Station
                                        </span>
                                        @break
                                    @case('on_mission')
                                        <span class="badge badge-info badge-lg">
                                            <i class="fas fa-plane mr-1"></i>On Mission
                                        </span>
                                        @break
                                    @case('on_leave')
                                        <span class="badge badge-warning badge-lg">
                                            <i class="fas fa-calendar-times mr-1"></i>On Leave
                                        </span>
                                        @break
                                @endswitch
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Submitted:</dt>
                            <dd class="col-sm-8">
                                @if($tracker->submitted_at)
                                    {{ $tracker->submitted_at->format('M d, Y \a\t h:i A') }}
                                @else
                                    <span class="text-muted">Not submitted</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4">Reviewed By:</dt>
                            <dd class="col-sm-8">
                                @if($tracker->reviewed_by)
                                    {{ \App\Models\Staff::find($tracker->reviewed_by)->full_name ?? 'Unknown' }}
                                    <br><small class="text-muted">{{ $tracker->reviewed_at ? $tracker->reviewed_at->format('M d, Y \a\t h:i A') : '' }}</small>
                                @else
                                    <span class="text-muted">Not reviewed</span>
                                @endif
                            </dd>

                            @if($tracker->admin_notes)
                                <dt class="col-sm-4">Admin Notes:</dt>
                                <dd class="col-sm-8">
                                    <div class="alert alert-info mb-0">
                                        {{ $tracker->admin_notes }}
                                    </div>
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>

                @if($tracker->remarks)
                    <div class="mt-3">
                        <h6>Staff Remarks:</h6>
                        <div class="alert alert-light">
                            {{ $tracker->remarks }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Status Specific Details -->
        @if($tracker->status === 'on_mission')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plane mr-2"></i>
                        Mission Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Mission Title:</dt>
                                <dd class="col-sm-8">{{ $tracker->mission_title }}</dd>

                                <dt class="col-sm-4">Mission Type:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-primary">{{ ucfirst($tracker->mission_type) }}</span>
                                </dd>

                                <dt class="col-sm-4">Purpose:</dt>
                                <dd class="col-sm-8">{{ $tracker->mission_purpose }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Start Date:</dt>
                                <dd class="col-sm-8">{{ $tracker->mission_start_date ? $tracker->mission_start_date->format('M d, Y') : '--' }}</dd>

                                <dt class="col-sm-4">End Date:</dt>
                                <dd class="col-sm-8">{{ $tracker->mission_end_date ? $tracker->mission_end_date->format('M d, Y') : '--' }}</dd>

                                <dt class="col-sm-4">Duration:</dt>
                                <dd class="col-sm-8">
                                    @if($tracker->mission_start_date && $tracker->mission_end_date)
                                        {{ $tracker->mission_start_date->diffInDays($tracker->mission_end_date) + 1 }} days
                                    @else
                                        --
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>

                    @if($tracker->mission_documents)
                        <div class="mt-3">
                            <h6>Mission Documents:</h6>
                            <div class="row">
                                @foreach(json_decode($tracker->mission_documents, true) as $index => $document)
                                    <div class="col-md-6 mb-2">
                                        <div class="card card-outline card-primary">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-pdf fa-2x text-danger mr-3"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">{{ $document['original_name'] }}</h6>
                                                        <small class="text-muted">{{ number_format($document['size'] / 1024, 1) }} KB</small>
                                                    </div>
                                                    <a href="{{ route('staff.tracker.download', [$tracker, 'mission', $index]) }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($tracker->status === 'on_leave')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-times mr-2"></i>
                        Leave Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Leave Type:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-warning">{{ $tracker->leaveType->name ?? 'Unknown' }}</span>
                                </dd>

                                <dt class="col-sm-4">Start Date:</dt>
                                <dd class="col-sm-8">{{ $tracker->leave_start_date ? $tracker->leave_start_date->format('M d, Y') : '--' }}</dd>

                                <dt class="col-sm-4">End Date:</dt>
                                <dd class="col-sm-8">{{ $tracker->leave_end_date ? $tracker->leave_end_date->format('M d, Y') : '--' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Duration:</dt>
                                <dd class="col-sm-8">
                                    @if($tracker->leave_start_date && $tracker->leave_end_date)
                                        {{ $tracker->leave_start_date->diffInDays($tracker->leave_end_date) + 1 }} days
                                    @else
                                        --
                                    @endif
                                </dd>

                                <dt class="col-sm-4">Approval Doc:</dt>
                                <dd class="col-sm-8">
                                    @if($tracker->leave_approval_document)
                                        @php $doc = json_decode($tracker->leave_approval_document, true); @endphp
                                        <a href="{{ route('staff.tracker.download', [$tracker, 'leave']) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-download mr-1"></i>
                                            {{ $doc['original_name'] ?? 'Document' }}
                                        </a>
                                    @else
                                        <span class="text-muted">No document</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Action Panel -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs mr-2"></i>
                    Admin Actions
                </h3>
            </div>
            <div class="card-body">
                @if($tracker->submission_status === 'submitted')
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Pending Review</strong><br>
                        This submission needs admin action.
                    </div>

                    <div class="btn-group-vertical btn-block mb-3">
                        <button class="btn btn-success" onclick="updateStatus('approved')">
                            <i class="fas fa-check mr-1"></i> Approve Submission
                        </button>
                        <button class="btn btn-info" onclick="updateStatus('reviewed')">
                            <i class="fas fa-eye mr-1"></i> Mark as Reviewed
                        </button>
                        <button class="btn btn-danger" onclick="updateStatus('rejected')">
                            <i class="fas fa-times mr-1"></i> Reject Submission
                        </button>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Status: {{ ucfirst($tracker->submission_status) }}</strong><br>
                        This submission has been processed.
                    </div>
                @endif

                @if($tracker->edit_request_status === 'pending')
                    <div class="alert alert-warning">
                        <i class="fas fa-edit mr-1"></i>
                        <strong>Edit Request Pending</strong><br>
                        Staff has requested to edit this submission.
                    </div>

                    <div class="btn-group-vertical btn-block mb-3">
                        <form method="POST" action="{{ route('admin.weekly-trackers.approve-edit', $tracker) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check mr-1"></i> Approve Edit Request
                            </button>
                        </form>
                        <button class="btn btn-danger btn-block" onclick="showRejectEditForm()">
                            <i class="fas fa-times mr-1"></i> Reject Edit Request
                        </button>
                    </div>
                @endif

                <hr>

                <div class="btn-group-vertical btn-block">
                    <a href="{{ route('admin.staff.show', $tracker->staff) }}" class="btn btn-outline-primary">
                        <i class="fas fa-user mr-1"></i> View Staff Profile
                    </a>
                    <a href="{{ route('admin.weekly-trackers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Trackers
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Quick Info
                </h3>
            </div>
            <div class="card-body">
                <dl>
                    <dt>Created:</dt>
                    <dd>{{ $tracker->created_at->format('M d, Y \a\t h:i A') }}</dd>

                    <dt>Last Updated:</dt>
                    <dd>{{ $tracker->updated_at->format('M d, Y \a\t h:i A') }}</dd>

                    @if($tracker->submitted_at)
                        <dt>Time to Submit:</dt>
                        <dd>{{ $tracker->created_at->diffForHumans($tracker->submitted_at) }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Submission Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="statusForm" method="POST" action="{{ route('admin.weekly-trackers.update-status', $tracker) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="submission_status">New Status</label>
                        <select class="form-control" id="submission_status" name="submission_status" required>
                            <option value="reviewed">Mark as Reviewed</option>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admin_notes">Admin Notes</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                  placeholder="Add any notes or feedback for the staff member..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Edit Request Modal -->
<div class="modal fade" id="rejectEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Edit Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.weekly-trackers.reject-edit', $tracker) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Reason for Rejection</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required
                                  placeholder="Explain why the edit request is being rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Edit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .media {
            align-items: center;
        }
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }
        .card-outline {
            border-width: 2px;
        }
    </style>
@stop

@section('js')
    <script>
        function updateStatus(status) {
            $('#submission_status').val(status);
            $('#statusModal').modal('show');
        }

        function showRejectEditForm() {
            $('#rejectEditModal').modal('show');
        }
    </script>
@stop
