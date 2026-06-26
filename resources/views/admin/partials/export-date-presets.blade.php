@php
    use Carbon\Carbon;

    $startInputId = $startInputId ?? 'start_date';
    $endInputId = $endInputId ?? 'end_date';
    $today = now();

    $presets = [
        'this_week' => [
            'label' => 'This week',
            'start' => $today->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'),
            'end' => $today->format('Y-m-d'),
        ],
        'this_month' => [
            'label' => 'This month',
            'start' => $today->copy()->startOfMonth()->format('Y-m-d'),
            'end' => $today->format('Y-m-d'),
        ],
        'last_month' => [
            'label' => 'Last month',
            'start' => $today->copy()->subMonth()->startOfMonth()->format('Y-m-d'),
            'end' => $today->copy()->subMonth()->endOfMonth()->format('Y-m-d'),
        ],
    ];
@endphp

<div class="export-date-presets btn-group btn-group-sm flex-wrap mb-2" role="group" aria-label="Date range presets">
    @foreach($presets as $key => $preset)
        <button type="button"
                class="btn btn-outline-secondary export-date-preset"
                data-start-input="{{ $startInputId }}"
                data-end-input="{{ $endInputId }}"
                data-start-date="{{ $preset['start'] }}"
                data-end-date="{{ $preset['end'] }}">
            {{ $preset['label'] }}
        </button>
    @endforeach
</div>

@once
    @push('js')
        <script>
            document.addEventListener('click', function (event) {
                const button = event.target.closest('.export-date-preset');
                if (!button) return;

                const startInput = document.getElementById(button.dataset.startInput);
                const endInput = document.getElementById(button.dataset.endInput);

                if (startInput) startInput.value = button.dataset.startDate;
                if (endInput) endInput.value = button.dataset.endDate;
            });
        </script>
    @endpush
@endonce
