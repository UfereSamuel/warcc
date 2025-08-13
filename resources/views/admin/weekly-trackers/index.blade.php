@extends('adminlte::page')

@section('title', 'Weekly Tracker Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Weekly Tracker Management</h1>
            <p class="text-muted">Review and approve staff weekly status submissions</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Weekly Trackers</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Filter Controls -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Weekly Trackers
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.weekly-trackers.index') }}" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="week">Week Starting</label>
                                <input type="date" class="form-control" id="week" name="week"
                                       value="{{ $week }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <select class="form-control" id="department" name="department">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept }}" {{ $department == $dept ? 'selected' : '' }}>
                                            {{ $dept }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Status</option>
                                    @foreach($statuses as $stat)
                                        <option value="{{ $stat }}" {{ $status == $stat ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $stat)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-search mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.weekly-trackers.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-times mr-1"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Week Overview -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h5><i class="fas fa-calendar-week mr-2"></i>Week Overview: {{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}</h5>
            <p class="mb-0">Review staff status submissions for this week. You can approve, review, or request changes to each submission.</p>
        </div>
    </div>
</div>

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-lg-2">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">At Duty Station</span>
                <span class="info-box-number">{{ $stats['at_duty_station'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-2">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-plane"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">On Mission</span>
                <span class="info-box-number">{{ $stats['on_mission'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-2">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">On Leave</span>
                <span class="info-box-number">{{ $stats['on_leave'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-2">
        <div class="info-box bg-gradient-secondary">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending Review</span>
                <span class="info-box-number">{{ $stats['pending_review'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-2">
        <div class="info-box bg-gradient-danger">
            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Not Submitted</span>
                <span class="info-box-number">{{ $stats['not_submitted'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Tracker Submissions -->
<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>
                    Weekly Tracker Submissions
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $trackers->count() }} Submissions</span>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($trackers->count() > 0)
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Staff Member</th>
                                <th>Department</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Submission Status</th>
                                <th class="text-center">Details</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trackers as $tracker)
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <img src="{{ $tracker->staff->profile_picture_url }}"
                                                 alt="{{ $tracker->staff->full_name }}"
                                                 class="img-circle mr-3"
                                                 style="width: 35px; height: 35px;">
                                            <div class="media-body">
                                                <h6 class="mb-0">{{ $tracker->staff->full_name }}</h6>
                                                <small class="text-muted">{{ $tracker->staff->staff_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $tracker->staff->department }}</span>
                                    </td>
                                    <td class="text-center">
                                        @switch($tracker->status)
                                            @case('at_duty_station')
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-building mr-1"></i>At Duty Station
                                                </span>
                                                @break
                                            @case('on_mission')
                                                <span class="badge badge-info">
                                                    <i class="fas fa-plane mr-1"></i>On Mission
                                                </span>
                                                @break
                                            @case('on_leave')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-calendar-times mr-1"></i>On Leave
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        @switch($tracker->submission_status)
                                            @case('draft')
                                                <span class="badge badge-secondary">Draft</span>
                                                @break
                                            @case('submitted')
                                                <span class="badge badge-warning">Submitted</span>
                                                @break
                                            @case('reviewed')
                                                <span class="badge badge-info">Reviewed</span>
                                                @break
                                            @case('approved')
                                                <span class="badge badge-success">Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        @if($tracker->status === 'on_mission')
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                {{ $tracker->mission_title ?? 'Mission details' }}
                                            </small>
                                        @elseif($tracker->status === 'on_leave')
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                {{ $tracker->leaveType->name ?? 'Leave details' }}
                                            </small>
                                        @else
                                            <span class="text-muted">Regular duty</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.weekly-trackers.show', $tracker) }}"
                                               class="btn btn-sm btn-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($tracker->submission_status === 'submitted')
                                                <button class="btn btn-sm btn-success"
                                                        onclick="updateStatus({{ $tracker->id }}, 'approved')"
                                                        title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info"
                                                        onclick="updateStatus({{ $tracker->id }}, 'reviewed')"
                                                        title="Mark as Reviewed">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endif
                                            @if($tracker->edit_request_status === 'pending')
                                                <button class="btn btn-sm btn-warning"
                                                        onclick="handleEditRequest({{ $tracker->id }})"
                                                        title="Edit Request Pending">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-week fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Submissions</h4>
                        <p class="text-muted">No weekly tracker submissions found for the selected criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <!-- Staff Not Submitted -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Missing Submissions
                </h3>
                <div class="card-tools">
                    <span class="badge badge-danger">{{ $missingStaff->count() }} Staff</span>
                </div>
            </div>
            <div class="card-body">
                @if($missingStaff->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($missingStaff as $staff)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="media align-items-center">
                                    <img src="{{ $staff->profile_picture_url }}"
                                         alt="{{ $staff->full_name }}"
                                         class="img-circle mr-3"
                                         style="width: 30px; height: 30px;">
                                    <div class="media-body">
                                        <h6 class="mb-0 text-truncate">{{ $staff->full_name }}</h6>
                                        <small class="text-muted">{{ $staff->department }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('admin.staff.show', $staff) }}"
                                   class="btn btn-xs btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted mb-0">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i><br>
                        All staff have submitted!
                    </p>
                @endif
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
            <form id="statusForm" method="POST">
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
                        <label for="admin_notes">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                  placeholder="Add any notes or feedback..."></textarea>
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

<!-- Edit Request Modal -->
<div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Handle Edit Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Staff member has requested to edit their weekly tracker submission. What would you like to do?</p>
                <div class="btn-group btn-block" role="group">
                    <button type="button" class="btn btn-success" onclick="approveEdit()">
                        <i class="fas fa-check mr-1"></i> Approve Edit
                    </button>
                    <button type="button" class="btn btn-danger" onclick="showRejectForm()">
                        <i class="fas fa-times mr-1"></i> Reject Edit
                    </button>
                </div>

                <div id="rejectForm" style="display: none;" class="mt-3">
                    <form id="rejectEditForm" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="rejection_reason">Reason for Rejection</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason"
                                      rows="3" required placeholder="Explain why the edit request is rejected..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times mr-1"></i> Reject Edit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .media {
            align-items: center;
        }
        .badge {
            font-size: 0.85rem;
        }
        .table th {
            font-weight: 600;
            color: #2c3e50;
            border-top: none;
        }
        .list-group-item {
            border-left: none;
            border-right: none;
        }
        .list-group-item:first-child {
            border-top: none;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }
        .text-truncate {
            max-width: 120px;
        }
    </style>
@stop

@section('js')
    <script>
        let currentTrackerId = null;

        // Auto-submit on filter change
        $('#week, #department, #status').change(function() {
            $(this).closest('form').submit();
        });

        // Update status function
        function updateStatus(trackerId, newStatus) {
            currentTrackerId = trackerId;
            $('#submission_status').val(newStatus);
            $('#statusForm').attr('action', `/admin/weekly-trackers/${trackerId}/status`);
            $('#statusModal').modal('show');
        }

        // Handle edit request
        function handleEditRequest(trackerId) {
            currentTrackerId = trackerId;
            $('#editRequestModal').modal('show');
        }

        // Approve edit
        function approveEdit() {
            if (currentTrackerId) {
                const form = $('<form method="POST" action="/admin/weekly-trackers/' + currentTrackerId + '/approve-edit">@csrf</form>');
                $('body').append(form);
                form.submit();
            }
        }

        // Show reject form
        function showRejectForm() {
            $('#rejectForm').show();
            $('#rejectEditForm').attr('action', `/admin/weekly-trackers/${currentTrackerId}/reject-edit`);
        }

        // Handle form submissions
        $('#statusForm').on('submit', function(e) {
            e.preventDefault();
            this.submit();
        });

        $('#rejectEditForm').on('submit', function(e) {
            e.preventDefault();
            this.submit();
        });
    </script>
@stop
