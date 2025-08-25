@extends('adminlte::page')

@section('title', 'Activity Requests Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Activity Requests Management</h1>
            <p class="text-muted">Review and manage staff activity proposals</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Activity Requests</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-clipboard-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Requests</span>
                <span class="info-box-number">{{ $stats['total'] }}</span>
                <span class="progress-description">All submitted requests</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending Review</span>
                <span class="info-box-number">{{ $stats['pending'] }}</span>
                <span class="progress-description">Awaiting your decision</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Approved</span>
                <span class="info-box-number">{{ $stats['approved'] }}</span>
                <span class="progress-description">Successfully approved</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box bg-gradient-danger">
            <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Rejected</span>
                <span class="info-box-number">{{ $stats['rejected'] }}</span>
                <span class="progress-description">Declined requests</span>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-search mr-2"></i>
            Search & Filter Activity Requests
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.activity-requests.index') }}" class="form-horizontal">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search"><strong>Search Requests</strong></label>
                        <input type="text" class="form-control form-control-lg" id="search" name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by title, description, staff name...">
                        <small class="form-text text-muted">Enter keywords to find specific requests</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status"><strong>Request Status</strong></label>
                        <select class="form-control form-control-lg" id="status" name="status">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Filter by current status</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="type"><strong>Activity Type</strong></label>
                        <select class="form-control form-control-lg" id="type" name="type">
                            <option value="">All Types</option>
                            @foreach($types as $typeOption)
                                <option value="{{ $typeOption }}" {{ request('type') === $typeOption ? 'selected' : '' }}>
                                    {{ ucfirst($typeOption) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Filter by activity type</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="department"><strong>Department</strong></label>
                        <select class="form-control form-control-lg" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}" {{ request('department') === $department ? 'selected' : '' }}>
                                    {{ $department }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Filter by staff department</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-search mr-2"></i> Search Requests
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Quick Action Buttons -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.activity-requests.index') }}" 
                           class="btn btn-outline-info btn-lg btn-block mb-2">
                            <i class="fas fa-list mr-2"></i>
                            <strong>View All Requests</strong>
                            <br><small>({{ $stats['total'] }} total)</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.activity-requests.index', ['status' => 'pending']) }}" 
                           class="btn btn-warning btn-lg btn-block mb-2">
                            <i class="fas fa-clock mr-2"></i>
                            <strong>Review Pending</strong>
                            <br><small>({{ $stats['pending'] }} need review)</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.activity-requests.index', ['status' => 'approved']) }}" 
                           class="btn btn-success btn-lg btn-block mb-2">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>View Approved</strong>
                            <br><small>({{ $stats['approved'] }} approved)</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.activity-requests.index', ['status' => 'rejected']) }}" 
                           class="btn btn-danger btn-lg btn-block mb-2">
                            <i class="fas fa-times-circle mr-2"></i>
                            <strong>View Rejected</strong>
                            <br><small>({{ $stats['rejected'] }} rejected)</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Requests List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clipboard-list mr-2"></i>
            Activity Requests List
        </h3>
        <div class="card-tools">
            @if($stats['pending'] > 0)
                <button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#batchActionsModal">
                    <i class="fas fa-tasks mr-2"></i> Batch Process ({{ $stats['pending'] }})
                </button>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($requests->count() > 0)
            <form id="batchForm" method="POST" action="{{ route('admin.activity-requests.batch-process') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                @if($requests->where('status', 'pending')->count() > 0)
                                    <th width="50" class="text-center">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="selectAll">
                                            <label for="selectAll" class="custom-control-label text-white">
                                                <strong>Select All</strong>
                                            </label>
                                        </div>
                                    </th>
                                @endif
                                <th><strong>Request Information</strong></th>
                                <th><strong>Staff Member</strong></th>
                                <th><strong>Activity Details</strong></th>
                                <th><strong>Current Status</strong></th>
                                <th width="200" class="text-center"><strong>Admin Actions</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr class="{{ $request->status === 'pending' ? 'table-warning' : '' }}">
                                    @if($requests->where('status', 'pending')->count() > 0)
                                        <td class="text-center">
                                            @if($request->status === 'pending')
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input request-checkbox" type="checkbox"
                                                           id="request_{{ $request->id }}" name="request_ids[]" value="{{ $request->id }}">
                                                    <label for="request_{{ $request->id }}" class="custom-control-label"></label>
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        <div class="request-info">
                                            <h5 class="text-primary mb-1">
                                                <i class="fas fa-clipboard mr-2"></i>{{ $request->title }}
                                            </h5>
                                            @if($request->description)
                                                <p class="text-muted mb-1">{{ Str::limit($request->description, 100) }}</p>
                                            @endif
                                            @if($request->location)
                                                <p class="text-info mb-0">
                                                    <i class="fas fa-map-marker-alt mr-1"></i> 
                                                    <strong>Location:</strong> {{ $request->location }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="staff-info">
                                            <h6 class="text-dark mb-1">
                                                <i class="fas fa-user mr-2"></i>{{ $request->requester->name }}
                                            </h6>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-envelope mr-1"></i>{{ $request->requester->email }}
                                            </p>
                                            <p class="text-info mb-0">
                                                <i class="fas fa-building mr-1"></i>{{ $request->requester->department }}
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="activity-details">
                                            <span class="badge badge-{{ $request->type_color }} badge-lg mb-2">
                                                <i class="fas fa-tag mr-1"></i>{{ $request->type_label }}
                                            </span>
                                            <p class="mb-1">
                                                <i class="fas fa-calendar mr-1"></i>
                                                <strong>Start:</strong> {{ $request->start_date->format('M d, Y') }}
                                            </p>
                                            @if($request->start_date->ne($request->end_date))
                                                <p class="mb-1">
                                                    <i class="fas fa-calendar-check mr-1"></i>
                                                    <strong>End:</strong> {{ $request->end_date->format('M d, Y') }}
                                                </p>
                                            @endif
                                            <p class="mb-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                <strong>Duration:</strong> {{ $request->duration_in_days }} day{{ $request->duration_in_days > 1 ? 's' : '' }}
                                            </p>
                                            @if($request->expected_participants)
                                                <p class="mb-0">
                                                    <i class="fas fa-users mr-1"></i>
                                                    <strong>Participants:</strong> {{ $request->expected_participants }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="status-info">
                                            <span class="badge badge-{{ $request->status_color }} badge-lg mb-2">
                                                <i class="fas fa-info-circle mr-1"></i>{{ $request->status_label }}
                                            </span>
                                            <p class="mb-1">
                                                <i class="fas fa-calendar-plus mr-1"></i>
                                                <strong>Submitted:</strong> {{ $request->created_at->format('M d, Y') }}
                                            </p>
                                            <p class="mb-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                <strong>Time:</strong> {{ $request->created_at->diffForHumans() }}
                                            </p>
                                            @if($request->reviewed_at)
                                                <p class="text-success mb-0">
                                                    <i class="fas fa-user-check mr-1"></i>
                                                    <strong>Reviewed:</strong> {{ $request->reviewed_at->diffForHumans() }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <!-- View Details Button -->
                                            <a href="{{ route('admin.activity-requests.show', $request) }}" 
                                               class="btn btn-info btn-lg btn-block mb-2">
                                                <i class="fas fa-eye mr-2"></i> View Details
                                            </a>

                                            @if($request->status === 'pending')
                                                <!-- Approve Button -->
                                                <button type="button" class="btn btn-success btn-lg btn-block mb-2"
                                                        data-toggle="modal" data-target="#approveModal"
                                                        data-request-id="{{ $request->id }}"
                                                        data-request-title="{{ $request->title }}">
                                                    <i class="fas fa-check mr-2"></i> Approve Request
                                                </button>
                                                
                                                <!-- Reject Button -->
                                                <button type="button" class="btn btn-danger btn-lg btn-block"
                                                        data-toggle="modal" data-target="#rejectModal"
                                                        data-request-id="{{ $request->id }}"
                                                        data-request-title="{{ $request->title }}">
                                                    <i class="fas fa-times mr-2"></i> Reject Request
                                                </button>
                                            @else
                                                <div class="alert alert-{{ $request->status === 'approved' ? 'success' : 'danger' }} mb-0">
                                                    <i class="fas fa-{{ $request->status === 'approved' ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                                    <strong>{{ ucfirst($request->status) }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-5x text-muted mb-4"></i>
                <h3 class="text-muted">No Activity Requests Found</h3>
                @if(request('status') || request('type') || request('search'))
                    <p class="text-muted mb-3">No activity requests match your current search criteria.</p>
                    <a href="{{ route('admin.activity-requests.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-list mr-2"></i> View All Requests
                    </a>
                @else
                    <p class="text-muted mb-3">No activity requests have been submitted yet.</p>
                @endif
            </div>
        @endif
    </div>
    @if($requests->hasPages())
        <div class="card-footer">
            {{ $requests->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Pending Requests Alert -->
@if($stats['pending'] > 0)
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning alert-dismissible">
            <h5 class="alert-heading">
                <i class="fas fa-exclamation-triangle mr-2"></i> Attention Required!
            </h5>
            <p class="mb-2">
                You have <strong>{{ $stats['pending'] }}</strong> pending activity request{{ $stats['pending'] > 1 ? 's' : '' }} 
                that need{{ $stats['pending'] == 1 ? 's' : '' }} your immediate review and decision.
            </p>
            <a href="{{ route('admin.activity-requests.index', ['status' => 'pending']) }}" class="btn btn-warning btn-lg">
                <i class="fas fa-clock mr-2"></i> Review Pending Requests Now
            </a>
        </div>
    </div>
</div>
@endif

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title" id="approveModalLabel">
                        <i class="fas fa-check-circle mr-2"></i>Approve Activity Request
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle mr-2"></i>
                        Are you sure you want to approve the activity request "<strong id="approveRequestTitle"></strong>"?
                    </div>

                    <div class="form-group">
                        <label for="admin_notes"><strong>Admin Notes (Optional)</strong></label>
                        <textarea class="form-control form-control-lg" id="admin_notes" name="admin_notes" rows="4"
                                  placeholder="Add any notes or comments about this approval..."></textarea>
                        <small class="form-text text-muted">These notes will be visible to the staff member</small>
                    </div>

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="create_activity" name="create_activity" checked>
                        <label class="custom-control-label" for="create_activity">
                            <strong>Automatically add this activity to the calendar</strong>
                        </label>
                        <small class="form-text text-muted">This will publish the approved activity to the public calendar</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check mr-2"></i>Approve Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h4 class="modal-title" id="rejectModalLabel">
                        <i class="fas fa-times-circle mr-2"></i>Reject Activity Request
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Are you sure you want to reject the activity request "<strong id="rejectRequestTitle"></strong>"?
                    </div>

                    <div class="form-group">
                        <label for="rejection_reason"><strong>Rejection Reason</strong> <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-lg" id="rejection_reason" name="rejection_reason" rows="4"
                                  placeholder="Please provide a clear and constructive reason for rejection..." required></textarea>
                        <small class="form-text text-muted">This reason will be shared with the staff member</small>
                    </div>

                    <div class="form-group">
                        <label for="admin_notes_reject"><strong>Additional Admin Notes (Optional)</strong></label>
                        <textarea class="form-control form-control-lg" id="admin_notes_reject" name="admin_notes" rows="3"
                                  placeholder="Add any additional internal notes..."></textarea>
                        <small class="form-text text-muted">These notes are for internal use only</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-times mr-2"></i>Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Batch Actions Modal -->
<div class="modal fade" id="batchActionsModal" tabindex="-1" role="dialog" aria-labelledby="batchActionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="batchActionsForm" method="POST" action="{{ route('admin.activity-requests.batch-process') }}">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h4 class="modal-title" id="batchActionsModalLabel">
                        <i class="fas fa-tasks mr-2"></i>Batch Process Multiple Requests
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Batch Processing:</strong> You can approve or reject multiple requests at once.
                    </div>

                    <div class="form-group">
                        <label for="batch_action"><strong>Select Action</strong></label>
                        <select class="form-control form-control-lg" id="batch_action" name="action" required>
                            <option value="">Choose an action...</option>
                            <option value="approve">✅ Approve Selected Requests</option>
                            <option value="reject">❌ Reject Selected Requests</option>
                        </select>
                        <small class="form-text text-muted">This action will apply to all selected requests</small>
                    </div>

                    <div class="form-group">
                        <label for="batch_admin_notes"><strong>Admin Notes (Optional)</strong></label>
                        <textarea class="form-control form-control-lg" id="batch_admin_notes" name="batch_admin_notes" rows="3"
                                  placeholder="Add notes that will apply to all selected requests..."></textarea>
                        <small class="form-text text-muted">These notes will be added to each selected request</small>
                    </div>

                    <div class="form-group" id="batch_rejection_reason_group" style="display: none;">
                        <label for="batch_rejection_reason"><strong>Rejection Reason</strong> <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-lg" id="batch_rejection_reason" name="batch_rejection_reason" rows="3"
                                  placeholder="Provide a reason for rejecting these requests..."></textarea>
                        <small class="form-text text-muted">This reason will be shared with all affected staff members</small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Selected Requests:</strong> <span id="selectedCount">0</span> request(s) selected
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg" id="batchSubmitBtn" disabled>
                        <i class="fas fa-tasks mr-2"></i>Process Selected Requests
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.info-box {
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.card {
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.action-buttons .btn {
    font-size: 14px;
    font-weight: 600;
}
.request-info h5, .staff-info h6, .activity-details p, .status-info p {
    margin-bottom: 0.5rem;
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
    transform: scale(1.01);
    transition: all 0.2s ease;
}
.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}
.form-control-lg {
    font-size: 1rem;
    padding: 0.75rem 1rem;
}
.btn-lg {
    font-size: 1rem;
    padding: 0.75rem 1.5rem;
}
.alert {
    border-radius: 10px;
}
.modal-lg {
    max-width: 800px;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Handle approve modal
    $('#approveModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var requestId = button.data('request-id');
        var requestTitle = button.data('request-title');

        var modal = $(this);
        modal.find('#approveRequestTitle').text(requestTitle);
        modal.find('#approveForm').attr('action', '/admin/activity-requests/' + requestId + '/approve');
    });

    // Handle reject modal
    $('#rejectModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var requestId = button.data('request-id');
        var requestTitle = button.data('request-title');

        var modal = $(this);
        modal.find('#rejectRequestTitle').text(requestTitle);
        modal.find('#rejectForm').attr('action', '/admin/activity-requests/' + requestId + '/reject');
    });

    // Handle select all checkbox
    $('#selectAll').change(function() {
        $('.request-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Handle individual checkboxes
    $('.request-checkbox').change(function() {
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        var count = $('.request-checkbox:checked').length;
        $('#selectedCount').text(count);
        $('#batchSubmitBtn').prop('disabled', count === 0);
    }

    // Handle batch action change
    $('#batch_action').change(function() {
        var action = $(this).val();
        if (action === 'reject') {
            $('#batch_rejection_reason_group').show();
            $('#batch_rejection_reason').prop('required', true);
        } else {
            $('#batch_rejection_reason_group').hide();
            $('#batch_rejection_reason').prop('required', false);
        }
    });

    // Handle batch actions form submission
    $('#batchActionsForm').submit(function(e) {
        var selectedIds = [];
        $('.request-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            e.preventDefault();
            alert('Please select at least one request to process.');
            return false;
        }

        // Add selected IDs to form
        selectedIds.forEach(function(id) {
            $('<input>').attr({
                type: 'hidden',
                name: 'request_ids[]',
                value: id
            }).appendTo('#batchActionsForm');
        });
    });
});
</script>
@stop
