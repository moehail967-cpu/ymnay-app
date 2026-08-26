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
    var $detailPanel   = $('#tw_detail_panel');
    var $detailImg     = $('#tw_detail_img');
    var $detailTitle   = $('#tw_detail_title');
    var $detailDate    = $('#tw_detail_date');
    var $detailDims    = $('#tw_detail_dimensions');
    var $detailSize    = $('#tw_detail_size');
    var $detailUrl     = $('#tw_detail_url');
    var $detailAlt     = $('#tw_detail_alt');
    var $detailAltSave = $('#tw_detail_alt_save');
    var $detailDelete  = $('#tw_detail_delete');

    var activeWrapper  = null;
    var isMultiple     = false;
    var selectedId     = null;
    var selectedIds    = [];
    var detailId       = null;

    /* ─── DETAIL PANEL ───────────────────────────────────────────── */
    function showDetail($li) {
        detailId = String($li.data('imgid'));
        $detailImg.attr('src', $li.data('imgsrc') || '');
        $detailTitle.text($li.data('title') || '');
        $detailDate.text($li.data('uploadat') || '');
        $detailDims.text($li.data('dimensions') || '');
        $detailSize.text($li.data('size') || '');
        $detailUrl.text($li.data('imgsrc') || '');
        $detailAlt.val($li.data('alt') || '');
        $detailPanel.removeClass('hidden');
    }

    function hideDetail() {
        $detailPanel.addClass('hidden');
        detailId = null;
    }

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
        isMultiple    = $(this).data('multiple') === true || $(this).data('multiple') === 'true';

        if (isMultiple) {
            var currentVal = activeWrapper.find('.tw-media-id-input').val() || '';
            selectedIds = currentVal ? currentVal.split('|').filter(function(v){ return v !== ''; }) : [];
            selectedId  = null;
        } else {
            selectedId  = activeWrapper.find('.tw-media-id-input').val() || null;
            selectedIds = [];
        }

        hideDetail();
        $('#tw_tab_library').trigger('click');

        $submitBtn.addClass('hidden');

        if (isMultiple && selectedIds.length > 0) {
            $submitBtn.removeClass('hidden');
        }

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
        selectedId  = null;
        selectedIds = [];
        isMultiple  = false;
        $submitBtn.addClass('hidden');
        hideDetail();
    }

    $('#tw_media_modal_close, #tw_media_modal_cancel').on('click', closeModal);
    $('#tw_media_modal_backdrop').on('click', closeModal);

    /* ─── SELECT IMAGE FROM LIBRARY ──────────────────────────────── */
    $(document).on('click', '#tw_media_image_list li', function () {
        var imgId = String($(this).data('imgid'));

        if (isMultiple) {
            var idx = selectedIds.indexOf(imgId);
            if (idx > -1) {
                selectedIds.splice(idx, 1);
                $(this).removeClass('ring-2 ring-sectionC');
            } else {
                selectedIds.push(imgId);
                $(this).addClass('ring-2 ring-sectionC');
            }
            if (selectedIds.length > 0) {
                $submitBtn.removeClass('hidden');
            } else {
                $submitBtn.addClass('hidden');
            }
        } else {
            $imageList.find('li').removeClass('ring-2 ring-sectionC');
            $(this).addClass('ring-2 ring-sectionC');
            selectedId = imgId;
            $submitBtn.removeClass('hidden');
        }

        // Show detail panel for both single and multiple modes
        showDetail($(this));
    });

    /* ─── ALT TEXT SAVE ──────────────────────────────────────────── */
    $detailAltSave.on('click', function () {
        if (!detailId) return;
        var altVal = $detailAlt.val();
        $.ajax({
            type: 'POST',
            url : cfg.altRoute,
            data: { _token: cfg.csrfToken, imgid: detailId, alt: altVal, user_type: cfg.userType },
            success: function () {
                $imageList.find('li[data-imgid="' + detailId + '"]').attr('data-alt', altVal);
                var $btn = $detailAltSave;
                $btn.removeClass('bg-amber-100 text-amber-600').addClass('bg-green-100 text-green-600');
                setTimeout(function () {
                    $btn.removeClass('bg-green-100 text-green-600').addClass('bg-amber-100 text-amber-600');
                }, 1200);
            }
        });
    });

    /* ─── DELETE IMAGE ───────────────────────────────────────────── */
    $detailDelete.on('click', function () {
        if (!detailId) return;

        var imgIdToDelete = detailId;

        Swal.fire({
            title             : '{{ __("Delete this image?") }}',
            text              : '{{ __("This will permanently remove the image from the library.") }}',
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor : '#94a3b8',
            confirmButtonText : '{{ __("Yes, delete it") }}',
            cancelButtonText  : '{{ __("Cancel") }}',
            customClass       : { container: 'tw-mu-swal' }
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $detailDelete.prop('disabled', true)
                .html('<i class="ti tabler-loader-2 animate-spin text-base"></i>');

            $.ajax({
                type    : 'POST',
                url     : cfg.deleteRoute,
                data    : { _token: cfg.csrfToken, img_id: imgIdToDelete, user_type: cfg.userType },
                complete: function () {
                    $detailDelete.prop('disabled', false)
                        .html('<i class="ti tabler-trash text-base"></i> {{ __("Delete") }}');

                    $imageList.find('li[data-imgid="' + imgIdToDelete + '"]').remove();
                    if (String(selectedId) === String(imgIdToDelete)) {
                        selectedId = null;
                        $submitBtn.addClass('hidden');
                    }
                    var idx = selectedIds.indexOf(String(imgIdToDelete));
                    if (idx > -1) selectedIds.splice(idx, 1);
                    if (selectedIds.length === 0) $submitBtn.addClass('hidden');
                    hideDetail();
                }
            });
        });
    });

    /* ─── SET IMAGE (submit) ─────────────────────────────────────── */
    $submitBtn.on('click', function () {
        if (!activeWrapper) return;

        if (isMultiple) {
            if (selectedIds.length === 0) return;

            activeWrapper.find('.tw-media-id-input').val(selectedIds.join('|'));

            var previewHtml = '';
            $.each(selectedIds, function (i, id) {
                var $li = $imageList.find('li[data-imgid="' + id + '"]');
                var src = $li.data('imgsrc') || '';
                if (src) {
                    previewHtml += '<div class="tw-gallery-item relative inline-block flex-shrink-0" data-imageid="' + id + '">' +
                        '<div class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200">' +
                            '<img src="' + src + '" alt="" class="w-full h-full object-cover">' +
                        '</div>' +
                        '<button type="button" class="tw-rmv-gallery-btn absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>' +
                    '</div>';
                }
            });

            activeWrapper.find('.tw-img-wrap').html(previewHtml);
            activeWrapper.find('.tw-media-open-btn').html(
                '<i class="mdi mdi-image-multiple-outline text-base"></i> {{ __("Change Gallery") }}'
            );
        } else {
            if (!selectedId) return;

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
        }

        closeModal();
    });

    /* ─── REMOVE SINGLE IMAGE ────────────────────────────────────── */
    $(document).on('click', '.tw-rmv-btn', function () {
        var $wrapper = $(this).closest('.tw-media-upload-wrapper');
        $wrapper.find('.tw-media-id-input').val('');
        $wrapper.find('.tw-img-wrap').html('<div class="tw-attachment-preview"></div>');
        $wrapper.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{ __("Upload Image") }}');
    });

    /* ─── REMOVE GALLERY ITEM ────────────────────────────────────── */
    $(document).on('click', '.tw-rmv-gallery-btn', function (e) {
        e.stopPropagation();
        var $item    = $(this).closest('.tw-gallery-item');
        var imgId    = String($item.data('imageid'));
        var $wrapper = $(this).closest('.tw-media-upload-wrapper');

        $item.remove();

        var current = $wrapper.find('.tw-media-id-input').val() || '';
        var ids = current.split('|').filter(function (v) { return v !== '' && v !== imgId; });
        $wrapper.find('.tw-media-id-input').val(ids.join('|'));

        if (ids.length === 0) {
            $wrapper.find('.tw-img-wrap').html('');
            $wrapper.find('.tw-media-open-btn').html(
                '<i class="mdi mdi-image-multiple-outline text-base"></i> {{ __("Upload Gallery Image") }}'
            );
        }
    });

    /* ─── BUILD LI HELPER ────────────────────────────────────────── */
    function buildLi(item, isSelectedCls) {
        return $('<li>')
            .addClass('cursor-pointer rounded-lg overflow-hidden border border-gray-100 hover:border-sectionC transition aspect-square ' + isSelectedCls)
            .attr({
                'data-imgid'     : item.image_id,
                'data-imgsrc'    : item.img_url,
                'data-title'     : item.title || '',
                'data-uploadat'  : item.upload_at || '',
                'data-dimensions': item.dimensions || '',
                'data-size'      : item.size || '',
                'data-alt'       : item.alt || ''
            })
            .html('<img src="' + item.img_url + '" alt="' + (item.title || '') + '" class="w-full h-full object-cover" />');
    }

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

                    var isSelectedCls = '';
                    if (isMultiple) {
                        isSelectedCls = (selectedIds.indexOf(String(item.image_id)) > -1) ? 'ring-2 ring-sectionC' : '';
                    } else {
                        isSelectedCls = (String(item.image_id) === String(selectedId)) ? 'ring-2 ring-sectionC' : '';
                    }

                    $imageList.append(buildLi(item, isSelectedCls));
                    if (isSelectedCls) $submitBtn.removeClass('hidden');
                });

                $imageList.removeClass('hidden');
                $loadmoreWrap.removeClass('hidden');
                $loadmoreBtn.text('{{ __("Load more") }}').show();
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

                    var isSelectedCls = '';
                    if (isMultiple) {
                        isSelectedCls = (selectedIds.indexOf(String(item.image_id)) > -1) ? 'ring-2 ring-sectionC' : '';
                    }

                    $imageList.append(buildLi(item, isSelectedCls));
                });
                $loadmoreBtn.text('{{ __("Load more") }}');
            },
            error: function () {
                $loadmoreBtn.text('{{ __("Load more") }}');
            }
        });
    });

    /* ─── DRAG-AND-DROP ─────────────────────────────────────────── */
    var $dropZone   = $('#tw_dropzone');
    var dragCounter = 0;

    $dropZone.on('dragenter', function (e) {
        e.preventDefault();
        dragCounter++;
        $(this).addClass('border-sectionC bg-gray-50');
    });

    $dropZone.on('dragleave', function (e) {
        e.preventDefault();
        dragCounter--;
        if (dragCounter === 0) {
            $(this).removeClass('border-sectionC bg-gray-50');
        }
    });

    $dropZone.on('dragover', function (e) {
        e.preventDefault();
    });

    $dropZone.on('drop', function (e) {
        e.preventDefault();
        dragCounter = 0;
        $(this).removeClass('border-sectionC bg-gray-50');
        var files = e.originalEvent.dataTransfer.files;
        if (files && files.length) {
            uploadFiles(files);
        }
    });

    /* ─── FILE INPUT CHANGE ──────────────────────────────────────── */
    $fileInput.on('change', function () {
        if (this.files && this.files.length) {
            uploadFiles(this.files);
        }
    });

    /* ─── UPLOAD QUEUE (sequential, multi-file) ──────────────────── */
    function uploadFiles(files) {
        var fileArray = Array.prototype.slice.call(files);
        var total     = fileArray.length;
        var current   = 0;

        $progressWrap.removeClass('hidden');

        function uploadNext() {
            if (current >= total) {
                setTimeout(function () {
                    $progressWrap.addClass('hidden');
                    $fileInput.val('');
                    $imageList.empty();
                    $('#tw_tab_library').trigger('click');
                }, 600);
                return;
            }

            var file     = fileArray[current];
            current++;

            var formData = new FormData();
            formData.append('file', file);
            formData.append('user_type', cfg.userType);
            formData.append('_token', cfg.csrfToken);

            $progressBar.css('width', '0%');
            $progressText.text('0%');
            $uploadStatus.text(
                total > 1
                    ? '{{ __("Uploading") }} ' + current + ' / ' + total
                    : '{{ __("Uploading...") }}'
            );

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
                    if (isMultiple) {
                        selectedIds.push(String(res.id));
                    } else {
                        selectedId = res.id;
                    }
                    uploadNext();
                },
                error: function () {
                    $uploadStatus.text('{{ __("Upload failed. Please try again.") }}');
                    $progressBar.css('width', '0%');
                    $progressText.text('0%');
                    setTimeout(uploadNext, 1000);
                }
            });
        }

        uploadNext();
    }

})(jQuery);
</script>
