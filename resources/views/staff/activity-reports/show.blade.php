@extends('layouts.staff')

@section('title', 'Activity Report')
@section('page-title', 'Activity Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.activity-reports.index') }}">Activity Reports</a></li>
    <li class="breadcrumb-item active">{{ $activityReport->title }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $activityReport->title }}</h3>
                <div class="card-tools">
                    <span class="badge badge-{{ $activityReport->status_color }}">{{ $activityReport->status_label }}</span>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Report Date</dt>
                    <dd class="col-sm-8">{{ $activityReport->report_date->format('l, F j, Y') }}</dd>

                    <dt class="col-sm-4">Calendar Activity</dt>
                    <dd class="col-sm-8">
                        @if($activityReport->activity)
                            <span class="badge badge-info">{{ $activityReport->activity->title }}</span>
                            <small class="text-muted d-block">
                                {{ $activityReport->activity->start_date->format('M d') }} — {{ $activityReport->activity->end_date->format('M d, Y') }}
                                @if($activityReport->activity->location)
                                    · {{ $activityReport->activity->location }}
                                @endif
                            </small>
                        @else
                            <span class="text-muted">Standalone report (not linked to calendar)</span>
                        @endif
                    </dd>

                    @if($activityReport->submitted_at)
                        <dt class="col-sm-4">Submitted</dt>
                        <dd class="col-sm-8">{{ $activityReport->submitted_at->format('M d, Y h:i A') }}</dd>
                    @endif

                    @if($activityReport->reviewed_at)
                        <dt class="col-sm-4">Reviewed</dt>
                        <dd class="col-sm-8">
                            {{ $activityReport->reviewed_at->format('M d, Y h:i A') }}
                            @if($activityReport->reviewer)
                                by {{ $activityReport->reviewer->full_name }}
                            @endif
                        </dd>
                    @endif
                </dl>

                <hr>

                <h5>Summary</h5>
                <p class="text-justify">{!! nl2br(e($activityReport->summary)) !!}</p>

                @if($activityReport->outcomes)
                    <h5 class="mt-3">Outcomes</h5>
                    <p class="text-justify">{!! nl2br(e($activityReport->outcomes)) !!}</p>
                @endif

                @if($activityReport->challenges)
                    <h5 class="mt-3">Challenges</h5>
                    <p class="text-justify">{!! nl2br(e($activityReport->challenges)) !!}</p>
                @endif

                @if($activityReport->recommendations)
                    <h5 class="mt-3">Recommendations</h5>
                    <p class="text-justify">{!! nl2br(e($activityReport->recommendations)) !!}</p>
                @endif

                @if($activityReport->attachment)
                    <h5 class="mt-3">Attachment</h5>
                    <a href="{{ route('staff.activity-reports.download', $activityReport) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download mr-1"></i> {{ $activityReport->attachment['original_name'] }}
                    </a>
                @endif

                @if($activityReport->admin_notes)
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-comment mr-1"></i> Admin Feedback</h6>
                        {!! nl2br(e($activityReport->admin_notes)) !!}
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('staff.activity-reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Reports
                </a>
                @if($activityReport->isEditableByStaff())
                    <a href="{{ route('staff.activity-reports.edit', $activityReport) }}" class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i> Edit Draft
                    </a>
                    <form method="POST" action="{{ route('staff.activity-reports.submit', $activityReport) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Submit this report for admin review?')">
                            <i class="fas fa-paper-plane mr-1"></i> Submit for Review
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
