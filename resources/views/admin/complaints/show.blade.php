@extends('adminlte::page')

@section('title', 'Complaint Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-file-alt"></i> Complaint Details</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('admin.complaints.download', $complaint->id) }}" class="btn btn-success">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Complaint Details -->
            <div class="card">
                <div class="card-header {{ $complaint->is_reviewed ? 'bg-success' : 'bg-warning' }}">
                    <h3 class="card-title">
                        <i class="fas fa-{{ $complaint->is_reviewed ? 'check-circle' : 'exclamation-triangle' }}"></i>
                        {{ $complaint->complaint_number }}
                    </h3>
                    <div class="card-tools">
                        <form action="{{ route('admin.complaints.toggle-review', $complaint->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light">
                                @if($complaint->is_reviewed)
                                    <i class="fas fa-times"></i> Mark as Unreviewed
                                @else
                                    <i class="fas fa-check"></i> Mark as Reviewed
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Category -->
                    <div class="form-group">
                        <label><i class="fas fa-list"></i> Category</label>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-info badge-lg mr-2" style="font-size: 1.1em;">
                                {{ $complaint->category_label }}
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#changeCategoryModal">
                                <i class="fas fa-edit"></i> Change Category
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label><i class="fas fa-file-alt"></i> Complaint Description</label>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($complaint->description)) !!}
                        </div>
                    </div>

                    <!-- Suggested Solution -->
                    @if($complaint->suggested_solution)
                    <div class="form-group">
                        <label><i class="fas fa-lightbulb"></i> Suggested Solution</label>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($complaint->suggested_solution)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Evidence -->
                    @if($complaint->evidence_path)
                    <div class="form-group">
                        <label><i class="fas fa-paperclip"></i> Evidence Attachment</label>
                        <div>
                            @php
                                $extension = pathinfo($complaint->evidence_path, PATHINFO_EXTENSION);
                            @endphp
                            
                            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <a href="{{ asset('storage/' . $complaint->evidence_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $complaint->evidence_path) }}" 
                                         alt="Evidence" 
                                         class="img-fluid border rounded" 
                                         style="max-width: 100%; max-height: 400px;">
                                </a>
                            @elseif($extension == 'pdf')
                                <a href="{{ asset('storage/' . $complaint->evidence_path) }}" 
                                   target="_blank" 
                                   class="btn btn-primary">
                                    <i class="fas fa-file-pdf"></i> View PDF Document
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $complaint->evidence_path) }}" 
                                   target="_blank" 
                                   class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download File
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title"><i class="fas fa-sticky-note"></i> Admin Notes (Internal)</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.complaints.update-notes', $complaint->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <textarea name="admin_notes" 
                                      class="form-control" 
                                      rows="6" 
                                      placeholder="Add internal notes about this complaint, actions taken, follow-up needed, etc...">{{ $complaint->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Notes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Submission Information -->
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Submission Info</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Complaint ID:</dt>
                        <dd class="col-sm-7"><strong>{{ $complaint->complaint_number }}</strong></dd>

                        <dt class="col-sm-5">Submitted:</dt>
                        <dd class="col-sm-7">
                            {{ $complaint->created_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $complaint->created_at->format('h:i A') }}</small><br>
                            <small class="text-muted">({{ $complaint->created_at->diffForHumans() }})</small>
                        </dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @if($complaint->is_reviewed)
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Reviewed
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i> Unreviewed
                                </span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Type:</dt>
                        <dd class="col-sm-7">
                            @if($complaint->is_anonymous)
                                <span class="badge badge-secondary">
                                    <i class="fas fa-user-secret"></i> Anonymous
                                </span>
                            @else
                                <span class="badge badge-primary">
                                    <i class="fas fa-user"></i> Identified
                                </span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">IP Address:</dt>
                        <dd class="col-sm-7">
                            <code>{{ $complaint->ip_address ?? 'N/A' }}</code>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Complainant Information (if not anonymous) -->
            @if(!$complaint->is_anonymous)
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-user"></i> Complainant Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8"><strong>{{ $complaint->complainant_name }}</strong></dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">
                            <a href="mailto:{{ $complaint->complainant_email }}">
                                {{ $complaint->complainant_email }}
                            </a>
                        </dd>

                        @if($complaint->complainant_phone)
                        <dt class="col-sm-4">Phone:</dt>
                        <dd class="col-sm-8">{{ $complaint->complainant_phone }}</dd>
                        @endif

                        @if($complaint->staff_id)
                        <dt class="col-sm-4">Staff ID:</dt>
                        <dd class="col-sm-8">
                            @if($complaint->staff)
                                <a href="{{ route('admin.staff.show', $complaint->staff_id) }}">
                                    {{ $complaint->staff->staff_id }}
                                </a>
                            @else
                                {{ $complaint->staff_id }}
                            @endif
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-header bg-danger">
                    <h3 class="card-title"><i class="fas fa-cogs"></i> Actions</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" 
                          method="POST" 
                          data-warcc-confirm="Are you sure you want to delete this complaint? This action cannot be undone.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Delete Complaint
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Category Modal -->
    <div class="modal fade" id="changeCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.complaints.update-category', $complaint->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Change Complaint Category</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category">Select New Category <span class="text-danger">*</span></label>
                            <select class="form-control" id="category" name="category" required>
                                @foreach($categories as $slug => $name)
                                    <option value="{{ $slug }}" {{ $complaint->category === $slug ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop


