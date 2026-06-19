<div class="modal fade" id="aiResultModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h5 class="modal-title text-white" id="aiResultTitle">
                    <i class="fas fa-robot mr-2"></i>AI Assistant
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="aiResultLoading" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="text-muted mb-0" id="aiResultLoadingText">Analyzing reports...</p>
                </div>
                <div id="aiResultError" class="alert alert-danger" style="display: none;"></div>
                <div id="aiResultContent" class="ai-markdown-body" style="display: none;"></div>
            </div>
            <div class="modal-footer" id="aiResultActions" style="display: none;">
                <small class="text-muted mr-auto" id="aiResultMeta"></small>
                <button type="button" class="btn btn-outline-secondary" id="aiCopyBtn">
                    <i class="fas fa-copy mr-1"></i> Copy
                </button>
                @if(!empty($showApplyToNotes))
                    <button type="button" class="btn btn-outline-success" id="aiApplyNotesBtn">
                        <i class="fas fa-paste mr-1"></i> Add to Admin Notes
                    </button>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.ai-markdown-body h1, .ai-markdown-body h2, .ai-markdown-body h3 { font-size: 1.1rem; font-weight: 600; margin-top: 1rem; }
.ai-markdown-body ul { padding-left: 1.25rem; }
.ai-markdown-body p { margin-bottom: 0.75rem; }
</style>
