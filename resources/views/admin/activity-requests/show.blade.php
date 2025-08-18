@extends('adminlte::page')

@section('title', 'Activity Request Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Activity Request Details</h1>
            <p class="text-muted">Review and manage activity request</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.activity-requests.index') }}">Activity Requests</a></li>
                <li class="breadcrumb-item active">Request Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Main Request Details -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    {{ $activityRequest->title }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-{{ $activityRequest->status_color }} badge-lg">
                        {{ $activityRequest->status_label }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Request Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5><i class="fas fa-info-circle text-info mr-2"></i>Basic Information</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="30%"><strong>Title:</strong></td>
                                <td>{{ $activityRequest->title }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td><span class="badge badge-{{ $activityRequest->type_color }}">{{ $activityRequest->type_label }}</span></td>
                            </tr>
                            @if($activityRequest->location)
                            <tr>
                                <td><strong>Location:</strong></td>
                                <td><i class="fas fa-map-marker-alt text-danger mr-1"></i>{{ $activityRequest->location }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td><span class="badge badge-{{ $activityRequest->status_color }}">{{ $activityRequest->status_label }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-calendar-alt text-success mr-2"></i>Schedule & Logistics</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="30%"><strong>Start Date:</strong></td>
                                <td>{{ $activityRequest->start_date->format('F d, Y (l)') }}</td>
                            </tr>
                            <tr>
                                <td><strong>End Date:</strong></td>
                                <td>{{ $activityRequest->end_date->format('F d, Y (l)') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Duration:</strong></td>
                                <td>{{ $activityRequest->duration_in_days }} day{{ $activityRequest->duration_in_days > 1 ? 's' : '' }}</td>
                            </tr>
                            @if($activityRequest->expected_participants)
                            <tr>
                                <td><strong>Participants:</strong></td>
                                <td><i class="fas fa-users text-primary mr-1"></i>{{ $activityRequest->expected_participants }} expected</td>
                            </tr>
                            @endif
                            @if($activityRequest->estimated_budget)
                            <tr>
                                <td><strong>Budget:</strong></td>
                                <td><strong class="text-success">GHS {{ number_format($activityRequest->estimated_budget, 2) }}</strong></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Description -->
                @if($activityRequest->description)
                <div class="mb-4">
                    <h5><i class="fas fa-file-alt text-secondary mr-2"></i>Description</h5>
                    <div class="border rounded p-3 bg-light">
                        {{ $activityRequest->description }}
                    </div>
                </div>
                @endif

                <!-- Justification -->
                <div class="mb-4">
                    <h5><i class="fas fa-balance-scale text-warning mr-2"></i>Justification</h5>
                    <div class="border rounded p-3 bg-light">
                        {{ $activityRequest->justification }}
                    </div>
                </div>

                <!-- Review Information -->
                @if($activityRequest->reviewed_at)
                <div class="mb-4">
                    <h5><i class="fas fa-user-check text-info mr-2"></i>Review Information</h5>
                    <div class="border rounded p-3 {{ $activityRequest->status === 'approved' ? 'bg-success-light' : 'bg-danger-light' }}">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Reviewed by:</strong> {{ $activityRequest->reviewer->name ?? 'System' }}<br>
                                <strong>Reviewed on:</strong> {{ $activityRequest->reviewed_at->format('F d, Y \a\t g:i A') }}<br>
                                <strong>Time since review:</strong> {{ $activityRequest->reviewed_at->diffForHumans() }}
                            </div>
                            <div class="col-md-6">
                                @if($activityRequest->admin_notes)
                                    <strong>Admin Notes:</strong><br>
                                    <em>{{ $activityRequest->admin_notes }}</em>
                                @endif
                                @if($activityRequest->rejection_reason)
                                    <strong>Rejection Reason:</strong><br>
                                    <div class="text-danger"><em>{{ $activityRequest->rejection_reason }}</em></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Approved Activity Link -->
                @if($activityRequest->status === 'approved' && $activityRequest->approvedActivity)
                <div class="mb-4">
                    <h5><i class="fas fa-calendar-check text-success mr-2"></i>Published Activity</h5>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        This request has been approved and published to the activity calendar.
                        <br>
                        <strong>Activity Title:</strong> {{ $activityRequest->approvedActivity->title }}
                        <br>
                        <a href="{{ route('admin.calendar.index') }}" class="btn btn-success btn-sm mt-2">
                            <i class="fas fa-calendar mr-1"></i>View in Calendar
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Staff Information -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>Requester Information
                </h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="{{ $activityRequest->requester->profile_picture_url ?? asset('images/default-avatar.png') }}"
                         alt="Profile Picture" class="img-circle" width="80" height="80">
                </div>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $activityRequest->requester->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $activityRequest->requester->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Department:</strong></td>
                        <td>{{ $activityRequest->requester->department }}</td>
                    </tr>
                    <tr>
                        <td><strong>Position:</strong></td>
                        <td>{{ $activityRequest->requester->position }}</td>
                    </tr>
                    <tr>
                        <td><strong>Submitted:</strong></td>
                        <td>{{ $activityRequest->created_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $activityRequest->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        @if($activityRequest->status === 'pending')
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>Admin Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-block mb-2"
                            data-toggle="modal" data-target="#approveModal">
                        <i class="fas fa-check mr-2"></i>Approve Request
                    </button>
                    <button type="button" class="btn btn-danger btn-block mb-2"
                            data-toggle="modal" data-target="#rejectModal">
                        <i class="fas fa-times mr-2"></i>Reject Request
                    </button>
                    <a href="{{ route('admin.activity-requests.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>Request Status
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-{{ $activityRequest->status === 'approved' ? 'success' : 'danger' }}">
                    <i class="fas fa-{{ $activityRequest->status === 'approved' ? 'check-circle' : 'times-circle' }} mr-2"></i>
                    This request has been <strong>{{ $activityRequest->status_label }}</strong>
                    @if($activityRequest->reviewed_at)
                        on {{ $activityRequest->reviewed_at->format('M d, Y') }}
                    @endif
                </div>
                <a href="{{ route('admin.activity-requests.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
        @endif

        <!-- Timeline -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>Request Timeline
                </h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="time-label">
                        <span class="bg-primary">{{ $activityRequest->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <i class="fas fa-plus bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $activityRequest->created_at->format('H:i') }}</span>
                            <h3 class="timeline-header">Request Submitted</h3>
                            <div class="timeline-body">
                                Activity request "{{ $activityRequest->title }}" was submitted by {{ $activityRequest->requester->name }}.
                            </div>
                        </div>
                    </div>

                    @if($activityRequest->reviewed_at)
                    <div class="time-label">
                        <span class="bg-{{ $activityRequest->status === 'approved' ? 'success' : 'danger' }}">
                            {{ $activityRequest->reviewed_at->format('M d, Y') }}
                        </span>
                    </div>
                    <div>
                        <i class="fas fa-{{ $activityRequest->status === 'approved' ? 'check' : 'times' }} bg-{{ $activityRequest->status === 'approved' ? 'green' : 'red' }}"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $activityRequest->reviewed_at->format('H:i') }}</span>
                            <h3 class="timeline-header">Request {{ ucfirst($activityRequest->status) }}</h3>
                            <div class="timeline-body">
                                Request was {{ $activityRequest->status }} by {{ $activityRequest->reviewer->name ?? 'System' }}.
                                @if($activityRequest->admin_notes)
                                    <br><strong>Notes:</strong> {{ $activityRequest->admin_notes }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <i class="fas fa-clock bg-gray"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.activity-requests.approve', $activityRequest) }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="approveModalLabel">
                        <i class="fas fa-check-circle mr-2"></i>Approve Activity Request
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve the activity request "<strong>{{ $activityRequest->title }}</strong>"?</p>

                    <div class="form-group">
                        <label for="admin_notes">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                  placeholder="Add any notes or comments about this approval..."></textarea>
                    </div>

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="create_activity" name="create_activity" checked>
                        <label class="custom-control-label" for="create_activity">
                            Automatically add this activity to the calendar
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>Approve Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.activity-requests.reject', $activityRequest) }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i class="fas fa-times-circle mr-2"></i>Reject Activity Request
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject the activity request "<strong>{{ $activityRequest->title }}</strong>"?</p>

                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"
                                  placeholder="Please provide a clear reason for rejection..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="admin_notes_reject">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="admin_notes_reject" name="admin_notes" rows="2"
                                  placeholder="Add any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i>Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1) !important;
        border-left: 4px solid #28a745;
    }

    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1) !important;
        border-left: 4px solid #dc3545;
    }

    .timeline {
        position: relative;
        margin: 0;
        padding: 0;
    }

    .timeline:before {
        content: '';
        position: absolute;
        left: 25px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #adb5bd;
    }

    .timeline > div {
        position: relative;
        margin: 0;
        padding: 0;
    }

    .timeline .time-label {
        margin: 15px 0 5px;
    }

    .timeline .time-label > span {
        display: inline-block;
        background-color: #0066cc;
        color: #fff;
        padding: 3px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .timeline > div > .fas {
        position: absolute;
        left: 18px;
        width: 15px;
        height: 15px;
        line-height: 15px;
        text-align: center;
        border-radius: 50%;
        color: #fff;
        font-size: 10px;
        margin-top: 3px;
    }

    .timeline .timeline-item {
        margin-left: 45px;
        margin-bottom: 15px;
        background: #fff;
        border-radius: 3px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        padding: 10px;
    }

    .timeline .timeline-header {
        margin: 0;
        color: #555;
        border-bottom: 1px solid #f4f4f4;
        padding-bottom: 5px;
        font-size: 14px;
        font-weight: 600;
    }

    .timeline .time {
        color: #999;
        float: right;
        font-size: 12px;
    }

    .timeline .timeline-body {
        padding-top: 10px;
        color: #666;
        font-size: 13px;
    }

    .bg-blue { background-color: #007bff !important; }
    .bg-green { background-color: #28a745 !important; }
    .bg-red { background-color: #dc3545 !important; }
    .bg-gray { background-color: #6c757d !important; }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Add any JavaScript interactions here if needed
});
</script>
@stop
