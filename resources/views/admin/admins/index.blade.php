@extends('adminlte::page')

@section('title', 'Admin Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Admin Management</h1>
            <p class="text-muted">Manage administrators and their privileges</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Admin Management</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Search and Filter Section -->
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-search mr-2"></i>
            Search & Filter Administrators
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.admins.index') }}" class="form-horizontal">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}"
                               placeholder="Name, email, staff ID, position...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="position_id">Position</label>
                        <select class="form-control" id="position_id" name="position_id">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="">All Genders</option>
                            @foreach($genders as $gender)
                                <option value="{{ $gender }}" {{ request('gender') === $gender ? 'selected' : '' }}>
                                    {{ ucfirst($gender) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search mr-1"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Admin Statistics -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-user-shield"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Admins</span>
                <span class="info-box-number">{{ $admins->total() }}</span>
                <span class="progress-description">All administrators</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Active Admins</span>
                <span class="info-box-number">{{ $admins->where('status', 'active')->count() }}</span>
                <span class="progress-description">Currently active</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-crown"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Super Admins</span>
                <span class="info-box-number">{{ $admins->where('staff_id', 'like', '%ADMIN%')->count() }}</span>
                <span class="progress-description">With full privileges</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-users-cog"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Department Admins</span>
                <span class="info-box-number">{{ $admins->where('staff_id', 'not like', '%ADMIN%')->count() }}</span>
                <span class="progress-description">Department level</span>
            </div>
        </div>
    </div>
</div>

<!-- Administrators List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-shield mr-2"></i>
            Administrators
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Add New Admin
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Admin Details</th>
                    <th>Contact Info</th>
                    <th>Position & Department</th>
                    <th>Admin Level</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th width="280">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                @if($admin->profile_picture)
                                    <img src="{{ asset('storage/' . $admin->profile_picture) }}" 
                                         alt="{{ $admin->full_name }}" 
                                         class="img-circle"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; font-size: 16px;">
                                        {{ strtoupper(substr($admin->first_name, 0, 1) . substr($admin->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <strong>{{ $admin->full_name }}</strong><br>
                                <small class="text-muted">ID: {{ $admin->staff_id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <i class="fas fa-envelope text-muted mr-1"></i>
                            <a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a>
                        </div>
                        @if($admin->phone)
                        <div class="mt-1">
                            <i class="fas fa-phone text-muted mr-1"></i>
                            <a href="tel:{{ $admin->phone }}">{{ $admin->phone }}</a>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div>
                                                            <strong>{{ $admin->position_title }}</strong>
                        </div>
                        <small class="text-muted">{{ $admin->department }}</small>
                    </td>
                    <td>
                        @if(str_contains($admin->staff_id, 'ADMIN'))
                            <span class="badge badge-warning">
                                <i class="fas fa-crown mr-1"></i> Super Admin
                            </span>
                        @else
                            <span class="badge badge-info">
                                <i class="fas fa-user-cog mr-1"></i> Department Admin
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($admin->status === 'active')
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle mr-1"></i> Active
                            </span>
                        @elseif($admin->status === 'inactive')
                            <span class="badge badge-warning">
                                <i class="fas fa-pause-circle mr-1"></i> Inactive
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-question-circle mr-1"></i> {{ ucfirst($admin->status) }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($admin->last_login)
                            <span title="{{ $admin->last_login->format('M d, Y h:i A') }}">
                                {{ $admin->last_login->diffForHumans() }}
                            </span>
                        @else
                            <span class="text-muted">Never</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <!-- View Details Button -->
                            <a href="{{ route('admin.staff.show', $admin) }}" 
                               class="btn btn-sm btn-info mb-1 w-100">
                                <i class="fas fa-eye mr-1"></i> View Details
                            </a>
                            
                            <!-- Edit Admin Button -->
                            <a href="{{ route('admin.staff.edit', $admin) }}" 
                               class="btn btn-sm btn-primary mb-1 w-100">
                                <i class="fas fa-edit mr-1"></i> Edit Admin
                            </a>
                            
                            <!-- Remove Admin Privileges Button (only for non-super admins) -->
                            @if($admin->id !== auth()->guard('staff')->id())
                                @if(!str_contains($admin->staff_id, 'ADMIN'))
                                    <form method="POST" action="{{ route('admin.staff.demote', $admin) }}" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="btn btn-sm btn-warning w-100" 
                                                onclick="return confirm('Are you sure you want to remove admin privileges from {{ $admin->full_name }}? This action cannot be undone.')">
                                            <i class="fas fa-user-minus mr-1"></i> Remove Admin
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-secondary w-100" disabled>
                                        <i class="fas fa-shield-alt mr-1"></i> Super Admin (Protected)
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-sm btn-secondary w-100" disabled>
                                    <i class="fas fa-user mr-1"></i> Current User
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-user-shield fa-3x mb-3"></i>
                            <p>No administrators found matching your criteria.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($admins->hasPages())
    <div class="card-footer">
        {{ $admins->withQueryString()->links() }}
    </div>
    @endif
</div>
@stop

@section('css')
<style>
.info-box {
    border-radius: 10px;
}
.card {
    border-radius: 10px;
}
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.action-buttons .btn {
    font-size: 12px;
    padding: 6px 12px;
    white-space: nowrap;
    text-align: left;
}
.action-buttons .btn i {
    width: 14px;
    text-align: center;
}
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@stop
