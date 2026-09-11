@extends('landlord.admin.admin-master')
@section('title') {{__('SMTP Settings')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- SMTP Settings --}}
    <div class="lg:col-span-2 bg-surface rounded-xl shadow-main border border-main">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-email-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('SMTP Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Configure email delivery settings')}}</p>
            </div>
        </div>

        <div class="px-4 sm:px-6 py-5">
            <div class="flex items-start gap-2 px-4 py-3 rounded-xl mb-5 border" style="background: var(--color-warning-bg, #fffbeb); border-color: var(--color-warning, #f59e0b)20;">
                <i class="mdi mdi-information-outline mt-0.5 flex-shrink-0" style="color: var(--color-warning, #f59e0b);"></i>
                <p class="text-xs" style="color: var(--color-warning, #f59e0b);">{{__('You can also change SMTP settings manually from @core>.env files.')}}</p>
            </div>

            <form class="forms-sample" method="post" action="{{route('landlord.admin.general.smtp.settings')}}">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Site Global Email')}}</label>
                        <input type="email" class="lnd-input" name="site_global_email" value="{{get_static_option_central('site_global_email')}}">
                        <p class="text-[11px] text-muted mt-1.5">{{__('All system emails will be sent from and received at this address.')}}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('SMTP Host')}}</label>
                        <input type="text" class="lnd-input" name="site_smtp_host" value="{{get_static_option_central('site_smtp_host')}}" placeholder="{{ __('smtp.example.com') }}">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('SMTP Username')}}</label>
                            <input type="text" class="lnd-input" name="site_smtp_username" value="{{get_static_option_central('site_smtp_username')}}">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('SMTP Password')}}</label>
                            <input type="password" class="lnd-input" name="site_smtp_password" value="{{get_static_option_central('site_smtp_password')}}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('SMTP Driver')}}</label>
                            <select name="site_smtp_driver" class="lnd-input">
                                <option value="smtp" @selected(get_static_option_central('site_smtp_driver') == 'smtp')>{{__('SMTP')}}</option>
                                <option value="sendmail" @selected(get_static_option_central('site_smtp_driver') == 'sendmail')>{{__('Sendmail')}}</option>
                                <option value="mailgun" @selected(get_static_option_central('site_smtp_driver') == 'mailgun')>{{__('Mailgun')}}</option>
                                <option value="postmark" @selected(get_static_option_central('site_smtp_driver') == 'postmark')>{{__('Postmark')}}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('SMTP Port')}}</label>
                            <select name="site_smtp_port" class="lnd-input">
                                <option value="25" @selected(get_static_option_central('site_smtp_port') == '25')>25</option>
                                <option value="587" @selected(get_static_option_central('site_smtp_port') == '587')>587</option>
                                <option value="465" @selected(get_static_option_central('site_smtp_port') == '465')>465</option>
                                <option value="2525" @selected(get_static_option_central('site_smtp_port') == '2525')>2525</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Encryption')}}</label>
                            <select name="site_smtp_encryption" class="lnd-input">
                                <option value="ssl" @selected(get_static_option_central('site_smtp_encryption') == 'ssl')>{{__('SSL')}}</option>
                                <option value="tls" @selected(get_static_option_central('site_smtp_encryption') == 'tls')>{{__('TLS')}}</option>
                                <option value="null" @selected(get_static_option_central('site_smtp_encryption') == 'null')>{{__('None')}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-4 mt-5 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Test Mail --}}
    <div class="bg-surface rounded-xl shadow-main border border-main h-fit lg:sticky lg:top-5">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: var(--color-info-bg, #e0f2fe);">
                <i class="mdi mdi-send-outline text-base" style="color: var(--color-info, #0ea5e9);"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Send Test Mail')}}</h3>
                <p class="text-xs text-muted">{{__('Verify your SMTP config')}}</p>
            </div>
        </div>

        <div class="px-4 sm:px-6 py-5">
            <div class="flex items-start gap-2 px-4 py-3 rounded-xl mb-5 border" style="background: var(--color-warning-bg, #fffbeb); border-color: var(--color-warning, #f59e0b)20;">
                <i class="mdi mdi-alert-outline mt-0.5 flex-shrink-0" style="color: var(--color-warning, #f59e0b);"></i>
                <p class="text-xs" style="color: var(--color-warning, #f59e0b);">{{__('If you see errors, contact your hosting provider to verify your SMTP credentials.')}}</p>
            </div>

            <form class="forms-sample" method="post" action="{{route('landlord.admin.general.smtp.settings.test.mail')}}">
                @csrf
                <div class="mb-5">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Recipient Email')}}</label>
                    <input type="email" class="lnd-input" name="email" placeholder="{{__('Enter email address')}}">
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold hover:opacity-90 transition" style="background: var(--color-info, #0ea5e9);">
                    <i class="mdi mdi-send-outline text-base"></i> {{__('Send Test Mail')}}
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
