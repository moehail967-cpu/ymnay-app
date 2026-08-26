@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{__('Change Password')}}
@endsection

@section('title')
    {{__('Change Password')}}
@endsection

@section('style')
@endsection

@section('section')
<!-- Main Content -->
<div class="col-span-full lg:col-span-9 px-4 lg:px-0 ">
    <!-- Top Header -->
    <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-40 border-y rounded-tr-3xl">
        <div class="flex items-center justify-between pr-8 pt-3 pb-4">
            <div class="flex items-center ">
                <!-- Mobile Menu Button -->
                <button id="menuBtn"
                        class="block ml-5 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                    <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                </button>

                <div class="ml-4 lg:ml-16">
                    <h1 class="text-lg font-medium text-secondary">{{__('Change Password')}}
                    </h1>
                    <p class=" text-xs lg:text-sm text-sub2Title mt-1">{{__('Update your account password to keep it secure')}}</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="mt-8 lg:pl-16 lg:pr-9">

        <x-error-msg-tw/>
        <x-flash-msg-tw/>

        <div class="rounded-2xl border border-borderCS px-6 py-6 w-full max-w-3xl" style="background-color: var(--section-bg-1, #ffffff)">

            <form action="{{route('landlord.user.password.change')}}" method="post" enctype="multipart/form-data" class="flex flex-col gap-5">
                @csrf

                <!-- Current Password -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-800">{{__('Current Password')}}</label>
                    <div class="relative flex items-center border border-borderCS rounded-xl focus-within:ring-2 focus-within:ring-teal-200 focus-within:border-teal-400 transition">
                        <i class="ti tabler-lock absolute left-3.5 text-gray-400 text-base pointer-events-none"></i>
                        <input id="currentPass" type="password" name="old_password" placeholder="{{__('Enter current password')}}"
                               class="w-full pl-10 pr-11 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none bg-transparent rounded-xl"/>
                        <button type="button" class="toggle-pass absolute right-3.5 text-gray-400 hover:text-gray-600 transition" data-target="currentPass">
                            <i class="ti tabler-eye text-base"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-800">{{__('New Password')}}</label>
                    <div class="relative flex items-center border border-borderCS rounded-xl focus-within:ring-2 focus-within:ring-teal-200 focus-within:border-teal-400 transition">
                        <i class="ti tabler-lock absolute left-3.5 text-gray-400 text-base pointer-events-none"></i>
                        <input id="newPass" type="password" name="password" placeholder="{{__('Enter new password')}}"
                               class="w-full pl-10 pr-11 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none bg-transparent rounded-xl"/>
                        <button type="button" class="toggle-pass absolute right-3.5 text-gray-400 hover:text-gray-600 transition" data-target="newPass">
                            <i class="ti tabler-eye text-base"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-800">{{__('Confirm Password')}}</label>
                    <div class="relative flex items-center border border-borderCS rounded-xl focus-within:ring-2 focus-within:ring-teal-200 focus-within:border-teal-400 transition">
                        <i class="ti tabler-lock absolute left-3.5 text-gray-400 text-base pointer-events-none"></i>
                        <input id="confirmPass" type="password" name="password_confirmation" placeholder="{{__('Confirm new password')}}"
                               class="w-full pl-10 pr-11 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none bg-transparent rounded-xl"/>
                        <button type="button" class="toggle-pass absolute right-3.5 text-gray-400 hover:text-gray-600 transition" data-target="confirmPass">
                            <i class="ti tabler-eye text-base"></i>
                        </button>
                    </div>
                    <p id="matchError" class="text-xs text-red-500 hidden">{{__('Passwords do not match.')}}</p>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-1">
                    <a href="{{route('landlord.user.home')}}"
                       class="px-6 py-3 rounded-xl text-sm font-semibold text-gray-700 bg-white border border-borderCS hover:bg-gray-50 transition">
                        {{__('Cancel')}}
                    </a>
                    <button type="submit" id="save"
                            class="px-6 py-3 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition bg-primary">
                        {{__('Update Password')}}
                    </button>
                </div>

            </form>
        </div>
    </main>
</div>
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('assets/new-landlord/js/active_page.js') }}"></script>

    <script>
        (function($){
            "use strict";

            // Toggle password visibility
            $(document).on('click', '.toggle-pass', function(e) {
                e.preventDefault();
                var targetId = $(this).data('target');
                var input = $('#' + targetId);
                var icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('tabler-eye').addClass('tabler-eye-off');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('tabler-eye-off').addClass('tabler-eye');
                }
            });

            // Password match check
            $(document).on('input', '#confirmPass', function() {
                var newPass = $('#newPass').val();
                var confirmPass = $(this).val();
                if (confirmPass.length > 0 && newPass !== confirmPass) {
                    $('#matchError').removeClass('hidden');
                } else {
                    $('#matchError').addClass('hidden');
                }
            });

        }(jQuery));
    </script>

    <script>
        <x-btn.save/>
    </script>

    <script>
        $('.close-bars, .body-overlay').on('click', function() {
            $('.dashboard-close, .dashboard-close-main, .body-overlay').removeClass('active');
        });
        $('.sidebar-icon').on('click', function() {
            $('.dashboard-close, .dashboard-close-main, .body-overlay').addClass('active');
        });
    </script>
@endsection
