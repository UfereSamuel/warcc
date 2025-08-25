@extends('adminlte::page')

@section('title', 'Edit Position')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Position</h1>
            <p class="text-muted">Update job position details</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.positions.index') }}">Positions</a></li>
                <li class="breadcrumb-item active">Edit Position</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Edit Position Form -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Position: {{ $position->title }}
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.positions.update', $position) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="title"><strong>Position Title</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $position->title) }}" 
                               placeholder="e.g., Regional Director, Finance Officer, Technical Officer Digital System"
                               required>
                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <small class="form-text text-muted">
                            Enter the full job title or designation (e.g., "Senior Software Engineer" not just "Engineer")
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                   {{ $position->is_active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                <strong>Position is active and available for staff assignment</strong>
                            </label>
                            <small class="form-text text-muted d-block">
                                Active positions can be assigned to new staff members. Inactive positions are hidden but retain existing assignments.
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save mr-2"></i>Update Position
                        </button>
                        <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Position Information -->
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Position Information
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Current Title:</strong></td>
                        <td>{{ $position->title }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @if($position->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-warning">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Staff Count:</strong></td>
                        <td>
                            <span class="badge badge-primary">{{ $position->staff_count ?? 0 }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $position->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td>{{ $position->updated_at->format('M d, Y') }}</td>
                    </tr>
                </table>

                @if($position->staff_count > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Note:</strong> This position is currently assigned to {{ $position->staff_count }} staff member(s). 
                        Changes will affect their profiles.
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-list mr-2"></i>View All Positions
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-tachometer-alt mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Danger Zone
                </h3>
            </div>
            <div class="card-body">
                @if($position->staff_count === 0)
                    <form method="POST" action="{{ route('admin.positions.destroy', $position) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this position? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash mr-2"></i>Delete Position
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary btn-block" disabled>
                        <i class="fas fa-shield-alt mr-2"></i>Protected (Has Staff)
                    </button>
                    <small class="text-muted d-block mt-2">
                        This position cannot be deleted because it has {{ $position->staff_count }} staff member(s) assigned.
                    </small>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.card {
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.form-control-lg {
    font-size: 1rem;
    padding: 0.75rem 1rem;
}
.btn-lg {
    font-size: 1rem;
    padding: 0.75rem 1.5rem;
}
.alert {
    border-radius: 10px;
}
</style>
@stop




