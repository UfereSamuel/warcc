@extends('layouts.staff')

@section('title', 'Activity Request Details')
@section('page-title', 'Activity Request Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.activity-requests.index') }}">Activity Requests</a></li>
    <li class="breadcrumb-item active">{{ $activityRequest->title }}</li>
@endsection

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
                                <strong>Reviewed by:</strong> {{ $activityRequest->reviewer->name ?? 'Administrator' }}<br>
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
                        Your request has been approved and published to the activity calendar!
                        <br>
                        <strong>Activity Title:</strong> {{ $activityRequest->approvedActivity->title }}
                        <br>
                        <a href="{{ route('staff.calendar.index') }}" class="btn btn-success btn-sm mt-2">
                            <i class="fas fa-calendar mr-1"></i>View in Calendar
                        </a>
                    </div>
                </div>
                @endif

                @if($activityRequest->status === 'pending')
                <div class="alert alert-info">
                    <i class="fas fa-clock mr-2"></i>
                    <strong>Pending Review:</strong> Your activity request is currently under review by an administrator.
                    You will be notified once a decision is made.
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Action Buttons -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($activityRequest->status === 'pending')
                        <a href="{{ route('staff.activity-requests.edit', $activityRequest) }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-edit mr-2"></i>Edit Request
                        </a>
                        <button type="button" class="btn btn-danger btn-block mb-2" data-toggle="modal" data-target="#deleteModal">
                            <i class="fas fa-trash mr-2"></i>Delete Request
                        </button>
                    @endif
                    <a href="{{ route('staff.activity-requests.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-2"></i>Back to My Requests
                    </a>
                    <a href="{{ route('staff.activity-requests.create') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus mr-2"></i>New Request
                    </a>
                </div>
            </div>
        </div>

        <!-- Request Summary -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>Request Summary
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Submitted:</strong></td>
                        <td>{{ $activityRequest->created_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $activityRequest->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td>{{ $activityRequest->updated_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $activityRequest->updated_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    @if($activityRequest->reviewed_at)
                    <tr>
                        <td><strong>Reviewed:</strong></td>
                        <td>{{ $activityRequest->reviewed_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $activityRequest->reviewed_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
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
                                You submitted your activity request "{{ $activityRequest->title }}".
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
                                Your request was {{ $activityRequest->status }} by an administrator.
                                @if($activityRequest->admin_notes)
                                    <br><strong>Admin Notes:</strong> {{ $activityRequest->admin_notes }}
                                @endif
                                @if($activityRequest->rejection_reason)
                                    <br><strong>Reason:</strong> {{ $activityRequest->rejection_reason }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                    <div>
                        <i class="fas fa-clock bg-yellow"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> Pending</span>
                            <h3 class="timeline-header">Awaiting Review</h3>
                            <div class="timeline-body">
                                Your request is currently under review by an administrator.
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

<!-- Delete Modal -->
@if($activityRequest->status === 'pending')
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('staff.activity-requests.destroy', $activityRequest) }}">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-trash mr-2"></i>Delete Activity Request
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the activity request "<strong>{{ $activityRequest->title }}</strong>"?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Warning:</strong> This action cannot be undone. Once deleted, you will need to create a new request if needed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i>Delete Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
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
    .bg-yellow { background-color: #ffc107 !important; }
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
