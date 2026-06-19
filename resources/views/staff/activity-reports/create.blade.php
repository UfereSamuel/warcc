@extends('layouts.staff')

@section('title', 'Submit Activity Report')
@section('page-title', 'Submit Activity Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.activity-reports.index') }}">Activity Reports</a></li>
    <li class="breadcrumb-item active">New Report</li>
@endsection

@section('content')
@if(isset($pendingActivities) && $pendingActivities->count())
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            You have {{ $pendingActivities->count() }} completed {{ Str::plural('activity', $pendingActivities->count()) }} awaiting a report.
            Select one below or use the quick links:
            @foreach($pendingActivities->take(3) as $activity)
                <a href="{{ route('staff.activity-reports.create', ['activity_calendar_id' => $activity->id]) }}" class="badge badge-primary ml-1">{{ Str::limit($activity->title, 40) }}</a>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus mr-2"></i>Report Details</h3>
            </div>
            <form method="POST" action="{{ route('staff.activity-reports.store') }}" enctype="multipart/form-data">
                @csrf
                @include('staff.activity-reports._form', [
                    'report' => null,
                    'selectedActivity' => $selectedActivity,
                    'calendarActivities' => $calendarActivities,
                ])
                <div class="card-footer">
                    <button type="submit" name="action" value="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Submit Report
                    </button>
                    <button type="submit" name="action" value="draft" class="btn btn-secondary">
                        <i class="fas fa-save mr-1"></i> Save Draft
                    </button>
                    <a href="{{ route('staff.activity-reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>About Reports</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Use this form to document outcomes after an activity has taken place.</p>
                <ul class="mb-0">
                    <li class="mb-2">Link to a <strong>calendar activity</strong> when reporting on a scheduled event.</li>
                    <li class="mb-2">Leave the calendar field empty for a <strong>standalone report</strong>.</li>
                    <li class="mb-2">Save as draft to finish later, or submit for admin review.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
