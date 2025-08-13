@extends('layouts.staff')

@section('title', 'Activity Calendar')
@section('page-title', 'Activity Calendar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Activity Calendar</li>
@endsection

@section('content')
<!-- Simple Filters -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filters
                </h3>
                <div class="card-tools">
                    <button class="btn btn-tool" type="button" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('staff.calendar.index') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Search activities...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="">All Types</option>
                                    @foreach($filterOptions['types'] as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    @foreach($filterOptions['statuses'] as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_from">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                       value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_to">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('staff.calendar.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times mr-1"></i> Clear Filters
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Showing {{ $activities->count() }} of {{ $activities->total() }} activities
                                @if(request()->hasAny(['search', 'type', 'status', 'date_from', 'date_to']))
                                    (filtered)
                                @endif
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Toggle Controls -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar mr-2"></i>
                    Activities & Events
                </h3>
                <div class="card-tools">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="listViewBtn">
                            <i class="fas fa-list mr-1"></i> List View
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="calendarViewBtn">
                            <i class="fas fa-calendar-alt mr-1"></i> Calendar View
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar View -->
<div class="row" id="calendarView">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- List View -->
<div class="row" id="listView" style="display: none;">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body table-responsive p-0">
                @if($activities->count() > 0)
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Date Range</th>
                                <th>Location</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr class="activity-row" data-activity-id="{{ $activity->id }}">
                                    <td>
                                        <strong>{{ $activity->title }}</strong>
                                        @if($activity->description)
                                            <br><small class="text-muted">{{ Str::limit($activity->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $activity->type_color }}">{{ $activity->type_label }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $activity->start_date->format('M d, Y') }}</strong>
                                        @if($activity->start_date->ne($activity->end_date))
                                            <br><small class="text-muted">to {{ $activity->end_date->format('M d, Y') }}</small>
                                        @endif
                                        <br><span class="badge badge-{{ $activity->status_color }} badge-sm">{{ ucfirst(str_replace('_', ' ', $activity->status)) }}</span>
                                    </td>
                                    <td>
                                        {{ $activity->location ?? 'Not specified' }}
                                    </td>
                                    <td>
                                        <small>{{ $activity->creator->full_name ?? 'Unknown' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Activities</h4>
                        <p class="text-muted">No activities or events have been created yet.</p>
                    </div>
                @endif
            </div>
            @if($activities->hasPages())
                <div class="card-footer">
                    {{ $activities->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Activity Details Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activityModalTitle">Activity Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="activityModalBody">
                <!-- Activity details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filter Buttons -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-2"></i>
                    Quick Filters
                </h3>
            </div>
            <div class="card-body">
                <div class="btn-group mr-2 mb-2" role="group">
                    <a href="{{ route('staff.calendar.index') }}"
                       class="btn btn-sm {{ !request()->hasAny(['type', 'status', 'date_from', 'date_to', 'search']) ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-list mr-1"></i> All Activities
                    </a>
                    <a href="{{ route('staff.calendar.index', ['date_from' => now()->format('Y-m-d')]) }}"
                       class="btn btn-sm {{ request('date_from') == now()->format('Y-m-d') && !request()->hasAny(['type', 'status', 'date_to', 'search']) ? 'btn-info' : 'btn-outline-info' }}">
                        <i class="fas fa-calendar-day mr-1"></i> From Today
                    </a>
                    <a href="{{ route('staff.calendar.index', ['date_to' => now()->format('Y-m-d')]) }}"
                       class="btn btn-sm {{ request('date_to') == now()->format('Y-m-d') && !request()->hasAny(['type', 'status', 'date_from', 'search']) ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="fas fa-history mr-1"></i> Past Activities
                    </a>
                </div>

                <div class="btn-group mr-2 mb-2" role="group">
                    <a href="{{ route('staff.calendar.index', ['type' => 'meeting']) }}"
                       class="btn btn-sm {{ request('type') == 'meeting' && !request()->hasAny(['status', 'date_from', 'date_to', 'search']) ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-users mr-1"></i> Meetings
                    </a>
                    <a href="{{ route('staff.calendar.index', ['type' => 'training']) }}"
                       class="btn btn-sm {{ request('type') == 'training' && !request()->hasAny(['status', 'date_from', 'date_to', 'search']) ? 'btn-info' : 'btn-outline-info' }}">
                        <i class="fas fa-graduation-cap mr-1"></i> Training
                    </a>
                    <a href="{{ route('staff.calendar.index', ['type' => 'event']) }}"
                       class="btn btn-sm {{ request('type') == 'event' && !request()->hasAny(['status', 'date_from', 'date_to', 'search']) ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="fas fa-calendar-check mr-1"></i> Events
                    </a>
                    <a href="{{ route('staff.calendar.index', ['type' => 'holiday']) }}"
                       class="btn btn-sm {{ request('type') == 'holiday' && !request()->hasAny(['status', 'date_from', 'date_to', 'search']) ? 'btn-warning' : 'btn-outline-warning' }}">
                        <i class="fas fa-calendar-minus mr-1"></i> Holidays
                    </a>
                    <a href="{{ route('staff.calendar.index', ['type' => 'deadline']) }}"
                       class="btn btn-sm {{ request('type') == 'deadline' && !request()->hasAny(['status', 'date_from', 'date_to', 'search']) ? 'btn-danger' : 'btn-outline-danger' }}">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Deadlines
                    </a>
                </div>

                <div class="btn-group mr-2 mb-2" role="group">
                    <a href="{{ route('staff.calendar.index', ['status' => 'not_yet_started']) }}"
                       class="btn btn-sm {{ request('status') == 'not_yet_started' && !request()->hasAny(['type', 'date_from', 'date_to', 'search']) ? 'btn-info' : 'btn-outline-info' }}">
                        <i class="fas fa-clock mr-1"></i> Upcoming
                    </a>
                    <a href="{{ route('staff.calendar.index', ['status' => 'ongoing']) }}"
                       class="btn btn-sm {{ request('status') == 'ongoing' && !request()->hasAny(['type', 'date_from', 'date_to', 'search']) ? 'btn-warning' : 'btn-outline-warning' }}">
                        <i class="fas fa-play mr-1"></i> Ongoing
                    </a>
                    <a href="{{ route('staff.calendar.index', ['status' => 'done']) }}"
                       class="btn btn-sm {{ request('status') == 'done' && !request()->hasAny(['type', 'date_from', 'date_to', 'search']) ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="fas fa-check mr-1"></i> Completed
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('styles')
<style>
    /* FullCalendar Customization */
    .fc-toolbar-title {
        font-size: 1.5rem !important;
        color: #348F41 !important;
        font-weight: 600 !important;
    }

    .fc-button-primary {
        background-color: #348F41 !important;
        border-color: #348F41 !important;
    }

    .fc-button-primary:hover {
        background-color: #2d7a36 !important;
        border-color: #2d7a36 !important;
    }

    .fc-event {
        border: none !important;
        border-radius: 4px !important;
        font-size: 0.85rem !important;
        font-weight: 500 !important;
    }

    .fc-event:hover {
        cursor: pointer !important;
        opacity: 0.8 !important;
    }

    /* View Toggle Buttons */
    .btn-group .btn.active {
        background-color: #348F41 !important;
        border-color: #348F41 !important;
        color: white !important;
    }

    /* Calendar Container */
    #calendar {
        max-width: 100%;
        margin: 0 auto;
    }

    /* Activity Type Colors */
    .fc-event.meeting {
        background-color: #007bff !important;
    }

    .fc-event.training {
        background-color: #17a2b8 !important;
    }

    .fc-event.event {
        background-color: #28a745 !important;
    }

    .fc-event.holiday {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .fc-event.deadline {
        background-color: #dc3545 !important;
    }

    /* List View Enhancements */
    .activity-row:hover {
        background-color: #f8f9fa !important;
        cursor: pointer;
    }

    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    // Build events URL with current filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    let eventsUrl = '{{ route('staff.calendar.api.events') }}';
    if (urlParams.toString()) {
        eventsUrl += '?' + urlParams.toString();
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        events: eventsUrl,
        eventClick: function(info) {
            showActivityDetails(info.event);
        },
        eventDidMount: function(info) {
            // Add activity type class for styling
            info.el.classList.add(info.event.extendedProps.type);
        }
    });

    // View Toggle Functionality
    const listViewBtn = document.getElementById('listViewBtn');
    const calendarViewBtn = document.getElementById('calendarViewBtn');
    const listView = document.getElementById('listView');
    const calendarView = document.getElementById('calendarView');

    listViewBtn.addEventListener('click', function() {
        listView.style.display = 'block';
        calendarView.style.display = 'none';
        listViewBtn.classList.add('btn-primary');
        listViewBtn.classList.remove('btn-outline-primary');
        calendarViewBtn.classList.add('btn-outline-primary');
        calendarViewBtn.classList.remove('btn-primary');

        // Store preference
        localStorage.setItem('staffCalendarView', 'list');
    });

    calendarViewBtn.addEventListener('click', function() {
        listView.style.display = 'none';
        calendarView.style.display = 'block';
        calendar.render(); // Re-render calendar when shown
        calendarViewBtn.classList.add('btn-primary');
        calendarViewBtn.classList.remove('btn-outline-primary');
        listViewBtn.classList.add('btn-outline-primary');
        listViewBtn.classList.remove('btn-primary');

        // Store preference
        localStorage.setItem('staffCalendarView', 'calendar');
    });

    // Restore view preference
    const savedView = localStorage.getItem('staffCalendarView');
    if (savedView === 'list') {
        listViewBtn.click();
    } else {
        // Initially render calendar
        calendar.render();
    }

    // List View Row Click Handler
    document.querySelectorAll('.activity-row').forEach(row => {
        row.addEventListener('click', function() {
            const activityId = this.dataset.activityId;
            const title = this.querySelector('td:first-child strong').textContent;
            const description = this.querySelector('td:first-child small') ?
                this.querySelector('td:first-child small').textContent : '';

            showActivityDetailsFromRow(this, title, description, activityId);
        });
    });

    // Filter functionality
    const filterForm = document.getElementById('filterForm');

    // Auto-submit on dropdown changes
    document.getElementById('type').addEventListener('change', function() {
        filterForm.submit();
    });

    document.getElementById('status').addEventListener('change', function() {
        filterForm.submit();
    });

    // Auto-submit on date changes
    document.getElementById('date_from').addEventListener('change', function() {
        filterForm.submit();
    });

    document.getElementById('date_to').addEventListener('change', function() {
        filterForm.submit();
    });

    // Search with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                filterForm.submit();
            }
        }, 500);
    });

    // Show Activity Details Modal (from calendar click)
    function showActivityDetails(event) {
        const modalTitle = document.getElementById('activityModalTitle');
        const modalBody = document.getElementById('activityModalBody');

        modalTitle.textContent = event.title;

        const startDate = event.start.toLocaleDateString();
        const endDate = event.end ? event.end.toLocaleDateString() : startDate;
        const dateRange = startDate === endDate ? startDate : startDate + ' - ' + endDate;

        modalBody.innerHTML =
            '<div class="row">' +
                '<div class="col-md-6">' +
                    '<h6><strong>Type:</strong></h6>' +
                    '<span class="badge badge-' + getTypeColor(event.extendedProps.type) + ' mb-2">' + (event.extendedProps.type_label || event.extendedProps.type) + '</span>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<h6><strong>Status:</strong></h6>' +
                    '<span class="badge badge-' + getStatusColor(event.extendedProps.status) + ' mb-2">' + formatStatus(event.extendedProps.status) + '</span>' +
                '</div>' +
            '</div>' +
            '<div class="row mt-3">' +
                '<div class="col-md-12">' +
                    '<h6><strong>Date Range:</strong></h6>' +
                    '<p><i class="fas fa-calendar mr-1"></i> ' + dateRange + '</p>' +
                '</div>' +
            '</div>' +
            (event.extendedProps.description ?
                '<div class="row">' +
                    '<div class="col-md-12">' +
                        '<h6><strong>Description:</strong></h6>' +
                        '<p>' + event.extendedProps.description + '</p>' +
                    '</div>' +
                '</div>' : '') +
            (event.extendedProps.location ?
                '<div class="row">' +
                    '<div class="col-md-12">' +
                        '<h6><strong>Location:</strong></h6>' +
                        '<p><i class="fas fa-map-marker-alt mr-1"></i> ' + event.extendedProps.location + '</p>' +
                    '</div>' +
                '</div>' : '') +
            (event.extendedProps.creator ?
                '<div class="row">' +
                    '<div class="col-md-12">' +
                        '<h6><strong>Created by:</strong></h6>' +
                        '<p><i class="fas fa-user mr-1"></i> ' + event.extendedProps.creator + '</p>' +
                    '</div>' +
                '</div>' : '');

        $('#activityModal').modal('show');
    }

    // Show details from list view row click
    function showActivityDetailsFromRow(row, title, description, activityId) {
        const modalTitle = document.getElementById('activityModalTitle');
        const modalBody = document.getElementById('activityModalBody');

        modalTitle.textContent = title;

        const typeElement = row.querySelector('.badge');
        const typeText = typeElement ? typeElement.textContent : 'Unknown';
        const statusElements = row.querySelectorAll('.badge');
        const statusElement = statusElements.length > 1 ? statusElements[1] : null;
        const statusText = statusElement ? statusElement.textContent : 'Unknown';
        const dateText = row.querySelector('td:nth-child(3) strong').textContent;
        const dateSubtext = row.querySelector('td:nth-child(3) small');
        const fullDateText = dateSubtext ? dateText + ' ' + dateSubtext.textContent : dateText;
        const locationCell = row.querySelector('td:nth-child(4)');
        const locationText = locationCell.textContent.trim();
        const creatorCell = row.querySelector('td:nth-child(5)');
        const creatorText = creatorCell ? creatorCell.textContent.trim() : 'Unknown';

        modalBody.innerHTML =
            '<div class="row">' +
                '<div class="col-md-6">' +
                    '<h6><strong>Type:</strong></h6>' +
                    '<span class="badge badge-primary mb-2">' + typeText + '</span>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<h6><strong>Status:</strong></h6>' +
                    '<span class="badge badge-success mb-2">' + statusText + '</span>' +
                '</div>' +
            '</div>' +
            '<div class="row mt-3">' +
                '<div class="col-md-12">' +
                    '<h6><strong>Date Range:</strong></h6>' +
                    '<p><i class="fas fa-calendar mr-1"></i> ' + fullDateText + '</p>' +
                '</div>' +
            '</div>' +
            (description ?
                '<div class="row">' +
                    '<div class="col-md-12">' +
                        '<h6><strong>Description:</strong></h6>' +
                        '<p>' + description + '</p>' +
                    '</div>' +
                '</div>' : '') +
            (locationText !== 'Not specified' ?
                '<div class="row">' +
                    '<div class="col-md-12">' +
                        '<h6><strong>Location:</strong></h6>' +
                        '<p><i class="fas fa-map-marker-alt mr-1"></i> ' + locationText + '</p>' +
                    '</div>' +
                '</div>' : '') +
            '<div class="row">' +
                '<div class="col-md-12">' +
                    '<h6><strong>Created by:</strong></h6>' +
                    '<p><i class="fas fa-user mr-1"></i> ' + creatorText + '</p>' +
                '</div>' +
            '</div>';

        $('#activityModal').modal('show');
    }

    // Helper functions
    function getTypeColor(type) {
        const colors = {
            'meeting': 'primary',
            'training': 'info',
            'event': 'success',
            'holiday': 'warning',
            'deadline': 'danger'
        };
        return colors[type] || 'secondary';
    }

    function getStatusColor(status) {
        const colors = {
            'done': 'success',
            'ongoing': 'warning',
            'not_yet_started': 'info'
        };
        return colors[status] || 'secondary';
    }

    function formatStatus(status) {
        return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
});
</script>
@endpush
