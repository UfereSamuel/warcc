@extends('adminlte::page')

@section('title', 'Activity Reports')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Activity Reports</h1>
            <p class="text-muted">Review post-activity reports submitted by staff</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Activity Reports</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row mb-3">
    <div class="col-lg-2 col-6">
        <div class="small-box bg-info"><div class="inner"><h3>{{ $stats['total'] }}</h3><p>Total</p></div><div class="icon"><i class="fas fa-file-alt"></i></div></div>
    </div>
    <div class="col-lg-2 col-6">
        <div class="small-box bg-secondary"><div class="inner"><h3>{{ $stats['draft'] }}</h3><p>Drafts</p></div><div class="icon"><i class="fas fa-edit"></i></div></div>
    </div>
    <div class="col-lg-2 col-6">
        <a href="{{ route('admin.activity-reports.index', ['status' => 'submitted']) }}" class="text-dark">
            <div class="small-box bg-warning"><div class="inner"><h3>{{ $stats['submitted'] }}</h3><p>Awaiting Review</p></div><div class="icon"><i class="fas fa-clock"></i></div></div>
        </a>
    </div>
    <div class="col-lg-2 col-6">
        <div class="small-box bg-success"><div class="inner"><h3>{{ $stats['reviewed'] }}</h3><p>Reviewed</p></div><div class="icon"><i class="fas fa-check"></i></div></div>
    </div>
    <div class="col-lg-2 col-6">
        <a href="{{ route('admin.activity-reports.index', ['mission' => 'yes']) }}" class="text-dark">
            <div class="small-box bg-gradient-success"><div class="inner"><h3>{{ $stats['mission'] }}</h3><p>Mission Reports</p></div><div class="icon"><i class="fas fa-plane"></i></div></div>
        </a>
    </div>
    <div class="col-lg-2 col-6">
        <a href="{{ route('admin.activity-reports.index', ['mission' => 'yes', 'status' => 'submitted']) }}" class="text-dark">
            <div class="small-box bg-gradient-warning"><div class="inner"><h3>{{ $stats['mission_submitted'] }}</h3><p>Mission Pending</p></div><div class="icon"><i class="fas fa-clipboard-check"></i></div></div>
        </a>
    </div>
</div>

@if(request()->hasAny(['search', 'status', 'linked', 'mission', 'staff_id']))
<div class="alert alert-light border mb-3 py-2">
    <span class="text-muted mr-2">Active filters:</span>
    @if(request('mission') === 'yes')
        <span class="badge badge-success mr-1">Mission reports</span>
    @elseif(request('mission') === 'no')
        <span class="badge badge-secondary mr-1">Non-mission</span>
    @endif
    @if(request('status'))
        <span class="badge badge-info mr-1">{{ ucfirst(request('status')) }}</span>
    @endif
    @if(request('linked') === 'yes')
        <span class="badge badge-primary mr-1">Calendar linked</span>
    @elseif(request('linked') === 'no')
        <span class="badge badge-secondary mr-1">Standalone</span>
    @endif
    @if(request('search'))
        <span class="badge badge-dark mr-1">Search: {{ request('search') }}</span>
    @endif
    <a href="{{ route('admin.activity-reports.index') }}" class="btn btn-xs btn-outline-secondary ml-2">Clear all</a>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-search mr-2"></i>Filter Reports</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="form-row">
            <div class="form-group col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search title, summary, staff..." value="{{ request('search') }}">
            </div>
            <div class="form-group col-md-2">
                <select name="status" class="form-control">
                    <option value="">All statuses</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
                    <option value="reviewed" @selected(request('status') === 'reviewed')>Reviewed</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <select name="linked" class="form-control">
                    <option value="">All reports</option>
                    <option value="yes" @selected(request('linked') === 'yes')>Linked to calendar</option>
                    <option value="no" @selected(request('linked') === 'no')>Standalone</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <select name="mission" class="form-control">
                    <option value="">All types</option>
                    <option value="yes" @selected(request('mission') === 'yes')>Mission reports</option>
                    <option value="no" @selected(request('mission') === 'no')>Non-mission</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <select name="staff_id" class="form-control">
                    <option value="">All staff</option>
                    @foreach($staffMembers as $member)
                        <option value="{{ $member->id }}" @selected(request('staff_id') == $member->id)>
                            {{ $member->full_name }} ({{ $member->staff_id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2">
                <button type="submit" class="btn btn-primary btn-block">Filter</button>
            </div>
        </form>
    </div>
</div>

@if($aiConfigured)
<div class="card card-outline card-primary mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-robot mr-2"></i>AI Report Assistant</h3>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            Select reports in the table below, then merge them into one briefing. Or open a single report and use <strong>Summarize with AI</strong>.
        </p>
        <button type="button" class="btn btn-primary" id="aiMergeSelectedBtn" disabled>
            <i class="fas fa-object-group mr-1"></i> Merge Selected Reports
        </button>
        <span class="text-muted ml-2" id="aiSelectionCount">0 selected</span>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <strong>AI assistant unavailable.</strong> Add <code>AI_API_KEY</code> to your <code>.env</code> file to enable summarization and merge features.
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">All Reports</h3>
        @if($aiConfigured)
            <small class="text-muted">Check rows to merge multiple reports</small>
        @endif
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    @if($aiConfigured)
                        <th width="40"><input type="checkbox" id="selectAllReports" title="Select all on page"></th>
                    @endif
                    <th>Title</th>
                    <th>Type</th>
                    <th>Staff</th>
                    <th>Mission / Activity</th>
                    <th>Report Date</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        @if($aiConfigured)
                            <td>
                                @if($report->status !== 'draft')
                                    <input type="checkbox" class="report-select" value="{{ $report->id }}">
                                @endif
                            </td>
                        @endif
                        <td><strong>{{ $report->title }}</strong></td>
                        <td>@include('admin.activity-reports.partials.report-type-badge', ['report' => $report])</td>
                        <td>{{ $report->staff->full_name ?? '—' }}</td>
                        <td>
                            @if($report->weeklyTracker)
                                <span class="badge badge-success">{{ $report->weeklyTracker->mission_title }}</span>
                                <small class="text-muted d-block">{{ $report->weeklyTracker->week_range }}</small>
                            @elseif($report->activity)
                                {{ $report->activity->title }}
                            @else
                                <span class="text-muted">Standalone</span>
                            @endif
                        </td>
                        <td>{{ $report->report_date->format('M d, Y') }}</td>
                        <td><span class="badge badge-{{ $report->status_color }}">{{ $report->status_label }}</span></td>
                        <td>{{ $report->submitted_at?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.activity-reports.show', $report) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $aiConfigured ? 9 : 8 }}" class="text-center text-muted py-4">No activity reports found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())
        <div class="card-footer">{{ $reports->links() }}</div>
    @endif
</div>

@if($aiConfigured)
    @include('admin.activity-reports.partials.ai-modal', ['showApplyToNotes' => false])
@endif
@stop

@if($aiConfigured)
@section('js')
    @include('admin.activity-reports.partials.ai-scripts', ['summarizeUrl' => null])
    <script>
        function updateAiSelection() {
            const ids = $('.report-select:checked').map(function () { return parseInt(this.value, 10); }).get();
            $('#aiSelectionCount').text(ids.length + ' selected');
            $('#aiMergeSelectedBtn').prop('disabled', ids.length < 2);
            return ids;
        }

        $(document).on('change', '.report-select, #selectAllReports', function () {
            if (this.id === 'selectAllReports') {
                $('.report-select').prop('checked', this.checked);
            }
            updateAiSelection();
        });

        $('#aiMergeSelectedBtn').on('click', function () {
            ActivityReportAi.merge(updateAiSelection());
        });
    </script>
@stop
@endif
