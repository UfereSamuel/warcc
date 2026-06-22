(function (window) {
    'use strict';

    const brand = {
        confirm: '#348F41',
        cancel: '#6c757d',
        danger: '#782C2D',
    };

    function hasSwal() {
        return typeof window.Swal !== 'undefined';
    }

    function toOptions(input) {
        if (typeof input === 'string') {
            return { text: input };
        }

        return input || {};
    }

    function formatMessage(message) {
        return String(message || '').replace(/\n/g, '<br>');
    }

    function fireConfirm(options) {
        const opts = toOptions(options);
        const text = opts.text || opts.html || opts.message || '';
        const title = opts.title || 'Please confirm';

        if (!hasSwal()) {
            const ok = window.confirm([title, text].filter(Boolean).join('\n\n'));
            return Promise.resolve({ isConfirmed: ok });
        }

        return Swal.fire({
            title: title,
            html: opts.html || formatMessage(text),
            icon: opts.icon || 'question',
            showCancelButton: true,
            confirmButtonText: opts.confirmText || 'Yes',
            cancelButtonText: opts.cancelText || 'Cancel',
            confirmButtonColor: opts.confirmColor || brand.confirm,
            cancelButtonColor: opts.cancelColor || brand.cancel,
            reverseButtons: true,
            focusCancel: opts.focusCancel !== false,
        });
    }

    function fireAlert(message, options) {
        const opts = toOptions(options);
        const text = typeof message === 'string' ? message : (opts.text || opts.html || opts.message || '');
        const title = opts.title || '';

        if (!hasSwal()) {
            window.alert([title, text].filter(Boolean).join('\n\n'));
            return Promise.resolve();
        }

        return Swal.fire({
            title: title,
            html: opts.html || formatMessage(text),
            icon: opts.icon || 'info',
            confirmButtonColor: brand.confirm,
            confirmButtonText: opts.confirmText || 'OK',
        });
    }

    window.WarccDialog = {
        confirm: fireConfirm,
        alert: fireAlert,
        success: function (message, options) {
            return fireAlert(message, Object.assign({ icon: 'success', title: 'Success' }, options || {}));
        },
        error: function (message, options) {
            return fireAlert(message, Object.assign({ icon: 'error', title: 'Error' }, options || {}));
        },
        warning: function (message, options) {
            return fireAlert(message, Object.assign({ icon: 'warning', title: 'Warning' }, options || {}));
        },
    };

    function getConfirmMessage(form, event) {
        const formMessage = form.getAttribute('data-warcc-confirm');
        if (formMessage) {
            return formMessage;
        }

        const submitter = event && event.submitter;
        if (submitter && submitter.getAttribute('data-warcc-confirm')) {
            return submitter.getAttribute('data-warcc-confirm');
        }

        return null;
    }

    function bindConfirmForms() {
        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (form.dataset.warccConfirmed === '1') {
                return;
            }

            const message = getConfirmMessage(form, event);
            if (!message) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            WarccDialog.confirm({ text: message }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                form.dataset.warccConfirmed = '1';

                if (typeof form.requestSubmit === 'function') {
                    const submitter = event.submitter || form.querySelector('[type="submit"]');
                    form.requestSubmit(submitter || undefined);
                } else {
                    form.submit();
                }
            });
        }, true);
    }

    function bindConfirmLinks() {
        document.addEventListener('click', function (event) {
            const element = event.target.closest('a[data-warcc-confirm], button[data-warcc-confirm]:not([type="submit"])');
            if (!element) {
                return;
            }

            if (element.closest('form[data-warcc-confirm]')) {
                return;
            }

            const message = element.getAttribute('data-warcc-confirm');
            if (!message) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            WarccDialog.confirm({ text: message }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                if (element.tagName === 'A' && element.href) {
                    window.location.href = element.href;
                }
            });
        }, true);
    }

    function init() {
        bindConfirmForms();
        bindConfirmLinks();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window);
