@extends('adminlte::page')

@section('title', 'Public Events Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Public Events</h1>
            <p class="text-muted">Manage public events displayed on the website</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Public Events</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Events</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['published'] }}</h3>
                <p>Published</p>
            </div>
            <div class="icon">
                <i class="fas fa-eye"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['draft'] }}</h3>
                <p>Drafts</p>
            </div>
            <div class="icon">
                <i class="fas fa-edit"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>{{ $stats['featured'] }}</h3>
                <p>Featured</p>
            </div>
            <div class="icon">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bullhorn mr-2"></i>
                    Public Events
                </h3>
                <div class="card-tools">
                    <div class="btn-group mr-3">
                        <a href="{{ route('admin.public-events.index') }}"
                           class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                            All
                        </a>
                        <a href="{{ route('admin.public-events.index', ['status' => 'published']) }}"
                           class="btn btn-sm {{ request('status') == 'published' ? 'btn-success' : 'btn-outline-success' }}">
                            Published
                        </a>
                        <a href="{{ route('admin.public-events.index', ['status' => 'draft']) }}"
                           class="btn btn-sm {{ request('status') == 'draft' ? 'btn-warning' : 'btn-outline-warning' }}">
                            Drafts
                        </a>
                        <a href="{{ route('admin.public-events.index', ['status' => 'archived']) }}"
                           class="btn btn-sm {{ request('status') == 'archived' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                            Archived
                        </a>
                    </div>
                    <div class="btn-group mr-3">
                        @foreach($categories as $cat)
                            <a href="{{ route('admin.public-events.index', ['category' => $cat]) }}"
                               class="btn btn-sm {{ request('category') == $cat ? 'btn-info' : 'btn-outline-info' }}">
                                {{ ucfirst($cat) }}
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.public-events.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> New Event
                    </a>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="card-body border-bottom">
                <form id="bulk-action-form" method="POST" action="{{ route('admin.public-events.bulk-action') }}">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" name="action" required>
                                <option value="">Bulk Actions...</option>
                                <option value="publish">Publish</option>
                                <option value="unpublish">Unpublish</option>
                                <option value="archive">Archive</option>
                                <option value="feature">Feature</option>
                                <option value="unfeature">Unfeature</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary" disabled id="bulk-submit">
                                Apply
                            </button>
                        </div>
                        <div class="col-md-7 text-right">
                            <small class="text-muted">
                                Selected: <span id="selected-count">0</span> events
                            </small>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body table-responsive p-0">
                @if($events->count() > 0)
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>Event</th>
                                <th>Category</th>
                                <th>Date Range</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Created</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="event-checkbox" name="event_ids[]" value="{{ $event->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($event->featured_image)
                                                <img src="{{ $event->featured_image_url }}" alt="Event Image"
                                                     class="img-circle elevation-2 mr-3" width="40" height="40">
                                            @else
                                                <div class="bg-secondary rounded-circle mr-3 d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-calendar text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $event->title }}</strong>
                                                @if($event->summary)
                                                    <br><small class="text-muted">{{ Str::limit($event->summary, 60) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $event->category_color }}">{{ $event->category_label }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $event->formatted_date_range }}</strong>
                                        @if($event->formatted_time_range)
                                            <br><small class="text-muted">{{ $event->formatted_time_range }}</small>
                                        @endif
                                        @if($event->location)
                                            <br><small class="text-info"><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $event->status_color }}">{{ $event->status_label }}</span>
                                        <br><span class="badge badge-{{ $event->event_status_color }} badge-sm mt-1">{{ $event->event_status_label }}</span>
                                    </td>
                                    <td>
                                        @if($event->is_featured)
                                            <span class="badge badge-warning">
                                                <i class="fas fa-star"></i> Featured
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $event->created_at->format('M d, Y') }}</small>
                                        <br><small class="text-muted">{{ $event->creator->full_name ?? 'Unknown' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.public-events.show', $event) }}"
                                               class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.public-events.edit', $event) }}"
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-{{ $event->status === 'published' ? 'secondary' : 'success' }}"
                                                    onclick="toggleStatus({{ $event->id }})" title="Toggle Status">
                                                <i class="fas fa-{{ $event->status === 'published' ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-{{ $event->is_featured ? 'warning' : 'outline-warning' }}"
                                                    onclick="toggleFeatured({{ $event->id }})" title="Toggle Featured">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.public-events.destroy', $event) }}"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this event?')">
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
                        <i class="fas fa-bullhorn fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Public Events</h4>
                        @if(request('status') || request('category'))
                            <p class="text-muted">No events found with the current filters.</p>
                            <a href="{{ route('admin.public-events.index') }}" class="btn btn-outline-primary mr-2">
                                <i class="fas fa-list mr-1"></i> View All Events
                            </a>
                        @else
                            <p class="text-muted">Start by creating your first public event.</p>
                        @endif
                        <a href="{{ route('admin.public-events.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Create First Event
                        </a>
                    </div>
                @endif
            </div>
            @if($events->hasPages())
                <div class="card-footer">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('js')
<script>
// Bulk selection functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.event-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkControls();
});

document.querySelectorAll('.event-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkControls);
});

function updateBulkControls() {
    const checkedBoxes = document.querySelectorAll('.event-checkbox:checked');
    const bulkSubmit = document.getElementById('bulk-submit');
    const selectedCount = document.getElementById('selected-count');

    selectedCount.textContent = checkedBoxes.length;
    bulkSubmit.disabled = checkedBoxes.length === 0;

    // Update the hidden input values in the form
    const form = document.getElementById('bulk-action-form');
    const existingInputs = form.querySelectorAll('input[name="event_ids[]"]');
    existingInputs.forEach(input => input.remove());

    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'event_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });
}

// Toggle status function
function toggleStatus(eventId) {
    fetch(`/admin/public-events/${eventId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
    });
}

// Toggle featured function
function toggleFeatured(eventId) {
    fetch(`/admin/public-events/${eventId}/toggle-featured`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating featured status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating featured status');
    });
}

// Bulk action form validation
document.getElementById('bulk-action-form').addEventListener('submit', function(e) {
    const action = this.querySelector('select[name="action"]').value;
    if (action === 'delete') {
        if (!confirm('Are you sure you want to delete the selected events? This action cannot be undone.')) {
            e.preventDefault();
        }
    }
});
</script>
@stop

@section('css')
<style>
    .table td {
        vertical-align: middle;
    }

    .small-box {
        border-radius: 8px;
    }

    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    .badge-purple {
        background-color: #6f42c1;
    }
</style>
@stop
