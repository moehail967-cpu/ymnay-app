@extends('tenant.admin.admin-master')
@section('title') {{__('Add New User')}} @endsection

@section('style')
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-account-plus text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add New User')}}</h3>
                <p class="text-xs text-muted">{{__('Create a new frontend user account')}}</p>
            </div>
        </div>
        <a href="{{route('tenant.admin.user')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{__('All Users')}}
        </a>
    </div>

    {{-- Form --}}
    <form action="{{route('tenant.admin.user.new')}}" method="post" class="px-4 sm:px-6 py-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            {{-- Name --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Name')}}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-account-outline text-muted text-base"></i>
                    <input type="text" name="name" value="{{old('name')}}" placeholder="{{__('name')}}"
                           class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
            </div>

            {{-- Username --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Username')}}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-at text-muted text-base"></i>
                    <input type="text" name="username" value="{{old('username')}}" placeholder="{{__('username')}}"
                           class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Email')}}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-email-outline text-muted text-base"></i>
                    <input type="email" name="email" value="{{old('email')}}" placeholder="{{__('email')}}"
                           class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
            </div>

            {{-- Mobile --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Mobile')}}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-phone-outline text-muted text-base"></i>
                    <input type="text" name="mobile" value="{{old('mobile')}}" placeholder="{{__('mobile')}}"
                           class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
            </div>

            {{-- Country --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Country')}}</label>
                <div class="bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <select name="country" class="countryField w-full bg-transparent text-sm text-dark outline-none" id="countryField">
                        <option value="">{{__('Select a country')}}</option>
                        @foreach($countries ?? [] as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- State --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('State')}}</label>
                <div class="bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <select name="state" class="stateField w-full bg-transparent text-sm text-dark outline-none" id="stateField">
                        <option value="">{{__('Select a state')}}</option>
                    </select>
                </div>
            </div>

            {{-- City --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('City')}}</label>
                <div class="bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <select name="city" class="cityField w-full bg-transparent text-sm text-dark outline-none" id="cityField">
                        <option value="">{{__('Select a city')}}</option>
                    </select>
                </div>
            </div>

            {{-- Postal Code --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Postal Code')}}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-map-marker-outline text-muted text-base"></i>
                    <input type="text" name="postal_code" value="{{old('postal_code')}}" placeholder="{{__('Postal code')}}"
                           class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
            </div>

            {{-- Company --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Company')}}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-domain text-muted text-base"></i>
                    <input type="text" name="company" value="{{old('company')}}" placeholder="{{__('company')}}"
                           class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Address')}}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-home-outline text-muted text-base"></i>
                    <input type="text" name="address" value="{{old('address')}}" placeholder="{{__('address')}}"
                           class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
            </div>
        </div>

        {{-- Image Upload --}}
        <div class="mt-6">
            <label class="block text-xs font-semibold text-dark mb-2">{{__('Image')}}</label>
            <x-fields.tw-media-upload name="image" dimentions="120 X 120 px"/>
        </div>

        {{-- Password --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 mt-6">
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Password')}}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-lock-outline text-muted text-base"></i>
                    <input type="password" name="password"
                           class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Confirm Password')}}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-lock-check-outline text-muted text-base"></i>
                    <input type="password" name="password_confirmation"
                           class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="mt-8">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                <i class="mdi mdi-check text-base"></i>
                {{__('Submit')}}
            </button>
        </div>
    </form>
</div>

<x-media-upload.tw-markup/>
@endsection

@section('scripts')
    <x-media-upload.tw-js/>

    <script>
        $(document).ready(function () {
            $(document).on('change', 'select[name=country]', function (e) {
                e.preventDefault();
                let country_id = $(this).val();
                $.post(`{{route('tenant.admin.au.state.all')}}`,
                    { _token: `{{csrf_token()}}`, country: country_id },
                    function (data) {
                        let stateField = $('.stateField');
                        stateField.empty().append(`<option value="">{{__('Select a state')}}</option>`);
                        let cityField = $('.cityField');
                        cityField.empty().append(`<option value="">{{__('Select a city')}}</option>`);
                        $.each(data.states, function (i, v) {
                            stateField.append(`<option value="${v.id}">${v.name}</option>`);
                        });
                    }
                );
            });

            $(document).on('change', 'select[name=state]', function (e) {
                e.preventDefault();
                let state_id = $(this).val();
                $.post(`{{route('tenant.admin.au.city.all')}}`,
                    { _token: `{{csrf_token()}}`, state: state_id },
                    function (data) {
                        let cityField = $('.cityField');
                        cityField.empty();
                        $.each(data.cities, function (i, v) {
                            cityField.append(`<option value="${v.id}">${v.name}</option>`);
                        });
                    }
                );
            });
        });
    </script>
@endsection
