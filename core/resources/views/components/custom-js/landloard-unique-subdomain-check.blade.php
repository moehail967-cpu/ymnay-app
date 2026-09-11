<script>
    function removeTags(str) {
        if ((str === null) || (str === '')) {
            return false;
        }
        str = str.toString();
        return str.replace(/(<([^>]+)>)/ig, '').replace('http','');
    }

    let register_payment_form = $('.register_page_payment_hidden_form');

    $(document).on('keyup paste change', 'input[name="{{$name}}"]', function (e) {
        $('#submit-btn').attr('disabled', true);

        var value = '';
        if ($(this).val() != '') {
            value = removeTags($(this).val()).toLowerCase().replace(/\s/g, "-");
            let newText = value.replace(/([@#%&"';><,`~.*+?^=!:${}()|\[\]\/\\])/g, "-");
            $(this).val(newText)
        }

        if (value.length < 1) {
            $('#subdomain-wrap').html('');
            return;
        }
        let msgWrap = $('#subdomain-wrap');
        msgWrap.html('');
        msgWrap.append('<span class="text-warning">{{__('availability checking..')}}</span>');
        let trial_button = $('#create-trial-account-button');
        trial_button.attr('disabled', true);


        $.ajax({
            url: "{{route('landlord.subdomain.check')}}",
            method: 'POST',
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {
                subdomain: value
            },
            success: function (res) {
                let site_name = '{{env('APP_NAME')}}';
                let primary_domain = '{{url('/')}}';
                primary_domain = primary_domain.replace('http://','').replace('https://','').split('.').shift();

                let restricted_words = [
                    'https', 'http', 'www', 'subdomain', 'domain', 'primary-domain', 'central-domain',
                    'landlord', 'landlords', 'tenant', 'tenants', 'multi-store', 'multistore', 'admin',
                    'user', 'user', site_name, primary_domain, @json(explode(',', get_static_option('forbidden_subdomains')))];
                let newestText = restricted_words.includes(value);
                let base_url = '{{str_replace(['http://','https://'], '', url('/'))}}';

                if(newestText != true)
                {
                    msgWrap.html('');
                    msgWrap.append('<span class="text-success"> ' + value+'.'+base_url + ' {{__('is available')}}</span>');
                    $('#submit-btn').attr('disabled', false);
                    $('#login_button').attr('disabled', false);
                    $('.order-btn').attr('disabled', false);
                    trial_button.attr('disabled', false);
                } else {
                    msgWrap.html('');
                    msgWrap.append('<span class="text-danger">{{__('This subdomain is not available')}}</span>');
                    $('.order-btn').attr('disabled', true);
                    $('#submit-btn').attr('disabled', true);
                }
            },
            error: function (xhr) {
                var responseData = xhr.responseJSON.errors;
                msgWrap.html('');
                msgWrap.append('<span class="text-danger"> ' + responseData.subdomain + '</span>');
                $('#login_button').attr('disabled', true)
                $('.order-btn').attr('disabled', true)
                $('#submit-btn').attr('disabled', true);
            }
        });
    });
    //subdomain check
</script>
