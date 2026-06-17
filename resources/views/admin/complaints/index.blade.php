@extends('adminlte::page')

@section('title', 'Complaints Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-exclamation-triangle"></i> Complaints Management</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.complaints.categories.index') }}" class="btn btn-info">
                <i class="fas fa-list"></i> Manage Categories
            </a>
            <form action="{{ route('admin.complaints.toggle-system') }}" method="POST" class="d-inline">
                @csrf
                @php
                    $systemSettings = \Illuminate\Support\Facades\DB::table('system_settings')->first();
                    $isEnabled = $systemSettings ? $systemSettings->complaints_system_enabled : false;
                @endphp
                <button type="submit" class="btn btn-{{ $isEnabled ? 'success' : 'danger' }}">
                    <i class="fas fa-power-off"></i> 
                    System is {{ $isEnabled ? 'ON' : 'OFF' }} - Click to {{ $isEnabled ? 'Disable' : 'Enable' }}
                </button>
            </form>
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

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total Complaints</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['unreviewed'] }}</h3>
                    <p>Unreviewed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['reviewed'] }}</h3>
                    <p>Reviewed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $stats['anonymous'] }}</h3>
                    <p>Anonymous</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-secret"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filter Complaints</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.complaints.index') }}" id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Review Status</label>
                            <select name="reviewed" class="form-control">
                                <option value="">All</option>
                                <option value="0" {{ request('reviewed') == '0' ? 'selected' : '' }}>Unreviewed</option>
                                <option value="1" {{ request('reviewed') == '1' ? 'selected' : '' }}>Reviewed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" class="form-control">
                                <option value="">All Categories</option>
                                @foreach(\App\Models\Complaint::getCategories() as $key => $label)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="anonymous" class="form-control">
                                <option value="">All</option>
                                <option value="1" {{ request('anonymous') == '1' ? 'selected' : '' }}>Anonymous</option>
                                <option value="0" {{ request('anonymous') == '0' ? 'selected' : '' }}>Identified</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <button type="button" class="btn btn-success float-right" onclick="downloadBulkPdf()">
                            <i class="fas fa-file-pdf"></i> Download Filtered as PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Complaints Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Complaints List ({{ $complaints->total() }} total)</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th width="5%">
                            <i class="fas fa-check-square"></i>
                        </th>
                        <th width="12%">Complaint #</th>
                        <th width="15%">Category</th>
                        <th width="25%">Description</th>
                        <th width="10%">Type</th>
                        <th width="10%">Submitted</th>
                        <th width="10%">Status</th>
                        <th width="13%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $complaint)
                        <tr class="{{ $complaint->is_reviewed ? '' : 'table-warning' }}">
                            <td>
                                @if(!$complaint->is_reviewed)
                                    <span class="badge badge-warning" title="Unreviewed">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </span>
                                @else
                                    <span class="badge badge-success" title="Reviewed">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $complaint->complaint_number }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $complaint->category_label }}
                                </span>
                            </td>
                            <td>
                                {{ Str::limit($complaint->description, 80) }}
                            </td>
                            <td>
                                @if($complaint->is_anonymous)
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-user-secret"></i> Anonymous
                                    </span>
                                @else
                                    <span class="badge badge-primary">
                                        <i class="fas fa-user"></i> Identified
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $complaint->created_at->format('M d, Y') }}</small><br>
                                <small class="text-muted">{{ $complaint->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <form action="{{ route('admin.complaints.toggle-review', $complaint->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $complaint->is_reviewed ? 'success' : 'warning' }}" title="Toggle Review Status">
                                        @if($complaint->is_reviewed)
                                            <i class="fas fa-check-circle"></i> Reviewed
                                        @else
                                            <i class="fas fa-eye"></i> Mark Reviewed
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.complaints.download', $complaint->id) }}" class="btn btn-sm btn-success" title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this complaint?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No complaints found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($complaints->hasPages())
        <div class="card-footer clearfix">
            {{ $complaints->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
@stop

@section('js')
<script>
function downloadBulkPdf() {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    const url = '{{ route("admin.complaints.bulk-download") }}?' + params.toString();
    window.location.href = url;
}
</script>
@stop

