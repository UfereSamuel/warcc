@extends('adminlte::page')

@section('title', 'Activity Report Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $activityReport->title }}</h1>
            <p class="text-muted">Submitted by {{ $activityReport->staff->full_name ?? 'Unknown' }}</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.activity-reports.index') }}">Activity Reports</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Report Content</h3>
                <div class="card-tools">
                    @include('admin.activity-reports.partials.report-type-badge', ['report' => $activityReport])
                    <span class="badge badge-{{ $activityReport->status_color }} ml-1">{{ $activityReport->status_label }}</span>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Staff Member</dt>
                    <dd class="col-sm-8">{{ $activityReport->staff->full_name }} ({{ $activityReport->staff->staff_id }})</dd>

                    <dt class="col-sm-4">Report Date</dt>
                    <dd class="col-sm-8">{{ $activityReport->report_date->format('F j, Y') }}</dd>

                    <dt class="col-sm-4">Calendar Activity</dt>
                    <dd class="col-sm-8">
                        @if($activityReport->activity)
                            <a href="{{ route('admin.calendar.edit', $activityReport->activity) }}">{{ $activityReport->activity->title }}</a>
                        @else
                            <span class="text-muted">Standalone report</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Weekly Tracker Mission</dt>
                    <dd class="col-sm-8">
                        @if($activityReport->weeklyTracker)
                            <span class="badge badge-success">{{ $activityReport->weeklyTracker->mission_title }}</span>
                            <small class="text-muted d-block">{{ $activityReport->weeklyTracker->week_range }}</small>
                        @else
                            <span class="text-muted">Not linked to a weekly tracker mission</span>
                        @endif
                    </dd>

                    @if($activityReport->submitted_at)
                        <dt class="col-sm-4">Submitted</dt>
                        <dd class="col-sm-8">{{ $activityReport->submitted_at->format('M d, Y h:i A') }}</dd>
                    @endif
                </dl>

                <hr>
                <h5>Summary</h5>
                <p>{!! nl2br(e($activityReport->summary)) !!}</p>

                @if($activityReport->outcomes)
                    <h5 class="mt-3">Outcomes</h5>
                    <p>{!! nl2br(e($activityReport->outcomes)) !!}</p>
                @endif

                @if($activityReport->challenges)
                    <h5 class="mt-3">Challenges</h5>
                    <p>{!! nl2br(e($activityReport->challenges)) !!}</p>
                @endif

                @if($activityReport->recommendations)
                    <h5 class="mt-3">Recommendations</h5>
                    <p>{!! nl2br(e($activityReport->recommendations)) !!}</p>
                @endif

                @if($activityReport->attachment)
                    <h5 class="mt-3">Attachment</h5>
                    <a href="{{ route('admin.activity-reports.download', $activityReport) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download mr-1"></i> {{ $activityReport->attachment['original_name'] }}
                    </a>
                @endif
            </div>
        </div>

        @include('admin.activity-reports.partials.mission-context', ['activityReport' => $activityReport])
    </div>

    <div class="col-md-4">
        @if($activityReport->status === 'submitted')
            <div class="card card-warning">
                <div class="card-header"><h3 class="card-title">Mark as Reviewed</h3></div>
                <form method="POST" action="{{ route('admin.activity-reports.review', $activityReport) }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="admin_notes">Admin Notes / Feedback</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="4"
                                      placeholder="Optional feedback for the staff member">{{ old('admin_notes', $activityReport->admin_notes) }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check mr-1"></i> Mark as Reviewed
                        </button>
                    </div>
                </form>
            </div>
        @elseif($activityReport->status === 'reviewed')
            <div class="card card-success">
                <div class="card-header"><h3 class="card-title">Reviewed</h3></div>
                <div class="card-body">
                    <p class="mb-1"><strong>Reviewed:</strong> {{ $activityReport->reviewed_at?->format('M d, Y h:i A') }}</p>
                    @if($activityReport->reviewer)
                        <p class="mb-1"><strong>By:</strong> {{ $activityReport->reviewer->full_name }}</p>
                    @endif
                    @if($activityReport->admin_notes)
                        <hr>
                        <p class="mb-0"><strong>Notes:</strong><br>{!! nl2br(e($activityReport->admin_notes)) !!}</p>
                    @endif
                </div>
            </div>
        @else
            <div class="card card-secondary">
                <div class="card-body">
                    <p class="mb-0 text-muted">This report is still a draft and has not been submitted by staff yet.</p>
                </div>
            </div>
        @endif

        @if($aiConfigured && $activityReport->status !== 'draft')
            <div class="card card-outline card-primary">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-robot mr-2"></i>AI Assistant</h3></div>
                <div class="card-body">
                    <p class="text-muted small">Generate an executive summary of this report for review notes or briefings.</p>
                    <button type="button" class="btn btn-primary btn-block" id="aiSummarizeBtn">
                        <i class="fas fa-magic mr-1"></i> Summarize with AI
                    </button>
                </div>
            </div>
        @elseif(!$aiConfigured)
            <div class="alert alert-warning mt-3">
                <small><strong>AI unavailable.</strong> Set <code>AI_API_KEY</code> in <code>.env</code> to enable summarization.</small>
            </div>
        @endif

        <a href="{{ route('admin.activity-reports.index') }}" class="btn btn-secondary btn-block mt-3">
            <i class="fas fa-arrow-left mr-1"></i> Back to Reports
        </a>
    </div>
</div>

@if($aiConfigured && $activityReport->status !== 'draft')
    @include('admin.activity-reports.partials.ai-modal', ['showApplyToNotes' => true])
@endif
@stop

@if($aiConfigured && $activityReport->status !== 'draft')
@section('js')
    @include('admin.activity-reports.partials.ai-scripts', [
        'summarizeUrl' => route('admin.activity-reports.ai.summarize', $activityReport),
    ])
    <script>
        $('#aiSummarizeBtn').on('click', function () {
            ActivityReportAi.summarize();
        });
    </script>
@stop
@endif
