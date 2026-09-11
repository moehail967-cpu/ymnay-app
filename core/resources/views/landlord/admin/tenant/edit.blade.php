@php
    $route_name = 'landlord';
@endphp

@extends($route_name.'.admin.admin-master')
@section('title') {{__('Edit User')}} @endsection

@section('style')
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="max-w-3xl">

    {{-- Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-6">

        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-account-edit text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Tenant')}}</h3>
                    <p class="text-xs text-muted">{{$user->name}} &mdash; {{$user->email}}</p>
                </div>
            </div>
            <a href="{{route('landlord.admin.tenant')}}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-soft border border-main text-primary text-sm font-semibold hover:bg-primary hover:text-white hover:border-primary transition whitespace-nowrap">
                <i class="mdi mdi-arrow-left text-base"></i>
                {{__('All Tenants')}}
            </a>
        </div>

        {{-- Form --}}
        <form action="{{route('landlord.admin.tenant.update.profile')}}" method="post">
            @csrf
            <input type="hidden" name="id" value="{{$user->id}}">

            <div class="px-4 sm:px-6 py-6 space-y-5">

                {{-- Name & Username --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-account-outline text-lg text-primary"></i>
                            <input type="text" name="name" value="{{$user->name}}"
                                   placeholder="{{__('Full name')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Username')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-at text-lg text-primary"></i>
                            <input type="text" name="username" value="{{$user->username}}"
                                   placeholder="{{__('Username')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                </div>

                {{-- Email & Mobile --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Email')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-email-outline text-lg text-primary"></i>
                            <input type="email" name="email" value="{{$user->email}}"
                                   placeholder="{{__('Email address')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Mobile')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-phone-outline text-lg text-primary"></i>
                            <input type="text" name="mobile" value="{{$user->mobile}}"
                                   placeholder="{{__('Phone number')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="flex items-center gap-3 pt-1">
                    <span class="flex-1 h-px bg-main"></span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Location')}}</span>
                    <span class="flex-1 h-px bg-main"></span>
                </div>

                {{-- Country & City --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Country')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-earth text-lg text-primary"></i>
                            <select name="country"
                                    class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="">{{__('Select Country')}}</option>
                                @foreach(country_list() as $country)
                                    <option value="{{$country}}" @if($user->country === $country) selected @endif>{{$country}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('City')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-city text-lg text-primary"></i>
                            <input type="text" name="city" value="{{$user->city}}"
                                   placeholder="{{__('City')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                </div>

                {{-- State & Company --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('State')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-map-marker-outline text-lg text-primary"></i>
                            <input type="text" name="state" value="{{$user->state}}"
                                   placeholder="{{__('State / Province')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Company')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-domain text-lg text-primary"></i>
                            <input type="text" name="company" value="{{$user->company}}"
                                   placeholder="{{__('Company name')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Address')}}</label>
                    <div class="flex items-start gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-home-outline text-lg text-primary mt-0.5"></i>
                        <input type="text" name="address" value="{{$user->address}}"
                               placeholder="{{__('Street address')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                {{-- Divider --}}
                <div class="flex items-center gap-3 pt-1">
                    <span class="flex-1 h-px bg-main"></span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Profile Image')}}</span>
                    <span class="flex-1 h-px bg-main"></span>
                </div>

                {{-- Image Upload --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Image')}}</label>
                    <x-fields.tw-media-upload name="image" :id="$user->image" dimentions="{{__('120 X 120 px image recommended')}}"/>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-4 sm:px-6 py-4 border-t border-main bg-secondary rounded-b-xl">
                <a href="{{route('landlord.admin.tenant')}}"
                   class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Update Profile')}}
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
