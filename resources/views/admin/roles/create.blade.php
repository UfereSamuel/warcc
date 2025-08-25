@extends('adminlte::page')

@section('title', 'Create New Role')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Create New Role</h1>
            <p class="text-muted">Add a new role with specific permissions</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Create Role</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-shield mr-2"></i>
                    New Role Information
                </h3>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Role Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary border-bottom pb-2">Role Details</h5>

                            <div class="form-group">
                                <label for="name">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       placeholder="e.g., Project Manager, Team Lead" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Enter a descriptive name for the role
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Note:</strong> Core system roles (Super Admin, Administrator, Staff) are protected and cannot be created.
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary border-bottom pb-2">Permissions</h5>
                            <p class="text-muted mb-3">Select the permissions this role should have:</p>

                            @foreach($permissions as $category => $categoryPermissions)
                                <div class="permission-category mb-3">
                                    <h6 class="text-primary mb-2">
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
                                                       {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Create Role
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
    border-left: 4px solid #007bff;
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
</style>
@stop



