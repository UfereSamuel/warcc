@extends('adminlte::page')

@section('title', 'Hero Slides Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Hero Slides Management</h1>
        <a href="{{ route('admin.content.hero-slides.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Slide
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manage Hero Slides</h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $heroSlides->total() }} slides</span>
                </div>
            </div>

            <div class="card-body">
                @if($heroSlides->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Image</th>
                                    <th>Title</th>
                                    <th>Subtitle</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-slides">
                                @foreach($heroSlides as $slide)
                                <tr data-id="{{ $slide->id }}">
                                    <td>
                                        <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}"
                                             class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong>{{ $slide->title }}</strong>
                                        @if($slide->description)
                                        <br><small class="text-muted">{{ Str::limit($slide->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $slide->subtitle ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $slide->order_index }}</span>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input status-toggle"
                                                   id="status-{{ $slide->id }}" data-id="{{ $slide->id }}"
                                                   {{ $slide->status === 'active' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="status-{{ $slide->id }}">
                                                <span class="badge badge-{{ $slide->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($slide->status) }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $slide->creator->full_name ?? 'Unknown' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.content.hero-slides.edit', $slide) }}"
                                               class="btn btn-sm btn-info" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-slide"
                                                    data-id="{{ $slide->id }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $heroSlides->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Hero Slides Found</h4>
                        <p class="text-muted">Create your first hero slide to get started.</p>
                        <a href="{{ route('admin.content.hero-slides.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add First Slide
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this hero slide? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Status toggle
    $('.status-toggle').change(function() {
        const slideId = $(this).data('id');
        const isChecked = $(this).is(':checked');

        $.post(`/admin/content/hero-slides/${slideId}/toggle-status`, {
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                const badge = $(`.status-toggle[data-id="${slideId}"]`).next().find('.badge');
                badge.removeClass('badge-success badge-secondary');
                badge.addClass(response.status === 'active' ? 'badge-success' : 'badge-secondary');
                badge.text(response.status.charAt(0).toUpperCase() + response.status.slice(1));

                toastr.success(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to update status');
            $(this).prop('checked', !isChecked);
        });
    });

    // Delete slide
    $('.delete-slide').click(function() {
        const slideId = $(this).data('id');
        $('#deleteForm').attr('action', `/admin/content/hero-slides/${slideId}`);
        $('#deleteModal').modal('show');
    });

    // Success/Error messages
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
});
</script>
@stop
