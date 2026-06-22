@extends('adminlte::page')

@section('title', 'Positions Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-briefcase text-primary"></i>
                Positions Management
            </h1>
            <p class="text-muted mb-0">Manage job positions and designations in the organization</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Positions</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="positions-page">
<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Positions</p>
            </div>
            <div class="icon">
                <i class="fas fa-briefcase"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['active'] }}</h3>
                <p>Active Positions</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['inactive'] }}</h3>
                <p>Inactive Positions</p>
            </div>
            <div class="icon">
                <i class="fas fa-pause-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $stats['with_staff'] }}</h3>
                <p>Positions with Staff</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tools"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('admin.positions.create') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i>
                            <strong>Add New Position</strong>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('admin.positions.index', ['filter' => 'active']) }}" class="btn btn-success btn-block">
                            <i class="fas fa-check-circle"></i>
                            <strong>View Active</strong>
                            <span class="badge badge-light ml-1">{{ $stats['active'] }}</span>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('admin.positions.index', ['filter' => 'inactive']) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-pause-circle"></i>
                            <strong>View Inactive</strong>
                            <span class="badge badge-light ml-1">{{ $stats['inactive'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Positions List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i>
            Organization Positions
        </h3>
        <div class="card-tools">
            <span class="badge badge-primary">{{ $positions->total() }} Total</span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($positions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50" class="text-center">#</th>
                            <th>Position Title</th>
                            <th width="120" class="text-center">Staff Count</th>
                            <th width="100" class="text-center">Status</th>
                            <th width="120" class="text-center">Created</th>
                            <th width="250" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $position)
                            <tr>
                                <td class="text-center">
                                    <span class="badge badge-secondary">{{ $loop->iteration }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-briefcase text-primary mr-2"></i>
                                        <div>
                                            <strong class="text-dark">{{ $position->title }}</strong>
                                            @if($position->staff_count > 0)
                                                <br><small class="text-success">
                                                    <i class="fas fa-users"></i> {{ $position->staff_count }} staff member(s)
                                                </small>
                                            @else
                                                <br><small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> No staff assigned
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($position->staff_count > 0)
                                        <span class="badge badge-success badge-pill">
                                            <i class="fas fa-users"></i> {{ $position->staff_count }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary badge-pill">
                                            <i class="fas fa-user-slash"></i> 0
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($position->is_active)
                                        <span class="badge badge-success badge-pill">
                                            <i class="fas fa-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-warning badge-pill">
                                            <i class="fas fa-pause"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">
                                        {{ $position->created_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.positions.edit', $position) }}" 
                                           class="btn btn-outline-primary btn-sm mb-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <!-- Toggle Status Button -->
                                        <form method="POST" action="{{ route('admin.positions.toggle-status', $position) }}" 
                                              style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-{{ $position->is_active ? 'warning' : 'success' }} btn-sm mb-1">
                                                <i class="fas fa-{{ $position->is_active ? 'pause' : 'play' }}"></i> 
                                                {{ $position->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <!-- Delete Button (only if no staff assigned) -->
                                        @if($position->staff_count === 0)
                                            <form method="POST" action="{{ route('admin.positions.destroy', $position) }}" 
                                                  style="display: inline;"
                                                  data-warcc-confirm="Are you sure you want to delete the position &quot;{{ $position->title }}&quot;? This action cannot be undone.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                                <i class="fas fa-shield-alt"></i> Protected
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-briefcase fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Positions Found</h4>
                <p class="text-muted mb-3">No job positions have been created yet.</p>
                <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Position
                </a>
            </div>
        @endif
    </div>

    @if($positions->hasPages())
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info" role="status" aria-live="polite">
                        Showing {{ $positions->firstItem() ?? 0 }} to {{ $positions->lastItem() ?? 0 }} of {{ $positions->total() }} positions
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="dataTables_paginate paging_simple_numbers">
                        <ul class="pagination pagination-sm m-0 float-md-right">
                            {{-- Previous Page Link --}}
                            @if ($positions->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $positions->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($positions->getUrlRange(1, $positions->lastPage()) as $page => $url)
                                @if ($page == $positions->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($positions->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $positions->nextPageUrl() }}" rel="next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Information Panel -->
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h5 class="alert-heading">
                <i class="fas fa-info-circle"></i> About Positions Management
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <ul class="mb-0">
                        <li><strong>Active positions</strong> can be assigned to new staff members</li>
                        <li><strong>Inactive positions</strong> are hidden from staff selection but retain existing assignments</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="mb-0">
                        <li><strong>Protected positions</strong> cannot be deleted if they have staff members assigned</li>
                        <li><strong>Position changes</strong> automatically update staff profiles</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@stop

@section('css')
<style>
.positions-page .small-box {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.positions-page .small-box .icon {
    color: rgba(255, 255, 255, 0.25);
}

.positions-page .small-box.bg-info .icon,
.positions-page .small-box.bg-warning .icon {
    color: rgba(0, 0, 0, 0.12);
}

.positions-page .card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.positions-page .table th {
    border-top: none;
    font-weight: 600;
    color: #343a40;
    background-color: #f1f3f5;
}

.positions-page .table td {
    vertical-align: middle;
    color: #212529;
}

.positions-page .btn-group-vertical .btn {
    border-radius: 4px;
    margin-bottom: 2px;
    min-width: 7.5rem;
}

.positions-page .btn-group-vertical .btn:last-child {
    margin-bottom: 0;
}

.positions-page .badge-pill {
    border-radius: 50rem;
}

.positions-page .alert {
    border-radius: 8px;
}

.positions-page .card-footer .dataTables_info {
    color: #495057 !important;
}
.positions-page .pagination {
    margin: 0;
    display: flex;
    list-style: none;
    padding: 0;
}

.positions-page .pagination .page-item {
    margin: 0 2px;
}

.positions-page .pagination .page-link {
    display: block;
    padding: 0.375rem 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #007bff;
    background-color: #fff;
    text-decoration: none;
    transition: all 0.15s ease-in-out;
}

.positions-page .pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #0056b3;
    text-decoration: none;
}

.positions-page .pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
    z-index: 1;
}

.positions-page .pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Card footer styling */
.positions-page .card-footer {
    background-color: #f8f9fa !important;
    border-top: 1px solid #dee2e6;
    padding: 1rem;
}

.positions-page .card-footer .dataTables_info {
    color: #495057 !important;
    font-size: 0.875rem;
    line-height: 1.5;
}

.positions-page .card-footer .dataTables_paginate {
    margin: 0;
}

/* Responsive pagination */
@media (max-width: 768px) {
    .positions-page .card-footer .row {
        text-align: center;
    }
    
    .positions-page .card-footer .float-md-right {
        float: none !important;
    }
    
    .positions-page .card-footer .dataTables_info {
        margin-bottom: 1rem;
    }
}
</style>
@stop
