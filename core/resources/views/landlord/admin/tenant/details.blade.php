@php
    $route_name = 'landlord';
@endphp

@extends($route_name.'.admin.admin-master')

@section('title') {{__('User Details')}} @endsection

@section('style')
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    $palettes = [
        ['bg' => 'bg-primary-soft',  'text' => 'text-primary'],
        ['bg' => 'bg-info-soft',     'text' => 'text-info'],
        ['bg' => 'bg-[#f3e8ff]',     'text' => 'text-[#9333ea]'],
        ['bg' => 'bg-warning-soft',  'text' => 'text-warning'],
        ['bg' => 'bg-danger-soft',   'text' => 'text-danger'],
    ];
    $pal      = $palettes[$user->id % count($palettes)];
    $initials = collect(explode(' ', $user->name))->map(fn($n) => strtoupper($n[0] ?? ''))->take(2)->join('');
@endphp

{{-- ====== User Profile Card ====== --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-account-details text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('User Details')}}</h3>
                <p class="text-xs text-muted">{{$user->name}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{route('landlord.admin.tenant.edit.profile', $user->id)}}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-soft border border-main text-primary text-sm font-semibold hover:bg-primary hover:text-white hover:border-primary transition whitespace-nowrap">
                <i class="mdi mdi-pencil-outline text-base"></i>
                {{__('Edit')}}
            </a>
            <a href="{{route('landlord.admin.tenant')}}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-muted transition whitespace-nowrap">
                <i class="mdi mdi-arrow-left text-base"></i>
                {{__('Go Back')}}
            </a>
        </div>
    </div>

    {{-- Profile Info --}}
    <div class="px-4 sm:px-6 py-6">
        <div class="flex flex-col sm:flex-row gap-6">

            {{-- Avatar --}}
            <div class="flex-shrink-0 flex flex-col items-center gap-3">
                <span class="w-20 h-20 rounded-2xl flex items-center justify-center text-2xl font-bold border-2 border-main {{ $pal['bg'] }} {{ $pal['text'] }} uppercase">
                    {{$initials}}
                </span>
                @if($user->email_verified === 1)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-success-soft text-success text-[10px] font-bold uppercase">
                        <i class="mdi mdi-check-circle text-xs"></i>{{__('Verified')}}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-danger-soft text-danger text-[10px] font-bold uppercase">
                        <i class="mdi mdi-close-circle text-xs"></i>{{__('Unverified')}}
                    </span>
                @endif
            </div>

            {{-- Details Grid --}}
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">

                <div class="flex items-start gap-2.5">
                    <i class="mdi mdi-account-outline text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Name')}}</p>
                        <p class="text-sm font-semibold text-dark truncate">{{$user->name}}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <i class="mdi mdi-email-outline text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Email')}}</p>
                        <p class="text-sm font-semibold text-dark truncate">{{$user->email}}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <i class="mdi mdi-at text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Username')}}</p>
                        <p class="text-sm font-semibold text-dark truncate">{{$user->username}}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <i class="mdi mdi-phone-outline text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Mobile')}}</p>
                        <p class="text-sm font-semibold text-dark">{{$user->mobile ?: '---'}}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <i class="mdi mdi-domain text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Company')}}</p>
                        <p class="text-sm font-semibold text-dark truncate">{{$user->company ?: '---'}}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <i class="mdi mdi-calendar-outline text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Registered')}}</p>
                        <p class="text-sm font-semibold text-dark">{{$user->created_at->format('d M Y')}}</p>
                        <p class="text-[11px] text-muted">{{$user->created_at->diffForHumans()}}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <i class="mdi mdi-home-outline text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Address')}}</p>
                        <p class="text-sm font-semibold text-dark">{{$user->address ?: '---'}}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <i class="mdi mdi-city text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('City / State')}}</p>
                        <p class="text-sm font-semibold text-dark">
                            {{collect([$user->city, $user->state])->filter()->join(', ') ?: '---'}}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <i class="mdi mdi-earth text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Country')}}</p>
                        <p class="text-sm font-semibold text-dark">{{$user->country ?: '---'}}</p>
                    </div>
                </div>

                {{-- Subdomains --}}
                <div class="flex items-start gap-2.5 sm:col-span-2 lg:col-span-3">
                    <i class="mdi mdi-web text-primary text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted mb-1.5">{{__('Subdomains')}}</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->tenant_details ?? [] as $td)
                                <a href="{{tenant_url_with_protocol(optional($td->domain)->domain)}}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-primary-soft border border-main text-primary text-xs font-semibold hover:bg-primary hover:text-white hover:border-primary transition">
                                    <i class="mdi mdi-open-in-new text-xs"></i>
                                    {{$td->id}}.{{env('CENTRAL_DOMAIN')}}
                                </a>
                            @empty
                                <span class="text-sm text-muted">{{__('No subdomains')}}</span>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>


{{-- ====== Package Information ====== --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Section Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-package-variant-closed text-success text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Package Information')}}</h3>
            <p class="text-xs text-muted">{{__('Subscriptions and store details')}}</p>
        </div>
    </div>

    @forelse($user->tenant_details ?? [] as $key => $tenant)
        @php
            $colors = [
                ['bg' => 'bg-primary-soft', 'text' => 'text-primary', 'border' => 'border-primary'],
                ['bg' => 'bg-success-soft', 'text' => 'text-success', 'border' => 'border-success'],
                ['bg' => 'bg-info-soft',    'text' => 'text-info',    'border' => 'border-info'],
                ['bg' => 'bg-warning-soft', 'text' => 'text-warning', 'border' => 'border-warning'],
                ['bg' => 'bg-[#f3e8ff]',    'text' => 'text-[#9333ea]','border' => 'border-[#9333ea]'],
            ];
            $c = $colors[$key % count($colors)];

            $tenant_payment_log = \App\Models\PaymentLogs::with('package')
                ->find($tenant->renewal_payment_log_id);

            $custom_theme_name = get_static_option_central($tenant?->theme_slug."_theme_name") ?? $tenant?->theme_slug;

            $payment = \App\Models\PaymentLogs::where(['tenant_id' => $tenant->id, 'status' => 'trial'])->latest()->first();
            $start_date = date('d M Y', strtotime($tenant->created_at));
            $renewal_at = date('d M Y', strtotime($tenant->start_date));
            $expire_date = $tenant?->expire_date != null ? date('d M Y', strtotime($tenant->expire_date)) : __('Lifetime');

            if (!empty($payment)) {
                $start_date = date('d M Y', strtotime($payment->start_date));
                $expire_date = date('d M Y', strtotime($payment->expire_date));
            }

            $url = '';
            $central = '.'.env('CENTRAL_DOMAIN');
            if(!empty($tenant->custom_domain?->custom_domain) && $tenant->custom_domain?->custom_domain_status == 'connected'){
                $custom_url = $tenant->custom_domain?->custom_domain;
                $url = tenant_url_with_protocol($custom_url);
            } else {
                $local_url = $tenant->id . $central;
                $url = tenant_url_with_protocol($local_url);
            }

            $hash_token = hash_hmac('sha512', $user->username.'_'.$tenant->id, $tenant->unique_key);
        @endphp

        <div class="px-4 sm:px-6 py-5 {{ !$loop->last ? 'border-b border-main' : '' }}">

            {{-- Store header strip --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center text-xs font-bold flex-shrink-0">
                        #{{$key + 1}}
                    </span>
                    <div>
                        <a href="{{tenant_url_with_protocol(optional($tenant->domain)->domain)}}"
                           target="_blank"
                           class="text-sm font-bold text-dark hover:text-primary transition flex items-center gap-1">
                            {{$tenant->id}}.{{env('CENTRAL_DOMAIN')}}
                            <i class="mdi mdi-open-in-new text-xs text-muted"></i>
                        </a>
                        <p class="text-[11px] text-muted">{{__('Payment Log ID')}}: #{{ $tenant->renewal_payment_log_id ?? optional($tenant->payment_log)->id}}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    @can('users-direct-login')
                        <a href="{{$url.'/token-login/'.$hash_token}}"
                           target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition whitespace-nowrap">
                            <i class="mdi mdi-login text-sm"></i>
                            {{__('Login as Super Admin')}}
                        </a>
                    @endcan
                    <form action="{{ route('landlord.admin.tenant.seed') }}" method="POST" class="migrate-form">
                        @csrf
                        <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">
                        <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-soft border border-main text-primary text-xs font-semibold hover:bg-primary hover:text-white hover:border-primary transition whitespace-nowrap migrate-btn" data-tenant="{{ $tenant->id }}">
                            <i class="mdi mdi-database-import text-sm"></i>
                            {{__('Seed')}}
                        </button>
                    </form>
                    <form method="post" action="{{route(route_prefix().'admin.tenant.domain.delete', $tenant->id)}}" class="tenant-delete-form">
                        @csrf
                        <button type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition whitespace-nowrap swal_delete_button_tenant">
                            <i class="mdi mdi-delete-outline text-sm"></i>
                            {{__('Delete')}}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Package Info --}}
                <div class="bg-secondary rounded-xl border border-main p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted mb-2.5">{{__('Package Info')}}</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-tag-outline text-primary text-sm flex-shrink-0"></i>
                            <span class="text-xs text-dark font-semibold truncate">{{ $tenant_payment_log?->package?->title ?? '---' }}</span>
                            @if(optional($tenant_payment_log)->status == 'trial')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-danger-soft text-danger text-[9px] font-bold uppercase">{{__('Trial')}}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-shape-outline text-primary text-sm flex-shrink-0"></i>
                            <span class="text-xs text-muted">{{ \App\Enums\PricePlanTypEnums::getText(optional(optional($tenant_payment_log?->package))?->type ?? 5) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-cash text-primary text-sm flex-shrink-0"></i>
                            <span class="text-xs text-dark font-semibold">{{amount_with_currency_symbol(optional(optional($tenant_payment_log?->package))?->price)}}</span>
                        </div>
                    </div>
                </div>

                {{-- Subscription Period --}}
                <div class="bg-secondary rounded-xl border border-main p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted mb-2.5">{{__('Subscription Period')}}</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-calendar-start text-primary text-sm flex-shrink-0"></i>
                            <span class="text-xs text-dark">
                                @if(($tenant->renew_status != 0) || ($tenant->is_renew != 0))
                                    {{__('Order')}}: {{$start_date}}
                                @else
                                    {{__('Order')}}: {{$start_date}}
                                @endif
                            </span>
                        </div>
                        @if(($tenant->renew_status != 0) || ($tenant->is_renew != 0))
                            <div class="flex items-center gap-2">
                                <i class="mdi mdi-calendar-refresh text-info text-sm flex-shrink-0"></i>
                                <span class="text-xs text-dark">{{__('Renewal')}}: {{$renewal_at}}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-calendar-end text-sm flex-shrink-0 {{ $expire_date === __('Lifetime') ? 'text-success' : 'text-danger' }}"></i>
                            <span class="text-xs text-dark font-semibold">{{__('Expires')}}: {{$expire_date}}</span>
                        </div>
                    </div>
                </div>

                {{-- Theme & Renew --}}
                <div class="bg-secondary rounded-xl border border-main p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted mb-2.5">{{__('Theme & Renewal')}}</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-palette-outline text-primary text-sm flex-shrink-0"></i>
                            <span class="text-xs text-dark font-semibold capitalize">{{$custom_theme_name}}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-autorenew text-primary text-sm flex-shrink-0"></i>
                            <span class="text-xs text-muted">{{__('Renew Count')}}:</span>
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-primary-soft text-primary text-[10px] font-bold">{{ $tenant_payment_log->renew_status ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Status --}}
                <div class="bg-secondary rounded-xl border border-main p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted mb-2.5">{{__('Payment Status')}}</p>
                    <div class="space-y-2">
                        @if(optional($tenant->payment_log)->payment_status == 'pending' && optional($tenant->payment_log)->status != 'trial')
                            <div class="flex items-start gap-2">
                                <i class="mdi mdi-alert-circle text-danger text-sm flex-shrink-0 mt-0.5"></i>
                                <span class="text-xs text-danger font-semibold">{{__('Last payment requires an action')}}</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <i class="mdi mdi-check-circle text-success text-sm flex-shrink-0"></i>
                                <span class="text-xs text-success font-semibold">{{__('Payment OK')}}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-store text-primary text-sm flex-shrink-0"></i>
                            <a href="{{tenant_url_with_protocol(optional($tenant->domain)->domain)}}"
                               target="_blank"
                               class="text-xs text-primary font-semibold hover:underline truncate">
                                {{$tenant->id}}.{{env('CENTRAL_DOMAIN')}}
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    @empty
        <div class="px-6 py-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-secondary border border-main flex items-center justify-center mx-auto mb-4">
                <i class="mdi mdi-package-variant text-2xl text-muted"></i>
            </div>
            <p class="text-sm font-semibold text-dark mb-1">{{__('No subscriptions')}}</p>
            <p class="text-xs text-muted">{{__('This user has no active packages or stores.')}}</p>
        </div>
    @endforelse

</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>

    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {

            // Tenant domain delete
            $(document).on('click', '.swal_delete_button_tenant', function (e) {
                e.preventDefault();
                var el = $(this);
                var translations = window.SwalTranslations || {};

                Swal.fire({
                    title: translations.deleteTitle || '{{__("Are you sure?")}}',
                    text: translations.deleteText || '{{__("You would not be able to revert this item!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2d6a4f',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: translations.confirmBtn || "{{__('Yes, Accept it!')}}",
                    cancelButtonText: translations.cancelBtn || "{{__('Cancel')}}",
                }).then(function (result) {
                    if (result.isConfirmed) {
                        el.closest('.tenant-delete-form').submit();
                    }
                });
            });

            // Tenant migration / seed
            $(document).on('click', '.migrate-btn', function () {
                var form = $(this).closest('form');
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("This will run migration and seeding for the selected tenant.")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{__("Yes, run it!")}}',
                    cancelButtonText: '{{__("Cancel")}}',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                }).then(function (result) {
                    if (result.isConfirmed === true) {
                        form.submit();
                    }
                });
            });

            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '{{__("Success")}}',
                text: @json(session('success')),
                timer: 4000,
                showConfirmButton: false
            });
            @endif

            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '{{__("Error")}}',
                text: @json(session('error')),
                timer: 4000,
                showConfirmButton: true
            });
            @endif

        });
    })(jQuery);
    </script>
@endsection
