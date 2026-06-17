@extends('adminlte::page')

@section('title', 'Complaint Categories')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-list"></i> Complaint Categories</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Complaints
            </a>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Add New Category
            </button>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Complaint Categories</h3>
            <div class="card-tools">
                <span class="badge badge-info">{{ $categories->count() }} Categories</span>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="10%">Order</th>
                        <th width="30%">Category Name</th>
                        <th width="20%">Slug</th>
                        <th width="15%">Complaints Count</th>
                        <th width="10%">Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-categories">
                    @forelse($categories as $index => $category)
                        <tr data-id="{{ $category->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge badge-secondary">{{ $category->sort_order }}</span>
                            </td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>
                                <code>{{ $category->slug }}</code>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $category->complaints()->count() }} complaints
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.complaints.categories.toggle', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $category->is_active ? 'success' : 'secondary' }}">
                                        @if($category->is_active)
                                            <i class="fas fa-check-circle"></i> Active
                                        @else
                                            <i class="fas fa-times-circle"></i> Inactive
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" 
                                            class="btn btn-sm btn-warning edit-category" 
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-order="{{ $category->sort_order }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.complaints.categories.destroy', $category->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure? This will fail if the category has complaints.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No categories found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alert Box -->
    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> Important Notes:</h5>
        <ul class="mb-0">
            <li>Categories determine the dropdown options in the complaint submission form</li>
            <li>Inactive categories won't appear in the form but existing complaints remain unchanged</li>
            <li>You cannot delete categories that have associated complaints</li>
            <li>Sort order determines the display order in the form</li>
        </ul>
    </div>
@stop

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.complaints.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_name">Category Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="category_name" 
                               name="name" 
                               placeholder="e.g., IT Issues"
                               required>
                        <small class="form-text text-muted">The slug will be automatically generated</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_category_name">Category Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_category_name" 
                               name="name" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="edit_sort_order">Sort Order <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control" 
                               id="edit_sort_order" 
                               name="sort_order" 
                               min="0"
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('js')
<script>
$(document).ready(function() {
    // Edit category
    $('.edit-category').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var order = $(this).data('order');
        
        $('#edit_category_name').val(name);
        $('#edit_sort_order').val(order);
        $('#editCategoryForm').attr('action', '/admin/complaints/categories/' + id);
        
        $('#editCategoryModal').modal('show');
    });
});
</script>
@stop


