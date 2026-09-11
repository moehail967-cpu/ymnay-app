@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Add New Admin')}} @endsection

@section('content')

    <div class="bg-surface rounded-xl shadow-main overflow-hidden">

        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center">
                    <i class="mdi mdi-account-plus text-success text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('New Admin')}}</h3>
                    <p class="text-xs text-muted">{{__('Create a new admin account')}}</p>
                </div>
            </div>
            <a href="{{route(route_prefix().'admin.all.user')}}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                <i class="mdi mdi-arrow-left text-base"></i> {{__('All Admins')}}
            </a>
        </div>

        <div class="p-6 md:p-8">
            <x-landlord-flash-msg/>
            <x-landlord-error-msg/>

            <form action="{{route(route_prefix().'admin.new.user')}}" method="post" enctype="multipart/form-data">
                @csrf

                {{-- Row 1: Full Name + Email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    {{-- Full Name --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Full Name')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-account-outline text-lg text-primary"></i>
                            <input type="text" name="name" value="{{old('name')}}"
                                   placeholder="{{__('E.g. Julian Vane')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('Visible in your team directory.')}}</p>
                    </div>

                    {{-- Email Address --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Email Address')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-at text-lg text-primary"></i>
                            <input type="text" name="email" value="{{old('email')}}"
                                   placeholder="{{__('julian@lumina.io')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('The onboarding link will be sent here.')}}</p>
                    </div>

                </div>

                {{-- Row 2: Username + Mobile --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Username')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-card-account-details text-lg text-primary"></i>
                            <input type="text" name="username" value="{{old('username')}}"
                                   placeholder="{{__('Enter username')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                        <p class="text-[11px] text-danger mt-1.5">{{__('User will login using this username.')}}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Mobile')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-phone-outline text-lg text-primary"></i>
                            <input type="text" name="mobile" value="{{old('mobile')}}"
                                   placeholder="{{__('+1 000 000 0000')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                </div>

                {{-- Row 3: Password + Confirm Password --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Password')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-outline text-lg text-primary"></i>
                            <input type="password" name="password"
                                   placeholder="{{__('••••••••')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Confirm Password')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-check-outline text-lg text-primary"></i>
                            <input type="password" name="password_confirmation"
                                   placeholder="{{__('••••••••')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                </div>

                {{-- Section: Workspace Access --}}
                <div class="mb-6">
                    <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-4">{{__('Workspace Access')}}</p>

                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Admin Role')}}</label>
                    <div class="relative">
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-shield-account-outline text-lg text-primary"></i>
                            <select name="role"
                                    class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="">{{__('Select access level')}}</option>
                                @foreach($roles as $role)
                                    <option value="{{$role}}" @selected(old('role') == $role)>{{$role}}</option>
                                @endforeach
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-muted mt-1.5 flex items-center gap-1">
                        <i class="mdi mdi-information-outline text-primary text-sm"></i>
                        {{__('Permissions can be customized per project after invitation.')}}
                    </p>
                </div>

                {{-- Section: Profile Image --}}
                <div class="mb-6">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Profile Image')}}</label>
                    <div class="tw-media-upload-wrapper bg-secondary border border-main rounded-xl p-4" id="img_upload_wrapper">
                        <div class="tw-img-wrap mb-3">
                            @php $image = get_attachment_image_by_id(get_static_option('image'), null, true); @endphp
                            @if(!empty($image))
                                <div class="tw-attachment-preview relative inline-block">
                                    <img src="{{$image['img_url']}}" alt="" class="tw-preview-img w-20 h-20 rounded-xl object-cover border border-[#d4eeea]">
                                    <button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-danger hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>
                                </div>
                            @endif
                        </div>
                        <input type="hidden" class="tw-media-id-input" name="image" value="{{get_static_option('image')}}">
                        <button type="button"
                                class="tw-media-open-btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main bg-surface text-sm font-medium text-brand hover:border-primary hover:text-primary transition"
                                data-target="img_upload_wrapper">
                            <i class="mdi mdi-image-plus text-base"></i>
                            {{ !empty($image) ? __('Change Image') : __('Upload Image') }}
                        </button>
                        <p class="text-[11px] text-muted mt-2">{{__('Allowed formats: jpg, jpeg, png')}}</p>
                    </div>
                </div>

                {{-- Section: Editorial Note / Bio --}}
                <div class="mb-8">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Editorial Note / Bio')}}</label>
                    <div class="bg-secondary border border-main rounded-xl px-4 py-3 flex gap-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-text-box-edit-outline text-lg text-primary mt-0.5 shrink-0"></i>
                        <textarea name="bio" rows="3"
                                  placeholder="{{__('Briefly describe focus area or special instructions...')}}"
                                  class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 resize-none p-0">{{old('bio')}}</textarea>
                    </div>
                </div>

                <div class="pt-5 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-account-plus text-base"></i> {{__('Add New Admin')}}
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
