<script>
    $(document).on('submit', '.custom-form-builder-land', function (e) {
        e.preventDefault();
        var form = $(this);
        var formID = form.attr('id');
        var btn = form.find('[type="submit"]');
        var msgContainer = form.find('.error-message');
        var formSelector = document.getElementById(formID);
        var formData = new FormData(formSelector);

        msgContainer.html('');

        $.ajax({
            url: "{{route(route_prefix().'frontend.form.builder.custom.submit')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}",
            },
            beforeSend: function () {
                btn.html('<i class="fas fa-spinner fa-spin"></i> {{__("Submitting..")}}');
                btn.prop('disabled', true);
            },
            processData: false,
            contentType: false,
            data: formData,
            success: function (data) {
                var alertClass = data.type === 'success' ? 'alert-success' : 'alert-danger';
                msgContainer.html('<div class="alert ' + alertClass + '">' + data.msg + '</div>');
                btn.text('{{__("Submit Message")}}');
                btn.prop('disabled', false);
                if (data.type === 'success') {
                    form[0].reset();
                }
                $('html, body').animate({ scrollTop: msgContainer.offset().top - 120 }, 400);
            },
            error: function (data) {
                var markup = '<ul class="alert alert-danger">';
                if (data.responseJSON && data.responseJSON.errors) {
                    $.each(data.responseJSON.errors, function (field, messages) {
                        if (Array.isArray(messages)) {
                            $.each(messages, function (i, msg) {
                                markup += '<li>' + msg + '</li>';
                            });
                        } else {
                            markup += '<li>' + messages + '</li>';
                        }
                    });
                } else if (data.responseJSON && data.responseJSON.message) {
                    markup += '<li>' + data.responseJSON.message + '</li>';
                } else {
                    markup += '<li>{{__("Something went wrong. Please try again.")}}</li>';
                }
                markup += '</ul>';
                msgContainer.html(markup);
                btn.text('{{__("Submit Message")}}');
                btn.prop('disabled', false);
                $('html, body').animate({ scrollTop: msgContainer.offset().top - 120 }, 400);
            }
        });
    });
</script>
