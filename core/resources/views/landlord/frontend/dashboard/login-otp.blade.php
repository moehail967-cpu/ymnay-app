@include('landlord.frontend.partials.header')
<link rel="stylesheet" href="{{asset('assets/new-landlord/css/auth.css')}}">

<div class="auth-canvas">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo mb-8">
            {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
        </div>

        {{-- Icon badge --}}
        <div class="auth-icon-badge">
            <i class="ti tabler-device-mobile" style="font-size:1.5rem; color:#0C4D54"></i>
        </div>

        {{-- Title --}}
        <h1 class="font-urbanist font-bold text-secondary text-center mb-1" style="font-size:1.625rem">{{__('OTP Sign In')}}</h1>
        <p class="text-sm text-center mb-7" style="color:#374253">{{__('Enter your phone number to receive a one-time code')}}</p>

        <x-error-msg-tw/>
        <x-flash-msg-tw/>

        <form action="{{route('landlord.user.login.otp')}}" method="post" class="flex flex-col gap-5">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-secondary">{{__('Phone Number')}} <span class="text-red-500">*</span></label>
                <input class="w-full py-3 px-4 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none"
                       style="transition:border-color .15s,box-shadow .15s"
                       type="tel" name="phone" id="telephone" value="{{old('phone')}}">
            </div>

            <button type="submit" id="login_btn" class="auth-btn">
                <i class="ti tabler-send" style="font-size:0.95rem"></i>
                {{__('Send OTP')}}
            </button>

            <div class="auth-or">{{__('or')}}</div>

            <p class="text-center text-sm" style="color:#6B7280">
                {{__('Use password instead?')}}
                <a href="{{route('landlord.user.login')}}" class="font-semibold hover:underline ml-1" style="color:#0C4D54">{{__('Sign In')}}</a>
            </p>

            <p class="text-center text-sm" style="color:#6B7280">
                {{__("Don't have an account?")}}
                <a href="{{route(route_prefix().'user.register')}}" class="font-semibold hover:underline ml-1" style="color:#0C4D54">{{__('Sign up')}}</a>
            </p>
        </form>

    </div>
</div>

<script src="{{asset('assets/new-landlord/js/plugin.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<x-custom-js.phone-number-config selector="#telephone" submit-button-id="login_btn"/>
</body>
</html>
