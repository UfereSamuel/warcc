@extends('adminlte::page')

@section('title', 'Staff Roster')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Staff Roster</h1>
            <p class="text-muted">Who is at the office, on mission, or on leave this week</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Staff Roster</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="card card-outline card-primary mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filter Roster</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.staff-roster.index') }}" class="form-row">
            <div class="form-group col-md-3">
                <label for="week">Week Starting</label>
                <input type="date" class="form-control" id="week" name="week"
                       value="{{ $weekStart->toDateString() }}">
            </div>
            <div class="form-group col-md-2">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="">All statuses</option>
                    @foreach($statuses as $statusKey)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>
                            {{ app(\App\Services\StaffStatusAnalyticsService::class)->statusLabel($statusKey) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="position_id">Position</label>
                <select class="form-control" id="position_id" name="position_id">
                    <option value="">All positions</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" @selected($positionId == $position->id)>
                            {{ $position->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2">
                <label for="search">Search</label>
                <input type="text" class="form-control" id="search" name="search"
                       placeholder="Name, ID, mission..." value="{{ $search }}">
            </div>
            <div class="form-group col-md-2">
                <label>&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
        @if(request()->hasAny(['status', 'position_id', 'search']))
            <a href="{{ route('admin.staff-roster.index', ['week' => $staffRoster['week_start']]) }}"
               class="btn btn-sm btn-outline-secondary">
                Clear filters
            </a>
        @endif
    </div>
</div>

@include('admin.partials.staff-roster-export-panel')

@include('admin.partials.staff-roster-widget', [
    'staffRoster' => $staffRoster,
    'entries' => $entries,
    'compact' => false,
])
@stop
