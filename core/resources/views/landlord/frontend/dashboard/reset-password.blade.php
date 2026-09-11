@include('landlord.frontend.partials.header')

<section class="bg-primary min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-lg py-10 px-8">

        <!-- Logo -->
        <div class="mb-12">
            <div class="flex items-center gap-2">
                {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-urbanist font-bold text-secondary mb-2">{{__('Reset Password')}}</h1>
        <p class="text-sub2Title mb-8">{{__('Enter your new password below')}}</p>

        <x-error-msg-tw/>
        <x-flash-msg-tw/>

        <!-- Form -->
        <form action="{{route('tenant.user.reset.password.change')}}" method="post" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf
            <input type="hidden" name="token" value="{{$token}}">

            <!-- Username (readonly) -->
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-secondary">{{__('Username')}}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ti tabler-user text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" name="username" value="{{$username}}" readonly
                           class="w-full pl-12 pr-4 py-3 border border-borderCS rounded-xl bg-gray-50 text-gray-700 outline-none cursor-not-allowed" />
                </div>
            </div>

            <!-- New Password -->
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-secondary">{{__('New Password')}}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ti tabler-lock text-gray-400 text-lg"></i>
                    </div>
                    <input type="password" name="password" placeholder="{{__('Enter new password')}}" id="newPass"
                           class="w-full pl-12 pr-12 py-3 border border-borderCS rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition text-gray-900 placeholder-gray-400" required />
                    <button type="button" class="toggle-pass absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600" data-target="newPass">
                        <i class="ti tabler-eye text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-secondary">{{__('Confirm Password')}}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ti tabler-lock text-gray-400 text-lg"></i>
                    </div>
                    <input type="password" name="password_confirmation" placeholder="{{__('Confirm new password')}}" id="confirmPass"
                           class="w-full pl-12 pr-12 py-3 border border-borderCS rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition text-gray-900 placeholder-gray-400" required />
                    <button type="button" class="toggle-pass absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600" data-target="confirmPass">
                        <i class="ti tabler-eye text-lg"></i>
                    </button>
                </div>
                <p id="matchError" class="text-xs text-red-500 hidden">{{__('Passwords do not match.')}}</p>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-teal-800 hover:bg-teal-900 text-white font-semibold py-3 rounded-xl transition-colors shadow-sm">
                {{__('Reset Password')}}
            </button>
        </form>

    </div>
</section>

<script src="{{asset('assets/new-landlord/js/plugin.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<script>
    (function($){
        "use strict";
        $(document).on('click', '.toggle-pass', function(e) {
            e.preventDefault();
            var targetId = $(this).data('target');
            var input = $('#' + targetId);
            var icon = $(this).find('i');
            if (input.attr('type') === 'password') { input.attr('type', 'text'); icon.removeClass('tabler-eye').addClass('tabler-eye-off'); }
            else { input.attr('type', 'password'); icon.removeClass('tabler-eye-off').addClass('tabler-eye'); }
        });
        $(document).on('input', '#confirmPass', function() {
            var newPass = $('#newPass').val();
            var confirmPass = $(this).val();
            if (confirmPass.length > 0 && newPass !== confirmPass) { $('#matchError').removeClass('hidden'); }
            else { $('#matchError').addClass('hidden'); }
        });
    })(jQuery);
</script>
</body>
</html>
