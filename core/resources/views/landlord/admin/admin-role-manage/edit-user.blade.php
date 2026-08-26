@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Edit Admin')}} @endsection

@section('style')
@endsection

@section('content')

@if(is_null(tenant()))

    {{-- ===== LANDLORD · TAILWIND UI ===== --}}
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
            <x-error-msg/>
            <x-flash-msg/>

            <form action="{{route(route_prefix().'admin.user.update')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{$admin->id}}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="lnd-label">{{__('Name')}}</label>
                        <input type="text" class="lnd-input" name="name" value="{{$admin->name}}" placeholder="{{__('Enter name')}}">
                    </div>

                    <div>
                        <label class="lnd-label">{{__('Email')}}</label>
                        <input type="text" class="lnd-input" name="email" value="{{$admin->email}}" placeholder="{{__('Email')}}">
                    </div>

                    <div>
                        <label class="lnd-label">{{__('Mobile')}}</label>
                        <input type="text" class="lnd-input" name="mobile" value="{{$admin->mobile}}" placeholder="{{__('Mobile')}}">
                    </div>

                    <div>
                        <label class="lnd-label">{{__('Role')}}</label>
                        <select name="role" class="lnd-input">
                            <option value="">{{__('Select Role')}}</option>
                            @foreach($roles as $role)
                                <option value="{{$role}}" @if(in_array($role, $adminRole)) selected @endif>{{$role}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="lnd-label">{{__('Profile Image')}}</label>
                        <x-fields.tw-media-upload name="image" :id="$admin->image"/>
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

@else

    {{-- ===== TENANT · BOOTSTRAP UI (unchanged) ===== --}}
    <div class="col-lg-12 col-ml-12">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between mb-4">
                            <h4 class="header-title mb-4">{{__('Edit Admin')}}</h4>
                            <div class="btn-wrapper">
                                <a class="btn btn-secondary" href="{{route(route_prefix().'admin.all.user')}}">{{__("All Admin")}}</a>
                            </div>
                        </div>
                        <x-error-msg/>
                        <x-flash-msg/>
                        <form action="{{route(route_prefix().'admin.user.update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" value="{{$admin->id}}">
                            <div class="form-group">
                                <label for="name">{{__('Name')}}</label>
                                <input type="text" class="form-control" value="{{$admin->name}}" name="name" placeholder="{{__('Enter name')}}">
                            </div>
                            <div class="form-group">
                                <label for="email">{{__('Email')}}</label>
                                <input type="text" class="form-control" value="{{$admin->email}}" name="email" placeholder="{{__('Email')}}">
                            </div>
                            <div class="form-group">
                                <label for="email">{{__('Mobile')}}</label>
                                <input type="text" class="form-control" value="{{$admin->mobile}}" name="mobile" placeholder="{{__('Mobile')}}">
                            </div>
                            <div class="form-group">
                                <label for="role">{{'Role'}}</label>
                                <select name="role" class="form-control">
                                    <option value="">{{__('Select Role')}}</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role}}" @if(in_array($role,$adminRole)) selected @endif>{{$role}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="site_favicon">{{__('Image')}}</label>
                                <x-fields.tw-media-upload name="image" :id="$admin->image"/>
                            </div>
                            <button id="update" type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-media-upload.tw-markup/>

@endif

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
