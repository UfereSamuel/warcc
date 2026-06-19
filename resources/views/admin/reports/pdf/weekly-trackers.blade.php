@extends('admin.reports.pdf.layout')

@section('title', 'Weekly Trackers Report')

@section('content')
    <div class="section">
        <div class="section-title">Submission Summary</div>
        <table class="stat-grid">
            <tr>
                <td><div class="stat-value">{{ $trackerStats['total'] }}</div>Total</td>
                <td><div class="stat-value">{{ $trackerStats['submitted'] }}</div>Submitted</td>
                <td><div class="stat-value">{{ $trackerStats['reviewed'] }}</div>Reviewed</td>
                <td><div class="stat-value">{{ $trackerStats['approved'] }}</div>Approved</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Submissions ({{ $trackers->count() }})</div>
        @if($trackers->count() > 0)
            <table>
                <tr>
                    <th>Staff</th>
                    <th>Position</th>
                    <th>Week Starting</th>
                    <th class="text-center">Status</th>
                </tr>
                @foreach($trackers as $tracker)
                <tr>
                    <td>{{ $tracker->staff->full_name ?? 'Unknown' }}</td>
                    <td>{{ $tracker->staff?->position?->title ?? 'Unassigned' }}</td>
                    <td>{{ \Carbon\Carbon::parse($tracker->week_start_date)->format('M d, Y') }}</td>
                    <td class="text-center">{{ ucfirst($tracker->status) }}</td>
                </tr>
                @endforeach
            </table>
        @else
            <p>No weekly tracker submissions for the selected period.</p>
        @endif
    </div>
@endsection
