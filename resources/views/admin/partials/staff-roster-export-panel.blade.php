@php
    $exportParams = array_filter([
        'week' => $weekStart->toDateString(),
        'status' => $status,
        'position_id' => $positionId,
        'search' => $search,
    ], fn ($value) => $value !== null && $value !== '');
@endphp

<div class="card card-outline card-success mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-export mr-2"></i>Export</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h5 class="mb-2">Roster detail (CSV)</h5>
                <p class="text-muted small mb-3">
                    Exports the staff list matching your filters above for
                    <strong>{{ $staffRoster['week_label'] }}</strong>.
                    @if($status || $positionId || $search)
                        Current filters are applied.
                    @else
                        All statuses and positions are included.
                    @endif
                </p>
                <a href="{{ route('admin.export.staff-roster', $exportParams) }}"
                   class="btn btn-success">
                    <i class="fas fa-file-csv mr-1"></i> Export roster CSV
                </a>
                <span class="text-muted small ml-2">{{ $entries->count() }} staff</span>
            </div>
            <div class="col-lg-6">
                <h5 class="mb-2">Weekly status summary (CSV)</h5>
                <p class="text-muted small mb-3">
                    Excel-friendly counts by status per week (Monday week start).
                    Summary ends at the week selected in the filters.
                </p>
                <form method="GET" action="{{ route('admin.export.staff-roster-summary') }}" class="form-inline flex-wrap">
                    <input type="hidden" name="week" value="{{ $weekStart->toDateString() }}">
                    <div class="form-group mr-2 mb-2">
                        <label for="summary_weeks" class="sr-only">Number of weeks</label>
                        <select name="weeks" id="summary_weeks" class="form-control form-control-sm">
                            @foreach([4, 8, 12, 26, 52] as $weekOption)
                                <option value="{{ $weekOption }}" @selected($weekOption === 12)>
                                    Last {{ $weekOption }} weeks
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-success mb-2">
                        <i class="fas fa-table mr-1"></i> Export summary CSV
                    </button>
                </form>
                <div class="mt-2">
                    @foreach([4, 12] as $quickWeeks)
                        <a href="{{ route('admin.export.staff-roster-summary', [
                            'week' => $weekStart->toDateString(),
                            'weeks' => $quickWeeks,
                        ]) }}"
                           class="btn btn-xs btn-outline-secondary mr-1 mb-1">
                            Quick: {{ $quickWeeks }} weeks
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
