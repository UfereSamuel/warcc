@extends('adminlte::page')

@section('title', 'Edit Role - ' . $role->name)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Role</h1>
            <p class="text-muted">{{ $role->name }} - {{ $role->permissions->count() }} permissions</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">{{ $role->name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    Edit Role Information
                </h3>
            </div>

            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- Role Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-warning border-bottom pb-2">Role Details</h5>

                            <div class="form-group">
                                <label for="name">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $role->name) }}"
                                       placeholder="e.g., Project Manager, Team Lead" required
                                       {{ in_array($role->name, ['Super Admin', 'Administrator', 'Staff']) ? 'readonly' : '' }}>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Enter a descriptive name for the role
                                </small>
                            </div>

                            @if(in_array($role->name, ['Super Admin', 'Administrator', 'Staff']))
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Protected Role:</strong> This is a core system role and cannot be renamed.
                                </div>
                            @endif

                            <div class="role-stats mt-3">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="info-box bg-info">
                                            <div class="info-box-content p-2 text-center">
                                                <span class="info-box-number">{{ $role->permissions->count() }}</span>
                                                <span class="info-box-text">Permissions</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box bg-success">
                                            <div class="info-box-content p-2 text-center">
                                                <span class="info-box-number">{{ \App\Models\Staff::role($role->name)->count() }}</span>
                                                <span class="info-box-text">Users</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-warning border-bottom pb-2">Permissions</h5>
                            <p class="text-muted mb-3">Select the permissions this role should have:</p>

                            @foreach($permissions as $category => $categoryPermissions)
                                <div class="permission-category mb-3">
                                    <h6 class="text-warning mb-2">
                                        <i class="fas fa-folder mr-1"></i>{{ $category }}
                                    </h6>
                                    <div class="permission-list">
                                        @foreach($categoryPermissions as $permission)
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input" 
                                                       id="perm_{{ $permission->id }}"
                                                       name="permissions[]" 
                                                       value="{{ $permission->name }}"
                                                       {{ in_array($permission->name, old('permissions', $role->permissions->pluck('name')->toArray())) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="perm_{{ $permission->id }}">
                                                    {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-2"></i>Update Role
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.permission-category {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #ffc107;
}

.permission-list {
    max-height: 200px;
    overflow-y: auto;
}

.custom-checkbox {
    margin-bottom: 8px;
}

.custom-control-label {
    font-size: 0.9rem;
    color: #495057;
}

.role-stats .info-box {
    min-height: 60px;
}

.role-stats .info-box-content {
    padding: 10px;
}

.role-stats .info-box-number {
    font-size: 1.5rem;
    font-weight: bold;
    display: block;
}

.role-stats .info-box-text {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
@stop
