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
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Requests</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['pending'] }}</h3>
                <p>Pending Review</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            @if($stats['pending'] > 0)
                <div class="small-box-footer">
                    <span class="badge badge-light">Needs Attention</span>
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['approved'] }}</h3>
                <p>Approved</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['rejected'] }}</h3>
                <p>Rejected</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>
                    Activity Requests
                </h3>
                <div class="card-tools">
                    <!-- Status Filter Buttons -->
                    <div class="btn-group mr-3">
                        <a href="{{ route('admin.activity-requests.index') }}"
                           class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                            All ({{ $stats['total'] }})
                        </a>
                        <a href="{{ route('admin.activity-requests.index', ['status' => 'pending']) }}"
                           class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                            Pending ({{ $stats['pending'] }})
                        </a>
                        <a href="{{ route('admin.activity-requests.index', ['status' => 'approved']) }}"
                           class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                            Approved ({{ $stats['approved'] }})
                        </a>
                        <a href="{{ route('admin.activity-requests.index', ['status' => 'rejected']) }}"
                           class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                            Rejected ({{ $stats['rejected'] }})
                        </a>
                    </div>

                    <!-- Type Filter -->
                    <div class="btn-group mr-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-filter mr-1"></i>
                            Type: {{ request('type') ? ucfirst(request('type')) : 'All' }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.activity-requests.index', request()->except('type')) }}">All Types</a>
                            @foreach($types as $typeOption)
                                <a class="dropdown-item" href="{{ route('admin.activity-requests.index', array_merge(request()->all(), ['type' => $typeOption])) }}">
                                    {{ ucfirst($typeOption) }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Batch Actions -->
                    @if($requests->where('status', 'pending')->count() > 0)
                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#batchActionsModal">
                            <i class="fas fa-tasks mr-1"></i> Batch Actions
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($requests->count() > 0)
                    <form id="batchForm" method="POST" action="{{ route('admin.activity-requests.batch-process') }}">
                        @csrf
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    @if($requests->where('status', 'pending')->count() > 0)
                                        <th width="40">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="selectAll">
                                                <label for="selectAll" class="custom-control-label"></label>
                                            </div>
                                        </th>
                                    @endif
                                    <th>Request Details</th>
                                    <th>Staff Member</th>
                                    <th>Type & Duration</th>
                                    <th>Status & Review</th>
                                    <th>Budget</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr class="{{ $request->status === 'pending' ? 'table-warning' : '' }}">
                                        @if($requests->where('status', 'pending')->count() > 0)
                                            <td>
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
                                            <div>
                                                <strong class="text-dark">{{ $request->title }}</strong>
                                                @if($request->description)
                                                    <br><small class="text-muted">{{ Str::limit($request->description, 80) }}</small>
                                                @endif
                                                @if($request->location)
                                                    <br><small class="text-info">
                                                        <i class="fas fa-map-marker-alt"></i> {{ $request->location }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $request->requester->name }}</strong>
                                                <br><small class="text-muted">{{ $request->requester->email }}</small>
                                                <br><small class="text-muted">{{ $request->requester->department }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $request->type_color }} mb-1">{{ $request->type_label }}</span>
                                            <br><strong>{{ $request->start_date->format('M d, Y') }}</strong>
                                            @if($request->start_date->ne($request->end_date))
                                                <br><small class="text-muted">to {{ $request->end_date->format('M d, Y') }}</small>
                                            @endif
                                            <br><small class="text-muted">{{ $request->duration_in_days }} day{{ $request->duration_in_days > 1 ? 's' : '' }}</small>
                                            @if($request->expected_participants)
                                                <br><small class="text-info">
                                                    <i class="fas fa-users"></i> {{ $request->expected_participants }} participants
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $request->status_color }} mb-1">{{ $request->status_label }}</span>
                                            <br><small class="text-muted">{{ $request->created_at->format('M d, Y') }}</small>
                                            <br><small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>

                                            @if($request->reviewed_at)
                                                <br><small class="text-success">
                                                    <i class="fas fa-user-check"></i>
                                                    Reviewed {{ $request->reviewed_at->diffForHumans() }}
                                                </small>
                                                @if($request->reviewer)
                                                    <br><small class="text-muted">by {{ $request->reviewer->name }}</small>
                                                @endif
                                            @endif

                                            @if($request->status === 'approved' && $request->approvedActivity)
                                                <br><small class="text-success">
                                                    <i class="fas fa-calendar-check"></i> Published to Calendar
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->estimated_budget)
                                                <strong class="text-success">
                                                    GHS {{ number_format($request->estimated_budget, 2) }}
                                                </strong>
                                            @else
                                                <small class="text-muted">Not specified</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group-vertical btn-group-sm" role="group">
                                                <a href="{{ route('admin.activity-requests.show', $request) }}"
                                                   class="btn btn-info mb-1" title="View Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>

                                                @if($request->status === 'pending')
                                                    <button type="button" class="btn btn-success mb-1"
                                                            data-toggle="modal" data-target="#approveModal"
                                                            data-request-id="{{ $request->id }}"
                                                            data-request-title="{{ $request->title }}"
                                                            title="Approve Request">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger"
                                                            data-toggle="modal" data-target="#rejectModal"
                                                            data-request-id="{{ $request->id }}"
                                                            data-request-title="{{ $request->title }}"
                                                            title="Reject Request">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                @else
                                                    <small class="text-muted">Reviewed</small>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Activity Requests</h4>
                        @if(request('status') || request('type'))
                            <p class="text-muted">No activity requests found with the current filters.</p>
                            <a href="{{ route('admin.activity-requests.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list mr-1"></i> View All Requests
                            </a>
                        @else
                            <p class="text-muted">No activity requests have been submitted yet.</p>
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
    </div>
</div>

<!-- Quick Stats Summary -->
@if($stats['pending'] > 0)
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Attention Required!</h5>
            You have <strong>{{ $stats['pending'] }}</strong> pending activity request{{ $stats['pending'] > 1 ? 's' : '' }} that need{{ $stats['pending'] == 1 ? 's' : '' }} your review.
            <a href="{{ route('admin.activity-requests.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm ml-2">
                Review Pending Requests
            </a>
        </div>
    </div>
</div>
@endif

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="approveForm" method="POST">
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
                    <p>Are you sure you want to approve the activity request "<strong id="approveRequestTitle"></strong>"?</p>

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
            <form id="rejectForm" method="POST">
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
                    <p>Are you sure you want to reject the activity request "<strong id="rejectRequestTitle"></strong>"?</p>

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

<!-- Batch Actions Modal -->
<div class="modal fade" id="batchActionsModal" tabindex="-1" role="dialog" aria-labelledby="batchActionsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="batchActionsForm" method="POST" action="{{ route('admin.activity-requests.batch-process') }}">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="batchActionsModalLabel">
                        <i class="fas fa-tasks mr-2"></i>Batch Process Requests
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Select an action to perform on the selected requests:</p>

                    <div class="form-group">
                        <label for="batch_action">Action</label>
                        <select class="form-control" id="batch_action" name="action" required>
                            <option value="">Choose an action...</option>
                            <option value="approve">Approve Selected Requests</option>
                            <option value="reject">Reject Selected Requests</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="batch_admin_notes">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="batch_admin_notes" name="batch_admin_notes" rows="3"
                                  placeholder="Add notes that will apply to all selected requests..."></textarea>
                    </div>

                    <div class="form-group" id="batch_rejection_reason_group" style="display: none;">
                        <label for="batch_rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="batch_rejection_reason" name="batch_rejection_reason" rows="3"
                                  placeholder="Provide a reason for rejecting these requests..."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Selected requests: <span id="selectedCount">0</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="batchSubmitBtn" disabled>
                        <i class="fas fa-tasks mr-1"></i>Process Requests
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    .btn-group-vertical .btn {
        border-radius: 0.25rem !important;
        margin-bottom: 2px;
    }

    .small-box .small-box-footer {
        position: relative;
        text-align: center;
        padding: 3px 0;
        color: #fff;
        color: rgba(255,255,255,0.8);
        display: block;
        z-index: 10;
        background: rgba(0,0,0,0.1);
        text-decoration: none;
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
