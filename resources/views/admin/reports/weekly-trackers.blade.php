@extends('adminlte::page')

@section('title', 'Weekly Tracker Submissions Report')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Weekly Tracker Submissions</h1>
            <p class="text-muted">Monitor status submissions and approval workflow</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Weekly Trackers</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Filter Controls -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filter Submissions
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.weekly-trackers') }}" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Status</option>
                                    @foreach($statuses as $stat)
                                        <option value="{{ $stat }}" {{ $status == $stat ? 'selected' : '' }}>
                                            {{ ucfirst($stat) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-search mr-1"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('admin.reports.weekly-trackers') }}" class="btn btn-secondary ml-2">
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

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Submissions</span>
                <span class="info-box-number">{{ $trackerStats['total'] }}</span>
                <span class="progress-description">All records</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending Review</span>
                <span class="info-box-number">{{ $trackerStats['submitted'] }}</span>
                <span class="progress-description">Need action</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-eye"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Under Review</span>
                <span class="info-box-number">{{ $trackerStats['reviewed'] }}</span>
                <span class="progress-description">Being processed</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Approved</span>
                <span class="info-box-number">{{ $trackerStats['approved'] }}</span>
                <span class="progress-description">Completed</span>
            </div>
        </div>
    </div>
</div>

<!-- Approval Rate Progress -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Submission Status Breakdown
                </h3>
            </div>
            <div class="card-body">
                @if($trackerStats['total'] > 0)
                    <div class="progress mb-3" style="height: 30px;">
                        @if($trackerStats['draft'] > 0)
                            <div class="progress-bar bg-secondary" style="width: {{ round(($trackerStats['draft'] / $trackerStats['total']) * 100) }}%">
                                Draft ({{ $trackerStats['draft'] }})
                            </div>
                        @endif
                        @if($trackerStats['submitted'] > 0)
                            <div class="progress-bar bg-warning" style="width: {{ round(($trackerStats['submitted'] / $trackerStats['total']) * 100) }}%">
                                Submitted ({{ $trackerStats['submitted'] }})
                            </div>
                        @endif
                        @if($trackerStats['reviewed'] > 0)
                            <div class="progress-bar bg-info" style="width: {{ round(($trackerStats['reviewed'] / $trackerStats['total']) * 100) }}%">
                                Reviewed ({{ $trackerStats['reviewed'] }})
                            </div>
                        @endif
                        @if($trackerStats['approved'] > 0)
                            <div class="progress-bar bg-success" style="width: {{ round(($trackerStats['approved'] / $trackerStats['total']) * 100) }}%">
                                Approved ({{ $trackerStats['approved'] }})
                            </div>
                        @endif
                    </div>

                    <div class="row text-center">
                        <div class="col-3">
                            <div class="description-block">
                                <h5 class="description-header text-secondary">{{ round(($trackerStats['draft'] / $trackerStats['total']) * 100) }}%</h5>
                                <span class="description-text">Draft</span>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="description-block">
                                <h5 class="description-header text-warning">{{ round(($trackerStats['submitted'] / $trackerStats['total']) * 100) }}%</h5>
                                <span class="description-text">Pending</span>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="description-block">
                                <h5 class="description-header text-info">{{ round(($trackerStats['reviewed'] / $trackerStats['total']) * 100) }}%</h5>
                                <span class="description-text">Review</span>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="description-block">
                                <h5 class="description-header text-success">{{ round(($trackerStats['approved'] / $trackerStats['total']) * 100) }}%</h5>
                                <span class="description-text">Approved</span>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-muted text-center">No submissions found for selected criteria.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Status Definitions
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <span class="badge badge-secondary mr-2">Draft</span>
                        Not yet submitted
                    </li>
                    <li class="mb-2">
                        <span class="badge badge-warning mr-2">Submitted</span>
                        Waiting for review
                    </li>
                    <li class="mb-2">
                        <span class="badge badge-info mr-2">Reviewed</span>
                        Under evaluation
                    </li>
                    <li class="mb-2">
                        <span class="badge badge-success mr-2">Approved</span>
                        Accepted & completed
                    </li>
                </ul>

                @if($trackerStats['total'] > 0)
                    <div class="mt-3 text-center">
                        <strong class="text-success">
                            {{ round((($trackerStats['approved'] + $trackerStats['reviewed']) / $trackerStats['total']) * 100) }}%
                        </strong>
                        <br>
                        <small class="text-muted">Processing Rate</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Submissions Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list mr-2"></i>
            Weekly Tracker Submissions
        </h3>
        <div class="card-tools">
            <span class="badge badge-info">{{ $trackers->total() }} Total Records</span>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        @if($trackers->count() > 0)
            <table class="table table-hover text-nowrap">
                <thead class="thead-light">
                    <tr>
                        <th>Staff Member</th>
                        <th>Department</th>
                        <th>Week Period</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Submitted On</th>
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
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($tracker->week_start_date)->format('M d') }}</strong>
                                -
                                <strong>{{ \Carbon\Carbon::parse($tracker->week_start_date)->addDays(6)->format('M d, Y') }}</strong>
                                <br>
                                <small class="text-muted">Week {{ \Carbon\Carbon::parse($tracker->week_start_date)->weekOfYear }}</small>
                            </td>
                            <td class="text-center">
                                @switch($tracker->status)
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
                                    @default
                                        <span class="badge badge-secondary">{{ ucfirst($tracker->status) }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                @if($tracker->status != 'draft')
                                    {{ $tracker->created_at->format('M d, Y') }}
                                    <br>
                                    <small class="text-muted">{{ $tracker->created_at->format('g:i A') }}</small>
                                @else
                                    <span class="text-muted">Not submitted</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.staff.show', $tracker->staff) }}"
                                       class="btn btn-info btn-sm" title="View Staff Profile">
                                        <i class="fas fa-user"></i>
                                    </a>
                                    @if($tracker->status == 'submitted')
                                        <button class="btn btn-warning btn-sm" title="Needs Review">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </button>
                                    @endif
                                    @if($tracker->status == 'approved')
                                        <button class="btn btn-success btn-sm" title="Approved">
                                            <i class="fas fa-check"></i>
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
                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Submissions Found</h4>
                <p class="text-muted">No weekly tracker submissions found for the selected criteria.</p>
            </div>
        @endif
    </div>

    @if($trackers->hasPages())
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        Showing {{ $trackers->firstItem() }} to {{ $trackers->lastItem() }} of {{ $trackers->total() }} results
                    </small>
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        {{ $trackers->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@stop

@section('css')
    <style>
        .description-block {
            border-right: 1px solid #dee2e6;
        }

        .description-block:last-child {
            border-right: none;
        }

        .progress {
            background-color: #e9ecef;
        }

        .table th {
            font-weight: 600;
            color: #2c3e50;
            border-top: none;
        }

        .media {
            align-items: center;
        }

        .badge {
            font-size: 0.85rem;
        }

        .btn-group .btn {
            margin-right: 2px;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-submit on filter change
        $('#department, #status').change(function() {
            $(this).closest('form').submit();
        });
    </script>
@stop
