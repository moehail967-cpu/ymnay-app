<script>
(function ($) {
    "use strict";

    var cfg            = window._twMediaConfig || {};
    var $modal         = $('#tw_media_modal');
    var $backdrop      = $('#tw_media_modal_backdrop');
    var $submitBtn     = $('#tw_media_modal_submit');
    var $imageList     = $('#tw_media_image_list');
    var $preloader     = $('#tw_library_preloader');
    var $loadmoreWrap  = $('#tw_loadmore_wrap');
    var $loadmoreBtn   = $('#tw_loadmore_btn');
    var $fileInput     = $('#tw_file_input');
    var $progressWrap  = $('#tw_upload_progress');
    var $progressBar   = $('#tw_progress_bar');
    var $progressText  = $('#tw_progress_text');
    var $uploadStatus  = $('#tw_upload_status');

    var activeWrapper  = null;
    var selectedId     = null;

    /* ─── TAB SWITCHING ──────────────────────────────────────────── */
    $(document).on('click', '.tw-tab-btn', function () {
        var target = $(this).data('tab');
        $('.tw-tab-btn').removeClass('border-sectionC text-sectionC').addClass('border-transparent text-gray-500');
        $(this).addClass('border-sectionC text-sectionC').removeClass('border-transparent text-gray-500');
        $('#tw_upload_panel, #tw_library_panel').addClass('hidden');
        $('#' + target).removeClass('hidden');

        if (target === 'tw_library_panel' && $imageList.children().length === 0) {
            loadLibrary();
        }
    });

    /* ─── OPEN MODAL ─────────────────────────────────────────────── */
    $(document).on('click', '.tw-media-open-btn', function () {
        activeWrapper = $('#' + $(this).data('target'));
        selectedId    = activeWrapper.find('.tw-media-id-input').val() || null;

        $('#tw_tab_library').trigger('click');

        $submitBtn.addClass('hidden');
        $modal.removeClass('hidden');
        $backdrop.removeClass('hidden');
        $('body').addClass('overflow-hidden');
    });

    /* ─── CLOSE MODAL ────────────────────────────────────────────── */
    function closeModal() {
        $modal.addClass('hidden');
        $backdrop.addClass('hidden');
        $('body').removeClass('overflow-hidden');
        $imageList.find('li').removeClass('ring-2 ring-sectionC');
        selectedId = null;
        $submitBtn.addClass('hidden');
    }

    $('#tw_media_modal_close, #tw_media_modal_cancel').on('click', closeModal);
    $('#tw_media_modal_backdrop').on('click', closeModal);

    /* ─── SELECT IMAGE FROM LIBRARY ──────────────────────────────── */
    $(document).on('click', '#tw_media_image_list li', function () {
        $imageList.find('li').removeClass('ring-2 ring-sectionC');
        $(this).addClass('ring-2 ring-sectionC');
        selectedId = $(this).data('imgid');
        $submitBtn.removeClass('hidden');
    });

    /* ─── SET IMAGE (submit) ─────────────────────────────────────── */
    $submitBtn.on('click', function () {
        if (!selectedId || !activeWrapper) return;

        var imgSrc = $imageList.find('li[data-imgid="' + selectedId + '"]').data('imgsrc');

        activeWrapper.find('.tw-media-id-input').val(selectedId);

        var $previewWrap = activeWrapper.find('.tw-img-wrap');
        $previewWrap.html(
            '<div class="tw-attachment-preview relative inline-block">' +
                '<img src="' + imgSrc + '" alt="" class="tw-preview-img w-24 h-24 rounded-lg object-cover border border-gray-200" />' +
                '<button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>' +
            '</div>'
        );

        activeWrapper.find('.tw-media-open-btn').html(
            '<i class="ti tabler-photo text-base"></i> {{ __("Change Image") }}'
        );

        closeModal();
    });

    /* ─── REMOVE IMAGE ───────────────────────────────────────────── */
    $(document).on('click', '.tw-rmv-btn', function () {
        var $wrapper = $(this).closest('.tw-media-upload-wrapper');
        $wrapper.find('.tw-media-id-input').val('');
        $wrapper.find('.tw-img-wrap').html('<div class="tw-attachment-preview"></div>');
        $wrapper.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{ __("Upload Image") }}');
    });

    /* ─── LOAD LIBRARY ───────────────────────────────────────────── */
    function loadLibrary(skip) {
        skip = skip || 0;
        if (skip === 0) {
            $preloader.removeClass('hidden');
            $imageList.addClass('hidden').empty();
            $loadmoreWrap.addClass('hidden');
        }

        $.ajax({
            type   : 'POST',
            url    : cfg.allRoute,
            data   : { _token: cfg.csrfToken, skip: skip, user_type: cfg.userType },
            success: function (data) {
                $preloader.addClass('hidden');
                if (!data || data.length === 0) {
                    if (skip === 0) {
                        $imageList.removeClass('hidden').html(
                            '<li class="col-span-full text-center text-sm text-gray-400 py-8">{{ __("No images found") }}</li>'
                        );
                    }
                    $loadmoreBtn.hide();
                    return;
                }

                $.each(data, function (i, item) {
                    if ($imageList.find('li[data-imgid="' + item.image_id + '"]').length) return;

                    var isSelected = (String(item.image_id) === String(selectedId)) ? 'ring-2 ring-sectionC' : '';
                    var li = $('<li>')
                        .addClass('cursor-pointer rounded-lg overflow-hidden border border-gray-100 hover:border-sectionC transition aspect-square ' + isSelected)
                        .attr({ 'data-imgid': item.image_id, 'data-imgsrc': item.img_url })
                        .html('<img src="' + item.img_url + '" alt="' + (item.title || '') + '" class="w-full h-full object-cover" />');
                    $imageList.append(li);

                    if (isSelected) $submitBtn.removeClass('hidden');
                });

                $imageList.removeClass('hidden');
                $loadmoreWrap.removeClass('hidden');
                $loadmoreBtn.text('{{ __("Load More") }}').show();
            },
            error: function () {
                $preloader.addClass('hidden');
            }
        });
    }

    /* ─── LOAD MORE ──────────────────────────────────────────────── */
    $loadmoreBtn.on('click', function () {
        var skip = $imageList.children('li').length;
        $(this).text('{{ __("Loading...") }}');
        $.ajax({
            type   : 'POST',
            url    : cfg.loadmoreRoute,
            data   : { _token: cfg.csrfToken, skip: skip, user_type: cfg.userType },
            success: function (data) {
                if (!data || data.length === 0) {
                    $loadmoreBtn.hide();
                    return;
                }
                $.each(data, function (i, item) {
                    if ($imageList.find('li[data-imgid="' + item.image_id + '"]').length) return;
                    var li = $('<li>')
                        .addClass('cursor-pointer rounded-lg overflow-hidden border border-gray-100 hover:border-sectionC transition aspect-square')
                        .attr({ 'data-imgid': item.image_id, 'data-imgsrc': item.img_url })
                        .html('<img src="' + item.img_url + '" alt="" class="w-full h-full object-cover" />');
                    $imageList.append(li);
                });
                $loadmoreBtn.text('{{ __("Load More") }}');
            },
            error: function () {
                $loadmoreBtn.text('{{ __("Load More") }}');
            }
        });
    });

    /* ─── FILE UPLOAD ────────────────────────────────────────────── */
    $fileInput.on('change', function () {
        var file = this.files[0];
        if (!file) return;

        var formData = new FormData();
        formData.append('file', file);
        formData.append('user_type', cfg.userType);
        formData.append('_token', cfg.csrfToken);

        $progressWrap.removeClass('hidden');
        $progressBar.css('width', '0%');
        $progressText.text('0%');
        $uploadStatus.text('{{ __("Uploading...") }}');

        $.ajax({
            type       : 'POST',
            url        : cfg.uploadRoute,
            data       : formData,
            processData: false,
            contentType: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        var pct = Math.round((e.loaded / e.total) * 100);
                        $progressBar.css('width', pct + '%');
                        $progressText.text(pct + '%');
                    }
                }, false);
                return xhr;
            },
            success: function (res) {
                $progressBar.css('width', '100%');
                $progressText.text('100%');
                $uploadStatus.text('{{ __("Upload complete!") }}');

                selectedId = res.id;
                setTimeout(function () {
                    $progressWrap.addClass('hidden');
                    $fileInput.val('');
                    $imageList.empty();
                    $('#tw_tab_library').trigger('click');
                }, 800);
            },
            error: function () {
                $uploadStatus.text('{{ __("Upload failed. Please try again.") }}');
                $progressBar.css('width', '0%');
                $progressText.text('0%');
            }
        });
    });

})(jQuery);
</script>
