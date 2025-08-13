@extends('adminlte::page')

@section('title', 'Mission Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Mission Management</h1>
            <p class="text-muted">Review and approve staff mission requests</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Missions</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-plane"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Missions</span>
                <span class="info-box-number">{{ $stats['total'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending</span>
                <span class="info-box-number">{{ $stats['pending'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Approved</span>
                <span class="info-box-number">{{ $stats['approved'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-danger">
            <span class="info-box-icon"><i class="fas fa-times"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Rejected</span>
                <span class="info-box-number">{{ $stats['rejected'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Controls -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Missions
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.missions.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $statusOption)
                                        <option value="{{ $statusOption }}" {{ $status == $statusOption ? 'selected' : '' }}>
                                            {{ ucfirst($statusOption) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.missions.index') }}" class="btn btn-secondary ml-2">
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

<!-- Missions List -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plane mr-2"></i>
                    Mission Requests
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $missions->total() }} Total</span>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($missions->count() > 0)
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Staff Member</th>
                                <th>Mission Details</th>
                                <th>Duration</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Submitted</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($missions as $mission)
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <img src="{{ $mission->staff->profile_picture_url }}"
                                                 alt="{{ $mission->staff->full_name }}"
                                                 class="img-circle mr-3"
                                                 style="width: 35px; height: 35px;">
                                            <div class="media-body">
                                                <h6 class="mb-0">{{ $mission->staff->full_name }}</h6>
                                                <small class="text-muted">{{ $mission->staff->department }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-1">{{ $mission->title }}</h6>
                                        <p class="mb-0 text-muted">{{ Str::limit($mission->purpose, 50) }}</p>
                                        <small class="text-info">{{ $mission->destination }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $mission->start_date->format('M d') }} - {{ $mission->end_date->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $mission->start_date->diffInDays($mission->end_date) + 1 }} days</small>
                                    </td>
                                    <td class="text-center">
                                        @switch($mission->status)
                                            @case('pending')
                                                <span class="badge badge-warning">Pending</span>
                                                @break
                                            @case('approved')
                                                <span class="badge badge-success">Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                                @break
                                            @case('completed')
                                                <span class="badge badge-info">Completed</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <small>{{ $mission->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @if($mission->status === 'pending')
                                                <form method="GET" action="{{ route('admin.missions.approve', $mission) }}" style="display: inline;">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <button class="btn btn-sm btn-danger"
                                                        onclick="rejectMission({{ $mission->id }})" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('staff.missions.show', $mission) }}"
                                               class="btn btn-sm btn-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-plane fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Mission Requests</h4>
                        <p class="text-muted">No mission requests found matching your criteria.</p>
                    </div>
                @endif
            </div>
            @if($missions->hasPages())
                <div class="card-footer">
                    {{ $missions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Mission Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Reason for Rejection</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason"
                                  rows="4" required placeholder="Please provide a reason for rejecting this mission request..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Mission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        function rejectMission(missionId) {
            $('#rejectForm').attr('action', `/admin/missions/${missionId}/reject`);
            $('#rejectModal').modal('show');
        }
    </script>
@stop
