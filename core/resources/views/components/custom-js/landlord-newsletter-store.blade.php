<script>
    $(document).on('submit', '#landlord-newsletter-form, .newsletter-form-widget', function (e) {
        e.preventDefault();

        var form = $(this);
        var email = form.find('input[type=email]').val();
        var button = form.find('button[type=submit]');
        var originalText = button.text();

        $.ajax({
            url: "{{route('landlord.frontend.newsletter.store.ajax')}}",
            type: "POST",
            data: {
                _token: "{{csrf_token()}}",
                email: email
            },
            beforeSend: function() {
                button.text('{{__("Submitting...")}}');
                button.attr('disabled', true);
            },
            success: function (data) {
                button.text(originalText);
                button.attr('disabled', false);
                form.find('input[type=email]').val('');
                toastr.success(data.msg);
            },
            error: function (data) {
                button.text(originalText);
                button.attr('disabled', false);
                var errors = data.responseJSON.errors;
                toastr.error(errors.email ? errors.email[0] : '{{__("Something went wrong!")}}');
            }
        });
    });
</script>
