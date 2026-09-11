@include('landlord.frontend.partials.header')

<section class="bg-primary min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-lg py-10 px-8">

        <!-- Logo -->
        <div class="mb-12">
            <div class="flex items-center gap-2">
                {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
            </div>
        </div>

        <!-- Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 rounded-full bg-teal-50 flex items-center justify-center">
                <i class="ti tabler-shield-check text-3xl text-sectionC"></i>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-urbanist font-bold text-secondary mb-2 text-center">{{__('Verify OTP')}}</h1>

        <!-- Countdown -->
        <h5 class="countdown text-center my-2 font-semibold"></h5>
        <p class="text-sub2Title mb-6 text-center">{{__('An OTP has been sent to your phone number.')}}</p>

        <x-error-msg-tw/>
        <x-flash-msg-tw/>

        <!-- Form -->
        <form action="{{route('landlord.user.login.otp.verification')}}" method="post" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf

            <!-- OTP Code -->
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-secondary">{{__('OTP Code')}} <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ti tabler-key text-gray-400 text-lg"></i>
                    </div>
                    <input type="number" name="otp" value="{{old('otp')}}" placeholder="{{__('Enter OTP code')}}"
                           class="w-full pl-12 pr-4 py-3 border border-borderCS rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition text-gray-900 placeholder-gray-400 text-center tracking-widest text-lg" required />
                </div>
            </div>

            <!-- Remember Me -->
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="remember" class="w-5 h-5 rounded cursor-pointer accent-sectionC">
                <span class="ml-2 text-secondary font-medium text-base">{{__('Remember me')}}</span>
            </label>

            <!-- Submit -->
            <button type="submit" id="login_btn"
                    class="w-full bg-teal-800 hover:bg-teal-900 text-white font-semibold py-3 rounded-xl transition-colors shadow-sm">
                {{__('Verify OTP')}}
            </button>

            <!-- Links -->
            <div class="flex items-center justify-between text-sm">
                <a href="{{route('landlord.user.login.otp')}}" class="text-sectionC font-medium hover:text-teal-800">
                    {{__('Update number?')}}
                </a>
                <a href="{{route('landlord.user.login.otp.resend')}}" class="text-sectionC font-medium hover:text-teal-800">
                    {{__('Resend OTP code?')}}
                </a>
            </div>
        </form>

    </div>
</section>

<script src="{{asset('assets/new-landlord/js/plugin.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
@php
    $expire_time = 0;
    if (!now()->isAfter($userOtp->expire_date))
    {
        $expire_time = $userOtp ? now()->diffInRealSeconds($userOtp->expire_date) : 0;
    }
@endphp
<script>
    let expire_time = `{{$expire_time}}`;

    let interval = setInterval(function() {
        if (expire_time > 0) {
            expire_time--;
        }

        let countdown = $('.countdown');
        if (parseInt(expire_time) === 0) {
            countdown.removeClass('text-secondary').addClass('text-red-500').text(`{{__('The OTP is expired')}}`);
            return clearInterval(interval);
        }

        countdown.addClass('text-secondary').text(expire_time + ` {{__('Seconds')}}`);
    }, 1000);
</script>
</body>
</html>
