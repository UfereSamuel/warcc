@extends('layouts.staff')

@section('title', 'My Activity Requests')
@section('page-title', 'My Activity Requests')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Activity Requests</li>
@endsection

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
                <i class="fas fa-calendar-plus"></i>
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
                    <div class="btn-group mr-3">
                        <a href="{{ route('staff.activity-requests.index') }}"
                           class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                            All
                        </a>
                        <a href="{{ route('staff.activity-requests.index', ['status' => 'pending']) }}"
                           class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                            Pending
                        </a>
                        <a href="{{ route('staff.activity-requests.index', ['status' => 'approved']) }}"
                           class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                            Approved
                        </a>
                        <a href="{{ route('staff.activity-requests.index', ['status' => 'rejected']) }}"
                           class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                            Rejected
                        </a>
                    </div>
                    <a href="{{ route('staff.activity-requests.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> New Request
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($requests->count() > 0)
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Date Range</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>
                                        <strong>{{ $request->title }}</strong>
                                        @if($request->description)
                                            <br><small class="text-muted">{{ Str::limit($request->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $request->type_color }}">{{ $request->type_label }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $request->start_date->format('M d, Y') }}</strong>
                                        @if($request->start_date->ne($request->end_date))
                                            <br><small class="text-muted">to {{ $request->end_date->format('M d, Y') }}</small>
                                        @endif
                                        <br><small class="text-muted">{{ $request->duration_in_days }} day{{ $request->duration_in_days > 1 ? 's' : '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $request->status_color }}">{{ $request->status_label }}</span>
                                        @if($request->status === 'approved' && $request->approvedActivity)
                                            <br><small class="text-success">
                                                <i class="fas fa-external-link-alt"></i> Published
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $request->created_at->format('M d, Y') }}</small>
                                        <br><small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('staff.activity-requests.show', $request) }}"
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($request->status === 'pending')
                                                <a href="{{ route('staff.activity-requests.edit', $request) }}"
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('staff.activity-requests.destroy', $request) }}"
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Are you sure you want to delete this request?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-plus fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Activity Requests</h4>
                        @if(request('status'))
                            <p class="text-muted">No {{ request('status') }} activity requests found.</p>
                            <a href="{{ route('staff.activity-requests.index') }}" class="btn btn-outline-primary mr-2">
                                <i class="fas fa-list mr-1"></i> View All Requests
                            </a>
                        @else
                            <p class="text-muted">You haven't submitted any activity requests yet.</p>
                        @endif
                        <a href="{{ route('staff.activity-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Create First Request
                        </a>
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

<!-- Quick Actions Info Box -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="info-box">
            <span class="info-box-icon bg-info">
                <i class="fas fa-info-circle"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Quick Actions</span>
                <span class="info-box-number">
                    <small>
                        • <strong>Pending requests</strong> can be edited or deleted
                        • <strong>Approved requests</strong> become published activities
                        • <strong>Rejected requests</strong> can be reviewed and resubmitted as new requests
                    </small>
                </span>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table td {
        vertical-align: middle;
    }

    .small-box {
        border-radius: 8px;
    }

    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    .info-box {
        border-radius: 8px;
    }
</style>
@stop
