@include('landlord.frontend.partials.header')
<link rel="stylesheet" href="{{asset('assets/new-landlord/css/auth.css')}}">

<div class="auth-canvas" style="align-items:flex-start; padding-top:2rem; padding-bottom:2rem">
    <div class="auth-card auth-card-wide" style="position:relative">

        {{-- Back to Home --}}
        <a href="{{url('/')}}" title="{{__('Back to Home')}}"
           style="position:absolute;top:1rem;right:1rem;display:flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:9999px;background:#f3f4f6;color:#374253;transition:background .2s"
           onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
            <i class="ti tabler-x" style="font-size:1rem;line-height:1"></i>
        </a>

        {{-- Logo --}}
        <div class="auth-logo mb-6">
            {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
        </div>

        {{-- Title --}}
        <h1 class="font-urbanist font-bold text-secondary mb-1" style="font-size:1.75rem">{{__('Create your account')}}</h1>
        <p class="text-sm mb-6" style="color:var(--heading-color, #374253)">{{__('Get started — it only takes a minute')}}</p>

        <div id="msg-wrapper"></div>
        <x-error-msg-tw />
        <x-flash-msg-tw />

        @if(url('/') == 'https://nazmart.net' || url('/') == 'https://nazmart.test')
            <div class="rounded-xl px-4 py-3 mb-5 text-center text-sm font-medium" style="background:#FFF1F2; border:1px solid #FECDD3; color:#B91C1C">
                {{__('Registration is disabled in this demonstration.')}}
            </div>
        @endif

        <div id="register-form-section">
        <form action="#" method="POST" class="flex flex-col gap-4">

            {{-- Identity --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-secondary">{{__('Full Name')}} <span class="text-red-500">*</span></label>
                    <div class="auth-field relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ti tabler-user text-gray-400"></i>
                        </div>
                        <input type="text" name="name" value="{{old('name')}}" placeholder="{{__('Your full name')}}"
                               class="w-full pl-11 pr-4 py-3 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none" />
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-secondary">{{__('Username')}} <span class="text-red-500">*</span></label>
                    <div class="auth-field relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ti tabler-at text-gray-400"></i>
                        </div>
                        <input type="text" name="username" value="{{old('username')}}" placeholder="{{__('Choose a username')}}"
                               class="w-full pl-11 pr-4 py-3 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none" />
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-secondary">{{__('Email Address')}} <span class="text-red-500">*</span></label>
                    <div class="auth-field relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ti tabler-mail text-gray-400"></i>
                        </div>
                        <input type="email" name="email" value="{{old('email')}}" placeholder="{{__('your@email.com')}}"
                               class="w-full pl-11 pr-4 py-3 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none" />
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-secondary">{{__('Phone Number')}} <span class="text-red-500">*</span></label>
                    <input class="w-full py-3 px-4 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none"
                           style="transition:border-color .15s,box-shadow .15s"
                           type="tel" name="phone" id="telephone" value="{{old('phone')}}">
                </div>
            </div>

            <x-fields.country-select-tw name="country" label="{{__('Country')}}" required="true" class="mt-0" />

            {{-- Security --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-semibold text-secondary">{{__('Password')}} <span class="text-red-500">*</span></label>
                        <a class="generate-password text-xs font-bold uppercase tracking-wide cursor-pointer hover:underline" href="javascript:void(0)" style="color:var(--main-color-one, #0C4D54)">
                            {{__('Generate')}}
                        </a>
                    </div>
                    <div class="auth-field relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ti tabler-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" placeholder="{{__('Create password')}}"
                               class="w-full pl-11 pr-12 py-3 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none" />
                        <button type="button" class="toggle-pass absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600" data-target-name="password">
                            <i class="ti tabler-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-secondary">{{__('Confirm Password')}} <span class="text-red-500">*</span></label>
                    <div class="auth-field relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ti tabler-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password_confirmation" placeholder="{{__('Repeat password')}}"
                               class="w-full pl-11 pr-12 py-3 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none" />
                        <button type="button" class="toggle-pass absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600" data-target-name="password_confirmation">
                            <i class="ti tabler-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Terms --}}
            <div class="flex items-start gap-3 rounded-xl p-3" style="background:var(--section-bg-3, #F9FAFB); border:1px solid var(--borderCS, #F0F0F0)">
                <input class="w-4 h-4 mt-0.5 cursor-pointer agree" style="accent-color:var(--main-color-one, #0C4D54); flex-shrink:0" name="terms_condition" type="checkbox" id="check15">
                <label class="text-sm leading-relaxed cursor-pointer" style="color:var(--body-color, #4B5563)" for="check15">
                    @php
                        $terms_condition_page = get_page_slug(get_static_option('terms_condition')) ?? '#';
                        $privacy_policy_page  = get_page_slug(get_static_option('privacy_policy')) ?? '#';
                    @endphp
                    {{__('I agree to the')}}
                    <a class="font-bold hover:underline" href="{{$terms_condition_page}}" target="_blank" style="color:var(--main-color-one, #0C4D54)">{{__('Terms')}}</a>
                    {{__('and')}}
                    <a class="font-bold hover:underline" href="{{$privacy_policy_page}}" target="_blank" style="color:var(--main-color-one, #0C4D54)">{{__('Privacy Policy')}}</a>
                </label>
            </div>

            <button type="submit" id="register_button" class="auth-btn">
                {{__('Create My Account')}}
            </button>

            <p class="text-center text-sm" style="color:var(--body-color, #6B7280)">
                {{__('Already have an account?')}}
                <a href="{{route('landlord.user.login')}}" class="font-semibold hover:underline ml-1" style="color:var(--main-color-one, #0C4D54)">{{__('Sign In')}}</a>
            </p>

        </form>
        </div>{{-- /register-form-section --}}

        {{-- OTP VERIFICATION SECTION --}}
        <div id="register-otp-section" style="display:none;">

            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background:rgba(var(--main-color-one-rgb, 12,77,84), 0.1)">
                    <i class="ti tabler-shield-check text-4xl" style="color:var(--main-color-one, #0C4D54)"></i>
                </div>
                <h2 class="font-urbanist font-bold text-secondary mb-2" style="font-size:1.5rem">{{__('Verify Your Email')}}</h2>
                <p class="text-sm" style="color:#374253">
                    {{__('We sent a 6-digit code to')}}
                    <strong id="reg-otp-email" style="color:var(--main-color-one, #0C4D54)"></strong>
                </p>
            </div>

            {{-- Countdown timer --}}
            <div class="text-center mb-4">
                <span id="reg-otp-timer" class="text-sm font-semibold"></span>
            </div>

            <div id="reg-otp-msg" class="mb-4"></div>

            {{-- 6 digit inputs --}}
            <div class="flex gap-2 justify-center mb-6" id="reg-otp-input-group">
                @for($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                       class="reg-otp-digit w-12 text-center text-2xl font-bold border-2 rounded-xl focus:outline-none"
                       style="height:3.5rem; border-color:#D1D5DB; transition:border-color .15s">
                @endfor
            </div>

            <button type="button" id="reg_verify_otp_btn" class="auth-btn mb-4">
                {{__('Verify & Create Account')}}
            </button>

            <div class="flex items-center justify-between text-sm">
                <button type="button" id="reg_resend_otp_btn" disabled
                        class="font-medium hover:underline disabled:opacity-40 disabled:cursor-not-allowed"
                        style="color:var(--main-color-one, #0C4D54)">
                    {{__('Resend OTP')}} (<span id="reg-resend-countdown">60</span>s)
                </button>
                <button type="button" id="reg_back_btn" class="hover:underline" style="color:#6B7280">
                    &#8592; {{__('Back to form')}}
                </button>
            </div>

        </div>{{-- /register-otp-section --}}

    </div>
</div>

<script src="{{asset('assets/new-landlord/js/plugin.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<script src="{{asset('assets/landlord/common/js/axios.min.js')}}"></script>
<x-custom-js.generate-password />
<x-custom-js.phone-number-config selector="#telephone" />

<script>
    var regOtpTimerInterval, regResendInterval;

    // ── Step 1: Submit registration form → send OTP ──
    document.getElementById('register_button').addEventListener('click', function (e) {
        e.preventDefault();
        var btn = this;
        btn.disabled = true;
        btn.innerText = "{{__('Sending OTP...')}}";

        var msgWrap = document.getElementById('msg-wrapper');
        msgWrap.innerHTML = '';

        let terms = document.querySelector('input[name="terms_condition"]').checked ? 'on' : '';

        axios({
            url: "{{route('landlord.user.register.store')}}",
            method: 'post',
            responseType: 'json',
            data: {
                name:                  document.querySelector('input[name="name"]').value,
                email:                 document.querySelector('input[name="email"]').value,
                username:              document.querySelector('input[name="username"]').value,
                password:              document.querySelector('input[name="password"]').value,
                country:               document.querySelector('select[name="country"]').value,
                phone:                 (window.iti1) ? iti1.getNumber() : document.querySelector('input[name="phone"]').value,
                password_confirmation: document.querySelector('input[name="password_confirmation"]').value,
                terms_condition:       terms,
                _token:                '{{csrf_token()}}'
            }
        }).then(function (response) {
            if (response.data.status === 'otp_sent') {
                // Show OTP section, hide form
                document.getElementById('register-form-section').style.display = 'none';
                document.getElementById('register-otp-section').style.display  = 'block';
                document.getElementById('reg-otp-email').innerText = response.data.email;
                startRegOtpTimer(300);
                startRegResendCooldown(60);
                document.querySelector('#reg-otp-input-group .reg-otp-digit').focus();
            } else {
                btn.disabled  = false;
                btn.innerText = "{{__('Create My Account')}}";
                msgWrap.innerHTML = '<div style="background:#FFF1F2;border:1px solid #FECDD3;color:#B91C1C;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1rem;font-size:.875rem">' + (response.data.msg ?? '{{__("Something went wrong.")}}') + '</div>';
            }
        }).catch(function (error) {
            btn.disabled  = false;
            btn.innerText = "{{__('Create My Account')}}";
            if (error.response && error.response.status === 422) {
                var errs  = error.response.data.errors;
                var child = '<div style="background:#FFF1F2;border:1px solid #FECDD3;color:#B91C1C;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1rem"><ul style="padding-left:1.25rem;font-size:.875rem;list-style:disc">';
                Object.entries(errs).forEach(function (v) { child += '<li>' + v[1] + '</li>'; });
                child += '</ul></div>';
                msgWrap.innerHTML = child;
            } else {
                var msg = error.response?.data?.message ?? '{{__("Something went wrong.")}}';
                msgWrap.innerHTML = '<div style="background:#FFF1F2;border:1px solid #FECDD3;color:#B91C1C;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1rem;font-size:.875rem">' + msg + '</div>';
            }
        });
    });

    $(document).ready(function () {

        // ── OTP: auto-advance between digit boxes ──
        $(document).on('input', '.reg-otp-digit', function () {
            let val = $(this).val().replace(/\D/g, '');
            $(this).val(val);
            if (val.length === 1) {
                let next = $(this).next('.reg-otp-digit');
                if (next.length) next.focus();
            }
        });
        $(document).on('keydown', '.reg-otp-digit', function (e) {
            if (e.key === 'Backspace' && !$(this).val()) {
                $(this).prev('.reg-otp-digit').focus();
            }
        });
        $(document).on('paste', '.reg-otp-digit', function (e) {
            e.preventDefault();
            let pasted = (e.originalEvent.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            let digits = $('#reg-otp-input-group .reg-otp-digit');
            $.each(pasted.split(''), function (i, ch) { digits.eq(i).val(ch); });
            digits.eq(Math.min(pasted.length, 5)).focus();
        });

        // ── OTP: focus ring highlight ──
        var _mainColor = getComputedStyle(document.documentElement).getPropertyValue('--main-color-one').trim() || '#0C4D54';
        $(document).on('focus', '.reg-otp-digit', function () {
            $(this).css('border-color', _mainColor);
        }).on('blur', '.reg-otp-digit', function () {
            $(this).css('border-color', '#D1D5DB');
        });

        // ── Step 2: Verify OTP → create account ──
        $(document).on('click', '#reg_verify_otp_btn', function () {
            let el  = $(this);
            let otp = $('#reg-otp-input-group .reg-otp-digit').map(function () { return $(this).val(); }).get().join('');

            if (otp.length !== 6) {
                showRegOtpMsg('warning', '{{__("Please enter all 6 digits.")}}');
                return;
            }

            el.prop('disabled', true).text('{{__("Verifying...")}}');
            $('#reg-otp-msg').html('');

            axios({
                url:          "{{route('landlord.user.register.otp.verify')}}",
                method:       'post',
                responseType: 'json',
                data: {
                    otp:    otp,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            }).then(function (response) {
                if (response.data.status === 'valid') {
                    clearInterval(regOtpTimerInterval);
                    clearInterval(regResendInterval);
                    el.text('{{__("Redirecting...")}}');

                    // Update CSRF token after session regeneration
                    if (response.data.csrf_token) {
                        $('meta[name="csrf-token"]').attr('content', response.data.csrf_token);
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = response.data.csrf_token;
                    }

                    @php $plan_id = $plan_id ?? ''; @endphp
                    @if(!empty($plan_id))
                        @php session()->put('trial-register', __('Account Registration Successful')) @endphp
                        window.location.href = '{{route('landlord.frontend.plan.view', [$plan_id, 'trial'])}}';
                    @else
                        window.location.href = '{{route('landlord.user.home')}}';
                    @endif

                } else if (response.data.status === 'expired') {
                    clearInterval(regOtpTimerInterval);
                    clearInterval(regResendInterval);
                    // Session gone — go back to the form
                    document.getElementById('register-otp-section').style.display  = 'none';
                    document.getElementById('register-form-section').style.display = 'block';
                    document.getElementById('msg-wrapper').innerHTML = '<div style="background:#FFF1F2;border:1px solid #FECDD3;color:#B91C1C;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1rem;font-size:.875rem">' + response.data.msg + '</div>';
                    el.prop('disabled', false).text('{{__("Verify & Create Account")}}');
                } else {
                    // Wrong OTP — clear inputs and show error
                    showRegOtpMsg('danger', response.data.msg);
                    $('#reg-otp-input-group .reg-otp-digit').val('');
                    $('#reg-otp-input-group .reg-otp-digit').first().focus();
                    el.prop('disabled', false).text('{{__("Verify & Create Account")}}');
                }
            }).catch(function () {
                el.prop('disabled', false).text('{{__("Verify & Create Account")}}');
                showRegOtpMsg('danger', '{{__("Request failed. Please try again.")}}');
            });
        });

        // ── Resend OTP ──
        $(document).on('click', '#reg_resend_otp_btn', function () {
            let el = $(this);
            el.prop('disabled', true);

            axios({
                url:          "{{route('landlord.user.register.otp.resend')}}",
                method:       'post',
                responseType: 'json',
                data: { _token: $('meta[name="csrf-token"]').attr('content') }
            }).then(function (response) {
                if (response.data.status === 'resent') {
                    startRegOtpTimer(300);
                    startRegResendCooldown(60);
                    showRegOtpMsg('success', response.data.msg);
                } else {
                    el.prop('disabled', false);
                    showRegOtpMsg('warning', response.data.msg);
                }
            }).catch(function () {
                el.prop('disabled', false);
            });
        });

        // ── Back to registration form ──
        $(document).on('click', '#reg_back_btn', function () {
            clearInterval(regOtpTimerInterval);
            clearInterval(regResendInterval);
            document.getElementById('register-otp-section').style.display  = 'none';
            document.getElementById('register-form-section').style.display = 'block';
            $('#reg-otp-input-group .reg-otp-digit').val('');
            $('#reg-otp-msg').html('');
        });

        // ── Password helpers ──
        $(document).on('click', '.generate-password', function () {
            let password = generateRandomPassword();
            $('input[name=password]').val(password);
            $('input[name=password_confirmation]').val(password);
        });
        $(document).on('click', '.toggle-pass', function (e) {
            e.preventDefault();
            var targetName = $(this).data('target-name');
            var input = $('input[name="' + targetName + '"]');
            var icon  = $(this).find('i');
            if (input.attr('type') === 'password') { input.attr('type', 'text');     icon.removeClass('tabler-eye').addClass('tabler-eye-off'); }
            else                                   { input.attr('type', 'password'); icon.removeClass('tabler-eye-off').addClass('tabler-eye'); }
        });
    });

    // ── Timer helpers ──
    function startRegOtpTimer(seconds) {
        clearInterval(regOtpTimerInterval);
        var remaining = seconds;
        var el = document.getElementById('reg-otp-timer');
        function tick() {
            if (remaining <= 0) {
                clearInterval(regOtpTimerInterval);
                el.style.color = '#DC2626';
                el.innerText   = '{{__("OTP expired — please go back and register again.")}}';
                document.getElementById('reg_verify_otp_btn').disabled = true;
                return;
            }
            var m = Math.floor(remaining / 60), s = remaining % 60;
            el.style.color = '#16A34A';
            el.innerText   = '{{__("Expires in")}} ' + m + ':' + String(s).padStart(2, '0');
            remaining--;
        }
        tick();
        regOtpTimerInterval = setInterval(tick, 1000);
    }

    function startRegResendCooldown(seconds) {
        clearInterval(regResendInterval);
        document.getElementById('reg_resend_otp_btn').disabled = true;
        var remaining = seconds;
        var countEl   = document.getElementById('reg-resend-countdown');
        function tick() {
            countEl.innerText = remaining;
            if (remaining <= 0) {
                clearInterval(regResendInterval);
                document.getElementById('reg_resend_otp_btn').disabled = false;
                countEl.innerText = '';
                return;
            }
            remaining--;
        }
        tick();
        regResendInterval = setInterval(tick, 1000);
    }

    function showRegOtpMsg(type, msg) {
        var colors = {
            danger:  { bg: '#FFF1F2', border: '#FECDD3', text: '#B91C1C' },
            warning: { bg: '#FFFBEB', border: '#FDE68A', text: '#92400E' },
            success: { bg: '#F0FDF4', border: '#BBF7D0', text: '#166534' },
        };
        var c = colors[type] || colors.danger;
        document.getElementById('reg-otp-msg').innerHTML =
            '<div style="background:' + c.bg + ';border:1px solid ' + c.border + ';color:' + c.text + ';border-radius:.75rem;padding:.75rem 1rem;font-size:.875rem">' + msg + '</div>';
    }
</script>
</body>
</html>
