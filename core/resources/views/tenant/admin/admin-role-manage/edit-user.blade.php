@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Edit Admin')}} @endsection

@section('content')

    <div class="bg-surface rounded-xl shadow-main overflow-hidden">

        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center">
                    <i class="mdi mdi-account-edit text-success text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Admin')}}</h3>
                    <p class="text-xs text-muted">{{__('Update admin account details')}}</p>
                </div>
            </div>
            <a href="{{route(route_prefix().'admin.all.user')}}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                <i class="mdi mdi-arrow-left text-base"></i> {{__('All Admins')}}
            </a>
        </div>

        <div class="p-6">
            <x-landlord-error-msg/>
            <x-landlord-flash-msg/>

            <form action="{{route(route_prefix().'admin.user.update')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{$admin->id}}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-account-outline text-lg text-primary"></i>
                            <input type="text" name="name" value="{{$admin->name}}"
                                   placeholder="{{__('Enter name')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Email')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-at text-lg text-primary"></i>
                            <input type="text" name="email" value="{{$admin->email}}"
                                   placeholder="{{__('Email')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Mobile')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-phone-outline text-lg text-primary"></i>
                            <input type="text" name="mobile" value="{{$admin->mobile}}"
                                   placeholder="{{__('Mobile')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Role')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-shield-account-outline text-lg text-primary"></i>
                            <select name="role"
                                    class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="">{{__('Select Role')}}</option>
                                @foreach($roles as $role)
                                    <option value="{{$role}}" @if(in_array($role, $adminRole)) selected @endif>{{$role}}</option>
                                @endforeach
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Profile Image')}}</label>
                        <div class="tw-media-upload-wrapper bg-secondary border border-main rounded-xl p-4" id="img_upload_wrapper">
                            <div class="tw-img-wrap mb-2">
                                @php
                                    $image = get_attachment_image_by_id($admin->image, null, true);
                                @endphp
                                @if(!empty($image))
                                    <div class="tw-attachment-preview relative inline-block">
                                        <img src="{{$image['img_url']}}" alt="" class="tw-preview-img w-24 h-24 rounded-xl object-cover border border-main">
                                        <button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-danger hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>
                                    </div>
                                @endif
                            </div>
                            <input type="hidden" class="tw-media-id-input" name="image" value="{{$admin->image}}">
                            <button type="button"
                                    class="tw-media-open-btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main bg-surface text-sm font-medium text-brand hover:border-primary hover:text-primary transition"
                                    data-target="img_upload_wrapper">
                                <i class="mdi mdi-image-plus text-base"></i>
                                {{ !empty($image) ? __('Change Image') : __('Upload Image') }}
                            </button>
                            <p class="text-xs text-muted mt-1.5">{{__('Allowed formats: jpg, jpeg, png')}}</p>
                        </div>
                    </div>

                </div>

                <div class="mt-6 pt-5 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save text-base"></i> {{__('Update Admin')}}
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
