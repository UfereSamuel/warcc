@extends('layouts.staff')

@section('title', 'Missions')
@section('page-title', 'Missions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Missions</li>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Missions</p>
            </div>
            <div class="icon">
                <i class="fas fa-plane"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['pending'] }}</h3>
                <p>Pending Approval</p>
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
                <i class="fas fa-check"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $stats['active'] }}</h3>
                <p>Active Now</p>
            </div>
            <div class="icon">
                <i class="fas fa-running"></i>
            </div>
        </div>
    </div>
</div>

<!-- Missions List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>
                    My Missions
                </h3>
                <div class="card-tools">
                    <a href="{{ route('staff.missions.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Request New Mission
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($missions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Mission</th>
                                    <th>Dates</th>
                                    <th>Location</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($missions as $mission)
                                <tr>
                                    <td>
                                        <strong>{{ $mission->title }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($mission->purpose, 50) }}</small>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>Start:</strong> {{ $mission->start_date->format('M d, Y') }}<br>
                                            <strong>End:</strong> {{ $mission->end_date->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <i class="fas fa-map-marker-alt mr-1 text-muted"></i>
                                        {{ $mission->location }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $mission->duration_in_days }} day{{ $mission->duration_in_days > 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($mission->status === 'pending')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pending
                                            </span>
                                        @elseif($mission->status === 'approved')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check mr-1"></i>
                                                Approved
                                            </span>
                                            @if($mission->is_active)
                                                <br><small class="text-primary"><strong>Active Now</strong></small>
                                            @endif
                                        @elseif($mission->status === 'rejected')
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times mr-1"></i>
                                                Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('staff.missions.show', $mission) }}"
                                               class="btn btn-sm btn-info"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($mission->status === 'pending')
                                                <a href="{{ route('staff.missions.edit', $mission) }}"
                                                   class="btn btn-sm btn-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST"
                                                      action="{{ route('staff.missions.destroy', $mission) }}"
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this mission?')">
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
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $missions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-plane fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No missions yet</h5>
                        <p class="text-muted">Request your first mission to get started</p>
                        <a href="{{ route('staff.missions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>
                            Request New Mission
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
