@extends('adminlte::page')

@section('title', 'Staff Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Staff Management</h1>
            <p class="text-muted">Manage all staff members and their information</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Staff Management</li>
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
            Search & Filter Staff
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="form-horizontal">
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
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select class="form-control" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                    {{ $department }}
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
                                <option value="{{ $gender }}" {{ request('gender') == $gender ? 'selected' : '' }}>
                                    {{ ucfirst($gender) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Clear
                    </a>
                    <a href="{{ route('admin.staff.create') }}" class="btn btn-success ml-2">
                        <i class="fas fa-plus mr-1"></i> Add New Staff
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Staff Statistics Cards -->
<div class="row mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Staff</span>
                <span class="info-box-number">{{ $staff->total() }}</span>
                <span class="progress-description">{{ $staff->where('status', 'active')->count() }} active</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-user-shield"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Administrators</span>
                <span class="info-box-number">{{ $staff->where('is_admin', true)->count() }}</span>
                <span class="progress-description">Admin users</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Departments</span>
                <span class="info-box-number">{{ $departments->count() }}</span>
                <span class="progress-description">Active departments</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Recent Hires</span>
                <span class="info-box-number">{{ $staff->where('hire_date', '>=', now()->subMonths(3))->count() }}</span>
                <span class="progress-description">Last 3 months</span>
            </div>
        </div>
    </div>
</div>

<!-- Staff Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list mr-2"></i>
            Staff Members
            @if(request()->hasAny(['search', 'status', 'role', 'department', 'gender']))
                <small class="text-muted">(Filtered Results)</small>
            @endif
        </h3>
        <div class="card-tools">
            <span class="badge badge-primary">{{ $staff->total() }} Total</span>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        @if($staff->count() > 0)
            <table class="table table-hover text-nowrap">
                <thead class="thead-light">
                    <tr>
                        <th>Photo</th>
                        <th>Staff Info</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Leave Balance</th>
                        <th>Hire Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $member)
                        <tr>
                            <td>
                                <img src="{{ $member->profile_picture_url }}"
                                     alt="{{ $member->full_name }}"
                                     class="img-circle elevation-2"
                                     style="width: 40px; height: 40px;">
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $member->full_name }}</strong>
                                    @if($member->is_admin)
                                        <span class="badge badge-warning badge-sm ml-1">Admin</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $member->staff_id }} | {{ $member->email }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $member->position }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $member->department }}</span>
                            </td>
                            <td>
                                @if($member->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($member->is_admin)
                                    <span class="badge badge-danger">Administrator</span>
                                @else
                                    <span class="badge badge-primary">Staff</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-bold">{{ $member->annual_leave_balance }}</span> days
                            </td>
                            <td>
                                <small>{{ $member->hire_date->format('M d, Y') }}</small>
                                <br>
                                <small class="text-muted">{{ $member->hire_date->diffForHumans() }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.staff.show', $member) }}"
                                       class="btn btn-info btn-sm"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.staff.edit', $member) }}"
                                       class="btn btn-warning btn-sm"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($member->id !== auth()->guard('staff')->id())
                                        <button type="button"
                                                class="btn btn-danger btn-sm"
                                                title="Delete"
                                                onclick="confirmDelete({{ $member->id }}, '{{ $member->full_name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Staff Members Found</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'status', 'role', 'department', 'gender']))
                        Try adjusting your search criteria or <a href="{{ route('admin.staff.index') }}">clear filters</a>.
                    @else
                        <a href="{{ route('admin.staff.create') }}" class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i> Add First Staff Member
                        </a>
                    @endif
                </p>
            </div>
        @endif
    </div>
    @if($staff->hasPages())
        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted">
                        Showing {{ $staff->firstItem() }} to {{ $staff->lastItem() }} of {{ $staff->total() }} results
                    </p>
                </div>
                <div class="col-md-6">
                    {{ $staff->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="staffName"></strong>?</p>
                <p class="text-muted">This action cannot be undone. All related data will be lost.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i> Delete Staff
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .info-box:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #2c3e50;
        }

        .btn-group .btn {
            margin: 0 1px;
        }

        .card-tools .badge {
            font-size: 0.9rem;
        }
    </style>
@stop

@section('js')
    <script>
        function confirmDelete(staffId, staffName) {
            $('#staffName').text(staffName);
            $('#deleteForm').attr('action', '/admin/staff/' + staffId);
            $('#deleteModal').modal('show');
        }

        // Auto-submit form on filter change
        $('#status, #role, #department, #gender').change(function() {
            $(this).closest('form').submit();
        });

        // Success message auto-hide
        setTimeout(function() {
            $('.alert-success').fadeOut();
        }, 5000);
    </script>
@stop
