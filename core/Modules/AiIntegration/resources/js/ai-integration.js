/**
 * AI Integration Plugin — Admin JS
 * Injects a "Generate with AI" bar above every supported Summernote editor.
 */
(function ($) {
    'use strict';

    var SUPPORTED_NAMES = ['description', 'blog_content', 'page_content', 'content'];
    var currentTarget   = null; // field name currently targeted

    var GENERATE_URL = (window.aiIntegration && window.aiIntegration.generateUrl)
        || (window.location.origin + '/admin-home/ai-integration/generate');

    // ── Init ──────────────────────────────────────────────────────────────────

    $(document).ready(function () {
        injectModal();
        bindModalEvents();

        // Run after Summernote has had time to initialise
        setTimeout(injectButtons, 600);
        setTimeout(injectButtons, 1500); // second pass for wizard/tab pages

        // Re-inject whenever a new editor is created
        $(document).on('summernote.init', function () {
            setTimeout(injectButtons, 200);
        });

        // Catch editors revealed by tab clicks
        $(document).on('click', '[data-toggle="tab"], .nav-link, .wizard-nav, .step-item', function () {
            setTimeout(injectButtons, 400);
        });
    });

    // ── Button injection ──────────────────────────────────────────────────────

    function injectButtons() {
        $('.note-editor').each(function () {
            var $editor = $(this);

            if ($editor.prev('.ai-bar-wrap').length) return; // already injected (JS)
            // Skip if there is already an inline blade-rendered AI button in the same container
            if ($editor.parent().find('.ai-bar-btn').length) return;

            var name = resolveFieldName($editor);
            if (!name) return;

            var baseName = name.split('[')[0];
            var isSupported = SUPPORTED_NAMES.indexOf(baseName) !== -1
                || hasAdjacentSummernote($editor);

            if (!isSupported) return;

            var typeHint = guessType(baseName);

            var $bar = $(
                '<div class="ai-bar-wrap">' +
                  '<button type="button" class="ai-bar-btn" ' +
                          'data-ai-target="' + escapeAttr(name) + '" ' +
                          'data-ai-type="' + typeHint + '">' +
                    '<i class="mdi mdi-robot-outline"></i> Generate with AI' +
                  '</button>' +
                '</div>'
            );

            $editor.before($bar);

            // Also add a small icon button inside the toolbar for power users
            var $toolbar = $editor.find('.note-toolbar');
            if ($toolbar.length && !$toolbar.find('.ai-note-btn').length) {
                var $group = $('<div class="note-btn-group"></div>');
                var $tbBtn = $(
                    '<button type="button" class="note-btn ai-note-btn" ' +
                             'title="Generate with AI" ' +
                             'data-ai-target="' + escapeAttr(name) + '" ' +
                             'data-ai-type="' + typeHint + '">' +
                      '<i class="mdi mdi-robot-outline"></i>' +
                    '</button>'
                );
                $group.append($tbBtn);
                $toolbar.append($group);
            }
        });
    }

    /**
     * Resolve the field name associated with a .note-editor element.
     * Handles both textarea-based and div-based Summernote patterns.
     */
    function resolveFieldName($editor) {
        // 1. Immediately preceding textarea
        var $prev = $editor.prev('textarea');
        if ($prev.length && $prev.attr('name')) return $prev.attr('name');

        // 2. Any preceding named element (hidden input, textarea)
        var found = null;
        $editor.prevAll().each(function () {
            var $el  = $(this);
            var name = $el.attr('name');
            if (name) { found = name; return false; }
            // Check inside (e.g. a wrapper div containing a hidden input)
            var $inner = $el.find('[name]').first();
            if ($inner.length) { found = $inner.attr('name'); return false; }
        });
        if (found) return found;

        // 3. Parent container search
        var $container = $editor.parent();
        var $hidden = $container.find('input[type="hidden"][name]').first();
        if ($hidden.length) return $hidden.attr('name');

        return null;
    }

    function hasAdjacentSummernote($editor) {
        return $editor.prev('.summernote').length > 0
            || $editor.prev('textarea.summernote').length > 0;
    }

    function guessType(name) {
        if (!name) return 'product';
        if (name.indexOf('blog') !== -1) return 'blog';
        if (name === 'page_content' || name === 'content') return 'page';
        return 'product';
    }

    // ── Modal ─────────────────────────────────────────────────────────────────

    function injectModal() {
        if ($('#aiModal').length) return;

        var providerLabels = { openai: 'OpenAI', deepseek: 'DeepSeek', gemini: 'Gemini' };
        var providerKey    = window.aiIntegration ? window.aiIntegration.provider : 'openai';
        var providerLabel  = providerLabels[providerKey] || 'AI';

        var html = [
            '<div id="aiModal" style="display:none;position:fixed;inset:0;z-index:999999;align-items:center;justify-content:center;">',
            '  <div id="aiModalOverlay"></div>',
            '  <div id="aiModalBox">',

            '    <div class="ai-modal-header">',
            '      <div class="ai-modal-title">',
            '        <span class="ai-icon-wrap"><i class="mdi mdi-robot-outline"></i></span>',
            '        Generate with AI',
            '        <span class="ai-provider-badge"><i class="mdi mdi-circle-small"></i>' + providerLabel + '</span>',
            '      </div>',
            '      <button type="button" class="ai-modal-close" id="aiModalClose">',
            '        <i class="mdi mdi-close"></i>',
            '      </button>',
            '    </div>',

            '    <div class="ai-modal-body">',

            '      <div class="ai-form-group">',
            '        <label>Describe what you want to write about</label>',
            '        <textarea id="aiHintInput" class="ai-textarea" rows="2"',
            '                  placeholder="e.g. Wireless noise-cancelling headphones with 30-hour battery"></textarea>',
            '      </div>',

            '      <div class="ai-form-row">',
            '        <div class="ai-form-group" style="margin-bottom:0">',
            '          <label>Content Type</label>',
            '          <select id="aiContentType" class="ai-select">',
            '            <option value="product">Product Description</option>',
            '            <option value="blog">Blog Post</option>',
            '            <option value="page">Page Description</option>',
            '          </select>',
            '        </div>',
            '        <div class="ai-form-group" style="margin-bottom:0">',
            '          <label>Tone</label>',
            '          <select id="aiTone" class="ai-select">',
            '            <option value="professional">Professional</option>',
            '            <option value="friendly">Friendly</option>',
            '            <option value="persuasive">Persuasive</option>',
            '            <option value="informative">Informative</option>',
            '            <option value="casual">Casual</option>',
            '          </select>',
            '        </div>',
            '      </div>',

            '      <button type="button" id="aiGenerateBtn" class="ai-generate-btn" style="margin-top:14px">',
            '        <i class="mdi mdi-robot-outline"></i> Generate',
            '      </button>',

            '      <div id="aiPreview"></div>',

            '      <button type="button" id="aiInsertBtn">',
            '        <i class="mdi mdi-check"></i> Insert into Editor',
            '      </button>',

            '    </div>',
            '  </div>',
            '</div>',
        ].join('');

        $('body').append(html);
    }

    // ── Events ────────────────────────────────────────────────────────────────

    function bindModalEvents() {
        // Standalone bar button OR toolbar icon button
        $(document).on('click', '.ai-bar-btn, .ai-note-btn', function (e) {
            e.preventDefault();
            currentTarget = $(this).data('ai-target');
            var typeHint  = $(this).data('ai-type') || guessType(currentTarget);
            $('#aiContentType').val(typeHint);
            openModal();
        });

        $(document).on('click', '#aiModalClose, #aiModalOverlay', closeModal);

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

        $(document).on('click', '#aiGenerateBtn', handleGenerate);
        $(document).on('click', '#aiInsertBtn',   handleInsert);
    }

    function openModal() {
        $('#aiPreview').hide().html('');
        $('#aiInsertBtn').hide();
        $('#aiHintInput').val('');
        $('#aiModal').css('display', 'flex');
        setTimeout(function () { $('#aiHintInput').focus(); }, 50);
    }

    function closeModal() {
        $('#aiModal').fadeOut(150);
    }

    // ── Generate ──────────────────────────────────────────────────────────────

    function handleGenerate() {
        var hint = $('#aiHintInput').val().trim();
        if (!hint) { $('#aiHintInput').focus(); return; }

        var $btn = $('#aiGenerateBtn');
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Generating…');
        $('#aiPreview').hide().html('');
        $('#aiInsertBtn').hide();

        $.ajax({
            url:         GENERATE_URL,
            method:      'POST',
            contentType: 'application/json',
            headers:     { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '' },
            data:        JSON.stringify({
                hint:         hint,
                content_type: $('#aiContentType').val(),
                tone:         $('#aiTone').val(),
            }),
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-robot-outline"></i> Regenerate');
                if (res.success) {
                    $('#aiPreview').html(res.content).show();
                    $('#aiInsertBtn').css('display', 'flex').show();
                } else {
                    showError(res.message || 'Generation failed.');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-robot-outline"></i> Generate');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong.';
                showError(msg);
            },
        });
    }

    // ── Insert ────────────────────────────────────────────────────────────────

    function handleInsert() {
        if (!currentTarget) return;

        var content = $('#aiPreview').html();
        if (!content) return;

        // 1. Try textarea with .summernote
        var $ta = $('textarea[name="' + currentTarget + '"]');
        if ($ta.length) {
            try { $ta.summernote('code', content); }
            catch (e) { $ta.val(content); }
            closeModal();
            return;
        }

        // 2. Div-based summernote (blog edit pattern): find hidden input + adjacent div
        var $hidden = $('input[type="hidden"][name="' + currentTarget + '"]');
        if ($hidden.length) {
            // Update the hidden input value
            $hidden.val(content);

            // Find the sibling .summernote div and update via summernote API
            var $divEditor = $hidden.nextAll('.summernote').first();
            if (!$divEditor.length) {
                $divEditor = $hidden.siblings().filter(function () {
                    return $(this).hasClass('summernote') || $(this).find('.note-editable').length;
                }).first();
            }
            if ($divEditor.length) {
                try { $divEditor.summernote('code', content); }
                catch (e) {}
            }
            closeModal();
            return;
        }

        // 3. Fallback: set any input/textarea matching the name
        var $any = $('[name="' + currentTarget + '"]');
        if ($any.length) {
            try { $any.summernote('code', content); }
            catch (e) { $any.val(content); }
        }

        closeModal();
        $('#aiPreview').html('').hide();
        $('#aiInsertBtn').hide();
        $('#aiHintInput').val('');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    function showError(msg) {
        $('#aiPreview')
            .html('<p style="color:#dc2626;font-size:13px;margin:0"><i class="mdi mdi-alert-circle-outline"></i> ' + escapeHtml(msg) + '</p>')
            .show();
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function escapeAttr(str) {
        return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

})(jQuery);
