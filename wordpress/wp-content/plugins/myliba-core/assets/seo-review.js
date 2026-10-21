(function ($) {
    'use strict';

    $(function () {
        var root = document.getElementById('myliba-review');
        if (!root) return;
        var buttons = root.querySelectorAll('[data-review-mode]');
        var message = root.querySelector('.myliba-review-message');
        var dirtyNotice = root.querySelector('.myliba-review-dirty');
        var result = root.querySelector('.myliba-review-result');
        var busy = false;
        var saving = false;
        var blockEditor = null;
        var usesBlocks = document.body.classList.contains('block-editor-page');
        var baseline;

        function editor() {
            if (!window.wp || !wp.data) return null;
            var store = wp.data.select('core/editor');
            return store && store.getCurrentPostId && store.getCurrentPostId() ? store : null;
        }

        function formSnapshot() {
            // Include all Myliba fields: the server fingerprint includes these too.
            var values = [];
            var selector = '[name^="_myliba_"]' + (usesBlocks ? '' : ', #title, #content, #excerpt, #post_name, #post_status, #post_author_override, #post_password');
            document.querySelectorAll(selector).forEach(function (field) {
                if (!field.name || field.type === 'hidden' && /nonce/.test(field.name)) return;
                values.push([field.name, field.type === 'checkbox' || field.type === 'radio' ? field.checked : field.value]);
            });
            if (window.tinymce && tinymce.get('content') && !tinymce.get('content').isHidden()) {
                values.push(['visual_content', tinymce.get('content').getContent()]);
            }
            values.sort(function (a, b) { return a[0].localeCompare(b[0]); });
            return JSON.stringify(values);
        }

        function updateDirty() {
            blockEditor = editor();
            var dirty = formSnapshot() !== baseline || !!(blockEditor && blockEditor.isEditedPostDirty());
            dirtyNotice.hidden = !dirty;
            var report = result.querySelector('.myliba-review-report');
            if (report) report.classList.toggle('has-unsaved-changes', dirty);
            buttons.forEach(function (button) { button.disabled = busy || dirty || saving; });
            return dirty;
        }

        baseline = formSnapshot();
        document.addEventListener('input', updateDirty);
        document.addEventListener('change', updateDirty);

        if (window.wp && wp.data) {
            wp.data.subscribe(function () {
                var store = editor();
                if (!store) return;
                var metaStore = wp.data.select('core/edit-post');
                var metaSaving = metaStore && metaStore.isSavingMetaBoxes && metaStore.isSavingMetaBoxes();
                var nowSaving = (store.isSavingPost() && !store.isAutosavingPost()) || metaSaving;
                // Gutenberg saves meta boxes separately. Reload after a successful full save
                // so analysis never races a pending SEO-field save or uses a stale baseline.
                if (saving && !nowSaving && store.didPostSaveRequestSucceed()) {
                    window.location.reload();
                    return;
                }
                saving = nowSaving;
                updateDirty();
            });
        }

        // TinyMCE content changes don't always bubble to the underlying textarea.
        function bindVisual(instance) {
            instance.on('init', function () {
                baseline = formSnapshot();
                updateDirty();
            });
            instance.on('change input undo redo', updateDirty);
        }
        if (window.tinymce) {
            tinymce.on('AddEditor', function (event) { if (event.editor.id === 'content') bindVisual(event.editor); });
            if (tinymce.get('content')) bindVisual(tinymce.get('content'));
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (busy || updateDirty()) return;
                busy = true;
                root.classList.add('is-loading');
                message.textContent = button.dataset.reviewMode === 'ai' ? 'AI içeriği değerlendiriyor…' : 'İçerik kontrol ediliyor…';
                updateDirty();
                var startedWith = formSnapshot();
                $.ajax({
                    url: window.ajaxurl,
                    method: 'POST',
                    timeout: 65000,
                    dataType: 'json',
                    data: {
                        action: 'myliba_seo_review',
                        post_id: root.dataset.postId,
                        nonce: root.dataset.nonce,
                        mode: button.dataset.reviewMode
                    }
                }).done(function (response) {
                    if (!response.success || !response.data || !response.data.html) {
                        message.textContent = response.data && response.data.message ? response.data.message : 'Analiz tamamlanamadı.';
                        return;
                    }
                    result.innerHTML = response.data.html;
                    message.textContent = startedWith !== formSnapshot() ? 'Analiz kaydedilmiş sürüme ait. Değişiklikleri kaydedip yeniden analiz edin.' : 'Analiz tamamlandı.';
                }).fail(function (xhr) {
                    var response = xhr.responseJSON;
                    message.textContent = response && response.data && response.data.message ? response.data.message : 'Analiz tamamlanamadı. Bağlantınızı kontrol edip tekrar deneyin. Oturumunuz sona erdiyse sayfayı yenileyin.';
                }).always(function () {
                    busy = false;
                    root.classList.remove('is-loading');
                    updateDirty();
                });
            });
        });
        updateDirty();
    });
}(jQuery));
