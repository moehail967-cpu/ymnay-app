@php
    $route_name = 'landlord';
@endphp

@extends($route_name.'.admin.admin-master')
@section('title') {{__('All Users')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <x-summernote.css/>
    <style>
        .change-theme { cursor: pointer; color: #0a53be; display: none; }
    </style>
    <style>.hover\:text-white:hover{color:#fff!important}</style>

@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Stats Strip --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-5">
    @php
        $totalVerified   = $all_users->where('email_verified', 1)->count();
        $totalUnverified = $all_users->where('email_verified', 0)->count();
        $totalShops      = $all_users->sum(fn($u) => $u->tenant_details->count());
    @endphp
    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-account-group text-primary text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Total Users')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$all_users->count()}}</p>
        </div>
    </div>
    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-email-check text-success text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Verified')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$totalVerified}}</p>
        </div>
    </div>
    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-email-remove text-danger text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Unverified')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$totalUnverified}}</p>
        </div>
    </div>
    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-store text-warning text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Total Shops')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$totalShops}}</p>
        </div>
    </div>
</div>

{{-- Table Card — no overflow-hidden so dropdowns aren't clipped --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-account-multiple text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Users')}}</h3>
                <p class="text-xs text-muted">{{__('All tenant user accounts')}}</p>
            </div>
        </div>
        <a href="{{route('landlord.admin.tenant.trash')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-danger-soft border border-main text-danger text-sm font-semibold hover:bg-danger hover:text-white hover:border-danger transition whitespace-nowrap">
            <i class="mdi mdi-delete-clock text-base"></i>
            {{__('Trash')}} {{$deleted_users > 0 ? "({$deleted_users})" : ""}}
        </a>
    </div>

    {{-- Warning Banner --}}
    <div class="px-4 sm:px-6 py-3 bg-danger-soft border-b border-main">
        <p class="text-xs text-danger flex items-start gap-2">
            <i class="mdi mdi-alert-circle-outline text-sm flex-shrink-0 mt-0.5"></i>
            <span>{{__('If you delete any user and if it\'s associated with any package than everything regarding with this user will be deleted')}}</span>
        </p>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="allUsersTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Verified')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Shops')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_users as $user)
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

                    $select = '<option value="" selected disabled>Select a subdomain</option>';
                    foreach ($user->tenant_details ?? [] as $tenant) {
                        $select .= '<option value="'.$tenant->id.'">'.optional($tenant->domain)->domain.'</option>';
                    }
                    $select .= '<option value="custom_domain__dd">Add new subdomain</option>';
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$user->id}}</span>
                    </td>

                    {{-- User --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center text-[10px] font-bold flex-shrink-0 border-2 border-main {{ $pal['bg'] }} {{ $pal['text'] }} uppercase">
                                {{$initials}}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-dark truncate leading-tight">{{$user->name}}</p>
                                <p class="text-[11px] text-muted truncate mt-0.5 flex items-center gap-1 flex-wrap">
                                    <i class="mdi mdi-at text-primary text-xs flex-shrink-0"></i>
                                    <span class="truncate max-w-[180px]">{{$user->email}}</span>
                                </p>
                                <p class="sm:hidden text-[11px] text-muted mt-0.5 flex items-center gap-1">
                                    <i class="mdi mdi-store text-primary text-xs"></i>
                                    {{$user->tenant_details->count()}} {{__('shop(s)')}}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="min-w-0">
                                <p class="text-[11px] text-muted truncate mt-0.5 flex items-center gap-1 flex-wrap">
                                    @if($user->email_verified === 0)
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-danger-soft text-danger text-[9px] font-bold uppercase">
                                            <i class="mdi mdi-close-circle text-[9px]"></i>{{__('Unverified')}}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-success-soft text-success text-[9px] font-bold uppercase">
                                            <i class="mdi mdi-check-circle text-[9px]"></i>{{__('Verified')}}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </td>
                    {{-- Shops --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary-soft text-primary text-xs font-bold border border-main">
                            <i class="mdi mdi-store-outline text-xs"></i>
                            {{$user->tenant_details->count()}}
                        </span>
                    </td>

                    {{-- Actions: dropdown menu --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">

                                {{-- Edit --}}
                                <a href="{{route('landlord.admin.tenant.edit.profile', $user->id)}}"
                                   title="{{__('Edit')}}"
                                   class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:text-white hover:bg-primary transition-all">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </a>

                                <a href="{{route('landlord.admin.tenant.details', $user->id)}}"
                                   title=" {{__('View Details')}}"
                                   class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:text-white hover:bg-primary transition-all">
                                    <span class="action-icon "><i class="mdi mdi-eye-outline"></i></span>
                                </a>


                                {{-- Trigger button --}}

                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-cog-outline text-sm"></i>
                                    {{__('Actions')}}
                                    <i class="mdi mdi-chevron-down text-sm"></i>
                                </button>

                                {{-- Dropdown panel --}}
                                <div class="row-action-menu hidden">

                                    <div class="menu-divider"></div>

                                    {{-- Change Password --}}
                                    <button type="button" data-id="{{$user->id}}" class="action-item tenant_change_password_btn">
                                        <span class="action-icon bg-info-soft"><i class="mdi mdi-lock-reset text-info"></i></span>
                                        {{__('Change Password')}}
                                    </button>

                                    {{-- Create / Renew Store --}}
                                    <button type="button" data-id="{{$user->id}}" data-select-markup="{{$select}}" class="action-item user_add_subscription">
                                        <span class="action-icon bg-success-soft"><i class="mdi mdi-store text-success"></i></span>
                                        {{__('Create / Renew Store')}}
                                    </button>

                                    {{-- Send Mail --}}
                                    <button type="button" data-id="{{$user->email}}" class="action-item send_mail_to_tenant_btn">
                                        <span class="action-icon bg-warning-soft"><i class="mdi mdi-email-outline text-warning"></i></span>
                                        {{__('Send Mail')}}
                                    </button>

                                    {{-- Login to Account --}}
                                    @can('users-direct-login')
                                        @php
                                            $local_url  = env('CENTRAL_DOMAIN');
                                            $url        = tenant_url_with_protocol($local_url);
                                            $hash_token = hash_hmac('sha512', $user->username, $user->id);
                                        @endphp
                                        <a href="{{$url.'/token-login/'.$hash_token.'?user_id='.$user->id}}" target="_blank">
                                            <span class="action-icon bg-[#f3e8ff]"><i class="mdi mdi-login text-[#9333ea]"></i></span>
                                            {{__('Login to Account')}}
                                        </a>
                                    @endcan

                                    {{-- Verify actions: only when email not verified --}}
                                    @if($user->email_verified < 1)
                                        <div class="menu-divider"></div>

                                        <form action="{{route(route_prefix().'admin.tenant.resend.verify.mail')}}" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$user->id}}">
                                            <button type="submit" class="action-item">
                                                <span class="action-icon bg-[#f3e8ff]"><i class="mdi mdi-email-check-outline text-[#9333ea]"></i></span>
                                                {{__('Send Verify Mail')}}
                                            </button>
                                        </form>

                                        <form action="{{route(route_prefix().'admin.tenant.verify.account')}}" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$user->id}}">
                                            <button type="submit" class="action-item">
                                                <span class="action-icon bg-success-soft"><i class="mdi mdi-check-decagram text-success"></i></span>
                                                {{__('Verify Account')}}
                                            </button>
                                        </form>
                                    @endif

                                    <div class="menu-divider"></div>

                                    {{-- Delete --}}
                                    <button type="button" class="action-item action-danger swal_delete_button">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete User')}}
                                    </button>
                                    <form method="post" action="{{route('landlord.admin.tenant.delete', $user->id)}}" class="hidden">
                                        @csrf
                                        <button type="submit" class="swal_form_submit_btn hidden"></button>
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

{{-- Change Password Modal --}}
<div id="tenant_pwd_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="pwd_modal_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-lock-reset text-info text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Change User Password')}}</h5>
                <p class="text-[11px] text-muted">{{__('Set a new secure password for this user')}}</p>
            </div>
            <button type="button" id="pwd_modal_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-info-soft hover:text-info transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <x-landlord-error-msg/>

        <form action="{{route('landlord.admin.tenant.change.password')}}" id="user_password_change_modal_form" method="post">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <input type="hidden" name="ch_user_id" id="ch_user_id">

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('New Password')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-lock-outline text-lg text-primary"></i>
                        <input type="password" name="password"
                               placeholder="{{__('Enter new password')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Confirm Password')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-lock-check-outline text-lg text-primary"></i>
                        <input type="password" name="password_confirmation"
                               placeholder="{{__('Re-enter password')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                <p class="text-[11px] text-muted flex items-center gap-1.5 pt-1">
                    <i class="mdi mdi-information-outline text-primary text-sm"></i>
                    {{__('Use at least 8 characters including numbers and symbols.')}}
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" id="pwd_modal_cancel"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Update Password')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Assign Subscription Modal --}}
<div id="subs_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="subs_modal_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-lg overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-store text-success text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Assign Subscription')}}</h5>
                <p class="text-[11px] text-muted">{{__('Create or renew a store for this user')}}</p>
            </div>
            <button type="button" id="subs_modal_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-success-soft hover:text-success transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <form action="{{route('landlord.admin.tenant.assign.subscription')}}" id="user_add_subscription_form" method="post" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">
                <input type="hidden" name="subs_user_id" id="subs_user_id">
                <input type="hidden" name="subs_pack_id" id="subs_pack_id">

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subdomain')}}</label>
                    <select class="subdomain w-full bg-secondary border border-main rounded-xl px-4 py-2.5 text-sm text-dark outline-none focus:border-primary transition" id="subdomain" name="subdomain"></select>
                    <p class="mt-1.5 text-right change-theme text-xs text-primary"><small>{{__('Change theme?')}}</small></p>
                </div>

                <div class="custom_subdomain_wrapper">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Add new subdomain')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-web text-lg text-primary"></i>
                        <input class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 custom_subdomain"
                               id="custom-subdomain" type="text" autocomplete="off"
                               value="{{old('subdomain')}}" placeholder="{{__('Subdomain')}}">
                    </div>
                    <div id="subdomain-wrap"></div>
                </div>

                <div id="custom_theme_wrapper">
                    @php $themes = getAllThemeSlug(); @endphp
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Theme')}}</label>
                    <select class="w-full bg-secondary border border-main rounded-xl px-4 py-2.5 text-sm text-dark outline-none focus:border-primary transition text-capitalize" name="custom_theme" id="custom-theme">
                        @foreach($themes as $theme)
                            @php $custom_theme_name = get_static_option_central("${theme}_theme_name") ?? $theme; @endphp
                            <option value="{{$theme}}">{{$custom_theme_name}}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Select A Package')}}</label>
                    <select class="w-full bg-secondary border border-main rounded-xl px-4 py-2.5 text-sm text-dark outline-none focus:border-primary transition package_id_selector" name="package">
                        <option value="">{{__('Select Package')}}</option>
                        @foreach(\App\Models\PricePlan::all() as $price)
                            <option value="{{$price->id}}" data-id="{{$price->id}}">
                                {{$price->title}} {{ '('.float_amount_with_currency_symbol($price->price).')' }} - {{\App\Enums\PricePlanTypEnums::getText($price->type)}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Payment Status')}}</label>
                    <select class="w-full bg-secondary border border-main rounded-xl px-4 py-2.5 text-sm text-dark outline-none focus:border-primary transition" name="payment_status">
                        <option value="complete">{{__('Complete')}}</option>
                        <option value="pending">{{__('Pending')}}</option>
                    </select>
                    <p class="text-[11px] text-primary mt-1.5 flex items-center gap-1">
                        <i class="mdi mdi-information-outline text-sm"></i>
                        {{__('You can set payment status pending or complete from here')}}
                    </p>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Account Status')}}</label>
                    <select class="w-full bg-secondary border border-main rounded-xl px-4 py-2.5 text-sm text-dark outline-none focus:border-primary transition" name="account_status">
                        <option value="complete">{{__('Complete')}}</option>
                        <option value="pending">{{__('Pending')}}</option>
                    </select>
                    <p class="text-[11px] text-primary mt-1.5 flex items-center gap-1">
                        <i class="mdi mdi-information-outline text-sm"></i>
                        {{__('You can set account status pending or complete from here')}}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" id="subs_modal_cancel"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-success rounded-xl hover:opacity-90 transition order-btn">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Submit')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Send Mail Modal --}}
<div id="mail_modal" class="hidden fixed inset-0 z-[999] overflow-y-auto">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="mail_modal_backdrop"></div>
    <div class="flex min-h-full items-center justify-center p-4">
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-lg border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary rounded-t-2xl">
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
                    <textarea name="message" class="summernote w-full"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary rounded-b-2xl">
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
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-media-upload.tw-js/>
    <x-summernote.js/>
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

            // close all open menus first
            document.querySelectorAll('.row-action-menu').forEach(function (m) {
                m.classList.add('hidden');
                m.style.maxHeight = '';
            });

            if (isHidden) {
                var rect       = btn.getBoundingClientRect();
                var spaceBelow = window.innerHeight - rect.bottom - 8;
                var spaceAbove = rect.top - 8;

                menu.style.right = (window.innerWidth - rect.right) + 'px';
                menu.style.left  = 'auto';

                if (spaceBelow >= spaceAbove || spaceBelow >= 150) {
                    // open downward
                    menu.style.top    = (rect.bottom + 4) + 'px';
                    menu.style.bottom = 'auto';
                    menu.style.maxHeight = Math.max(150, spaceBelow) + 'px';
                } else {
                    // flip upward
                    menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                    menu.style.top    = 'auto';
                    menu.style.maxHeight = Math.max(150, spaceAbove) + 'px';
                }

                menu.classList.remove('hidden');
            }
        };

        // close on outside click
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.row-action-wrap')) {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            }
        });

        // close on page scroll, but NOT when scrolling inside the menu itself
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

        $(document).ready(function () {

            // ── Explicit DataTable init (fallback if tw-js fails) ────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#allUsersTable')) {
                $('#allUsersTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── Change Password Modal ────────────────────────────────────
            $(document).on('click', '.tenant_change_password_btn', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                $('#ch_user_id').val($(this).data('id'));
                openModal('tenant_pwd_modal');
            });
            $('#pwd_modal_close, #pwd_modal_cancel, #pwd_modal_backdrop').on('click', function () {
                closeModal('tenant_pwd_modal');
            });

            // ── Assign Subscription Modal ────────────────────────────────
            let theme_selected_first = false;

            $(document).on('click', '.user_add_subscription', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                $('#subs_user_id').val($(this).data('id'));
                $('#subs_modal #subdomain').html($(this).data('select-markup'));
                openModal('subs_modal');
            });
            $('#subs_modal_close, #subs_modal_cancel, #subs_modal_backdrop').on('click', function () {
                closeModal('subs_modal');
            });

            // ── Send Mail Modal ──────────────────────────────────────────
            function openMailModal() {
                openModal('mail_modal');
                
                // Re-initialize Summernote when the modal is shown to fix layout issues.
                // We MUST use dialogsInBody: true so that toolbars and image modals don't get trapped/hidden by the parent Tailwind modal.
                if ($.fn.summernote) {
                    if ($('textarea.summernote').data('summernote')) {
                        $('textarea.summernote').summernote('destroy');
                    }
                    $('textarea.summernote').summernote({
                        placeholder: "{{__('Start writing your message here...')}}",
                        tabsize: 2,
                        height: 200,
                        dialogsInBody: true, // This fixes both the dropdowns (tabs) and the Insert Image modal issue.
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
                            onPaste: function (e) {
                                var buf = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                                e.preventDefault();
                                document.execCommand('insertText', false, buf);
                            }
                        }
                    });
                }
            }

            function closeMailModal() {
                if ($.fn.summernote && $('textarea.summernote').data('summernote')) {
                    $('textarea.summernote').summernote('destroy');
                }
                closeModal('mail_modal');
            }

            $(document).on('click', '.send_mail_to_tenant_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                $('#send_mail_to_subscriber_edit_modal_form').find('#email').val($(this).data('id'));
                openMailModal();
            });
            $('#mail_modal_close, #mail_modal_cancel, #mail_modal_backdrop').on('click', closeMailModal);

            // ── Package selector ─────────────────────────────────────────
            $(document).on('change', '.package_id_selector', function () {
                $('#subs_pack_id').val($(this).val());
            });

            // ── Subdomain switcher ───────────────────────────────────────
            let $customWrap = $('.custom_subdomain_wrapper');
            $customWrap.hide();

            $(document).on('change', '#subdomain', function () {
                if ($(this).val() === 'custom_domain__dd') {
                    $customWrap.slideDown();
                    $customWrap.find('#custom-subdomain').attr('name', 'custom_subdomain');
                    $('#custom_theme_wrapper').show();
                    $('.change-theme').hide();
                } else {
                    $customWrap.slideUp();
                    $customWrap.find('#custom-subdomain').removeAttr('name');
                    $('#custom_theme_wrapper').hide();
                    $('.change-theme').show();

                    if (!theme_selected_first) {
                        $.ajax({
                            url: '{{route('landlord.admin.tenant.check.subdomain.theme')}}',
                            type: 'POST',
                            data: { _token: '{{csrf_token()}}', subdomain: $(this).val() },
                            beforeSend: function () { $('#custom-theme option').attr('selected', false); },
                            success: function (res) { $('#custom-theme option[value=' + res.theme_slug + ']').attr('selected', true); }
                        });
                    }
                }
            });

            $(document).on('click', '.change-theme', function () { $('#custom_theme_wrapper').show(); });
            $(document).on('change', '#custom-theme', function () { theme_selected_first = true; });
            $(document).on('submit', '#user_add_subscription_form', function () {
                $(this).find('button[type=submit]').attr('disabled', true);
            });

$(document).on('submit', '#send_mail_to_subscriber_edit_modal_form', function (e) {
                var content = $('textarea.summernote').summernote('code');
                $('textarea.summernote').val(content);
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
