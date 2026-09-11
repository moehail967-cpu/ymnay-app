@extends(route_prefix().'admin.admin-master')

@section('title')
    {{ __('SMS Gateway') }}
@endsection

@section('style')
    <style>.iti{ width: 100%; }</style>
@endsection

@section('content')

<x-flash-msg/>
<x-error-msg/>

{{-- Hero Banner --}}
<div class="featured-card rounded-2xl p-6 sm:p-8 mb-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur-sm flex items-center justify-center">
                <i class="las la-sms text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white font-urbanist">{{__('SMS Gateway')}}</h2>
                <p class="text-sm text-white/70 mt-0.5">{{__('Manage SMS providers and OTP configuration')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" data-modal-open="settings_option_modal"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white/15 backdrop-blur-sm text-white text-xs font-semibold hover:bg-white/25 transition">
                <i class="las la-cog text-sm"></i> {{__('SMS Settings')}}
            </button>
            <button type="button" data-modal-open="test_sms_modal"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white/15 backdrop-blur-sm text-white text-xs font-semibold hover:bg-white/25 transition">
                <i class="las la-paper-plane text-sm"></i> {{__('Test SMS')}}
            </button>
        </div>
    </div>
</div>

{{-- OTP Toggle Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6 p-5">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background:var(--color-primary-soft);">
                <i class="las la-shield-alt text-base" style="color:var(--color-primary)"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-dark font-urbanist">{{__('OTP Verification')}}</h4>
                <p class="text-[11px] text-muted">{{__('Enable or disable login OTP verification for users')}}</p>
            </div>
        </div>
        <label class="switch">
            <input type="checkbox" name="otp_login_status" @if(!empty(get_static_option('otp_login_status'))) checked @endif>
            <span class="slider onff"></span>
        </label>
    </div>
</div>

{{-- Gateway Cards --}}
<div class="sms-gateway-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6" @style(['display: none' => empty(get_static_option('otp_login_status'))])>
    @php
        $gatewayMeta = [
            'twilio' => ['icon' => 'las la-phone-volume', 'color' => '#E74C3C', 'bg' => '#FDF2F2', 'desc' => 'Cloud communications platform for SMS, voice & video'],
            'msg91' => ['icon' => 'las la-comment-dots', 'color' => '#2563EB', 'bg' => '#EFF6FF', 'desc' => 'Enterprise-grade communication APIs for India & global'],
            'sendra' => ['icon' => 'lab la-whatsapp', 'color' => '#059669', 'bg' => '#ECFDF5', 'desc' => 'WhatsApp Business API messaging and templates'],
        ];
    @endphp

    @foreach(\Modules\SmsGateway\Http\Services\OtpTraitService::gateways() as $key => $item)
        @php
            $sms_gateway = \Modules\SmsGateway\Entities\SmsGateway::where('name', $key)->first();
            $status = $sms_gateway->status ?? 0;
            $otp_time = $sms_gateway->otp_expire_time ?? 0;
            $credentials = $sms_gateway->credentials ?? '{}';
            $link = \Modules\SmsGateway\Http\Services\OtpTraitService::getLink($key);
            $meta = $gatewayMeta[$key] ?? ['icon' => 'las la-sms', 'color' => '#6B7280', 'bg' => '#F3F4F6', 'desc' => ''];
        @endphp

        <div class="plugin-card">
            <div class="p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:{{$meta['bg']}}">
                        <i class="{{$meta['icon']}} text-xl" style="color:{{$meta['color']}}"></i>
                    </div>
                    @if($status)
                        <span class="plugin-status">
                            <span class="plugin-status-dot active"></span>
                            {{__('Active')}}
                        </span>
                    @else
                        <span class="plugin-status">
                            <span class="plugin-status-dot"></span>
                            {{__('Inactive')}}
                        </span>
                    @endif
                </div>

                <h3 class="plugin-name text-capitalize">{{$item}}</h3>
                <p class="plugin-desc mt-1">{{__($meta['desc'])}}</p>

                <a href="{{$link}}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-medium mt-2" style="color:var(--color-primary)">
                    {{__('Documentation')}} <i class="las la-external-link-alt text-xs"></i>
                </a>
            </div>

            <div class="plugin-footer">
                <div class="flex items-center gap-2">
                    <button type="button"
                            data-option="{{$key}}"
                            data-status="{{$status}}"
                            class="plugin-btn {{$status ? 'btn-deactivate' : 'btn-activate'}} pl_active_deactive">
                        {{$status ? __('Deactivate') : __('Activate')}}
                    </button>
                    <button type="button"
                            data-modal-open="{{$key}}_modal"
                            data-option="{{$key}}"
                            data-otp-time="{{$otp_time}}"
                            data-credentials="{{$credentials}}"
                            class="plugin-btn btn-activate pl_settings">
                        <i class="las la-cog"></i> {{__('Settings')}}
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ==================== MODALS ==================== --}}

{{-- Twilio Modal --}}
<div class="tw-modal" id="twilio_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:#FDF2F2;">
                    <i class="las la-phone-volume" style="color:#E74C3C;"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Twilio Configuration')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="las la-times"></i></button>
        </div>
        <form action="{{route(route_prefix().'admin.sms.settings')}}" method="POST">
            @csrf
            <input type="hidden" name="sms_gateway_name" value="twilio">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Twilio SID')}} <span class="text-danger">*</span></label>
                    <input type="text" name="twilio_sid" class="tw-modal-input" placeholder="{{__('Enter Twilio SID')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Twilio Auth Token')}} <span class="text-danger">*</span></label>
                    <input type="text" name="twilio_auth_token" class="tw-modal-input" placeholder="{{__('Enter Auth Token')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Valid Twilio Number')}} <span class="text-danger">*</span></label>
                    <input type="text" name="twilio_number" class="tw-modal-input" placeholder="{{__('e.g. +1234567890')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('OTP Expire Time')}}</label>
                    <select name="user_otp_expire_time" class="tw-modal-input">
                        <option value="30">{{__('30 Second')}}</option>
                        @for($i=1; $i<=5; $i=$i+0.5)
                            <option value="{{$i}}">{{__($i . ($i > 1 ? ' Minutes' : ' Minute'))}}</option>
                        @endfor
                    </select>
                    <p class="text-[10px] text-muted mt-1">{{__('How long the OTP code stays valid')}}</p>
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Cancel')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="las la-save text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>
{{-- MSG91 Modal --}}
<div class="tw-modal" id="msg91_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:#EFF6FF;">
                    <i class="las la-comment-dots" style="color:#2563EB;"></i>
                </div>
                <h5 class="tw-modal-title">{{__('MSG91 Configuration')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="las la-times"></i></button>
        </div>
        <form action="{{route(route_prefix().'admin.sms.settings')}}" method="POST">
            @csrf
            <input type="hidden" name="sms_gateway_name" value="msg91">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('MSG91 Auth Key')}} <span class="text-danger">*</span></label>
                    <input type="text" name="msg91_auth_key" class="tw-modal-input" placeholder="{{__('Enter Auth Key')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('OTP Template ID')}} <span class="text-danger">*</span></label>
                    <input type="text" name="msg91_otp_template_id" class="tw-modal-input" placeholder="{{__('Enter OTP Template ID')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Notify User Register Template ID')}}</label>
                    <input type="text" name="msg91_notify_user_register_template_id" class="tw-modal-input" placeholder="{{__('Template ID')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Notify Admin Register Template ID')}}</label>
                    <input type="text" name="msg91_notify_admin_register_template_id" class="tw-modal-input" placeholder="{{__('Template ID')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Notify User Order Template ID')}}</label>
                    <input type="text" name="msg91_notify_user_order_template_id" class="tw-modal-input" placeholder="{{__('Template ID')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Notify Admin Order Template ID')}}</label>
                    <input type="text" name="msg91_notify_admin_order_template_id" class="tw-modal-input" placeholder="{{__('Template ID')}}">
                </div>
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('OTP Expire Time')}}</label>
                    <select name="user_otp_expire_time" class="tw-modal-input">
                        <option value="30">{{__('30 Second')}}</option>
                        @for($i=1; $i<=5; $i=$i+0.5)
                            <option value="{{$i}}">{{__($i . ($i > 1 ? ' Minutes' : ' Minute'))}}</option>
                        @endfor
                    </select>
                    <p class="text-[10px] text-muted mt-1">{{__('How long the OTP code stays valid')}}</p>
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Cancel')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="las la-save text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Sendra Modal --}}
@php
    $saved_gateway = \Modules\SmsGateway\Entities\SmsGateway::active()->where('name', 'sendra')->first();
    $sendra_creds = json_decode($saved_gateway->credentials ?? '') ?? '';
    $sendra_otp_template_id = $sendra_creds->sendra_otp_template_id ?? '';
    $sendra_notify_user_register_template_id = $sendra_creds->sendra_notify_user_register_template_id ?? '';
    $sendra_notify_admin_register_template_id = $sendra_creds->sendra_notify_admin_register_template_id ?? '';
    $sendra_notify_user_order_template_id = $sendra_creds->sendra_notify_user_order_template_id ?? '';
    $sendra_notify_admin_order_template_id = $sendra_creds->sendra_notify_admin_order_template_id ?? '';
@endphp

<div class="tw-modal" id="sendra_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:#ECFDF5;">
                    <i class="lab la-whatsapp" style="color:#059669;"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Sendra Configuration')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="las la-times"></i></button>
        </div>
        <form action="{{route(route_prefix().'admin.sms.settings')}}" method="POST">
            @csrf
            <input type="hidden" name="sms_gateway_name" value="sendra">
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Sendra API Token')}} <span class="text-danger">*</span></label>
                    <input type="text" name="sendra_api_token" class="tw-modal-input" placeholder="{{__('Enter API Token')}}">
                </div>

                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('OTP Template ID')}} <span class="text-danger">*</span></label>
                    <select name="sendra_otp_template_id" id="sendra_otp_template_id" class="tw-modal-input">
                        <option value="">{{__('Select a template')}}</option>
                        @foreach($templates['templates'] ?? [] as $template)
                            <option value="{{$template['name']}}" data-lang="{{$template['language']}}" {{$sendra_otp_template_id === $template['name'] ? 'selected' : ''}}>{{$template['name']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="sendra_otp_template_language" value="">
                </div>

                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Notify User Register Template ID')}}</label>
                    <select name="sendra_notify_user_register_template_id" id="sendra_notify_user_register_template_id" class="tw-modal-input">
                        <option value="">{{__('Select a template')}}</option>
                        @foreach($templates['templates'] ?? [] as $template)
                            <option value="{{$template['name']}}" data-lang="{{$template['language']}}" {{$sendra_notify_user_register_template_id === $template['name'] ? 'selected' : ''}}>{{$template['name']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="sendra_notify_user_register_template_language" value="">
                </div>

                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Notify Admin Register Template ID')}}</label>
                    <select name="sendra_notify_admin_register_template_id" id="sendra_notify_admin_register_template_id" class="tw-modal-input">
                        <option value="">{{__('Select a template')}}</option>
                        @foreach($templates['templates'] ?? [] as $template)
                            <option value="{{$template['name']}}" data-lang="{{$template['language']}}" {{$sendra_notify_admin_register_template_id === $template['name'] ? 'selected' : ''}}>{{$template['name']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="sendra_notify_admin_register_template_language" value="">
                </div>

                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Notify User Order Template ID')}}</label>
                    <select name="sendra_notify_user_order_template_id" id="sendra_notify_user_order_template_id" class="tw-modal-input">
                        <option value="">{{__('Select a template')}}</option>
                        @foreach($templates['templates'] ?? [] as $template)
                            <option value="{{$template['name']}}" data-lang="{{$template['language']}}" {{$sendra_notify_user_order_template_id === $template['name'] ? 'selected' : ''}}>{{$template['name']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="sendra_notify_user_order_template_language" value="">
                </div>

                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Notify Admin Order Template ID')}}</label>
                    <select name="sendra_notify_admin_order_template_id" id="sendra_notify_admin_order_template_id" class="tw-modal-input">
                        <option value="">{{__('Select a template')}}</option>
                        @foreach($templates['templates'] ?? [] as $template)
                            <option value="{{$template['name']}}" data-lang="{{$template['language']}}" {{$sendra_notify_admin_order_template_id === $template['name'] ? 'selected' : ''}}>{{$template['name']}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="sendra_notify_admin_order_template_language" value="">
                </div>

                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('OTP Expire Time')}}</label>
                    <select name="user_otp_expire_time" class="tw-modal-input">
                        <option value="30">{{__('30 Second')}}</option>
                        @for($i=1; $i<=5; $i=$i+0.5)
                            <option value="{{$i}}">{{__($i . ($i > 1 ? ' Minutes' : ' Minute'))}}</option>
                        @endfor
                    </select>
                    <p class="text-[10px] text-muted mt-1">{{__('How long the OTP code stays valid')}}</p>
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Cancel')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="las la-save text-sm"></i> {{__('Save Changes')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- SMS Settings Modal --}}
<div class="tw-modal" id="settings_option_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-info-bg);">
                    <i class="las la-cog" style="color:var(--color-info);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('SMS Settings')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="las la-times"></i></button>
        </div>
        <form action="{{route(route_prefix().'admin.sms.options')}}" method="POST">
            @csrf
            <div class="tw-modal-body">
                <p class="text-xs font-semibold text-dark mb-3">{{__('Receive SMS when actions are triggered')}}</p>

                <div class="tw-modal-tenant-toggle">
                    <div>
                        <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('New user registered — Admin')}}</p>
                        <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Notify admin when a new user registers')}}</p>
                    </div>
                    <label class="switch"><input type="checkbox" name="new_user_admin" @if(!empty(get_static_option('new_user_admin'))) checked @endif><span class="slider onff"></span></label>
                </div>

                <div class="tw-modal-tenant-toggle">
                    <div>
                        <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('New user registered — User')}}</p>
                        <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Send welcome SMS to new user')}}</p>
                    </div>
                    <label class="switch"><input type="checkbox" name="new_user_user" @if(!empty(get_static_option('new_user_user'))) checked @endif><span class="slider onff"></span></label>
                </div>

                @if(!tenant())
                    <div class="tw-modal-tenant-toggle">
                        <div>
                            <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('New tenant created — Admin')}}</p>
                            <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Notify admin when a new shop is created')}}</p>
                        </div>
                        <label class="switch"><input type="checkbox" name="new_tenant_admin" @if(!empty(get_static_option('new_tenant_admin'))) checked @endif><span class="slider onff"></span></label>
                    </div>

                    <div class="tw-modal-tenant-toggle">
                        <div>
                            <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('New tenant created — User')}}</p>
                            <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Send confirmation SMS to tenant owner')}}</p>
                        </div>
                        <label class="switch"><input type="checkbox" name="new_tenant_user" @if(!empty(get_static_option('new_tenant_user'))) checked @endif><span class="slider onff"></span></label>
                    </div>
                @endif

                @tenant
                    <div class="tw-modal-tenant-toggle">
                        <div>
                            <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('New order placed — Admin')}}</p>
                            <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Notify admin when a new order is placed')}}</p>
                        </div>
                        <label class="switch"><input type="checkbox" name="new_order_admin" @if(!empty(get_static_option('new_order_admin'))) checked @endif><span class="slider onff"></span></label>
                    </div>

                    <div class="tw-modal-tenant-toggle">
                        <div>
                            <p class="text-xs font-bold" style="color:var(--color-text-dark);">{{__('New order placed — User')}}</p>
                            <p class="text-[10px]" style="color:var(--color-text-muted);">{{__('Send order confirmation SMS to buyer')}}</p>
                        </div>
                        <label class="switch"><input type="checkbox" name="new_order_user" @if(!empty(get_static_option('new_order_user'))) checked @endif><span class="slider onff"></span></label>
                    </div>
                @endtenant

                <div class="tw-modal-field mt-3">
                    <label class="tw-modal-label">{{__('Receiving Phone Number')}} <span class="text-danger">*</span></label>
                    <input type="tel" name="receiving_phone_number" value="{{get_static_option('receiving_phone_number')}}"
                           class="tw-modal-input" placeholder="{{__('e.g. +1234567890')}}" id="set-telephone">
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Cancel')}}</button>
                <button type="submit" class="tw-modal-btn tw-modal-btn-save"><i class="las la-save text-sm"></i> {{__('Update')}}</button>
            </div>
        </form>
    </div>
</div>

{{-- Test SMS Modal --}}
<div class="tw-modal" id="test_sms_modal">
    <div class="tw-modal-backdrop" data-modal-close></div>
    <div class="tw-modal-panel">
        <div class="tw-modal-head">
            <div class="tw-modal-head-info">
                <div class="tw-modal-icon" style="background:var(--color-success-bg);">
                    <i class="las la-paper-plane" style="color:var(--color-success);"></i>
                </div>
                <h5 class="tw-modal-title">{{__('Send Test SMS')}}</h5>
            </div>
            <button type="button" class="tw-modal-close" data-modal-close><i class="las la-times"></i></button>
        </div>
        <form action="{{route(route_prefix().'admin.sms.test')}}" method="POST">
            @csrf
            <div class="tw-modal-body">
                <div class="tw-modal-field">
                    <label class="tw-modal-label">{{__('Phone Number')}} <span class="text-danger">*</span></label>
                    <input type="tel" name="test_phone_number" class="tw-modal-input" placeholder="{{__('e.g. +1234567890')}}" id="telephone">
                </div>
            </div>
            <div class="tw-modal-foot">
                <button type="button" class="tw-modal-btn tw-modal-btn-cancel" data-modal-close>{{__('Cancel')}}</button>
                <button type="submit" id="test-sms-btn" class="tw-modal-btn tw-modal-btn-save" disabled>
                    <i class="las la-paper-plane text-sm"></i> {{__('Send')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        (function ($) {
            "use strict";

            // ====== TW-Modal open/close ======
            $(document).on('click', '[data-modal-open]', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-modal-open');
                $('#' + id).addClass('open');
                $('body').css('overflow', 'hidden');
            });
            $(document).on('click', '[data-modal-close]', function () {
                $(this).closest('.tw-modal').removeClass('open');
                $('body').css('overflow', '');
            });
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $('.tw-modal.open').removeClass('open');
                    $('body').css('overflow', '');
                }
            });

            // ====== OTP Toggle ======
            $(document).on('change', 'input[name=otp_login_status]', function (e) {
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You can revert this anytime")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1a5c4e',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: "{{__('Yes!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.get("{{route(route_prefix().'admin.sms.login.otp.status')}}")
                            .then((response) => {
                                if (response.data.type === 'success') {
                                    toastr.success(`{{__('Settings updated')}}`);
                                    $('.sms-gateway-grid').toggle();
                                }
                            });
                    } else {
                        location.reload();
                    }
                });
            });

            // ====== Settings modal prefill ======
            $(document).on('click', '.pl_settings', function (e) {
                e.preventDefault();
                var el = $(this);
                var option = el.attr('data-option');
                var otp_expire_time = el.attr('data-otp-time');
                var credentials = el.attr('data-credentials');
                credentials = jQuery.parseJSON(credentials);

                var modal = $('#' + option + '_modal');
                for (var item in credentials) {
                    modal.find('input[name=' + item + ']').val(credentials[item]);
                }
                modal.find('select[name=user_otp_expire_time] option[value=' + otp_expire_time + ']').attr('selected', true);

                modal.addClass('open');
                $('body').css('overflow', 'hidden');
            });

            // ====== Activate / Deactivate ======
            $(document).on("click", '.pl_active_deactive', function (e) {
                e.preventDefault();
                var el = $(this);
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You can revert this anytime")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1a5c4e',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: "{{__('Yes!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post("{{route(route_prefix().'admin.sms.status')}}", {
                            option_name: el.data('option'),
                            status: el.data('status')
                        }).then((response) => {
                            if (response.data.type === 'success') {
                                location.reload();
                            }
                        });
                    }
                });
            });

            // ====== Sendra template language sync ======
            var template_id_lang = [
                'sendra_otp_template',
                'sendra_notify_user_register_template',
                'sendra_notify_admin_register_template',
                'sendra_notify_user_order_template',
                'sendra_notify_admin_order_template'
            ];

            template_id_lang.forEach(function(template) {
                $(document).on('change', '#' + template + '_id', function () {
                    var lang = $(this).find('option:selected').attr('data-lang');
                    $('input[name=' + template + '_language]').val(lang);
                });
            });
        })(jQuery);
    </script>

    <x-custom-js.phone-number-config selector="#telephone" submit-button-id="test-sms-btn" key="1"/>
    <x-custom-js.phone-number-config selector="#set-telephone" submit-button-id="test-sms-btn" key="2"/>

    <script>
        $(document).ready(function () {
            setTimeout(() => {
                $('#set-telephone').val(`{{get_static_option('receiving_phone_number')}}`);
            }, 1000);
        });
    </script>
@endsection
