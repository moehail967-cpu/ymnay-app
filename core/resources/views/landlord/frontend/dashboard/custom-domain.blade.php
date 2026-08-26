@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{__('Custom Domain')}}
@endsection

@section('title')
    {{__('Custom Domain')}}
@endsection

@section('style')
@endsection

@section('section')
    @php
        $central_domain = env('CENTRAL_DOMAIN');
    @endphp

<div class="col-span-full lg:col-span-9">
    <!-- Top Header -->
    <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-b rounded-t-3xl">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 px-4 sm:px-6 lg:pr-8 pt-3 pb-4">
            <div class="flex items-center w-full lg:w-auto">
                <!-- Mobile Menu Button -->
                <button id="menuBtn"
                        class="block lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none pr-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="icon-base ti tabler-menu-2 icon-24px sm:icon-28px"></i>
                </button>

                <div class="ml-3 lg:ml-0 flex-1 lg:flex-none">
                    <h1 class="text-lg sm:text-xl font-medium text-secondary leading-tight">{{__('Custom Domain')}}</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1 line-clamp-1 sm:line-clamp-none">{{__('Configure and manage your custom domain settings')}}</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row w-full lg:w-auto gap-3 lg:gap-2">
                <!-- Search Input -->
                <form method="GET" action="" class="flex-1 sm:flex-initial">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="flex items-center gap-2 border border-gray-300 rounded-lg bg-white px-3 py-2.5 w-full sm:w-64 md:w-72 focus-within:border-gray-400 transition">
                        <i class="icon-base ti tabler-search text-sub2Title text-base flex-shrink-0"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{__('Search domain...')}}"
                               class="flex-1 text-sm text-sub2Title placeholder-gray-400 bg-transparent outline-none min-w-0">
                    </div>
                </form>
                <!-- Status Filter -->
                <form method="GET" action="" id="statusFilterForm" class="flex-1 sm:flex-initial">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="status" onchange="document.getElementById('statusFilterForm').submit()"
                            class="w-full border border-gray-300 rounded-lg bg-white px-3 py-2.5 text-sm text-sub2Title outline-none focus:border-gray-400 transition cursor-pointer">
                        <option value="">{{__('All Status')}}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{__('Pending')}}</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{__('In Progress')}}</option>
                        <option value="connected" {{ request('status') == 'connected' ? 'selected' : '' }}>{{__('Connected')}}</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{__('Rejected')}}</option>
                    </select>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="p-6">
        <!-- Read Before Sending -->
        <div class="bg-primary/5 rounded-2xl p-5 mb-4 border border-borderCS">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="ti tabler-info-circle text-white text-xl"></i>
                </div>
                <h2 class="font-urbanist text-2xl font-medium text-secondary mt-1">{{get_static_option_central('custom_domain_settings_title') ?? __('Read Before Sending Custom Domain Request')}}</h2>
            </div>
            <div class="mb-4">
                <p id="desc-text" class="text-sub2Title text-base leading-relaxed overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                    {{get_static_option_central('custom_domain_settings_description') ?? __('Before sending request for your custom domain, you need to add CNAME records (given in below table) in your custom domain from your domain registrar account.')}}
                </p>
                <button id="read-more-btn" onclick="toggleReadMore()" class="text-sm font-semibold text-gray-800 mt-1 hover:underline focus:outline-none">{{__('Read More..')}}</button>
            </div>
            @php
                $screenshotId  = get_static_option_central('custom_domain_settings_screem_show_image');
                $screenshotImg = $screenshotId ? (get_attachment_image_by_id($screenshotId)['img_url'] ?? null) : null;
            @endphp
            @if($screenshotImg)
            <div class="mb-4">
                <img src="{{ $screenshotImg }}" alt="{{ __('DNS Setup Example') }}"
                     class="w-full rounded-xl border border-borderCS object-contain max-h-72">
            </div>
            @endif
            <hr class="border-gray-100 mb-4"/>
            <div class="flex items-center gap-2 text-sectionC text-base font-normal">
                <i class="ti tabler-alert-circle"></i>
                <span>{{__('Need help? Contact your domain registrar support for assistance with CNAME records')}}</span>
            </div>
        </div>

        <!-- CNAME Records Configuration -->
        <div class="rounded-2xl mb-4 border border-borderCS overflow-hidden" style="background-color: var(--section-bg-1, #ffffff)">
            <div class="p-5 pb-3">
                <h3 class="text-lg font-medium text-secondary mb-1">{{get_static_option_central('custom_domain_table_title') ?? __('CNAME Records Configuration')}}</h3>
                <p class="text-sm font-normal text-sub2Title">{{__('Add CNAME records (take data from below table) in your custom domain from your domain registrar panel')}}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                    <tr class="border-t border-b border-gray-100 bg-[#F8FAFB]">
                        <th class="text-left text-secondary text-base font-medium px-5 py-3">{{__('Type')}}</th>
                        <th class="text-left text-secondary text-base font-medium px-5 py-3">{{__('Host')}}</th>
                        <th class="text-start text-secondary text-base font-medium pl-28 py-3">{{__('Value')}}</th>
                        <th class="text-right text-secondary text-base font-medium pr-14 py-3">{{__('TTL')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Row 1 -->
                    <tr class="border-b border-borderCS">
                        <td class="px-5 py-4 text-sm text-sub2Title">{{__('CNAME Record')}}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sub2Title text-sm">www</span>
                                <button onclick="copyText(this, 'www')" class="text-sub2Title hover:text-gray-600">
                                    <i class="ti tabler-copy text-base"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <span class="bg-gray-100 text-secondary text-sm px-3 py-1 rounded-md">{{env('CENTRAL_DOMAIN')}}</span>
                                <button onclick="copyText(this, '{{env('CENTRAL_DOMAIN')}}')" class="text-sub2Title hover:text-gray-600">
                                    <i class="ti tabler-copy text-base"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-sub2Title text-right">{{__('Automatic')}}</td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="border-b border-borderCS">
                        <td class="px-5 py-4 text-sm text-secondary">{{__('CNAME Record')}}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <i class="ti tabler-at text-sub2Title text-base"></i>
                                <button onclick="copyText(this, '@')" class="text-sub2Title hover:text-gray-600">
                                    <i class="ti tabler-copy text-base"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <span class="bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-md">{{env('CENTRAL_DOMAIN')}}</span>
                                <button onclick="copyText(this, '{{env('CENTRAL_DOMAIN')}}')" class="text-sub2Title hover:text-gray-600">
                                    <i class="ti tabler-copy text-base"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-secondary text-right">{{__('Automatic')}}</td>
                    </tr>
                    <!-- Row 3 - Cloudflare A Record -->
                    <tr class="border-b border-borderCS">
                        <td colspan="4" class="px-5 py-4 text-sm text-sub2Title bg-primary/5">{{__('Use this if you are using cloudFlare')}}</td>
                    </tr>
                    <tr class="border-b border-borderCS">
                        <td class="px-5 py-4 text-sm text-sub2Title">{{__('A Record')}}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <i class="ti tabler-at text-sub2Title text-base"></i>
                                <button onclick="copyText(this, '@')" class="text-sub2Title hover:text-gray-600">
                                    <i class="ti tabler-copy text-base"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <span class="bg-gray-100 text-secondary text-sm px-3 py-1 rounded-md">{{get_static_option_central('server_ip') ?? $_SERVER['SERVER_ADDR']}}</span>
                                <button onclick="copyText(this, '{{get_static_option_central('server_ip') ?? $_SERVER['SERVER_ADDR']}}')" class="text-sub2Title hover:text-gray-600">
                                    <i class="ti tabler-copy text-base"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-secondary text-right">{{__('Automatic')}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Custom Domain Requested -->
        <div class="rounded-2xl shadow-sm border border-borderCS overflow-hidden" style="background-color: var(--section-bg-1, #ffffff)">
            <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-lg font-medium text-secondary">{{__('Custom Domain Request')}}</h3>
                    <p class="text-sm text-sub2Title  mt-0.5">{{__('Manage and track your custom domain requests')}}</p>
                </div>
                <button onclick="document.getElementById('new_custom_domain').classList.remove('hidden')" class="flex items-center gap-2 bg-primary hover:bg-gray-900 text-white text-sm font-normal px-4 py-2.5 rounded-xl whitespace-nowrap">
                    <i class="ti tabler-plus text-base"></i>
                    {{__('Request Custom Domain')}}
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                    <tr class="border-t border-b border-gray-100">
                        <th class="text-left text-sm font-medium text-secondary px-5 py-3">{{__('Current Domain')}}</th>
                        <th class="text-left text-sm font-medium text-secondary px-5 py-3">{{__('Requested Domain')}}</th>
                        <th class="text-left text-sm font-medium text-secondary px-5 py-3">{{__('Requested Domain Status')}}</th>
                        <th class="text-left text-sm font-medium text-secondary px-5 py-3">{{__('Date')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($custom_domain_details ?? [] as $tenant)
                        <tr class="border-b border-borderCS">
                            <td class="px-5 py-4 text-sm text-gray-700">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                                    {{$tenant->id . '.'. env('CENTRAL_DOMAIN')}}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                @if(optional($tenant->custom_domain)->custom_domain)
                                    <span class="bg-[#8200DB]/10 text-gray-700 text-xs px-3 py-1 rounded-md">{{optional($tenant->custom_domain)->custom_domain}}</span>
                                @else
                                    <span class="text-sub2Title">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm">
                                @if(optional($tenant->custom_domain)->custom_domain_status == 'pending')
                                    <span class="flex items-center gap-1.5 bg-yellow-50 text-yellow-700 text-xs font-medium px-3 py-1.5 rounded-full w-fit border border-yellow-200">
                                        <i class="ti tabler-clock text-sm"></i> {{__('Pending')}}
                                    </span>
                                @elseif(optional($tenant->custom_domain)->custom_domain_status == 'in_progress')
                                    <span class="flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-medium px-3 py-1.5 rounded-full w-fit border border-blue-200">
                                        <i class="ti tabler-loader text-sm"></i> {{__('In Progress')}}
                                    </span>
                                @elseif(optional($tenant->custom_domain)->custom_domain_status == 'connected')
                                    <span class="flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-medium px-3 py-1.5 rounded-full w-fit border border-green-200">
                                        <i class="ti tabler-check text-sm"></i> {{__('Connected')}}
                                    </span>
                                @elseif(optional($tenant->custom_domain)->custom_domain_status == 'rejected')
                                    <span class="flex items-center gap-1.5 bg-red-50 text-red-700 text-xs font-medium px-3 py-1.5 rounded-full w-fit border border-red-200">
                                        <i class="ti tabler-x text-sm"></i> {{__('Rejected')}}
                                    </span>
                                @elseif(optional($tenant->custom_domain)->custom_domain_status == null)
                                    <span class="text-sub2Title">&mdash;</span>
                                @else
                                    <span class="flex items-center gap-1.5 bg-gray-50 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-full w-fit border border-gray-200">
                                        {{__(optional($tenant->custom_domain)->custom_domain_status) ?? __('Removed')}}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                @if(!empty($tenant->custom_domain))
                                    {{date('d-m-Y',strtotime(optional($tenant->custom_domain)->updated_at))}}
                                @else
                                    <span class="text-sub2Title">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-6">
                {!! $custom_domain_details->links('vendor.pagination.tailwind') !!}
            </div>
        </div>
    </main>
</div>

<!-- Custom Domain Request Modal -->
<div id="new_custom_domain" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="document.getElementById('new_custom_domain').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between p-5 border-b border-borderCS">
            <h5 class="text-lg font-medium text-secondary">{{__('Request Custom Domain')}}</h5>
            <button onclick="document.getElementById('new_custom_domain').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="ti tabler-x text-xl"></i>
            </button>
        </div>
        <form action="{{route('landlord.user.dashboard.custom.domain')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="p-5 space-y-4">

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                    {{__('You already have a custom domain ('.$central_domain.') connected with your portfolio website. if you request another domain now & if it gets connected with our server, then your current domain ('.$central_domain.') will be removed')}}
                </div>

                <input type="hidden" name="user_id" value="{{$user_domain_infos->id}}">

                <div>
                    @php
                        $domain_list = optional($user_domain_infos)->tenant_details;
                    @endphp
                    <label for="old_domain" class="block text-sm font-medium text-secondary mb-1.5">{{__('Select your domain')}}</label>
                    <select class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:border-primary" name="old_domain" id="old_domain">
                        <option value="">{{__('Select a domain')}}</option>
                        @foreach($domain_list as $domain)
                            @php
                                $tenant = \App\Models\Tenant::find($domain->id);
                            @endphp
                            @if(tenant_plan_sidebar_permission('custom_domain', $tenant))
                                <option value="{{$domain->id}}">{{$domain->id}}</option>
                            @endif
                        @endforeach
                    </select>
                    <small class="text-xs text-sub2Title mt-1">{{__('Select the domain which you want to change')}}</small>
                </div>

                <div>
                    <label for="custom_domain" class="block text-sm font-medium text-secondary mb-1.5">{{__('Enter your custom domain')}}</label>
                    <input type="text" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:border-primary" name="custom_domain" id="custom_domain" value="{{$custom_domain_info->custom_domain ?? ''}}">
                    <div id="subdomain-wrap" class="mt-1"></div>
                </div>

                <div class="text-xs text-sub2Title">
                    {{sprintf(__('Do not use http:// or https:// The valid format will be exactly like this one - domain.tld, domain.tld or subdomain.domain.tld, subdomain.domain.tld'))}}
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700">
                    <p>{{__("If you are unable to locate your subdomain in the list, it is possible that the custom domain feature may not be included in your subscription plan.")}}</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-5 border-t border-borderCS">
                <button type="button" onclick="document.getElementById('new_custom_domain').classList.add('hidden')" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl">{{__('Close')}}</button>
                <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-xl" id="login_button">{{__('Send Request')}}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    @parent
    <script src="{{ asset('assets/new-landlord/js/active_page.js') }}"></script>
    <script>
    function toggleReadMore() {
            const descText = document.getElementById('desc-text');
            const btn = document.getElementById('read-more-btn');
            if (descText.style.webkitLineClamp === '3') {
                descText.style.webkitLineClamp = 'unset';
                btn.textContent = '{{__("Read Less")}}';
            } else {
                descText.style.webkitLineClamp = '3';
                btn.textContent = '{{__("Read More..")}}';
            }
        }

        function copyText(btn, text) {
            navigator.clipboard.writeText(text).then(function() {
                const icon = btn.querySelector('i');
                icon.className = 'ti tabler-check text-base text-green-500';
                setTimeout(function() {
                    icon.className = 'ti tabler-copy text-base';
                }, 2000);
            });
        }

        $(function () {
            $(document).ready(function ($) {
                "use strict";

                function removeTags(str) {
                    if ((str === null) || (str === '')) {
                        return false;
                    }
                    str = str.toString();
                    return str.replace(/(<([^>]+)>)/ig, '');
                }

                $(document).on('keyup paste change', 'input[name="custom_domain"]', function (e) {
                    var value = '';
                    if ($(this).val() != '') {
                        value = removeTags($(this).val()).toLowerCase().replace(/\s/g, "-");
                        $(this).val(value)
                    }

                    if (value.length < 1) {
                        $('#subdomain-wrap').html('');
                        return;
                    }
                    let msgWrap = $('#subdomain-wrap');
                    msgWrap.html('');
                    msgWrap.append('<span class="text-yellow-600 text-xs">{{__('availability checking..')}}</span>');

                    axios({
                        url: "{{route('landlord.subdomain.custom-domain.check')}}",
                        method: 'post',
                        responseType: 'json',
                        data: {
                            subdomain: value
                        }
                    }).then(function (res) {
                        msgWrap.html('');
                        msgWrap.append('<span class="text-green-600 text-xs"> ' + value + ' {{__('is available')}}</span>');
                        $('#login_button').attr('disabled', false)
                    }).catch(function (error) {
                        var responseData = error.response.data.errors;
                        msgWrap.html('');
                        msgWrap.append('<span class="text-red-600 text-xs"> ' + responseData.subdomain + '</span>');
                        $('#login_button').attr('disabled', true)
                    });
                });
            });
        });
    </script>
@endsection
