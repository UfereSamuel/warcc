@extends('adminlte::page')

@section('title', 'System Settings')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>System Settings</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">System Settings</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs mr-2"></i>
                    Website Configuration
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetSettings()" title="Reset to Default">
                        <i class="fas fa-undo mr-1"></i>
                        Reset to Default
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" id="settings-form">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                <i class="fas fa-globe mr-1"></i>
                                General
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab">
                                <i class="fas fa-address-book mr-1"></i>
                                Contact Info
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="social-tab" data-toggle="tab" href="#social" role="tab">
                                <i class="fas fa-share-alt mr-1"></i>
                                Social Media
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="media-tab" data-toggle="tab" href="#media" role="tab">
                                <i class="fab fa-youtube mr-1"></i>
                                Media & YouTube
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="system-tab" data-toggle="tab" href="#system" role="tab">
                                <i class="fas fa-cog mr-1"></i>
                                System
                            </a>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="settingsTabsContent">
                        <!-- General Settings -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3">
                                        <i class="fas fa-globe text-primary mr-2"></i>
                                        General Website Information
                                    </h5>
                                </div>
                            </div>

                            @foreach($generalSettings as $setting)
                                <div class="form-group row">
                                    <label for="{{ $setting->key }}" class="col-sm-3 col-form-label">
                                        {{ $setting->label }}
                                        @if($setting->description)
                                            <i class="fas fa-info-circle text-muted ml-1" title="{{ $setting->description }}"></i>
                                        @endif
                                    </label>
                                    <div class="col-sm-9">
                                        @if($setting->type === 'textarea')
                                            <textarea class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                      id="{{ $setting->key }}" 
                                                      name="settings[{{ $setting->key }}]" 
                                                      rows="3" 
                                                      placeholder="{{ $setting->description }}">{{ old('settings.'.$setting->key, $setting->value) }}</textarea>
                                        @elseif($setting->type === 'image')
                                            @if($setting->value)
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}" class="img-thumbnail" style="max-height: 100px;">
                                                </div>
                                            @endif
                                            <input type="file" class="form-control-file @error('files.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="files[{{ $setting->key }}]" 
                                                   accept="image/*">
                                            <input type="hidden" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                                        @else
                                            <input type="text" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                                   placeholder="{{ $setting->description }}">
                                        @endif
                                        
                                        @if($setting->description)
                                            <small class="form-text text-muted">{{ $setting->description }}</small>
                                        @endif
                                        
                                        @error('settings.'.$setting->key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('files.'.$setting->key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Contact Settings -->
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3">
                                        <i class="fas fa-address-book text-info mr-2"></i>
                                        Contact Information
                                    </h5>
                                </div>
                            </div>

                            @foreach($contactSettings as $setting)
                                <div class="form-group row">
                                    <label for="{{ $setting->key }}" class="col-sm-3 col-form-label">
                                        {{ $setting->label }}
                                        @if($setting->description)
                                            <i class="fas fa-info-circle text-muted ml-1" title="{{ $setting->description }}"></i>
                                        @endif
                                    </label>
                                    <div class="col-sm-9">
                                        @if($setting->type === 'textarea')
                                            <textarea class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                      id="{{ $setting->key }}" 
                                                      name="settings[{{ $setting->key }}]" 
                                                      rows="3" 
                                                      placeholder="{{ $setting->description }}">{{ old('settings.'.$setting->key, $setting->value) }}</textarea>
                                        @elseif($setting->type === 'email')
                                            <input type="email" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                                   placeholder="{{ $setting->description }}">
                                        @elseif($setting->type === 'phone')
                                            <input type="tel" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                                   placeholder="{{ $setting->description }}">
                                        @elseif($setting->type === 'url')
                                            <input type="url" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                                   placeholder="{{ $setting->description }}">
                                        @else
                                            <input type="text" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                                   placeholder="{{ $setting->description }}">
                                        @endif
                                        
                                        @if($setting->description)
                                            <small class="form-text text-muted">{{ $setting->description }}</small>
                                        @endif
                                        
                                        @error('settings.'.$setting->key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Social Media Settings -->
                        <div class="tab-pane fade" id="social" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3">
                                        <i class="fas fa-share-alt text-success mr-2"></i>
                                        Social Media Links
                                    </h5>
                                    <p class="text-muted mb-4">Add your social media profile URLs. Leave blank to hide from website.</p>
                                </div>
                            </div>

                            @foreach($socialSettings as $setting)
                                <div class="form-group row">
                                    <label for="{{ $setting->key }}" class="col-sm-3 col-form-label">
                                        <i class="fab fa-{{ str_replace('social_', '', $setting->key) }} mr-2"></i>
                                        {{ $setting->label }}
                                        @if($setting->description)
                                            <i class="fas fa-info-circle text-muted ml-1" title="{{ $setting->description }}"></i>
                                        @endif
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="url" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                               id="{{ $setting->key }}" 
                                               name="settings[{{ $setting->key }}]" 
                                               value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                               placeholder="{{ $setting->description }}">
                                        
                                        @if($setting->description)
                                            <small class="form-text text-muted">{{ $setting->description }}</small>
                                        @endif
                                        
                                        @error('settings.'.$setting->key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Media & YouTube Settings -->
                        <div class="tab-pane fade" id="media" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3">
                                        <i class="fab fa-youtube text-danger mr-2"></i>
                                        Media & YouTube Integration
                                    </h5>
                                    <p class="text-muted mb-4">Configure YouTube channel integration to display videos, livestreams, and press releases on your website.</p>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <strong>Setup Instructions:</strong>
                                        <ol class="mb-0 mt-2">
                                            <li>Get your YouTube Channel ID from YouTube Studio → Settings → Channel → Advanced</li>
                                            <li>Copy your full YouTube channel URL</li>
                                            <li>Optional: Create a YouTube Data API key in Google Cloud Console for enhanced features</li>
                                            <li>Configure how many videos to show on your homepage</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            @foreach($mediaSettings as $setting)
                                <div class="form-group row">
                                    <label for="{{ $setting->key }}" class="col-sm-3 col-form-label">
                                        <i class="fab fa-youtube text-danger mr-1"></i>
                                        {{ $setting->label }}
                                        @if($setting->description)
                                            <i class="fas fa-info-circle text-muted ml-1" title="{{ $setting->description }}"></i>
                                        @endif
                                    </label>
                                    <div class="col-sm-9">
                                        @if($setting->type === 'boolean')
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="{{ $setting->key }}" 
                                                       name="settings[{{ $setting->key }}]" 
                                                       value="1" 
                                                       {{ old('settings.'.$setting->key, $setting->value) == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="{{ $setting->key }}">
                                                    {{ $setting->description }}
                                                </label>
                                            </div>
                                        @elseif($setting->type === 'number')
                                            <input type="number" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                                   min="0" max="20"
                                                   placeholder="{{ $setting->description }}">
                                        @elseif($setting->type === 'url')
                                            <input type="url" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                                   placeholder="{{ $setting->description }}">
                                        @else
                                            <input type="text" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                                   placeholder="{{ $setting->description }}">
                                        @endif
                                        
                                        @if($setting->description && $setting->type !== 'boolean')
                                            <small class="form-text text-muted">{{ $setting->description }}</small>
                                        @endif
                                        
                                        @error('settings.'.$setting->key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            <!-- YouTube Preview Section -->
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label class="col-form-label">
                                        <i class="fas fa-eye text-primary mr-1"></i>
                                        Preview
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <p class="text-muted mb-3">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Once configured, YouTube content will appear in the following locations:
                                            </p>
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-home text-primary mr-2"></i>
                                                    <strong>Homepage:</strong> Latest videos section (if enabled)
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-play-circle text-danger mr-2"></i>
                                                    <strong>Media Page:</strong> Full channel content with videos, playlists, and live streams
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-footer text-secondary mr-2"></i>
                                                    <strong>Footer:</strong> YouTube channel link (if social media URL provided)
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="tab-pane fade" id="system" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3">
                                        <i class="fas fa-cog text-warning mr-2"></i>
                                        System Configuration
                                    </h5>
                                    <p class="text-muted mb-4">Configure system-wide settings and preferences.</p>
                                </div>
                            </div>

                            @foreach($systemSettings as $setting)
                                <div class="form-group row">
                                    <label for="{{ $setting->key }}" class="col-sm-3 col-form-label">
                                        {{ $setting->label }}
                                        @if($setting->description)
                                            <i class="fas fa-info-circle text-muted ml-1" title="{{ $setting->description }}"></i>
                                        @endif
                                    </label>
                                    <div class="col-sm-9">
                                        @if($setting->type === 'boolean')
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="{{ $setting->key }}" 
                                                       name="settings[{{ $setting->key }}]" 
                                                       value="1" 
                                                       {{ old('settings.'.$setting->key, $setting->value) == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="{{ $setting->key }}">
                                                    {{ $setting->description }}
                                                </label>
                                            </div>
                                        @elseif($setting->type === 'select')
                                            @if($setting->key === 'timezone')
                                                <select class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                        id="{{ $setting->key }}" 
                                                        name="settings[{{ $setting->key }}]">
                                                    @foreach(timezone_identifiers_list() as $timezone)
                                                        <option value="{{ $timezone }}" {{ old('settings.'.$setting->key, $setting->value) === $timezone ? 'selected' : '' }}>
                                                            {{ $timezone }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @elseif($setting->key === 'date_format')
                                                <select class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                        id="{{ $setting->key }}" 
                                                        name="settings[{{ $setting->key }}]">
                                                    <option value="Y-m-d" {{ old('settings.'.$setting->key, $setting->value) === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2024-12-31)</option>
                                                    <option value="d/m/Y" {{ old('settings.'.$setting->key, $setting->value) === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2024)</option>
                                                    <option value="m/d/Y" {{ old('settings.'.$setting->key, $setting->value) === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/2024)</option>
                                                    <option value="F j, Y" {{ old('settings.'.$setting->key, $setting->value) === 'F j, Y' ? 'selected' : '' }}>Month Day, Year (December 31, 2024)</option>
                                                </select>
                                            @endif
                                        @else
                                            <input type="text" class="form-control @error('settings.'.$setting->key) is-invalid @enderror" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                                   placeholder="{{ $setting->description }}">
                                        @endif
                                        
                                        @if($setting->description && $setting->type !== 'boolean')
                                            <small class="form-text text-muted">{{ $setting->description }}</small>
                                        @endif
                                        
                                        @error('settings.'.$setting->key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                Save Settings
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Reset Settings
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset all settings to their default values?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will overwrite all your current settings.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.settings.reset') }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo mr-1"></i>
                        Reset to Default
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
function resetSettings() {
    $('#resetModal').modal('show');
}

// Initialize tooltips
$(document).ready(function() {
    $('[title]').tooltip();
    
    // Form validation
    $('#settings-form').on('submit', function(e) {
        var hasError = false;
        
        // Validate required fields
        $(this).find('input[required], textarea[required], select[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                hasError = true;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (hasError) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
    
    // Remove validation errors on input
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@stop
