@extends('tenant.admin.admin-master')

@section('title')
    {{__('Custom Domain')}}
@endsection
@section('style')
    <style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')
@php $central_domain = env('CENTRAL_DOMAIN'); @endphp

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
{{--        <h2 class="text-lg font-bold text-dark font-urbanist">{{__('Custom Domain')}}</h2>--}}
        <p class="text-xs text-muted">{{__('Connect your own domain to this store')}}</p>
    </div>
    <button onclick="openDomainModal()"
        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap self-start sm:self-auto">
        <i class="mdi mdi-web-plus text-base"></i>
        {{__('Request Custom Domain')}}
    </button>
</div>

{{-- Success / Warning / Info flash shown outside (danger shown inside modal) --}}
@if(session('type') && session('type') !== 'danger')
    <x-flash-msg-tw/>
@endif

<div class="grid grid-cols-1 xl:grid-cols-5 gap-5">

    {{-- DNS Setup Guide --}}
    <div class="xl:col-span-3 bg-surface rounded-2xl shadow-main border border-main overflow-hidden">
        <div class="px-5 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-dns-outline text-info text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">
                    {{get_static_option_central('custom_domain_settings_title') ?: __('DNS Configuration Guide')}}
                </h3>
                <p class="text-xs text-muted leading-snug">
                    {{get_static_option_central('custom_domain_settings_description') ?: __('Add the following records to your DNS provider')}}
                </p>
            </div>
        </div>

        {{-- Screenshot Example --}}
        @php
            $screenshotId  = get_static_option_central('custom_domain_settings_screem_show_image');
            $screenshotImg = $screenshotId ? (get_attachment_image_by_id($screenshotId)['img_url'] ?? null) : null;
        @endphp
        @if($screenshotImg)
        <div class="px-5 pt-4">
            <img src="{{ $screenshotImg }}" alt="{{ __('DNS Setup Example') }}"
                 class="w-full rounded-xl border border-main object-contain max-h-72">
        </div>
        @endif

        {{-- DNS Table --}}
        <div class="px-5 py-4">
            @if(get_static_option_central('custom_domain_table_title'))
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted mb-3">
                    {{get_static_option_central('custom_domain_table_title')}}
                </p>
            @endif
            <div class="overflow-x-auto rounded-xl border border-main">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-secondary">
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest text-muted border-b border-main">{{__('Type')}}</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest text-muted border-b border-main">{{__('Host')}}</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest text-muted border-b border-main">{{__('Value')}}</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest text-muted border-b border-main">{{__('TTL')}}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border-subtle)]">
                        <tr class="hover:bg-secondary transition-colors">
                            <td class="px-4 py-3">
                                <span class="tw-pill tw-pill-info">CNAME</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-dark font-semibold">www</td>
                            <td class="px-4 py-3 font-mono text-xs text-brand">{{env('CENTRAL_DOMAIN')}}</td>
                            <td class="px-4 py-3 text-xs text-muted">{{__('Automatic')}}</td>
                        </tr>
                        <tr class="hover:bg-secondary transition-colors">
                            <td class="px-4 py-3">
                                <span class="tw-pill tw-pill-info">CNAME</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-dark font-semibold">@</td>
                            <td class="px-4 py-3 font-mono text-xs text-brand">{{env('CENTRAL_DOMAIN')}}</td>
                            <td class="px-4 py-3 text-xs text-muted">{{__('Automatic')}}</td>
                        </tr>
                        <tr class="bg-warning-soft/40">
                            <td colspan="4" class="px-4 py-2.5">
                                <div class="flex items-center gap-2 text-xs text-warning font-semibold">
                                    <i class="mdi mdi-cloud-outline text-sm"></i>
                                    {{__('Use the record below if you are using Cloudflare')}}
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-secondary transition-colors">
                            <td class="px-4 py-3">
                                <span class="tw-pill tw-pill-warning">A Record</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-dark font-semibold">@</td>
                            <td class="px-4 py-3 font-mono text-xs text-brand">{{get_static_option_central('server_ip') ?? ($_SERVER['SERVER_ADDR'] ?? '')}}</td>
                            <td class="px-4 py-3 text-xs text-muted">{{__('Automatic')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Quick Help Steps --}}
    <div class="xl:col-span-2 bg-surface rounded-2xl shadow-main border border-main overflow-hidden">
        <div class="px-5 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-lightbulb-on-outline text-success text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('How It Works')}}</h3>
                <p class="text-xs text-muted">{{__('3 simple steps to connect your domain')}}</p>
            </div>
        </div>
        <div class="px-5 py-5 flex flex-col gap-4">
            @php
                $steps = [
                    ['icon' => 'mdi-web', 'color' => 'bg-primary-soft text-primary', 'title' => __('Request Domain'), 'desc' => __('Click "Request Custom Domain" and enter your domain name.')],
                    ['icon' => 'mdi-dns', 'color' => 'bg-info-soft text-info', 'title' => __('Update DNS Records'), 'desc' => __('Add the CNAME or A records shown on the left to your DNS provider.')],
                    ['icon' => 'mdi-check-circle-outline', 'color' => 'bg-success-soft text-success', 'title' => __('Wait for Approval'), 'desc' => __('Our team will verify and connect your domain. You will be notified once live.')],
                ];
            @endphp
            @foreach($steps as $i => $step)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg {{$step['color']}} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="mdi {{$step['icon']}} text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-dark">{{$i+1}}. {{$step['title']}}</p>
                        <p class="text-xs text-muted leading-relaxed mt-0.5">{{$step['desc']}}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Current Domain Status --}}
<div class="bg-surface rounded-2xl shadow-main border border-main overflow-hidden mt-5">
    <div class="px-5 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-earth text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Domain Request Status')}}</h3>
                <p class="text-xs text-muted">{{__('Track the progress of your custom domain request')}}</p>
            </div>
        </div>
        <button onclick="openDomainModal()"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary hover:text-white hover:border-primary transition">
            <i class="mdi mdi-plus text-sm"></i>
            {{__('New Request')}}
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-secondary border-b border-main">
                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Current Domain')}}</th>
                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Requested Domain')}}</th>
                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Status')}}</th>
                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Date')}}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-[var(--color-border-subtle)] hover:bg-secondary transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-web text-muted text-sm"></i>
                            <span class="text-sm font-semibold text-dark font-mono">
                                {{ optional(optional(tenant()->domains)->first())->domain ?? '—' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-arrow-right-circle-outline text-primary text-sm"></i>
                            <span class="text-sm font-mono text-brand">
                                {{optional($custom_domain_info)->custom_domain ?? '—'}}
                            </span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        @php $status = optional($custom_domain_info)->custom_domain_status; @endphp
                        @if($status === 'pending')
                            <span class="tw-pill tw-pill-warning">
                                <i class="mdi mdi-clock-outline mr-1"></i>{{__('Pending')}}
                            </span>
                        @elseif($status === 'in_progress')
                            <span class="tw-pill tw-pill-info">
                                <i class="mdi mdi-progress-clock mr-1"></i>{{__('In Progress')}}
                            </span>
                        @elseif($status === 'connected')
                            <span class="tw-pill tw-pill-success">
                                <i class="mdi mdi-check-circle-outline mr-1"></i>{{__('Connected')}}
                            </span>
                        @elseif($status === 'rejected')
                            <span class="tw-pill tw-pill-danger">
                                <i class="mdi mdi-close-circle-outline mr-1"></i>{{__('Rejected')}}
                            </span>
                        @elseif($status === null)
                            <span class="tw-pill tw-pill-gray">{{__('Not Requested')}}</span>
                        @else
                            <span class="tw-pill tw-pill-danger">
                                <i class="mdi mdi-link-variant-off mr-1"></i>{{$status ?? __('Removed')}}
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-muted">
                        <div class="flex items-center gap-1.5">
                            <i class="mdi mdi-calendar-outline text-muted text-sm"></i>
                            {{date('d M Y', strtotime($user_domain_infos->created_at))}}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ============================================================
     Request Domain Modal — Custom Tailwind overlay
     ============================================================ --}}
<div id="domainRequestModal"
     class="fixed inset-0 z-[999] hidden items-center justify-center"
     aria-modal="true" role="dialog">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDomainModal()"></div>

    {{-- Dialog --}}
    <div class="relative w-full max-w-lg mx-4 bg-surface rounded-2xl shadow-xl border border-main overflow-hidden">

        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-web-plus text-primary text-base"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-dark font-urbanist">{{__('Request Custom Domain')}}</h4>
                    <p class="text-xs text-muted">{{__('Enter your domain to submit a connection request')}}</p>
                </div>
            </div>
            <button onclick="closeDomainModal()"
                class="w-8 h-8 rounded-lg hover:bg-secondary border border-transparent hover:border-main flex items-center justify-center text-muted hover:text-dark transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>

        <form action="{{route('tenant.admin.custom.domain.requests')}}" method="post">
            @csrf
            <div class="px-6 py-5 flex flex-col gap-5">

                {{-- Validation errors --}}
                <x-error-msg-tw/>

                {{-- Danger flash (e.g. reserved domain) —— shown here since modal auto-opens --}}
                @if(session('type') === 'danger')
                    <x-flash-msg-tw/>
                @endif

                {{-- Info notice --}}
                <div class="flex items-start gap-3 bg-warning-soft/60 border border-warning/20 rounded-xl px-4 py-3">
                    <i class="mdi mdi-information-outline text-warning text-base flex-shrink-0 mt-0.5"></i>
                    <p class="text-xs text-warning leading-relaxed">
                        {{__('You already have a custom domain connected. If you request a new one and it gets approved, your current domain will be replaced.')}}
                    </p>
                </div>

                {{-- Domain Input --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-muted mb-2">
                        {{__('Your Custom Domain')}}
                    </label>
                    <input type="hidden" name="user_id" value="{{$user_domain_infos->id}}">
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-web text-muted text-base flex-shrink-0"></i>
                        <input type="text"
                               name="custom_domain"
                               id="custom_domain_input"
                               placeholder="{{ __('yourdomain.com') }}"
                               value="{{ old('custom_domain', $custom_domain_info->custom_domain ?? '') }}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-[var(--color-text-subtle)] outline-none border-none focus:ring-0 p-0">
                    </div>
                    <div id="subdomain-wrap" class="mt-1.5 min-h-[1.25rem]"></div>
                    <p class="text-[11px] text-muted mt-1.5 leading-relaxed">
                        {{__('Valid formats: domain.tld · www.domain.tld · subdomain.domain.tld')}}
                        <br>
                        <span class="font-semibold text-danger">{{__('Do not include http:// or https://')}}</span>
                    </p>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-main bg-secondary flex items-center justify-end gap-2">
                <button type="button" onclick="closeDomainModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-surface border border-main text-dark text-sm font-semibold hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit" id="domain_submit_btn"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="mdi mdi-send-outline text-base"></i>
                    {{__('Send Request')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    "use strict";

    /* ---------- modal helpers ---------- */
    function openDomainModal() {
        var modal = document.getElementById('domainRequestModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeDomainModal() {
        var modal = document.getElementById('domainRequestModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
    window.openDomainModal  = openDomainModal;
    window.closeDomainModal = closeDomainModal;

    /* ---------- domain availability check ---------- */
    var debounceTimer;

    function stripTags(str) {
        return (str || '').toString().replace(/(<([^>]+)>)/ig, '');
    }

    document.getElementById('custom_domain_input').addEventListener('input', function () {
        var input = this;
        var value = stripTags(input.value).toLowerCase().replace(/\s/g, '-');
        input.value = value;

        var wrap = document.getElementById('subdomain-wrap');
        var btn  = document.getElementById('domain_submit_btn');

        if (value.length < 1) {
            wrap.innerHTML = '';
            btn.disabled = false;
            return;
        }

        clearTimeout(debounceTimer);
        wrap.innerHTML = '<span class="text-xs text-warning flex items-center gap-1"><i class="mdi mdi-loading mdi-spin"></i>{{__("Checking availability…")}}</span>';
        btn.disabled = true;

        debounceTimer = setTimeout(function () {
            axios({
                url: "{{route('tenant.admin.custom.domain.check')}}",
                method: 'post',
                responseType: 'json',
                data: { subdomain: value }
            }).then(function (res) {
                var d = res.data;
                if (d.type === 'success') {
                    wrap.innerHTML = '<span class="text-xs text-success flex items-center gap-1"><i class="mdi mdi-check-circle-outline"></i>' + d.msg + '</span>';
                    btn.disabled = false;
                } else {
                    wrap.innerHTML = '<span class="text-xs text-danger flex items-center gap-1"><i class="mdi mdi-close-circle-outline"></i>' + d.msg + '</span>';
                    btn.disabled = true;
                }
            }).catch(function (err) {
                var errors = err.response && err.response.data && err.response.data.errors;
                var msg = errors && errors.subdomain ? errors.subdomain : '{{__("An error occurred.")}}';
                wrap.innerHTML = '<span class="text-xs text-danger flex items-center gap-1"><i class="mdi mdi-alert-circle-outline"></i>' + msg + '</span>';
                btn.disabled = true;
            });
        }, 400);
    });

    /* ---------- auto-reopen on validation error or danger flash ---------- */
    @if($errors->any() || session('type') === 'danger')
    openDomainModal();
    @endif

    /* ---------- close modal on Escape ---------- */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDomainModal();
    });
})();
</script>
@endsection
