@extends('adminlte::page')

@section('title', 'Countries Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-globe-africa"></i> Countries Management</h1>
        </div>
        <div class="col-sm-6 text-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCountryModal">
                <i class="fas fa-plus"></i> Add New Country
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
            <h3 class="card-title">Manage Countries in West Africa</h3>
            <div class="card-tools">
                <span class="badge badge-info">{{ $countries->count() }} Countries</span>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="8%">Order</th>
                        <th width="10%">Flag</th>
                        <th width="20%">Country Name</th>
                        <th width="25%">Official Name</th>
                        <th width="12%">Flag Code</th>
                        <th width="10%">Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $index => $country)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge badge-secondary">{{ $country->sort_order }}</span>
                            </td>
                            <td>
                                <img src="{{ $country->flag_url }}" 
                                     alt="{{ $country->name }} Flag" 
                                     class="img-fluid" 
                                     style="height: 30px;">
                            </td>
                            <td>
                                <strong>{{ $country->name }}</strong>
                            </td>
                            <td>
                                {{ $country->official_name }}
                            </td>
                            <td>
                                <code>{{ strtoupper($country->flag_code) }}</code>
                            </td>
                            <td>
                                <form action="{{ route('admin.countries.toggle', $country->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $country->is_active ? 'success' : 'secondary' }}">
                                        @if($country->is_active)
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
                                            class="btn btn-sm btn-warning edit-country" 
                                            data-id="{{ $country->id }}"
                                            data-name="{{ $country->name }}"
                                            data-official="{{ $country->official_name }}"
                                            data-flag="{{ $country->flag_code }}"
                                            data-order="{{ $country->sort_order }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.countries.destroy', $country->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          data-warcc-confirm="Are you sure you want to delete {{ $country->name }}?">
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
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No countries found.</p>
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
            <li>These countries will be displayed on the public homepage</li>
            <li>Inactive countries won't appear on the homepage but remain in the database</li>
            <li>Flag codes are ISO 3166-1 alpha-2 codes (e.g., GH for Ghana, NG for Nigeria)</li>
            <li>Sort order determines the display order on the homepage</li>
            <li>Flags are fetched from <a href="https://flagcdn.com" target="_blank">flagcdn.com</a></li>
        </ul>
    </div>
@stop

<!-- Add Country Modal -->
<div class="modal fade" id="addCountryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.countries.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add New Country</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Country Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               placeholder="e.g., Ghana"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="official_name">Official Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="official_name" 
                               name="official_name" 
                               placeholder="e.g., Republic of Ghana"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="flag_code">Flag Code (ISO 2-letter) <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="flag_code" 
                               name="flag_code" 
                               placeholder="e.g., gh"
                               maxlength="2"
                               pattern="[a-zA-Z]{2}"
                               required>
                        <small class="form-text text-muted">
                            2-letter ISO code (e.g., GH, NG, SN). 
                            <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank">View codes</a>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Country
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Country Modal -->
<div class="modal fade" id="editCountryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCountryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Country</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Country Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_name" 
                               name="name" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="edit_official_name">Official Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_official_name" 
                               name="official_name" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="edit_flag_code">Flag Code (ISO 2-letter) <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_flag_code" 
                               name="flag_code" 
                               maxlength="2"
                               pattern="[a-zA-Z]{2}"
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
                        <i class="fas fa-save"></i> Update Country
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('js')
<script>
$(document).ready(function() {
    // Edit country
    $('.edit-country').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var official = $(this).data('official');
        var flag = $(this).data('flag');
        var order = $(this).data('order');
        
        $('#edit_name').val(name);
        $('#edit_official_name').val(official);
        $('#edit_flag_code').val(flag);
        $('#edit_sort_order').val(order);
        $('#editCountryForm').attr('action', '/admin/countries/' + id);
        
        $('#editCountryModal').modal('show');
    });
});
</script>
@stop

