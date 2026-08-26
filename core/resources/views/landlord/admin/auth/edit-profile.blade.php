@extends(route_prefix().'.admin.admin-master')
@section('title') {{__('Edit Profile')}} @endsection
@section('content')

    <x-landlord-error-msg/>
    <x-landlord-flash-msg/>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-surface rounded-xl shadow-main border border-main overflow-hidden">

            {{-- Card Header --}}
            <div class="flex items-center gap-4 px-6 py-5 border-b border-main">
                <div class="w-10 h-10 rounded-lg bg-info-soft flex items-center justify-center shrink-0">
                    <i class="mdi mdi-account-edit-outline text-info text-xl"></i>
                </div>
                <div>
                    <h5 class="text-sm font-semibold text-dark">{{__('Edit Profile')}}</h5>
                    <p class="text-[11px] text-muted mt-0.5">{{__('Update your account information')}}</p>
                </div>
            </div>

            {{-- Form Body --}}
            <form method="post" action="{{route(route_prefix().'admin.edit.profile')}}">
                @csrf
                <div class="px-6 py-6 space-y-5">

                    {{-- Avatar --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Profile Image')}}
                        </label>
                        <x-fields.tw-media-upload name="image" :id="auth('admin')->user()->image"/>
                    </div>

                    <div class="border-t border-main"></div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Name')}}
                        </label>
                        <input type="text"
                               name="name"
                               class="lnd-input"
                               placeholder="{{__('Enter your name')}}"
                               value="{{auth('admin')->user()->name}}">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Email Address')}}
                        </label>
                        <input type="email"
                               name="email"
                               class="lnd-input"
                               placeholder="{{__('Enter your email')}}"
                               value="{{auth('admin')->user()->email}}">
                    </div>

                    {{-- Mobile --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Mobile')}}
                        </label>
                        <input type="text"
                               name="mobile"
                               class="lnd-input"
                               placeholder="{{__('Enter your mobile number')}}"
                               value="{{auth('admin')->user()->mobile}}">
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

    <x-media-upload.tw-markup/>

@endsection
@section('scripts')
    <x-media-upload.tw-js/>
@endsection
