@extends('layouts.staff')

@section('title', 'Edit Activity Report')
@section('page-title', 'Edit Activity Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.activity-reports.index') }}">Activity Reports</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit Report</h3>
            </div>
            <form method="POST" action="{{ route('staff.activity-reports.update', $activityReport) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('staff.activity-reports._form', [
                    'report' => $activityReport,
                    'selectedActivity' => $activityReport->activity,
                    'calendarActivities' => $calendarActivities,
                ])
                <div class="card-footer">
                    <button type="submit" name="action" value="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Submit Report
                    </button>
                    <button type="submit" name="action" value="draft" class="btn btn-secondary">
                        <i class="fas fa-save mr-1"></i> Save Draft
                    </button>
                    <a href="{{ route('staff.activity-reports.show', $activityReport) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
