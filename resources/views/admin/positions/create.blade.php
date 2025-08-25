@extends('adminlte::page')

@section('title', 'Create New Position')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Create New Position</h1>
            <p class="text-muted">Add a new job position to the organization</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.positions.index') }}">Positions</a></li>
                <li class="breadcrumb-item active">Create Position</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Create Position Form -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-plus mr-2"></i>
                    New Position Details
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.positions.store') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="title"><strong>Position Title</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" 
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
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
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
                            <i class="fas fa-save mr-2"></i>Create Position
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
        <!-- Information Card -->
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Position Guidelines
                </h3>
            </div>
            <div class="card-body">
                <h6><strong>Best Practices:</strong></h6>
                <ul class="mb-3">
                    <li>Use clear, descriptive titles</li>
                    <li>Include seniority level if applicable</li>
                    <li>Be specific about the role</li>
                    <li>Use consistent naming conventions</li>
                </ul>

                <h6><strong>Examples:</strong></h6>
                <ul class="mb-3">
                    <li><strong>Good:</strong> "Senior Program Officer"</li>
                    <li><strong>Good:</strong> "Technical Officer Digital Systems"</li>
                    <li><strong>Avoid:</strong> "Officer" (too vague)</li>
                    <li><strong>Avoid:</strong> "Manager" (unclear level)</li>
                </ul>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Note:</strong> Position titles should be professional and accurately reflect the role's responsibilities and level within the organization.
                </div>
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




