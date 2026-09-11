@extends('tenant.admin.admin-master')
@section('title') {{__('Email Settings')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- SMTP Settings --}}
    <div class="bg-surface rounded-xl shadow-main border border-main">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-envelope text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('SMTP Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Configure email delivery for your store')}}</p>
            </div>
        </div>

        <div class="px-4 sm:px-6 py-5">
            <form class="forms-sample" method="post" action="{{route('tenant.admin.general.email.settings')}}">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label class="lnd-label">{{__('Site Global Email')}}</label>
                        <input type="email" class="lnd-input" name="tenant_site_global_email" value="{{get_static_option('tenant_site_global_email')}}">
                        <p class="text-xs text-muted mt-1.5">{{__('You will get all mail to this email, also this will be in your user from address in all the mail sent from the system.')}}</p>
                    </div>

                    <div>
                        <label class="lnd-label">{{__('SMTP Host')}}</label>
                        <input type="text" class="lnd-input" name="site_smtp_host" value="{{get_static_option('site_smtp_host')}}">
                    </div>

                    <div>
                        <label class="lnd-label">{{__('SMTP Username')}}</label>
                        <input type="text" class="lnd-input" name="site_smtp_username" value="{{get_static_option('site_smtp_username')}}">
                    </div>

                    <div>
                        <label class="lnd-label">{{__('SMTP Password')}}</label>
                        <input type="password" class="lnd-input" name="site_smtp_password" value="{{get_static_option('site_smtp_password')}}">
                    </div>

                    <div>
                        <label class="lnd-label">{{__('SMTP Driver')}}</label>
                        <select name="site_smtp_driver" class="lnd-input">
                            <option value="smtp" @selected(get_static_option('site_smtp_driver') == 'smtp')>{{__('smtp')}}</option>
                            <option value="sendmail" @selected(get_static_option('site_smtp_driver') == 'sendmail')>{{__('sendmail')}}</option>
                            <option value="mailgun" @selected(get_static_option('site_smtp_driver') == 'mailgun')>{{__('mailgun')}}</option>
                            <option value="postmark" @selected(get_static_option('site_smtp_driver') == 'postmark')>{{__('postmark')}}</option>
                        </select>
                    </div>

                    <div>
                        <label class="lnd-label">{{__('SMTP Port')}}</label>
                        <select name="site_smtp_port" class="lnd-input">
                            <option value="25" @selected(get_static_option('site_smtp_port') == '25')>25</option>
                            <option value="587" @selected(get_static_option('site_smtp_port') == '587')>587</option>
                            <option value="465" @selected(get_static_option('site_smtp_port') == '465')>465</option>
                            <option value="2525" @selected(get_static_option('site_smtp_port') == '2525')>2525</option>
                        </select>
                    </div>

                    <div>
                        <label class="lnd-label">{{__('SMTP Encryption')}}</label>
                        <select name="site_smtp_encryption" class="lnd-input">
                            <option value="ssl" @selected(get_static_option('site_smtp_encryption') == 'ssl')>{{__('SSL')}}</option>
                            <option value="tls" @selected(get_static_option('site_smtp_encryption') == 'tls')>{{__('TLS')}}</option>
                            <option value="" @selected(get_static_option('site_smtp_encryption') == '')>{{__('None')}}</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 mt-5 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="las la-save"></i> {{__('Save Changes')}}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Test Mail --}}
    <div class="bg-surface rounded-xl shadow-main border border-main h-fit">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-paper-plane text-info text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Send Test Mail')}}</h3>
                <p class="text-xs text-muted">{{__('Verify your SMTP configuration')}}</p>
            </div>
        </div>

        <div class="px-4 sm:px-6 py-5">
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-5 flex items-start gap-2">
                <i class="las la-exclamation-triangle text-amber-500 text-lg mt-0.5 flex-shrink-0"></i>
                <p class="text-xs text-amber-700">{{__('If you see any error here, please contact your hosting provider to make sure you have added valid and proper SMTP details.')}}</p>
            </div>

            <form class="forms-sample" method="post" action="{{route('tenant.admin.general.mail.settings')}}">
                @csrf
                <div class="mb-5">
                    <label class="lnd-label">{{__('Email')}}</label>
                    <input type="email" class="lnd-input" name="email" placeholder="{{__('Enter email address')}}">
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-paper-plane"></i> {{__('Send Test Mail')}}
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
