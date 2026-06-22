@extends('layouts.staff')

@section('title', 'Activity Reports')
@section('page-title', 'Activity Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Activity Reports</li>
@endsection

@section('content')
@if(isset($pendingActivities) && $pendingActivities->count())
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <h5 class="mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Reports Due</h5>
            <p class="mb-2">The following completed activities need a post-activity report from you:</p>
            <ul class="mb-0">
                @foreach($pendingActivities as $activity)
                    <li>
                        <strong>{{ $activity->title }}</strong>
                        ({{ $activity->type_label }} — ended {{ $activity->end_date->format('M d, Y') }})
                        <a href="{{ route('staff.activity-reports.create', ['activity_calendar_id' => $activity->id]) }}" class="btn btn-sm btn-primary ml-2">
                            <i class="fas fa-file-alt mr-1"></i> Submit Report
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

@if(isset($pendingMissionTrackers) && $pendingMissionTrackers->count())
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <h5 class="mb-2"><i class="fas fa-plane mr-2"></i>Mission Reports Due</h5>
            <p class="mb-2">You have submitted weekly tracker missions that still need a report:</p>
            <ul class="mb-0">
                @foreach($pendingMissionTrackers as $tracker)
                    @php $draftReport = $tracker->getMissionReport(); @endphp
                    <li class="mb-2">
                        <strong>{{ $tracker->mission_title }}</strong>
                        <span class="text-muted">({{ $tracker->week_range }})</span>
                        @if($draftReport && $draftReport->status === 'draft')
                            <a href="{{ route('staff.activity-reports.edit', $draftReport) }}" class="btn btn-sm btn-secondary ml-2">
                                <i class="fas fa-edit mr-1"></i> Continue Draft
                            </a>
                        @else
                            <a href="{{ route('staff.activity-reports.create', ['weekly_tracker_id' => $tracker->id]) }}" class="btn btn-sm btn-primary ml-2">
                                <i class="fas fa-file-alt mr-1"></i> Submit Report
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<div class="row mb-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Reports</p>
            </div>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $stats['draft'] }}</h3>
                <p>Drafts</p>
            </div>
            <div class="icon"><i class="fas fa-edit"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['submitted'] }}</h3>
                <p>Submitted</p>
            </div>
            <div class="icon"><i class="fas fa-paper-plane"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['reviewed'] }}</h3>
                <p>Reviewed</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i>My Activity Reports</h3>
        <div class="card-tools">
            <div class="btn-group mr-2">
                <a href="{{ route('staff.activity-reports.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                <a href="{{ route('staff.activity-reports.index', ['status' => 'draft']) }}" class="btn btn-sm {{ request('status') === 'draft' ? 'btn-secondary' : 'btn-outline-secondary' }}">Draft</a>
                <a href="{{ route('staff.activity-reports.index', ['status' => 'submitted']) }}" class="btn btn-sm {{ request('status') === 'submitted' ? 'btn-warning' : 'btn-outline-warning' }}">Submitted</a>
                <a href="{{ route('staff.activity-reports.index', ['status' => 'reviewed']) }}" class="btn btn-sm {{ request('status') === 'reviewed' ? 'btn-success' : 'btn-outline-success' }}">Reviewed</a>
            </div>
            <a href="{{ route('staff.activity-reports.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> New Report
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        @if($reports->count())
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Calendar Activity</th>
                        <th>Report Date</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td><strong>{{ $report->title }}</strong></td>
                            <td>
                                @if($report->weeklyTracker)
                                    <span class="badge badge-success">{{ $report->weeklyTracker->mission_title }}</span>
                                @elseif($report->activity)
                                    <span class="badge badge-info">{{ $report->activity->title }}</span>
                                @else
                                    <span class="text-muted">Standalone report</span>
                                @endif
                            </td>
                            <td>{{ $report->report_date->format('M d, Y') }}</td>
                            <td><span class="badge badge-{{ $report->status_color }}">{{ $report->status_label }}</span></td>
                            <td>{{ $report->submitted_at?->format('M d, Y h:i A') ?? '—' }}</td>
                            <td>
                                <a href="{{ route('staff.activity-reports.show', $report) }}" class="btn btn-xs btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($report->isEditableByStaff())
                                    <a href="{{ route('staff.activity-reports.edit', $report) }}" class="btn btn-xs btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No activity reports yet</h5>
                <p class="text-muted">Submit a report after completing an activity, with or without linking it to the calendar.</p>
                <a href="{{ route('staff.activity-reports.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Create Report
                </a>
            </div>
        @endif
    </div>
    @if($reports->hasPages())
        <div class="card-footer">{{ $reports->links() }}</div>
    @endif
</div>
@endsection
