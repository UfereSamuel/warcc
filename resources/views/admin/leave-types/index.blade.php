@extends('adminlte::page')

@section('title', 'Leave Types Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Leave Types Management</h1>
            <p class="text-muted">Manage leave categories and policies</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Leave Types</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>
                    Leave Types
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.leave-types.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add Leave Type
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($leaveTypes->count() > 0)
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th class="text-center">Max Days</th>
                                <th class="text-center">Requires Approval</th>
                                <th class="text-center">Usage</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveTypes as $leaveType)
                                <tr>
                                    <td>
                                        <strong>{{ $leaveType->name }}</strong>
                                    </td>
                                    <td>
                                        {{ $leaveType->description ?? 'No description provided' }}
                                    </td>
                                    <td class="text-center">
                                        @if($leaveType->max_days)
                                            <span class="badge badge-info">{{ $leaveType->max_days }} days</span>
                                        @else
                                            <span class="text-muted">No limit</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($leaveType->requires_approval)
                                            <span class="badge badge-warning">Yes</span>
                                        @else
                                            <span class="badge badge-success">No</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ $leaveType->leaveRequests->count() }} requests</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.leave-types.edit', $leaveType) }}"
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.leave-types.destroy', $leaveType) }}"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this leave type?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-list fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Leave Types</h4>
                        <p class="text-muted">No leave types have been created yet.</p>
                        <a href="{{ route('admin.leave-types.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Create First Leave Type
                        </a>
                    </div>
                @endif
            </div>
            @if($leaveTypes->hasPages())
                <div class="card-footer">
                    {{ $leaveTypes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@stop
