@extends('landlord.admin.admin-master')
@section('title') {{__('All Failed Tenants')}} @endsection

@section('style')
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-alert-circle-outline text-danger text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Failed Tenants')}}</h3>
                <p class="text-xs text-muted">{{__('Tenants that failed during creation')}}</p>
            </div>
        </div>
        <a href="{{route('landlord.admin.tenant')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-soft border border-main text-primary text-sm font-semibold hover:bg-primary hover:text-white hover:border-primary transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{__('All Tenants')}}
        </a>
    </div>

    {{-- Warning Banner --}}
    <div class="px-4 sm:px-6 py-3 bg-danger-soft border-b border-main">
        <p class="text-xs text-danger flex items-start gap-2">
            <i class="mdi mdi-alert-circle-outline text-sm flex-shrink-0 mt-0.5"></i>
            <span>{{__('These tenants encountered errors during creation. You can regenerate or delete them.')}}</span>
        </p>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="failedTenantsTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Tenant Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Domain')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Theme')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Payment Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($tenants as $user)
                @php
                    $payment_log = $user?->payment_log;
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- Tenant Name --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-danger-soft flex items-center justify-center text-danger text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($user->id, 0, 2)) }}
                            </span>
                            <span class="text-sm font-semibold text-dark">{{$user->id}}</span>
                        </div>
                    </td>

                    {{-- Domain --}}
                    <td class="px-4 py-3.5">
                        <span class="text-xs text-muted font-mono">{{$user->id.'.'.env('CENTRAL_DOMAIN')}}</span>
                    </td>

                    {{-- Theme --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-primary-soft text-primary text-[11px] font-bold border border-main">
                            <i class="mdi mdi-palette-outline text-[10px]"></i>
                            {{$user->theme_slug}}
                        </span>
                    </td>

                    {{-- Payment Status --}}
                    <td class="px-4 py-3.5">
                        @php $pStatus = $payment_log?->payment_status; @endphp
                        @if($pStatus === 'complete')
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[10px]"></i> {{$pStatus}}
                            </span>
                        @elseif($pStatus)
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">
                                <i class="mdi mdi-clock-outline text-[10px]"></i> {{$pStatus}}
                            </span>
                        @else
                            <span class="text-xs text-muted">&mdash;</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">

                                {{-- Trigger button --}}
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-cog-outline text-sm"></i>
                                    {{__('Actions')}}
                                    <i class="mdi mdi-chevron-down text-sm"></i>
                                </button>

                                {{-- Dropdown panel --}}
                                <div class="row-action-menu hidden">

                                    {{-- Open Payment Log --}}
                                    @if(!empty($payment_log))
                                        <button type="button" class="action-item payment_log_modal_open_btn"
                                                data-email="{{$payment_log?->email}}"
                                                data-name="{{$payment_log?->name}}"
                                                data-package="{{$payment_log?->package_name}}"
                                                data-gateway="{{$payment_log?->package_gateway}}"
                                                data-tenant="{{$payment_log?->tenant_id}}"
                                                data-theme="{{$payment_log?->theme_slug}}"
                                                data-status="{{$payment_log?->status}}"
                                                data-payment_status="{{$payment_log?->payment_status}}"
                                                data-transaction_id="{{$payment_log?->transaction_id}}"
                                                data-created_at="{{$payment_log?->created_at}}">
                                            <span class="action-icon bg-info-soft"><i class="mdi mdi-file-document-outline text-info"></i></span>
                                            {{__('Open Payment Log')}}
                                        </button>
                                    @endif

                                    {{-- Edit Tenant --}}
                                    <button type="button" data-id="{{$user->id}}" class="action-item tenant_edit_btn">
                                        <span class="action-icon bg-primary-soft"><i class="mdi mdi-pencil-outline text-primary"></i></span>
                                        {{__('Edit Tenant')}}
                                    </button>

                                    {{-- Regenerate --}}
                                    <button type="button" class="action-item user_add_subscription"
                                            data-id="{{$user->id}}"
                                            data-status="{{$payment_log?->status}}"
                                            data-user="{{ !empty($payment_log?->user_id) }}">
                                        <span class="action-icon bg-success-soft"><i class="mdi mdi-refresh text-success"></i></span>
                                        {{__('Regenerate')}}
                                    </button>

                                    {{-- Create Payment Log --}}
                                    @if(empty($user->payment_log))
                                        <button type="button" data-id="{{$user->id}}" class="action-item tenant_create_payment_log_btn">
                                            <span class="action-icon bg-warning-soft"><i class="mdi mdi-plus-circle-outline text-warning"></i></span>
                                            {{__('Create Payment Log')}}
                                        </button>
                                    @endif

                                    <div class="menu-divider"></div>

                                    {{-- Delete --}}
                                    <button type="button" class="action-item action-danger swal_delete_button"
                                            data-id="{{$user->id}}">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete Tenant')}}
                                    </button>
                                    <form method="post" action="{{route('landlord.admin.tenant.failed.delete', $user->id)}}" class="hidden d-none">
                                        @csrf
                                        <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- ===== TAILWIND MODALS ===== --}}

{{-- Regenerate Tenant Modal --}}
<div id="regenerate_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="regenerate_modal_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-refresh text-success text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Regenerate Tenant')}}</h5>
                <p class="text-[11px] text-muted">{{__('Retry tenant creation with updated status')}}</p>
            </div>
            <button type="button" id="regenerate_modal_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-success-soft hover:text-success transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <form action="{{route('landlord.admin.tenant.failed.assign.subscription')}}"
              id="user_add_subscription_form" method="post" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <input type="hidden" name="subs_tenant_id" id="subs_user_id">
                <input type="hidden" name="subs_pack_id" id="subs_pack_id">

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Account Status')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-shield-account-outline text-lg text-primary"></i>
                        <select name="account_status"
                                class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option value="complete">{{__('Complete')}}</option>
                            <option value="pending">{{__('Pending')}}</option>
                            <option value="trial">{{__('Trial')}}</option>
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                    <p class="text-[11px] text-primary mt-1.5 flex items-center gap-1">
                        <i class="mdi mdi-information-outline text-sm"></i>
                        {{__('You can set account status pending or complete from here')}}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" id="regenerate_modal_cancel"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-success rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Submit')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Tenant Modal --}}
<div id="edit_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="edit_modal_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-pencil-outline text-primary text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Tenant')}}</h5>
                <p class="text-[11px] text-muted">{{__('Change the tenant domain name')}}</p>
            </div>
            <button type="button" id="edit_modal_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-primary-soft hover:text-primary transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <form action="{{route('landlord.admin.tenant.failed.edit')}}" id="tenant_edit_modal_form" method="post">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <input type="hidden" name="tenant_id" id="tenant_id">

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Domain Name')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-web text-lg text-primary"></i>
                        <input type="text" name="tenant_name"
                               placeholder="{{__('Enter Domain Name')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" id="edit_modal_cancel"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Update')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Payment Log Modal --}}
<div id="payment_log_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="payment_log_modal_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-file-document-outline text-info text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Tenant Payment Log')}}</h5>
                <p class="text-[11px] text-muted">{{__('Payment and account details')}}</p>
            </div>
            <button type="button" id="payment_log_modal_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-info-soft hover:text-info transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <div class="px-6 py-5 payment-log-body max-h-[65vh] overflow-y-auto">
            {{-- Populated dynamically via JS --}}
        </div>

        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
            <button type="button" id="payment_log_modal_cancel"
                    class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                {{__('Close')}}
            </button>
        </div>
    </div>
</div>

{{-- Create Payment Log Modal --}}
<div id="create_payment_log_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="create_pl_modal_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-lg overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-plus-circle-outline text-warning text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Create Tenant Payment Log')}}</h5>
                <p class="text-[11px] text-muted">{{__('Manually create a payment log for this tenant')}}</p>
            </div>
            <button type="button" id="create_pl_modal_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-warning-soft hover:text-warning transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <form action="{{route('landlord.admin.tenant.failed.manual.paymentlog')}}" method="POST">
            @csrf
            <div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">
                <input type="hidden" name="tenant_id" id="create_pl_tenant_id">

                {{-- User Select --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('User')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-account-outline text-lg text-primary"></i>
                        <select name="user" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option value="" selected disabled>{{__('Select a user')}}</option>
                            @foreach($users as $u)
                                <option value="{{$u->id}}">{{$u->name}}</option>
                            @endforeach
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>

                {{-- Subdomain --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subdomain')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-web text-lg text-primary"></i>
                        <input class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 custom_subdomain"
                               id="custom-subdomain" type="text" autocomplete="off"
                               value="{{old('subdomain')}}" placeholder="{{__('Subdomain')}}">
                    </div>
                    <div id="subdomain-wrap" class="mt-1"></div>
                </div>

                {{-- Theme --}}
                <div>
                    @php $themes = getAllThemeSlug(); @endphp
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Theme')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-palette-outline text-lg text-primary"></i>
                        <select name="custom_theme" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer text-capitalize">
                            @foreach($themes as $theme)
                                <option value="{{$theme}}">{{$theme}}</option>
                            @endforeach
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>

                {{-- Package --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Select A Package')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-package-variant text-lg text-primary"></i>
                        <select name="package" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer package_id_selector">
                            <option value="">{{__('Select Package')}}</option>
                            @foreach(\App\Models\PricePlan::all() as $price)
                                <option value="{{$price->id}}" data-id="{{$price->id}}">
                                    {{$price->title}} {{ '('.float_amount_with_currency_symbol($price->price).')' }}
                                    - {{\App\Enums\PricePlanTypEnums::getText($price->type)}}
                                </option>
                            @endforeach
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>

                {{-- Payment Status --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Payment Status')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-credit-card-check-outline text-lg text-primary"></i>
                        <select name="payment_status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option value="complete">{{__('Complete')}}</option>
                            <option value="pending">{{__('Pending')}}</option>
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                    <p class="text-[11px] text-primary mt-1.5 flex items-center gap-1">
                        <i class="mdi mdi-information-outline text-sm"></i>
                        {{__('You can set payment status pending or complete from here')}}
                    </p>
                </div>

                {{-- Account Status --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Account Status')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-shield-account-outline text-lg text-primary"></i>
                        <select name="status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option value="complete">{{__('Complete')}}</option>
                            <option value="pending">{{__('Pending')}}</option>
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                    <p class="text-[11px] text-primary mt-1.5 flex items-center gap-1">
                        <i class="mdi mdi-information-outline text-sm"></i>
                        {{__('You can set account status pending or complete from here')}}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" id="create_pl_modal_cancel"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-success rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Create')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Send Mail Modal --}}
<div id="mail_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="mail_modal_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-lg overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-email-outline text-warning text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Send Mail To Subscriber')}}</h5>
                <p class="text-[11px] text-muted">{{__('Compose and send an email to this user')}}</p>
            </div>
            <button type="button" id="mail_modal_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-warning-soft hover:text-warning transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <form action="{{route(route_prefix().'admin.tenant.send.mail')}}" id="send_mail_to_subscriber_edit_modal_form" method="post">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Email')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5">
                        <i class="mdi mdi-at text-lg text-primary"></i>
                        <input type="text" readonly
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                               id="email" name="email" placeholder="{{__('Email')}}">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subject')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-text-box-outline text-lg text-primary"></i>
                        <input type="text"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                               id="subject" name="subject" placeholder="{{__('Subject')}}">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Message')}}</label>
                    <input type="hidden" name="message">
                    <div class="summernote"></div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" id="mail_modal_cancel"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button id="submit" type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-warning rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-send text-base"></i>
                    {{__('Send Mail')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script src="{{global_asset('assets/landlord/common/js/summernote-lite.min.js')}}"></script>
    <x-custom-js.landloard-unique-subdomain-check :name="'custom_subdomain'"/>

    <script>
    (function ($) {
        "use strict";

        // ── Modal helpers ────────────────────────────────────────────────
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // ── Row action dropdown (fixed-position, flips up near bottom) ──
        window.toggleRowMenu = function (btn) {
            var menu = btn.nextElementSibling;
            var isHidden = menu.classList.contains('hidden');

            document.querySelectorAll('.row-action-menu').forEach(function (m) {
                m.classList.add('hidden');
            });

            if (isHidden) {
                var rect       = btn.getBoundingClientRect();
                var menuHeight = 320;
                var spaceBelow = window.innerHeight - rect.bottom;
                var spaceAbove = rect.top;

                menu.style.right = (window.innerWidth - rect.right) + 'px';
                menu.style.left  = 'auto';

                if (spaceBelow >= Math.min(menuHeight, 200) || spaceBelow >= spaceAbove) {
                    menu.style.top    = (rect.bottom + 4) + 'px';
                    menu.style.bottom = 'auto';
                } else {
                    menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                    menu.style.top    = 'auto';
                }

                menu.classList.remove('hidden');
            }
        };

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.row-action-wrap')) {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            }
        });

        window.addEventListener('scroll', function (e) {
            if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
            document.querySelectorAll('.row-action-menu').forEach(function (m) {
                m.classList.add('hidden');
            });
        }, true);

        // close on .tw-table-wrap horizontal scroll
        document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
            wrap.addEventListener('scroll', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            });
        });

        function escHtml(str) {
            if (str == null) return '';
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }

        $(document).ready(function () {

            // ── DataTable init ────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#failedTenantsTable')) {
                $('#failedTenantsTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── Edit Tenant Modal ──────────────────────────────────────
            $(document).on('click', '.tenant_edit_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let id = $(this).data('id');
                let modal = $('#tenant_edit_modal_form');
                modal.find('input[name=tenant_id]').val(id);
                modal.find('input[name=tenant_name]').val(id);
                openModal('edit_modal');
            });
            $('#edit_modal_close, #edit_modal_cancel, #edit_modal_backdrop').on('click', function () {
                closeModal('edit_modal');
            });

            // ── Payment Log Modal ──────────────────────────────────────
            $(document).on('click', '.payment_log_modal_open_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let el = $(this);
                let email = el.data('email');
                let name = el.data('name');
                let pkg = el.data('package');
                let gateway = el.data('gateway');
                let tenant = el.data('tenant');
                let theme = el.data('theme');
                let status = el.data('status');
                let payment_status = el.data('payment_status');
                let transaction_id = el.data('transaction_id');
                let created_at = el.data('created_at');

                let statusColor = payment_status === 'complete'
                    ? 'bg-success-soft text-success'
                    : 'bg-warning-soft text-warning';

                let markup = `
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('User Information')}}</p>
                            <div class="bg-secondary rounded-xl border border-main p-3 space-y-1.5">
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-at text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Email')}}</span> ${escHtml(email)}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-account-outline text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Name')}}</span> ${escHtml(name)}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-web text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Tenant')}}</span> ${escHtml(tenant)}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-shield-check-outline text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Status')}}</span> ${escHtml(status)}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Theme Information')}}</p>
                            <div class="bg-secondary rounded-xl border border-main p-3">
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-palette-outline text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Theme')}}</span> ${escHtml(theme)}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Payment Information')}}</p>
                            <div class="bg-secondary rounded-xl border border-main p-3 space-y-1.5">
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-package-variant text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Package')}}</span> ${escHtml(pkg)}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-credit-card-outline text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Gateway')}}</span> ${escHtml(gateway)}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-check-circle-outline text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Payment')}}</span> <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase ${statusColor}">${escHtml(payment_status)}</span></p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-identifier text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Txn ID')}}</span> ${escHtml(transaction_id)}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-calendar-outline text-primary"></i> <span class="text-muted w-20 shrink-0">{{__('Date')}}</span> ${escHtml(created_at)}</p>
                            </div>
                        </div>
                    </div>
                `;

                let body = $('#payment_log_modal .payment-log-body');
                body.html(markup);
                openModal('payment_log_modal');
            });
            $('#payment_log_modal_close, #payment_log_modal_cancel, #payment_log_modal_backdrop').on('click', function () {
                closeModal('payment_log_modal');
            });

            // ── Create Payment Log Modal ───────────────────────────────
            $(document).on('click', '.tenant_create_payment_log_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let tenant_id = $(this).data('id');
                $('#create_pl_tenant_id').val(tenant_id);
                openModal('create_payment_log_modal');
            });
            $('#create_pl_modal_close, #create_pl_modal_cancel, #create_pl_modal_backdrop').on('click', function () {
                closeModal('create_payment_log_modal');
            });

            // ── Regenerate Tenant Modal ────────────────────────────────
            $(document).on('click', '.user_add_subscription', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let user_id = $(this).data('id');
                let status = $(this).data('status');

                $('#subs_user_id').val(user_id);

                let modal = $('#regenerate_modal');
                modal.find('select option').attr('selected', false);

                if (status !== undefined) {
                    modal.find('select option[value=' + status + ']').attr('selected', true);
                }

                openModal('regenerate_modal');
            });
            $('#regenerate_modal_close, #regenerate_modal_cancel, #regenerate_modal_backdrop').on('click', function () {
                closeModal('regenerate_modal');
            });

            $(document).on('change', '.package_id_selector', function () {
                $('#subs_pack_id').val($(this).val());
            });

            $(document).on('submit', '#user_add_subscription_form', function () {
                $(this).find('button[type=submit]').attr('disabled', true);
            });

            // ── Send Mail Modal ────────────────────────────────────────
            $(document).on('click', '.send_mail_to_tenant_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                $('#send_mail_to_subscriber_edit_modal_form').find('#email').val($(this).data('id'));
                $('.summernote').summernote('reset');
                openModal('mail_modal');
            });
            $('#mail_modal_close, #mail_modal_cancel, #mail_modal_backdrop').on('click', function () {
                closeModal('mail_modal');
            });

            // ── Summernote ─────────────────────────────────────────────
            if ($.fn.summernote) {
                $('.summernote').summernote({
                    placeholder: "{{__('Start writing your message here...')}}",
                    tabsize: 2,
                    height: 200,
                    toolbar: [
                        ['style',  ['style']],
                        ['font',   ['bold', 'underline', 'clear']],
                        ['color',  ['color']],
                        ['para',   ['ul', 'ol', 'paragraph']],
                        ['table',  ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view',   ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onChange: function (contents, $editable) {
                            $(this).prev('input').val(contents);
                        },
                        onPaste: function (e) {
                            var buf = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                            e.preventDefault();
                            document.execCommand('insertText', false, buf);
                        }
                    }
                });
            }

            $(document).on('submit', '#send_mail_to_subscriber_edit_modal_form', function (e) {
                var content = $('.summernote').summernote('code');
                $('input[name=message]').val(content);
                if (!content.trim() || content === '<p><br></p>') {
                    e.preventDefault();
                    alert('{{__("Please enter a message")}}');
                    return false;
                }
            });

        });
    })(jQuery);
    </script>
@endsection
