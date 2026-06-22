<div class="row mb-3">
    <div class="col-12">
        <div class="card card-outline card-info collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-rss mr-2"></i>
                    Subscribe in Outlook or Google Calendar
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Add RCC activities to your personal calendar. Events sync one-way from WARCC (read-only in Outlook/Google).
                    Calendars typically refresh every hour.
                </p>

                <div class="form-group mb-3">
                    <label for="calendarFeedUrl">Subscription URL</label>
                    <div class="input-group">
                        <input type="text"
                               class="form-control"
                               id="calendarFeedUrl"
                               value="{{ $staff->calendar_feed_url }}"
                               readonly>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="copyFeedUrlBtn" title="Copy URL">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted" id="copyFeedUrlStatus"></small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fab fa-microsoft mr-1"></i> Microsoft Outlook</h6>
                        <ol class="small pl-3 mb-0">
                            <li>Open Outlook Calendar on the web or desktop app.</li>
                            <li>Choose <strong>Add calendar</strong> → <strong>Subscribe from web</strong>.</li>
                            <li>Paste the subscription URL above and confirm.</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fab fa-google mr-1"></i> Google Calendar</h6>
                        <ol class="small pl-3 mb-0">
                            <li>Open Google Calendar in your browser.</li>
                            <li>Click <strong>+</strong> next to <em>Other calendars</em> → <strong>From URL</strong>.</li>
                            <li>Paste the subscription URL above and add the calendar.</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Keep this link private — anyone with it can view RCC activities.
                </small>
                <form method="POST" action="{{ route('staff.calendar.feed.regenerate') }}"
                      data-warcc-confirm="Regenerating will invalidate your current link. You must re-subscribe in Outlook/Google. Continue?">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-sync-alt mr-1"></i> Regenerate Link
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyBtn = document.getElementById('copyFeedUrlBtn');
    const feedInput = document.getElementById('calendarFeedUrl');
    const statusEl = document.getElementById('copyFeedUrlStatus');

    if (!copyBtn || !feedInput) {
        return;
    }

    copyBtn.addEventListener('click', function() {
        feedInput.select();
        feedInput.setSelectionRange(0, feedInput.value.length);

        const copied = function() {
            statusEl.textContent = 'Copied to clipboard.';
            statusEl.classList.remove('text-danger');
            statusEl.classList.add('text-success');
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(feedInput.value).then(copied).catch(function() {
                document.execCommand('copy');
                copied();
            });
        } else {
            document.execCommand('copy');
            copied();
        }
    });
});
</script>
@endpush
