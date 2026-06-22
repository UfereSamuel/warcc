@extends('adminlte::page')

@section('title', 'Website Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-globe-americas"></i> Website Management</h1>
            <p class="text-muted mb-0">Edit public homepage content — super administrator only</p>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('home') }}" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Public Site
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-home"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Homepage Content</span>
                    <a href="{{ route('admin.content.homepage') }}" class="info-box-number text-info">Edit</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-info-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">About Page</span>
                    <a href="{{ route('admin.content.about') }}" class="info-box-number text-warning">Edit</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-images"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Hero Slides</span>
                    <a href="{{ route('admin.content.hero-slides.index') }}" class="info-box-number text-primary">Manage</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-cogs"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Site Settings</span>
                    <a href="{{ route('admin.settings.index') }}" class="info-box-number text-secondary">Manage</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-bullhorn"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Public Events</span>
                    <a href="{{ route('admin.public-events.index') }}" class="info-box-number text-success">Manage</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-purple"><i class="fas fa-th-large"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Content Hub</span>
                    <a href="{{ route('admin.content.index') }}" class="info-box-number text-purple">Open</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-secondary mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-image mr-2"></i> Default Hero (fallback when no slides)</h3>
        </div>
        <form method="POST" action="{{ route('admin.website-management.default-hero.update') }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="default_hero_title">Headline</label>
                    <input type="text" class="form-control" id="default_hero_title" name="default_hero_title"
                           value="{{ old('default_hero_title', $content['default_hero_title']) }}" required>
                </div>
                <div class="form-group mb-0">
                    <label for="default_hero_description">Description</label>
                    <textarea class="form-control" id="default_hero_description" name="default_hero_description" rows="3" required>{{ old('default_hero_description', $content['default_hero_description']) }}</textarea>
                    <small class="form-text text-muted">Shown on the homepage when no hero slides are configured.</small>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-save mr-1"></i> Save Default Hero
                </button>
            </div>
        </form>
    </div>

    <div class="card card-outline card-info mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-eye mr-2"></i> Vision &amp; Mission Statements</h3>
        </div>
        <form method="POST" action="{{ route('admin.website-management.vision-mission.update') }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <p class="text-muted">These statements appear on the homepage and the About page.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="organization_mission_title">Mission Title</label>
                            <input type="text" class="form-control" id="organization_mission_title" name="organization_mission_title"
                                   value="{{ old('organization_mission_title', $content['organization_mission_title']) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="organization_mission_text">Mission Statement</label>
                            <textarea class="form-control" id="organization_mission_text" name="organization_mission_text" rows="5" required>{{ old('organization_mission_text', $content['organization_mission_text']) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="organization_vision_title">Vision Title</label>
                            <input type="text" class="form-control" id="organization_vision_title" name="organization_vision_title"
                                   value="{{ old('organization_vision_title', $content['organization_vision_title']) }}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="organization_vision_text">Vision Statement</label>
                            <textarea class="form-control" id="organization_vision_text" name="organization_vision_text" rows="5" required>{{ old('organization_vision_text', $content['organization_vision_text']) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-save mr-1"></i> Save Vision &amp; Mission
                </button>
            </div>
        </form>
    </div>

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-bullseye mr-2"></i> Mission Focus Areas</h3>
        </div>
        <form method="POST" action="{{ route('admin.website-management.mission-section.update') }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="mission_title">Section Title</label>
                    <input type="text" class="form-control" id="mission_title" name="mission_title"
                           value="{{ old('mission_title', $content['mission_title']) }}" required>
                </div>
                <div class="form-group">
                    <label for="mission_description">Section Description</label>
                    <textarea class="form-control" id="mission_description" name="mission_description" rows="3" required>{{ old('mission_description', $content['mission_description']) }}</textarea>
                </div>
                <hr>
                <h5 class="mb-3">Feature Cards</h5>
                @foreach($content['mission_cards'] as $index => $card)
                    @php $n = $index + 1; @endphp
                    <div class="border rounded p-3 mb-3 bg-light">
                        <h6 class="text-muted">Card {{ $n }}</h6>
                        <div class="form-group mb-2">
                            <label for="mission_card_{{ $n }}_title">Title</label>
                            <input type="text" class="form-control" id="mission_card_{{ $n }}_title"
                                   name="mission_card_{{ $n }}_title"
                                   value="{{ old('mission_card_'.$n.'_title', $card['title']) }}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="mission_card_{{ $n }}_text">Description</label>
                            <textarea class="form-control" id="mission_card_{{ $n }}_text"
                                      name="mission_card_{{ $n }}_text" rows="2" required>{{ old('mission_card_'.$n.'_text', $card['text']) }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save Mission Focus Areas
                </button>
            </div>
        </form>
    </div>

    <div class="card card-outline card-success mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-globe-africa mr-2"></i> Serving West Africa Section</h3>
        </div>
        <form method="POST" action="{{ route('admin.website-management.serving-section.update') }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="serving_title">Section Title</label>
                    <input type="text" class="form-control" id="serving_title" name="serving_title"
                           value="{{ old('serving_title', $content['serving_title']) }}" required>
                </div>
                <div class="form-group mb-0">
                    <label for="serving_description">Section Description</label>
                    <textarea class="form-control" id="serving_description" name="serving_description" rows="3" required>{{ old('serving_description', $content['serving_description']) }}</textarea>
                    <small class="form-text text-muted">
                        Use <code>{count}</code> where the number of active countries should appear (currently {{ $countries->where('is_active', true)->count() }}).
                    </small>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-1"></i> Save Serving Section
                </button>
            </div>
        </form>
    </div>

    <div class="card card-outline card-warning mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i> Featured Events Section</h3>
        </div>
        <form method="POST" action="{{ route('admin.website-management.featured-events-section.update') }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="featured_events_title">Section Title</label>
                    <input type="text" class="form-control" id="featured_events_title" name="featured_events_title"
                           value="{{ old('featured_events_title', $content['featured_events_title']) }}" required>
                </div>
                <div class="form-group mb-0">
                    <label for="featured_events_description">Section Description</label>
                    <textarea class="form-control" id="featured_events_description" name="featured_events_description" rows="3" required>{{ old('featured_events_description', $content['featured_events_description']) }}</textarea>
                    <small class="form-text text-muted">Individual events are managed under Public Events.</small>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i> Save Featured Events Section
                </button>
            </div>
        </form>
    </div>

    <div class="card card-outline card-purple mb-4" style="border-top-color:#6f42c1;">
        <div class="card-header" style="border-top-color:#6f42c1;">
            <h3 class="card-title"><i class="fas fa-heart mr-2"></i> Core Values Section</h3>
        </div>
        <form method="POST" action="{{ route('admin.website-management.core-values-section.update') }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="core_values_title">Section Title</label>
                    <input type="text" class="form-control" id="core_values_title" name="core_values_title"
                           value="{{ old('core_values_title', $content['core_values_title']) }}" required>
                </div>
                <div class="form-group">
                    <label for="core_values_description">Section Description</label>
                    <textarea class="form-control" id="core_values_description" name="core_values_description" rows="2" required>{{ old('core_values_description', $content['core_values_description']) }}</textarea>
                </div>
                <hr>
                <h5 class="mb-3">Value Cards</h5>
                @foreach($content['core_values_cards'] as $index => $card)
                    @php $n = $index + 1; @endphp
                    <div class="border rounded p-3 mb-3 bg-light">
                        <h6 class="text-muted">Card {{ $n }}</h6>
                        <div class="form-group mb-2">
                            <label for="core_values_card_{{ $n }}_title">Title</label>
                            <input type="text" class="form-control" id="core_values_card_{{ $n }}_title"
                                   name="core_values_card_{{ $n }}_title"
                                   value="{{ old('core_values_card_'.$n.'_title', $card['title']) }}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="core_values_card_{{ $n }}_text">Description</label>
                            <textarea class="form-control" id="core_values_card_{{ $n }}_text"
                                      name="core_values_card_{{ $n }}_text" rows="2" required>{{ old('core_values_card_'.$n.'_text', $card['text']) }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary" style="background-color:#6f42c1;border-color:#6f42c1;">
                    <i class="fas fa-save mr-1"></i> Save Core Values
                </button>
            </div>
        </form>
    </div>

    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-flag mr-2"></i> West African Countries</h3>
            <div class="card-tools">
                <span class="badge badge-info">{{ $countries->count() }} total</span>
                <button type="button" class="btn btn-primary btn-sm ml-2" data-toggle="modal" data-target="#addCountryModal">
                    <i class="fas fa-plus"></i> Add Country
                </button>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
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
                            <td><span class="badge badge-secondary">{{ $country->sort_order }}</span></td>
                            <td>
                                <img src="{{ $country->flag_url }}" alt="{{ $country->name }} Flag" class="img-fluid" style="height: 30px;">
                            </td>
                            <td><strong>{{ $country->name }}</strong></td>
                            <td>{{ $country->official_name }}</td>
                            <td><code>{{ strtoupper($country->flag_code) }}</code></td>
                            <td>
                                <form action="{{ route('admin.website-management.countries.toggle', $country->id) }}" method="POST" class="d-inline">
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
                                    <button type="button" class="btn btn-sm btn-warning edit-country"
                                            data-id="{{ $country->id }}"
                                            data-name="{{ $country->name }}"
                                            data-official="{{ $country->official_name }}"
                                            data-flag="{{ $country->flag_code }}"
                                            data-order="{{ $country->sort_order }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.website-management.countries.destroy', $country->id) }}"
                                          method="POST" class="d-inline"
                                          data-warcc-confirm="Delete {{ $country->name }}?">
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
                            <td colspan="8" class="text-center py-4 text-muted">No countries configured.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <small class="text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Active countries appear on the homepage grid. Flags use ISO 2-letter codes from
                <a href="https://flagcdn.com" target="_blank">flagcdn.com</a>.
            </small>
        </div>
    </div>

    <div class="modal fade" id="addCountryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.website-management.countries.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add Country</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Country Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Ghana" required>
                        </div>
                        <div class="form-group">
                            <label for="official_name">Official Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="official_name" name="official_name" placeholder="e.g., Republic of Ghana" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="flag_code">Flag Code (ISO 2-letter) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="flag_code" name="flag_code" placeholder="e.g., gh" maxlength="2" pattern="[a-zA-Z]{2}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_official_name">Official Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_official_name" name="official_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_flag_code">Flag Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_flag_code" name="flag_code" maxlength="2" pattern="[a-zA-Z]{2}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="edit_sort_order">Sort Order <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('.edit-country').on('click', function() {
        $('#edit_name').val($(this).data('name'));
        $('#edit_official_name').val($(this).data('official'));
        $('#edit_flag_code').val($(this).data('flag'));
        $('#edit_sort_order').val($(this).data('order'));
        $('#editCountryForm').attr('action', '{{ url('admin/website-management/countries') }}/' + $(this).data('id'));
        $('#editCountryModal').modal('show');
    });
});
</script>
@stop
