<style>
/* ===== Support Ticket Form — shared component ===== */
.st-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;max-width:740px;}
.st-card-head{padding:16px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;}
.st-card-head i{font-size:20px;color:var(--main-color-one,#333);}
.st-card-head h5{margin:0;font-size:16px;font-weight:700;color:#111827;}
.st-card-body{padding:28px 24px;}
.st-alert{padding:12px 16px;border-radius:8px;margin-bottom:18px;font-size:13px;}
.st-alert-success{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.3);color:#166534;}
.st-alert-error{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3);color:#991b1b;}
.st-field{display:flex;flex-direction:column;gap:5px;margin-bottom:18px;}
.st-field:last-child{margin-bottom:0;}
.st-label{font-size:13px;font-weight:600;color:#374151;}
.st-req{color:#ef4444;}
.st-input{width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:10px 14px;font-size:14px;color:#111827;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s;font-family:inherit;}
.st-input:focus{border-color:var(--main-color-one,#333);box-shadow:0 0 0 3px color-mix(in srgb,var(--main-color-one,#333) 12%,transparent);}
select.st-input{cursor:pointer;}
textarea.st-input{resize:vertical;min-height:120px;}
.st-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
@media(max-width:600px){.st-grid-2{grid-template-columns:1fr;}}
.st-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 32px;border-radius:8px;font-size:14px;font-weight:700;background:var(--main-color-one,#333);color:#fff;border:none;cursor:pointer;transition:opacity .2s;margin-top:4px;}
.st-btn:hover{opacity:.88;}
.st-btn i{font-size:17px;}
</style>

<div class="st-card">
    <div class="st-card-head">
        <i class="mdi mdi-headset"></i>
        <h5>{{ get_static_option('support_ticket_form_title') ?: __('Open a Support Ticket') }}</h5>
    </div>
    <div class="st-card-body">

        @if(session('msg'))
        <div class="st-alert st-alert-success">
            <i class="mdi mdi-check-circle-outline" style="margin-right:6px;"></i>{{ session('msg') }}
        </div>
        @endif

        @if($errors->any())
        <div class="st-alert st-alert-error">
            @foreach($errors->all() as $error)
                <div><i class="mdi mdi-alert-circle-outline" style="margin-right:6px;"></i>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        @if(auth()->guard('web')->check())
        <form action="{{ theme_user_ticket_store_url() }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="via" value="{{ __('website') }}">

            <div class="st-grid-2">
                <div class="st-field">
                    <label class="st-label">{{ __('Title') }} <span class="st-req">*</span></label>
                    <input class="st-input" type="text" name="title" value="{{ old('title') }}" placeholder="{{ __('Ticket title') }}" required>
                </div>
                <div class="st-field">
                    <label class="st-label">{{ __('Subject') }} <span class="st-req">*</span></label>
                    <input class="st-input" type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('Brief subject') }}" required>
                </div>
                <div class="st-field">
                    <label class="st-label">{{ __('Priority') }} <span class="st-req">*</span></label>
                    <select class="st-input" name="priority" required>
                        @foreach(['low' => __('Low'), 'medium' => __('Medium'), 'high' => __('High'), 'urgent' => __('Urgent')] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('priority') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="st-field">
                    <label class="st-label">{{ __('Department') }} <span class="st-req">*</span></label>
                    <select class="st-input" name="departments" required>
                        <option value="">{{ __('Select department') }}</option>
                        @foreach($departments ?? [] as $dep)
                            <option value="{{ $dep->id }}" @selected(old('departments') == $dep->id)>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="st-field">
                <label class="st-label">{{ __('Description') }} <span class="st-req">*</span></label>
                <textarea class="st-input" name="description" rows="6" placeholder="{{ __('Describe your issue in detail…') }}" required>{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="st-btn">
                <i class="mdi mdi-send-outline"></i>
                {{ get_static_option('support_ticket_button_text') ?: __('Submit Ticket') }}
            </button>
        </form>
        @else
            @include('tenant.frontend.partials.ajax-login-form', ['title' => get_static_option('support_ticket_login_notice')])
        @endif

    </div>
</div>
