@extends(route_prefix().'.admin.admin-master')
@section('title') {{__('Change Password')}} @endsection
@section('content')

    <x-error-msg/>
    <x-flash-msg/>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-surface rounded-xl shadow-main border border-main overflow-hidden">

            {{-- Card Header --}}
            <div class="flex items-center gap-4 px-6 py-5 border-b border-main">
                <div class="w-10 h-10 rounded-lg bg-primary-soft flex items-center justify-center shrink-0">
                    <i class="mdi mdi-lock-reset text-primary text-xl"></i>
                </div>
                <div>
                    <h5 class="text-sm font-semibold text-dark">{{__('Change Password')}}</h5>
                    <p class="text-[11px] text-muted mt-0.5">{{__('Update your account password')}}</p>
                </div>
            </div>

            {{-- Form Body --}}
            <form method="post" action="{{route(route_prefix().'admin.change.password')}}">
                @csrf
                <div class="px-6 py-6 space-y-5">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('New Password')}}
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password"
                                   id="password_field"
                                   class="lnd-input pr-11"
                                   placeholder="{{__('Enter new password')}}">
                            <button type="button"
                                    onclick="togglePassword('password_field', 'eye_icon_1')"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-dark transition">
                                <i id="eye_icon_1" class="mdi mdi-eye-outline text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Confirm Password')}}
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password_confirmation"
                                   id="confirm_field"
                                   class="lnd-input pr-11"
                                   placeholder="{{__('Re-enter new password')}}">
                            <button type="button"
                                    onclick="togglePassword('confirm_field', 'eye_icon_2')"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-dark transition">
                                <i id="eye_icon_2" class="mdi mdi-eye-outline text-lg"></i>
                            </button>
                        </div>
                    </div>

                </div>

                {{-- Card Footer --}}
                <div class="px-6 py-4 bg-secondary border-t border-main flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i>
                        {{__('Save Changes')}}
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon  = document.getElementById(iconId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('mdi-eye-outline', 'mdi-eye-off-outline');
            } else {
                field.type = 'password';
                icon.classList.replace('mdi-eye-off-outline', 'mdi-eye-outline');
            }
        }
    </script>

@endsection
