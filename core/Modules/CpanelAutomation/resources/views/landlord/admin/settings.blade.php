@extends(route_prefix().'admin.admin-master')

@section('title')
    {{ __('Cpanel Automation') }}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-server text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Cpanel Automation Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Automate user website database creation on shared hosting with cPanel')}}</p>
            </div>
        </div>
        <form action="{{route('landlord.admin.cpanel.automation.database.create.test')}}" method="post">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-success text-white text-xs font-semibold hover:opacity-90 transition">
                <i class="las la-flask"></i>
                {{__('Create Test Database')}}
            </button>
        </form>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <div class="bg-secondary border border-main rounded-xl px-4 py-3 mb-5 flex items-start gap-2">
            <i class="las la-info-circle text-info text-lg mt-0.5 flex-shrink-0"></i>
            <p class="text-xs text-muted">{{__('If you are using shared hosting and cPanel, you can automate user website database creation. No more manual database creation needed.')}}</p>
        </div>

        <form action="{{route('landlord.admin.cpanel.automation.settings')}}" method="post">
            @csrf

            <div class="mb-5">
                <div class="flex items-center justify-between">
                    <label class="lnd-label mb-0">{{__('Enable or Disable')}}</label>
                    <label class="dr-toggle">
                        <input type="hidden" name="_cpanel_automation_status" value="">
                        <input type="checkbox" name="_cpanel_automation_status" value="on"
                            {{!empty($settings['_cpanel_automation_status']) && $settings['_cpanel_automation_status'] === 'on' ? 'checked' : ''}}>
                        <span class="dr-toggle-track"></span>
                    </label>
                </div>
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Cpanel URL')}}</label>
                <input type="text" class="lnd-input" name="_cpanel_url" value="{{$settings['_cpanel_url'] ?? ''}}" placeholder="{{__('https://yourdomain.com:2083')}}">
                <p class="text-xs text-muted mt-1">{{__('Example: https://yourdomain.com:2083')}}</p>
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Cpanel Username')}}</label>
                <input type="text" class="lnd-input" name="_cpanel_username" value="{{$settings['_cpanel_username'] ?? ''}}" placeholder="{{__('Your cPanel username')}}">
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Cpanel Token')}}</label>
                <input type="text" class="lnd-input" name="_cpanel_access_token" value="{{!empty($settings['_cpanel_access_token']) ? $settings['_cpanel_access_token'] : ''}}" placeholder="{{__('Your cPanel access token')}}">
                <div class="flex items-start gap-1.5 mt-1.5">
                    <i class="las la-shield-alt text-warning text-sm mt-0.5 flex-shrink-0"></i>
                    <p class="text-xs text-warning">{{__('Do not share this token with anyone')}}</p>
                </div>
            </div>

            <div class="pt-4 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
