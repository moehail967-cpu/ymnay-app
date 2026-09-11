@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Integrations')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    $method = is_null(tenant()) ? 'get_static_option_central' : 'get_static_option';

    $integrations = [
        [
            'key'    => 'google_analytics_gt4',
            'name'   => __('Google Analytics GT4'),
            'desc'   => __('Track website traffic and user behavior with Google Analytics GT4.'),
            'icon'   => 'mdi-google-analytics',
            'color'  => 'warning',
            'status' => $method('google_analytics_gt4_status'),
            'modal'  => 'google_analytics_modal',
            'group'  => __('Analytics'),
        ],
        [
            'key'    => 'google_tag_manager',
            'name'   => __('Google Tag Manager'),
            'desc'   => __('Manage and deploy marketing tags without code changes.'),
            'icon'   => 'mdi-tag-multiple-outline',
            'color'  => 'info',
            'status' => $method('google_tag_manager_status'),
            'modal'  => 'google_tag_manager_modal',
            'group'  => __('Analytics'),
        ],
        [
            'key'    => 'facebook_pixels',
            'name'   => __('Facebook Pixels'),
            'desc'   => __('Track conversions from Facebook ads and optimize targeting.'),
            'icon'   => 'mdi-facebook',
            'color'  => 'info',
            'status' => $method('facebook_pixels_status'),
            'modal'  => 'facebook_pixels_modal',
            'group'  => __('Analytics'),
        ],
        [
            'key'    => 'adroll_pixels',
            'name'   => __('Adroll'),
            'desc'   => __('Retarget visitors with Adroll display ad campaigns.'),
            'icon'   => 'mdi-bullseye-arrow',
            'color'  => 'info',
            'status' => $method('adroll_pixels_status'),
            'modal'  => 'adroll_pixels_modal',
            'group'  => __('Analytics'),
        ],
        [
            'key'    => 'captcha',
            'name'   => __('Google Captcha V3'),
            'desc'   => __('Protect forms from spam and bot submissions.'),
            'icon'   => 'mdi-shield-check-outline',
            'color'  => 'warning',
            'status' => $method('captcha_status'),
            'modal'  => 'google_captcha_modal',
            'group'  => __('Security'),
        ],
        [
            'key'    => 'whatsapp',
            'name'   => __('WhatsApp'),
            'desc'   => __('Let visitors contact you directly via WhatsApp chat.'),
            'icon'   => 'mdi-whatsapp',
            'color'  => 'success',
            'status' => $method('whatsapp_status'),
            'modal'  => 'whatsapp_modal',
            'group'  => __('Chat'),
        ],
        [
            'key'    => 'messenger',
            'name'   => __('Messenger'),
            'desc'   => __('Embed Facebook Messenger chat widget on your site.'),
            'icon'   => 'mdi-facebook-messenger',
            'color'  => 'info',
            'status' => $method('messenger_status'),
            'modal'  => 'messenger_modal',
            'group'  => __('Chat'),
            'help'   => 'https://www.facebook.com/business/help/1524587524402327',
        ],
        [
            'key'    => 'twakto',
            'name'   => __('Tawk.to'),
            'desc'   => __('Free live chat widget for real-time customer support.'),
            'icon'   => 'mdi-chat-processing-outline',
            'color'  => 'success',
            'status' => $method('twakto_status'),
            'modal'  => 'twakto_modal',
            'group'  => __('Chat'),
        ],
        [
            'key'    => 'crsip',
            'name'   => __('Crisp'),
            'desc'   => __('All-in-one messaging platform with live chat and chatbot.'),
            'icon'   => 'mdi-message-text-outline',
            'color'  => 'info',
            'status' => $method('crsip_status'),
            'modal'  => 'crsip_modal',
            'group'  => __('Chat'),
        ],
        [
            'key'    => 'tidio',
            'name'   => __('Tidio'),
            'desc'   => __('AI-powered chat and chatbot for customer engagement.'),
            'icon'   => 'mdi-robot-outline',
            'color'  => 'info',
            'status' => $method('tidio_status'),
            'modal'  => 'tidio_modal',
            'group'  => __('Chat'),
        ],
        [
            'key'    => 'instagram',
            'name'   => __('Instagram'),
            'desc'   => __('Display Instagram feed on your website if supported by theme.'),
            'icon'   => 'mdi-instagram',
            'color'  => 'danger',
            'status' => $method('instagram_status'),
            'modal'  => 'instagram_modal',
            'group'  => __('Social'),
        ],
    ];

    $enabledCount = collect($integrations)->filter(fn($i) => $i['status'] === 'on')->count();
    $totalCount   = count($integrations);

    $colorMap = [
        'warning' => ['bg' => 'var(--color-warning-bg)', 'text' => 'var(--color-warning)'],
        'info'    => ['bg' => 'var(--color-info-bg)',    'text' => 'var(--color-info)'],
        'success' => ['bg' => 'var(--color-success-bg)', 'text' => 'var(--color-success)'],
        'danger'  => ['bg' => 'var(--color-danger-bg)',  'text' => 'var(--color-danger)'],
    ];
@endphp

{{-- Hero Banner --}}
<div class="featured-card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-connection text-2xl text-white/80"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">{{__('Third Party')}}</p>
                <p class="text-xl font-extrabold text-white leading-tight mt-0.5">{{__('Integrations')}}</p>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-6 sm:gap-8 mt-5 pt-4 border-t border-white/10">
        <div>
            <p class="text-[10px] text-white/40 uppercase tracking-wider font-bold">{{__('Total')}}</p>
            <p class="text-lg font-extrabold text-white mt-0.5">{{$totalCount}}</p>
        </div>
        <div class="w-px h-8 bg-white/10"></div>
        <div>
            <p class="text-[10px] text-white/40 uppercase tracking-wider font-bold">{{__('Enabled')}}</p>
            <p class="text-lg font-extrabold text-emerald-300 mt-0.5">{{$enabledCount}}</p>
        </div>
        <div class="w-px h-8 bg-white/10"></div>
        <div>
            <p class="text-[10px] text-white/40 uppercase tracking-wider font-bold">{{__('Disabled')}}</p>
            <p class="text-lg font-extrabold text-white/50 mt-0.5">{{$totalCount - $enabledCount}}</p>
        </div>
    </div>
</div>

{{-- Integration Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($integrations as $item)
    @php
        $isOn = $item['status'] === 'on';
        $c    = $colorMap[$item['color']];
    @endphp
    <div class="plugin-card" id="card_{{$item['key']}}">
        <div class="p-5 flex-1">
            <div class="flex items-start gap-3.5">
                <div class="plugin-icon" style="background: {{$c['bg']}};">
                    <i class="mdi {{$item['icon']}} text-lg" style="color: {{$c['text']}};"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="plugin-name truncate">{{$item['name']}}</h4>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="plugin-category" style="background:var(--color-bg-secondary); color:var(--color-text-brand);">
                            {{$item['group']}}
                        </span>
                    </div>
                </div>
                @if($isOn)
                    <span class="plugin-status is-active" style="background:var(--color-success-bg); color:var(--color-success);">
                        <span class="status-dot"></span> {{__('On')}}
                    </span>
                @else
                    <span class="plugin-status" style="background:var(--color-bg-muted); color:var(--color-text-subtle);">
                        <span class="status-dot" style="background:var(--color-text-subtle);"></span> {{__('Off')}}
                    </span>
                @endif
            </div>
            <p class="plugin-desc mt-3.5">{{$item['desc']}}</p>
        </div>
        <div class="plugin-footer">
            <button type="button"
                    data-option="{{$item['key']}}_status"
                    data-status="{{$item['status']}}"
                    class="pl_active_deactive plugin-btn flex-1 {{ $isOn ? 'btn-deactivate' : 'btn-activate' }}">
                <i class="mdi {{ $isOn ? 'mdi-toggle-switch' : 'mdi-toggle-switch-off-outline' }} text-sm"></i>
                {{$isOn ? __('Disable') : __('Enable')}}
            </button>
            <button type="button" data-modal-open="{{$item['modal']}}" class="plugin-btn">
                <i class="mdi mdi-cog-outline text-sm"></i> {{__('Settings')}}
            </button>
            @if(!empty($item['help']))
                <a href="{{$item['help']}}" target="_blank" class="plugin-btn" title="{{__('Help')}}">
                    <i class="mdi mdi-help-circle-outline text-sm"></i>
                </a>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════════
     TAILWIND MODALS
     ═══════════════════════════════════════════════════════════ --}}

{{-- Google Analytics GT4 --}}
<div class="tw-modal" id="google_analytics_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-warning-bg);">
                    <i class="mdi mdi-google-analytics" style="color:var(--color-warning);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Google Analytics GT4')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="google_analytics">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('GT4 Measurement ID')}}</label>
                    <input type="text" name="google_analytics_gt4_ID" value="{{$method('google_analytics_gt4_ID')}}" class="tw-modal-input" placeholder="{{ __('G-XXXXXXXXXX') }}">
                </div>
                @if(is_null(tenant()))
                <div class="tw-modal-tenant-toggle">
                    <div>
                        <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('Load in Tenant Sites')}}</p>
                        <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Enable this script for all tenants')}}</p>
                    </div>
                    <label class="switch"><input type="checkbox" name="google_analytics_gt4_tenant" @if(!empty($method('google_analytics_gt4_tenant'))) checked @endif><span class="slider onff"></span></label>
                </div>
                @endif
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Google Tag Manager --}}
<div class="tw-modal" id="google_tag_manager_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-info-bg);">
                    <i class="mdi mdi-tag-multiple-outline" style="color:var(--color-info);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Google Tag Manager')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="google_tag_manager">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Container ID')}}</label>
                    <input type="text" name="google_tag_manager_ID" value="{{$method('google_tag_manager_ID')}}" class="tw-modal-input" placeholder="{{ __('GTM-XXXXXXX') }}">
                </div>
                @if(is_null(tenant()))
                <div class="tw-modal-tenant-toggle">
                    <div>
                        <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('Load in Tenant Sites')}}</p>
                        <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Enable this script for all tenants')}}</p>
                    </div>
                    <label class="switch"><input type="checkbox" name="google_tag_manager_tenant" @if(!empty($method('google_tag_manager_tenant'))) checked @endif><span class="slider onff"></span></label>
                </div>
                @endif
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Facebook Pixels --}}
<div class="tw-modal" id="facebook_pixels_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-info-bg);">
                    <i class="mdi mdi-facebook" style="color:var(--color-info);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Facebook Pixels')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="facebook_pixels">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Pixel ID')}}</label>
                    <input type="text" name="facebook_pixels_id" value="{{$method('facebook_pixels_id')}}" class="tw-modal-input" placeholder="{{__('Enter Facebook Pixel ID')}}">
                </div>
                @if(is_null(tenant()))
                <div class="tw-modal-tenant-toggle">
                    <div>
                        <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('Load in Tenant Sites')}}</p>
                        <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Enable this script for all tenants')}}</p>
                    </div>
                    <label class="switch"><input type="checkbox" name="facebook_pixels_tenant" @if(!empty($method('facebook_pixels_tenant'))) checked @endif><span class="slider onff"></span></label>
                </div>
                @endif
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Adroll --}}
<div class="tw-modal" id="adroll_pixels_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-info-bg);">
                    <i class="mdi mdi-bullseye-arrow" style="color:var(--color-info);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Adroll')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="adroll_pixels">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Adviser ID')}}</label>
                    <input type="text" name="adroll_adviser_id" value="{{$method('adroll_adviser_id')}}" class="tw-modal-input" placeholder="{{__('Enter adviser ID')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Publisher ID')}}</label>
                    <input type="text" name="adroll_publisher_id" value="{{$method('adroll_publisher_id')}}" class="tw-modal-input" placeholder="{{__('Enter publisher ID')}}">
                </div>
                @if(is_null(tenant()))
                <div class="tw-modal-tenant-toggle">
                    <div>
                        <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('Load in Tenant Sites')}}</p>
                        <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Enable this script for all tenants')}}</p>
                    </div>
                    <label class="switch"><input type="checkbox" name="adroll_pixels_tenant" @if(!empty($method('adroll_pixels_tenant'))) checked @endif><span class="slider onff"></span></label>
                </div>
                @endif
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Google Captcha V3 --}}
<div class="tw-modal" id="google_captcha_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-warning-bg);">
                    <i class="mdi mdi-shield-check-outline" style="color:var(--color-warning);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Google Captcha V3')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="google_captcha_v3">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Site Key')}}</label>
                    <input type="text" name="site_google_captcha_v3_site_key" value="{{$method('site_google_captcha_v3_site_key')}}" class="tw-modal-input" placeholder="{{__('Enter site key')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Secret Key')}}</label>
                    <input type="text" name="site_google_captcha_v3_secret_key" value="{{$method('site_google_captcha_v3_secret_key')}}" class="tw-modal-input" placeholder="{{__('Enter secret key')}}">
                </div>
                @if(is_null(tenant()))
                <div class="tw-modal-tenant-toggle">
                    <div>
                        <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('Load in Tenant Sites')}}</p>
                        <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Enable this script for all tenants')}}</p>
                    </div>
                    <label class="switch"><input type="checkbox" name="site_google_captcha_v3_tenant" @if(!empty($method('site_google_captcha_v3_tenant'))) checked @endif><span class="slider onff"></span></label>
                </div>
                @endif
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- WhatsApp --}}
<div class="tw-modal" id="whatsapp_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-success-bg);">
                    <i class="mdi mdi-whatsapp" style="color:var(--color-success);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('WhatsApp')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="whatsapp">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Phone Number with Country Code')}}</label>
                    <input type="text" name="whatsapp_mobile_number" value="{{get_static_option('whatsapp_mobile_number')}}" class="tw-modal-input" placeholder="+1234567890">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Initial Message')}}</label>
                    <input type="text" name="whatsapp_initial_text" value="{{get_static_option('whatsapp_initial_text')}}" class="tw-modal-input" placeholder="{{__('Hi, I need help with...')}}">
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Messenger --}}
<div class="tw-modal" id="messenger_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-info-bg);">
                    <i class="mdi mdi-facebook-messenger" style="color:var(--color-info);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Messenger')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="messenger">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Page ID')}}</label>
                    <input type="text" name="messenger_page_id" value="{{get_static_option('messenger_page_id')}}" class="tw-modal-input" placeholder="{{__('Enter Facebook Page ID')}}">
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Tawk.to --}}
<div class="tw-modal" id="twakto_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-success-bg);">
                    <i class="mdi mdi-chat-processing-outline" style="color:var(--color-success);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Tawk.to')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="twakto">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Widget ID')}}</label>
                    <input type="text" name="twakto_widget_id" value="{{get_static_option('twakto_widget_id')}}" class="tw-modal-input" placeholder="{{__('Enter Tawk.to widget ID')}}">
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Crisp --}}
<div class="tw-modal" id="crsip_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-info-bg);">
                    <i class="mdi mdi-message-text-outline" style="color:var(--color-info);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Crisp')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="crsip">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Website ID')}}</label>
                    <input type="text" name="crsip_website_id" value="{{get_static_option('crsip_website_id')}}" class="tw-modal-input" placeholder="{{__('Enter Crisp website ID')}}">
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Tidio --}}
<div class="tw-modal" id="tidio_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-info-bg);">
                    <i class="mdi mdi-robot-outline" style="color:var(--color-info);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Tidio')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="tidio">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Chat Page ID')}}</label>
                    <input type="text" name="tidio_chat_page_id" value="{{get_static_option('tidio_chat_page_id')}}" class="tw-modal-input" placeholder="{{__('Enter Tidio chat page ID')}}">
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Instagram --}}
<div class="tw-modal" id="instagram_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-danger-bg);">
                    <i class="mdi mdi-instagram" style="color:var(--color-danger);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Instagram')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="mdi mdi-close"></i></button>
        </div>
        <form action="{{route(route_prefix().'integration')}}" method="post">
            @csrf
            <input type="hidden" name="data_type" value="instagram">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Access Token')}}</label>
                    <input type="text" name="instagram_access_token" value="{{get_static_option('instagram_access_token')}}" class="tw-modal-input" placeholder="{{__('Enter Instagram access token')}}">
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Close')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="mdi mdi-content-save-outline text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function ($) {
    "use strict";

    // ── Tailwind Modal open/close ────────────────────────────────
    $(document).on('click', '[data-modal-open]', function (e) {
        e.preventDefault();
        var id = $(this).data('modal-open');
        $('#' + id).addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $(document).on('click', '[data-modal-close]', function (e) {
        e.preventDefault();
        var modal = $(this).closest('.tw-modal');
        modal.removeClass('open');
        $('body').css('overflow', '');
    });

    // Close on Escape key
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('.tw-modal.open').removeClass('open');
            $('body').css('overflow', '');
        }
    });

    // ── Enable / Disable toggle ──────────────────────────────────
    $(document).on('click', '.pl_active_deactive', function (e) {
        e.preventDefault();
        var el = $(this);

        Swal.fire({
            title: '{{ __("Confirm Action") }}',
            text: '{{ __("You can toggle this integration anytime.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1a5c4e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '{{ __("Yes, proceed") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then(function (result) {
            if (result.isConfirmed) {
                var optionName = el.data('option');
                var status = el.data('status');

                axios.post("{{ route(route_prefix().'integration.activation') }}", {
                    option_name: optionName,
                    status: status
                }).then(function (response) {
                    if (response.data.type === 'success') {
                        toastr.success(response.data.msg);
                        setTimeout(function () { location.reload(); }, 500);
                    }
                });
            }
        });
    });

    // ── URL hash card highlight ──────────────────────────────────
    var hash = window.location.hash.replace('#', '');
    if (hash) {
        var card = $('#card_' + hash);
        if (card.length) {
            card.css({ boxShadow: '0 0 0 3px var(--color-primary)', transition: 'box-shadow 0.3s' });
            $('html, body').animate({ scrollTop: card.offset().top - 100 }, 400);
            setTimeout(function () { card.css({ boxShadow: '' }); }, 3000);
        }
    }

})(jQuery);
</script>
@endsection
