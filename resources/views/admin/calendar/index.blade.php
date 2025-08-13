@extends('adminlte::page')

@section('title', 'Activity Calendar Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Activity Calendar Management</h1>
            <p class="text-muted">Manage organizational activities and events</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Calendar</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
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
                    <div class="btn-group mr-3" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="listViewBtn">
                            <i class="fas fa-list mr-1"></i> List View
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="calendarViewBtn">
                            <i class="fas fa-calendar-alt mr-1"></i> Calendar View
                        </button>
                    </div>
                    <a href="{{ route('admin.calendar.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add Activity
                    </a>
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
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
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
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.calendar.edit', $activity) }}"
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.calendar.destroy', $activity) }}"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this activity?')">
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
                        <i class="fas fa-calendar fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Activities</h4>
                        <p class="text-muted">No activities or events have been created yet.</p>
                        <a href="{{ route('admin.calendar.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Create First Activity
                        </a>
                    </div>
                @endif
            </div>
            @if($activities->hasPages())
                <div class="card-footer">
                    {{ $activities->links() }}
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
                <a href="#" class="btn btn-warning" id="editActivityBtn">
                    <i class="fas fa-edit mr-1"></i> Edit Activity
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
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
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        events: '/admin/calendar/api/events',
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
    });

    calendarViewBtn.addEventListener('click', function() {
        listView.style.display = 'none';
        calendarView.style.display = 'block';
        calendar.render(); // Re-render calendar when shown
        calendarViewBtn.classList.add('btn-primary');
        calendarViewBtn.classList.remove('btn-outline-primary');
        listViewBtn.classList.add('btn-outline-primary');
        listViewBtn.classList.remove('btn-primary');
    });

    // Initially render calendar
    calendar.render();

    // Show Activity Details Modal
    function showActivityDetails(event) {
        const modalTitle = document.getElementById('activityModalTitle');
        const modalBody = document.getElementById('activityModalBody');
        const editBtn = document.getElementById('editActivityBtn');

        modalTitle.textContent = event.title;
        editBtn.href = `/admin/calendar/${event.id}/edit`;

        const startDate = event.start.toLocaleDateString();
        const endDate = event.end ? event.end.toLocaleDateString() : startDate;
        const dateRange = startDate === endDate ? startDate : `${startDate} - ${endDate}`;

        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6><strong>Type:</strong></h6>
                    <span class="badge badge-${getTypeColor(event.extendedProps.type)} mb-2">${event.extendedProps.type_label || event.extendedProps.type}</span>
                </div>
                <div class="col-md-6">
                    <h6><strong>Status:</strong></h6>
                    <span class="badge badge-${getStatusColor(event.extendedProps.status)} mb-2">${formatStatus(event.extendedProps.status)}</span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6><strong>Date Range:</strong></h6>
                    <p>${dateRange}</p>
                </div>
            </div>
            ${event.extendedProps.description ? `
            <div class="row">
                <div class="col-md-12">
                    <h6><strong>Description:</strong></h6>
                    <p>${event.extendedProps.description}</p>
                </div>
            </div>
            ` : ''}
            ${event.extendedProps.location ? `
            <div class="row">
                <div class="col-md-12">
                    <h6><strong>Location:</strong></h6>
                    <p><i class="fas fa-map-marker-alt mr-1"></i> ${event.extendedProps.location}</p>
                </div>
            </div>
            ` : ''}
        `;

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
@stop
