<style>
.form-message-show .nl-msg { display:inline-block; font-size:13px; font-weight:500; padding:6px 16px; border-radius:6px; line-height:1.5; margin-bottom:2px; transition:opacity .4s ease; }
.form-message-show .nl-msg.nl-fade { opacity:0; }
.form-message-show .nl-msg-error   { background:rgba(255,255,255,.92); color:#dc2626; }
.form-message-show .nl-msg-success { background:rgba(255,255,255,.92); color:#16a34a; }
.form-message-show .nl-msg-warning { background:rgba(255,255,255,.92); color:#d97706; }
.form-message-show .nl-msg-info    { background:rgba(255,255,255,.92); color:#2563eb; }
</style>
<script>
    $(document).on('click', '.newsletter-submit-btn', function (e) {
        e.preventDefault();

        var email = $('.footer-widget input.email').val();

        var errrContaner = $(this).parent().parent().find('.form-message-show');
        errrContaner.html('');
        var el = $(this);

        function nlShowMsg(container, html, delay) {
            container.html(html);
            var msg = container.find('.nl-msg');
            setTimeout(function () {
                msg.addClass('nl-fade');
                setTimeout(function () { container.html(''); }, 400);
            }, delay || 4000);
        }

        $.ajax({
            url: "{{route('tenant.frontend.subscribe.newsletter')}}",
            type: "POST",
            data: {
                _token: "{{csrf_token()}}",
                email: email
            },

            beforeSend: function() {
                $('.loader').show();
                el.text('Submiting..');
                el.attr('disabled', true);
            },

            success: function (data) {
                $('.loader').hide();
                el.text('Subscribe');
                el.attr('disabled', false);
                if (data && data.msg) {
                    $('.email').val('');
                    var cls = data.type === 'danger' ? 'nl-msg-error' : 'nl-msg-' + (data.type || 'success');
                    nlShowMsg(errrContaner, '<span class="nl-msg ' + cls + '">' + data.msg + '</span>', 4000);
                } else {
                    $('.email').val('');
                    nlShowMsg(errrContaner, '<span class="nl-msg nl-msg-success">{{ __("Subscribed successfully!") }}</span>', 4000);
                }
            },
            error: function (data) {
                $('.loader').hide();
                el.text('Subscribe');
                el.attr('disabled', false);
                var json = data.responseJSON || {};
                var msg = (json.errors && json.errors.email && json.errors.email[0])
                    ? json.errors.email[0]
                    : (json.message || '{{ __("Something went wrong. Please try again.") }}');
                nlShowMsg(errrContaner, '<span class="nl-msg nl-msg-error">' + msg + '</span>', 5000);
            }
        });
    });
</script>
