<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
(function () {
    let lastAiContent = '';

    window.ActivityReportAi = {
        summarizeUrl: @json($summarizeUrl ?? null),
        mergeUrl: @json(route('admin.activity-reports.ai.merge')),
        csrf: @json(csrf_token()),

        openLoading(title, message) {
            $('#aiResultTitle').html('<i class="fas fa-robot mr-2"></i>' + title);
            $('#aiResultLoadingText').text(message || 'Analyzing...');
            $('#aiResultLoading').show();
            $('#aiResultError, #aiResultContent, #aiResultActions').hide();
            $('#aiResultModal').modal('show');
        },

        showError(message) {
            $('#aiResultLoading').hide();
            $('#aiResultError').text(message).show();
            $('#aiResultContent, #aiResultActions').hide();
        },

        showResult(data) {
            lastAiContent = data.content || '';
            $('#aiResultLoading').hide();
            $('#aiResultError').hide();
            $('#aiResultTitle').html('<i class="fas fa-robot mr-2"></i>' + (data.title || 'AI Result'));
            $('#aiResultContent').html(
                typeof marked !== 'undefined'
                    ? marked.parse(lastAiContent)
                    : $('<div/>').text(lastAiContent).html().replace(/\n/g, '<br>')
            ).show();
            $('#aiResultMeta').text('Model: ' + (data.model || 'unknown') + ' · Reports: ' + (data.report_count || 1));
            $('#aiResultActions').show();
        },

        request(url, payload) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload || {}),
            }).then(r => r.json().then(data => ({ ok: r.ok, data })));
        },

        summarize(url) {
            const targetUrl = url || this.summarizeUrl;
            if (!targetUrl) return;
            this.openLoading('Summarizing Report', 'Generating executive summary...');
            this.request(targetUrl, {}).then(({ ok, data }) => {
                if (ok && data.success) this.showResult(data);
                else this.showError(data.message || 'Summarization failed.');
            }).catch(() => this.showError('Network error. Please try again.'));
        },

        merge(reportIds) {
            if (!reportIds || reportIds.length < 2) {
                alert('Select at least two reports to merge.');
                return;
            }
            this.openLoading('Merging Reports', 'Synthesizing ' + reportIds.length + ' reports...');
            this.request(this.mergeUrl, { report_ids: reportIds }).then(({ ok, data }) => {
                if (ok && data.success) this.showResult(data);
                else this.showError(data.message || 'Merge failed.');
            }).catch(() => this.showError('Network error. Please try again.'));
        }
    };

    $('#aiCopyBtn').on('click', function () {
        if (!lastAiContent) return;
        navigator.clipboard.writeText(lastAiContent).then(() => {
            const btn = $(this);
            btn.html('<i class="fas fa-check mr-1"></i> Copied');
            setTimeout(() => btn.html('<i class="fas fa-copy mr-1"></i> Copy'), 2000);
        });
    });

    $('#aiApplyNotesBtn').on('click', function () {
        const notes = $('#admin_notes');
        if (notes.length && lastAiContent) {
            const existing = notes.val().trim();
            notes.val(existing ? existing + '\n\n--- AI Summary ---\n' + lastAiContent : lastAiContent);
            $('#aiResultModal').modal('hide');
        }
    });
})();
</script>
