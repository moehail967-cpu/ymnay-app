@extends('landlord.admin.admin-master')

@section('title')
    {{__('Account Settings')}}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
@endsection

@section('content')

    <div class="bg-surface rounded-xl shadow-main overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center">
                    <i class="mdi mdi-cog-outline text-warning text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Account Settings')}}</h3>
                    <p class="text-xs text-muted">{{__('Configure tenant account auto-removal rules')}}</p>
                </div>
            </div>
            <a href="{{route('landlord.admin.tenant')}}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                <i class="mdi mdi-arrow-left text-base"></i> {{__('All Tenants')}}
            </a>
        </div>

        {{-- Body --}}
        <div class="p-6 md:p-8">
            <x-landlord-flash-msg/>
            <x-landlord-error-msg/>

            <form action="{{route('landlord.admin.tenant.settings')}}" method="post">
                @csrf

                {{-- Auto-Remove Toggle --}}
                <div class="mb-6">
                    <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-4">{{__('Auto Removal')}}</p>

                    <div class="flex items-center justify-between bg-secondary border border-main rounded-xl px-5 py-4">
                        <div class="flex items-center gap-3">
                            <i class="mdi mdi-delete-clock-outline text-xl text-primary"></i>
                            <div>
                                <span class="text-sm font-semibold text-dark">{{__('Auto remove account')}}</span>
                                <p class="text-[11px] text-muted mt-0.5">{{__('Automatically flag expired accounts for removal.')}}</p>
                            </div>
                        </div>
                        <label class="dr-toggle">
                            <input type="checkbox" name="tenant_account_auto_remove" class="auto-remove-switcher"
                                   @checked(!empty(get_static_option('tenant_account_auto_remove')))>
                            <span class="dr-toggle-track"></span>
                        </label>
                    </div>
                </div>

                {{-- Conditional Settings (shown when auto-remove is ON) --}}
                @php
                    $fields = [
                        1 => __('One Day'),
                        2 => __('Two Days'),
                        3 => __('Three Days'),
                        4 => __('Four Days'),
                        5 => __('Five Days'),
                        6 => __('Six Days'),
                        7 => __('Seven Days'),
                        14 => __('Fourteen Days'),
                        30 => __('Thirty Days'),
                    ];
                    $account_expiry = get_static_option('tenant_account_delete_notify_mail_days');
                    $decoded = json_decode($account_expiry) ?? [];
                @endphp

                <div class="action-wrapper space-y-6">

                    {{-- Notification Days Multi-Select --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Deletion Mail Alert Schedule')}}</label>
                        <p class="text-[11px] text-muted mb-3">{{__('Select how many days before expiration the account deletion mail alert will be sent.')}}</p>
                        <div class="flex items-start gap-2.5">
                            <i class="mdi mdi-email-alert-outline text-lg text-primary mt-2.5 shrink-0"></i>
                            <select name="tenant_account_delete_notify_mail_days[]" class="expiration_dates w-full" multiple="multiple">
                                @foreach($fields as $key => $field)
                                    <option value="{{$key}}"
                                        @foreach($decoded as $day)
                                            {{$day == $key ? 'selected' : ''}}
                                        @endforeach
                                    >{{__($field)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Removal Days Input --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Remove Account After Expiration (Days)')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition max-w-md">
                            <i class="mdi mdi-calendar-remove-outline text-lg text-primary"></i>
                            <input type="number" name="account_remove_day_within_expiration" min="1"
                                   value="{{get_static_option('account_remove_day_within_expiration')}}"
                                   placeholder="{{__('E.g. 30')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Info Note --}}
                    <div class="flex items-start gap-2.5 bg-info-soft border border-subtle rounded-xl px-4 py-3">
                        <i class="mdi mdi-information-outline text-primary text-lg mt-0.5 shrink-0"></i>
                        <div class="text-[12px] text-dark leading-relaxed">
                            {{__('It will not remove accounts automatically. Admin have to delete the accounts manually.')}}
                            <span class="text-primary font-semibold">{{__('This feature requires cron jobs.')}}</span>
                        </div>
                    </div>

                </div>

                {{-- Submit --}}
                <div class="pt-6 mt-6 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Settings')}}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('.expiration_dates').select2({
                placeholder: '{{__("Select days")}}',
            });

            let auto_remove = `{{get_static_option('tenant_account_auto_remove')}}`;
            let wrapper = $('.action-wrapper');

            if (auto_remove === '') {
                wrapper.hide();
            }

            $(document).on('change', '.auto-remove-switcher', function () {
                wrapper.toggle();
            });
        });
    </script>
@endsection
