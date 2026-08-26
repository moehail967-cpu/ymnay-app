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
                <i class="ti tabler-mail-check text-3xl text-sectionC"></i>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-urbanist font-bold text-secondary mb-2 text-center">{{__('Verify Your Email')}}</h1>
        <p class="text-sub2Title mb-4 text-center">{{__('Check your mail for the verification code.')}}</p>

        <!-- Info Alert -->
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl px-4 py-3 mb-6 text-sm text-center">
            <i class="ti tabler-alert-triangle text-base"></i>
            {{__('A verification code has been sent to your email address.')}}
        </div>

        <x-flash-msg-tw/>
        <x-error-msg-tw/>

        <!-- Form -->
        <form action="{{route('landlord.user.email.verify')}}" method="post" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf

            <!-- Verify Code -->
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-secondary">{{__('Verification Code')}}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ti tabler-key text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" name="verify_code" placeholder="{{__('Enter verification code')}}"
                           class="w-full pl-12 pr-4 py-3 border border-borderCS rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition text-gray-900 placeholder-gray-400 text-center tracking-widest text-lg" required />
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" id="verify"
                    class="w-full bg-teal-800 hover:bg-teal-900 text-white font-semibold py-3 rounded-xl transition-colors shadow-sm">
                {{__('Verify Email')}}
            </button>

            <!-- Resend -->
            <p class="text-center text-gray-600">
                {{__('Did not get the code?')}}
                <a href="{{route('landlord.user.email.verify.resend')}}" id="send" class="text-sectionC font-semibold hover:text-teal-800">{{__('Resend Code')}}</a>
            </p>
        </form>

    </div>
</section>

<script src="{{asset('assets/new-landlord/js/plugin.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<script>
    (function($){
        "use strict";
        $(document).ready(function () {
            <x-btn.custom :id="'verify'" :title="__('Verifying')"/>
            <x-btn.custom :id="'send'" :title="__('Sending Verify Code')"/>
        });
    })(jQuery);
</script>
</body>
</html>
