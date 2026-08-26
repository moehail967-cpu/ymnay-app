<script>
    (function ($) {
        var customDragandDrop = function (element) {
            $(element).addClass("dp-file-upload__input");
            var $el = $(element);
            var placeholder = element.placeholder || "Drag and drop file here or click to browse";

            $el.wrap('<div class="dp-file-upload"><div class="dp-file-upload__drop-area"></div></div>');
            var $dropArea = $el.closest('.dp-file-upload__drop-area');
            $dropArea.prepend('<span class="dp-file-upload__icon"><i class="mdi mdi-cloud-upload-outline"></i></span>');
            $el.after('<span class="dp-file-upload__msg">' + placeholder + '</span><button type="button" class="dp-file-upload__delete"><i class="mdi mdi-close"></i></button>');

            var $wrapper = $el.closest(".dp-file-upload");

            $wrapper.on("dragenter focus click", ".dp-file-upload__input", function () {
                $(this).closest(".dp-file-upload__drop-area").addClass("is-active");
            });

            $wrapper.on("dragleave blur drop", ".dp-file-upload__input", function () {
                $(this).closest(".dp-file-upload__drop-area").removeClass("is-active");
            });

            $wrapper.on("change", ".dp-file-upload__input", function () {
                var filesCount = $(this)[0].files.length;
                var $msg = $wrapper.find(".dp-file-upload__msg");
                var $del = $wrapper.find(".dp-file-upload__delete");
                if (filesCount === 1) {
                    var fileName = $(this).val().split("\\").pop();
                    $msg.text(fileName);
                    $del.css("display", "inline-flex");
                } else if (filesCount > 1) {
                    $msg.text(filesCount + " files selected");
                    $del.css("display", "inline-flex");
                } else {
                    $msg.text(placeholder);
                    $del.css("display", "none");
                }
            });

            $wrapper.on("click", ".dp-file-upload__delete", function () {
                $wrapper.find(".dp-file-upload__input").val(null);
                $(this).css("display", "none");
                $wrapper.find(".dp-file-upload__msg").text(placeholder);
            });
        };

        $.fn.kwtFileUpload = function () {
            $.each($(this), function (index, element) {
                customDragandDrop(element);
            });
            return this;
        };
    })(jQuery);

    jQuery(document).ready(function ($) {
        $(".demo1").kwtFileUpload();
    });
</script>
