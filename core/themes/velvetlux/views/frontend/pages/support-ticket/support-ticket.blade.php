@extends(theme_path('frontend.user.dashboard.user-master'))
@section('title') {{ __('Support Ticket') }} @endsection
@section('page-title') {{ __('Support Ticket') }} @endsection
@section('dashboard-content')

@if(session('msg'))
<div style="padding:12px 16px;margin-bottom:18px;border:1px solid #48BB78;background:rgba(72,187,120,.08);color:#276749;font-size:12px;letter-spacing:1px;">
    {{ session('msg') }}
</div>
@endif

@if($errors->any())
<div style="padding:12px 16px;margin-bottom:18px;border:1px solid #E53E3E;background:rgba(229,62,62,.08);color:#C53030;font-size:12px;">
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

@if(auth()->guard('web')->check())
<div class="vl-dash-card">
    <div class="vl-dash-card-header">
        <i class="mdi mdi-headset" style="margin-right:8px;opacity:.7;"></i>
        {{ get_static_option('support_ticket_form_title') ?: __('Open a Support Ticket') }}
    </div>
    <div class="vl-dash-card-body">
        <form action="{{ theme_user_ticket_store_url() }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="via" value="{{ __('website') }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label class="vl-dash-label">{{ __('Title') }} <span style="color:#E53E3E;">*</span></label>
                    <input class="vl-dash-input" type="text" name="title" placeholder="{{ __('Ticket title') }}" value="{{ old('title') }}" required>
                </div>
                <div>
                    <label class="vl-dash-label">{{ __('Subject') }} <span style="color:#E53E3E;">*</span></label>
                    <input class="vl-dash-input" type="text" name="subject" placeholder="{{ __('Brief subject') }}" value="{{ old('subject') }}" required>
                </div>
                <div>
                    <label class="vl-dash-label">{{ __('Priority') }} <span style="color:#E53E3E;">*</span></label>
                    <select class="vl-dash-input" name="priority" required>
                        @foreach(['low'=>__('Low'),'medium'=>__('Medium'),'high'=>__('High'),'urgent'=>__('Urgent')] as $val=>$label)
                            <option value="{{ $val }}" @selected(old('priority')===$val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="vl-dash-label">{{ __('Department') }} <span style="color:#E53E3E;">*</span></label>
                    <select class="vl-dash-input" name="departments" required>
                        <option value="">{{ __('Select department') }}</option>
                        @foreach($departments ?? [] as $dep)
                            <option value="{{ $dep->id }}" @selected(old('departments')==$dep->id)>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label class="vl-dash-label">{{ __('Description') }} <span style="color:#E53E3E;">*</span></label>
                <textarea class="vl-dash-input" name="description" rows="6" placeholder="{{ __('Describe your issue in detail…') }}" required style="resize:vertical;">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="vl-btn vl-btn-primary">
                <i class="mdi mdi-send-outline"></i> {{ get_static_option('support_ticket_button_text') ?: __('Submit Ticket') }}
            </button>
        </form>
    </div>
</div>
@else
    @include('tenant.frontend.partials.ajax-login-form', ['title' => get_static_option('support_ticket_login_notice')])
@endif

@endsection
