<script>
    (function($){
        "use strict";
        $(document).on('click', '#login_btn', function (e) {
            e.preventDefault();
            var formContainer = $('#login_form_order_page');
            var el = $(this);
            var username = formContainer.find('input[name="username"]').val();
            var password = formContainer.find('input[name="password"]').val();
            var remember = formContainer.find('input[name="remember"]').val();

            el.text('{{__("Please Wait..")}}');

            $.ajax({
                method: 'post',
                url: "{{route('tenant.user.ajax.login')}}",
                data: {
                    _token: "{{csrf_token()}}",
                    username : username,
                    password : password,
                    remember : remember,
                },
                success: function (data){
                    if(data.status === 'invalid'){
                        el.text('{{__("Login")}}')
                        formContainer.find('.error-wrap').html('<div class="alert alert-danger">'+data.msg+'</div>');
                    }else{
                        formContainer.find('.error-wrap').html('');
                        el.text('{{__("Login Success.. Redirecting ..")}}');
                        location.reload();
                    }
                },
                error: function (data){
                    var response = data.responseJSON.errors;
                    formContainer.find('.error-wrap').html('<ul class="alert alert-danger"></ul>');
                    $.each(response,function (value,index){
                        formContainer.find('.error-wrap ul').append('<li>'+index+'</li>');
                    });
                    el.text('{{__("Login")}}');
                }
            });
        });
    })(jQuery)
</script>
@if(moduleExists('SocialAuth') && get_static_option('google_login_enable'))
@php
    if (tenant()) {
        // Tenant subdomain → bounce through central domain with from_tenant param
        $centralBase = rtrim(get_static_option('site_url') ?? config('app.url'), '/');
        $googleUrl   = $centralBase . '/auth/google?from_tenant=' . request()->getHost();
    } else {
        // Landlord / central domain → direct named route
        $googleUrl = route('landlord.user.google.redirect');
    }
@endphp
<style>
.sc-google-wrap{display:flex;align-items:center;gap:10px;margin:14px 0 4px}
.sc-google-wrap::before,.sc-google-wrap::after{content:'';flex:1;height:1px;background:currentColor;opacity:.15}
.sc-google-wrap span{font-size:12px;opacity:.5;white-space:nowrap}
.sc-google-btn{display:flex;align-items:center;justify-content:center;gap:9px;width:100%;padding:10px 16px;border:1px solid #dadce0;border-radius:8px;background:#fff;color:#3c4043;font-size:14px;font-weight:500;text-decoration:none;cursor:pointer;transition:background .15s,box-shadow .15s;margin-bottom:4px}
.sc-google-btn:hover{background:#f8f9fa;box-shadow:0 1px 4px rgba(0,0,0,.12);color:#3c4043;text-decoration:none}
</style>
<script>
(function(){
    var btn = document.getElementById('login_btn');
    if(!btn) return;
    var wrap = document.createElement('div');
    wrap.innerHTML = '<div class="sc-google-wrap"><span>{{ __("or") }}</span></div>'
        + '<a href="{{ $googleUrl }}" class="sc-google-btn">'
        + '<svg width="18" height="18" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">'
        + '<path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>'
        + '<path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>'
        + '<path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>'
        + '<path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>'
        + '</svg>'
        + '{{ __("Continue with Google") }}</a>';
    btn.parentNode.insertBefore(wrap, btn.nextSibling);
})();
</script>
@endif
