@extends('adminlte::page')

@section('title', 'Roles & Permissions Management')

@section('content_header')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark d-flex align-items-center">
                <i class="fas fa-user-shield text-primary mr-3"></i>
                Roles & Permissions
            </h1>
            <p class="text-muted mt-2 mb-0">
                <i class="fas fa-info-circle mr-1"></i>
                Manage system roles, permissions, and user access levels
            </p>
        </div>
        <div class="col-sm-6">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb bg-transparent mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <i class="fas fa-home mr-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-primary">
                        <i class="fas fa-user-shield mr-1"></i>Roles & Permissions
                    </li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<!-- System Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card primary">
            <div class="stats-card-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-card-number">{{ $roles->count() }}</div>
                <div class="stats-card-title">Total Roles</div>
                <div class="stats-card-subtitle">System roles configured</div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card success">
            <div class="stats-card-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-card-number">{{ $permissions->flatten()->count() }}</div>
                <div class="stats-card-title">Permissions</div>
                <div class="stats-card-subtitle">Available permissions</div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card warning">
            <div class="stats-card-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-card-number">{{ $permissions->count() }}</div>
                <div class="stats-card-title">Categories</div>
                <div class="stats-card-subtitle">Permission groups</div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stats-card info">
            <div class="stats-card-icon">
                <i class="fas fa-crown"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-card-number">{{ $roles->where('name', '!=', 'Staff')->count() }}</div>
                <div class="stats-card-title">Admin Roles</div>
                <div class="stats-card-subtitle">Administrative levels</div>
            </div>
        </div>
    </div>
</div>

<!-- System Roles Management -->
<div class="content-section">
    <div class="section-header">
        <div class="section-title">
            <i class="fas fa-users-cog text-primary mr-2"></i>
            <span>System Roles</span>
        </div>
        <div class="section-actions">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-2"></i>Create New Role
            </a>
        </div>
    </div>
    
    <div class="row">
        @foreach($roles as $role)
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="role-card 
                @if($role->name === 'Super Admin') role-card-super-admin
                @elseif($role->name === 'Administrator') role-card-admin
                @else role-card-staff @endif">
                
                <div class="role-card-header">
                    <div class="role-icon">
                        @if($role->name === 'Super Admin')
                            <i class="fas fa-crown"></i>
                        @elseif($role->name === 'Administrator')
                            <i class="fas fa-user-shield"></i>
                        @else
                            <i class="fas fa-users"></i>
                        @endif
                    </div>
                    <div class="role-info">
                        <h4 class="role-name">{{ $role->name }}</h4>
                        <div class="role-badges">
                            <span class="role-badge permissions">
                                <i class="fas fa-key mr-1"></i>{{ $role->permissions->count() }} Permissions
                            </span>
                            <span class="role-badge users">
                                <i class="fas fa-users mr-1"></i>{{ \App\Models\Staff::role($role->name)->count() }} Users
                            </span>
                        </div>
                    </div>
                </div>

                @if($role->permissions->count() > 0)
                <div class="role-card-body">
                    <div class="permissions-preview">
                        <h6 class="permissions-title">
                            <i class="fas fa-shield-alt mr-1"></i>Key Permissions
                        </h6>
                        <div class="permissions-list">
                            @foreach($role->permissions->take(4) as $permission)
                                <span class="permission-tag">
                                    {{ str_replace('_', ' ', ucwords($permission->name, '_')) }}
                                </span>
                            @endforeach
                            @if($role->permissions->count() > 4)
                                <span class="permission-tag more">
                                    +{{ $role->permissions->count() - 4 }} more
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="role-card-footer">
                    <div class="role-actions">
                        <a href="{{ route('admin.roles.edit', $role) }}" 
                           class="btn-action edit" 
                           title="Edit Role">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        
                        @if(!in_array($role->name, ['Super Admin', 'Administrator', 'Staff']))
                            <form method="POST" 
                                  action="{{ route('admin.roles.destroy', $role) }}" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to delete the {{ $role->name }} role?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action delete" title="Delete Role">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        @else
                            <span class="btn-action protected" title="Protected System Role">
                                <i class="fas fa-lock mr-1"></i>Protected
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Permissions Overview -->
<div class="content-section">
    <div class="section-header">
        <div class="section-title">
            <i class="fas fa-shield-alt text-success mr-2"></i>
            <span>Available Permissions</span>
        </div>
        <div class="section-actions">
            <button type="button" class="btn btn-outline-secondary" data-toggle="collapse" data-target="#permissionsCollapse">
                <i class="fas fa-eye mr-1"></i>Toggle View
            </button>
        </div>
    </div>
    
    <div class="collapse show" id="permissionsCollapse">
        <div class="permissions-grid">
            @foreach($permissions as $category => $categoryPermissions)
            <div class="permission-category">
                <div class="category-header">
                    <div class="category-icon">
                        @if($category === 'Manage')
                            <i class="fas fa-cogs"></i>
                        @elseif($category === 'View')
                            <i class="fas fa-eye"></i>
                        @elseif($category === 'Create')
                            <i class="fas fa-plus"></i>
                        @elseif($category === 'Edit')
                            <i class="fas fa-edit"></i>
                        @elseif($category === 'Delete')
                            <i class="fas fa-trash"></i>
                        @elseif($category === 'Export')
                            <i class="fas fa-download"></i>
                        @elseif($category === 'Approve')
                            <i class="fas fa-check-circle"></i>
                        @else
                            <i class="fas fa-key"></i>
                        @endif
                    </div>
                    <div class="category-info">
                        <h5 class="category-name">{{ $category }}</h5>
                        <span class="category-count">{{ $categoryPermissions->count() }} permissions</span>
                    </div>
                </div>
                
                <div class="category-permissions">
                    @foreach($categoryPermissions as $permission)
                        <div class="permission-item">
                            <i class="fas fa-key permission-icon"></i>
                            <span class="permission-name">
                                {{ str_replace('_', ' ', ucwords($permission->name, '_')) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* ===== MODERN DESIGN SYSTEM ===== */

/* Statistics Cards */
.stats-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
}

.stats-card.primary { --accent-color: #007bff; --accent-light: #4dabf7; }
.stats-card.success { --accent-color: #28a745; --accent-light: #51cf66; }
.stats-card.warning { --accent-color: #ffc107; --accent-light: #ffd43b; }
.stats-card.info { --accent-color: #17a2b8; --accent-light: #3bc9db; }

.stats-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
    margin-right: 20px;
    box-shadow: 0 4px 15px rgba(var(--accent-color), 0.3);
}

.stats-card-content {
    padding: 20px;
    display: flex;
    align-items: center;
}

.stats-card-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
    margin-bottom: 5px;
}

.stats-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #34495e;
    margin-bottom: 2px;
}

.stats-card-subtitle {
    font-size: 0.9rem;
    color: #7f8c8d;
}

/* Section Layout */
.content-section {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
    overflow: hidden;
}

.section-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 25px 30px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: between;
    align-items: center;
}

.section-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    flex: 1;
}

.section-actions {
    margin-left: auto;
}

.section-actions .btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 8px 20px;
    transition: all 0.2s ease;
}

/* Role Cards */
.role-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    margin: 15px;
}

.role-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.role-card-super-admin {
    border-left: 5px solid #dc3545;
}

.role-card-admin {
    border-left: 5px solid #ffc107;
}

.role-card-staff {
    border-left: 5px solid #17a2b8;
}

.role-card-header {
    padding: 25px;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
}

.role-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    margin-right: 20px;
}

.role-card-super-admin .role-icon {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
}

.role-card-admin .role-icon {
    background: linear-gradient(135deg, #ffc107, #f39c12);
}

.role-card-staff .role-icon {
    background: linear-gradient(135deg, #17a2b8, #3498db);
}

.role-name {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.role-badges {
    display: flex;
    gap: 10px;
}

.role-badge {
    background: #e9ecef;
    color: #495057;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.role-badge.permissions {
    background: #e3f2fd;
    color: #1976d2;
}

.role-badge.users {
    background: #f3e5f5;
    color: #7b1fa2;
}

.role-card-body {
    padding: 20px 25px;
}

.permissions-title {
    font-size: 1rem;
    font-weight: 600;
    color: #34495e;
    margin-bottom: 12px;
}

.permissions-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.permission-tag {
    background: #f8f9fa;
    color: #495057;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.permission-tag:hover {
    background: #e9ecef;
    border-color: #dee2e6;
}

.permission-tag.more {
    background: #e3f2fd;
    color: #1976d2;
    border-color: #bbdefb;
}

.role-card-footer {
    padding: 20px 25px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.role-actions {
    display: flex;
    gap: 10px;
}

.btn-action {
    flex: 1;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.9rem;
    text-decoration: none;
    text-align: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-action.edit {
    background: #e3f2fd;
    color: #1976d2;
    border: 1px solid #bbdefb;
}

.btn-action.edit:hover {
    background: #1976d2;
    color: white;
    text-decoration: none;
}

.btn-action.delete {
    background: #ffebee;
    color: #d32f2f;
    border: 1px solid #ffcdd2;
}

.btn-action.delete:hover {
    background: #d32f2f;
    color: white;
}

.btn-action.protected {
    background: #f5f5f5;
    color: #757575;
    border: 1px solid #e0e0e0;
    cursor: not-allowed;
}

/* Permissions Grid */
.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    padding: 30px;
}

.permission-category {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.permission-category:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-color: #007bff;
}

.category-header {
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
}

.category-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.category-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2px;
}

.category-count {
    font-size: 0.9rem;
    color: #7f8c8d;
}

.category-permissions {
    padding: 20px;
}

.permission-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.permission-item:last-child {
    border-bottom: none;
}

.permission-icon {
    color: #007bff;
    margin-right: 12px;
    font-size: 0.9rem;
}

.permission-name {
    color: #495057;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .section-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .section-actions {
        margin-left: 0;
    }
    
    .stats-card-content {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-card-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .role-card-header {
        flex-direction: column;
        text-align: center;
    }
    
    .role-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .permissions-grid {
        grid-template-columns: 1fr;
        padding: 20px;
    }
}

/* Hover Effects */
.stats-card:hover .stats-card-icon {
    transform: scale(1.1);
}

.permission-item:hover {
    background: #f8f9fa;
    border-radius: 6px;
    padding-left: 12px;
    padding-right: 12px;
}

.permission-item:hover .permission-icon {
    color: #0056b3;
    transform: scale(1.1);
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stats-card, .role-card, .permission-category {
    animation: fadeInUp 0.6s ease forwards;
}
</style>
@stop
