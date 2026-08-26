@extends('tenant.frontend.user.dashboard.user-master')

@section('title') {{__('Support Ticket')}} @endsection
@section('site-title') {{__('Support Ticket')}} @endsection

@section('section')
<div class="hf-dash-card">
    <div class="hf-dash-card-title"><i class="las la-ticket-alt"></i> {{__('Support Ticket')}}</div>
    @if(auth()->guard('web')->check())
        <p style="font-size:13px;color:#888;margin-bottom:20px;">{{get_static_option('support_ticket_form_title')}}</p>
        {!! theme_error_msg() !!}
        <form action="{{theme_user_ticket_store_url()}}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="via" value="{{__('website')}}">

            <div class="hf-form-group mb-3">
                <label class="hf-form-label">{{__('Title')}}</label>
                <input type="text" class="hf-form-input" name="title" placeholder="{{__('Title')}}">
            </div>
            <div class="hf-form-group mb-3">
                <label class="hf-form-label">{{__('Subject')}}</label>
                <input type="text" class="hf-form-input" name="subject" placeholder="{{__('Subject')}}">
            </div>
            <div class="hf-form-grid mb-3">
                <div class="hf-form-group">
                    <label class="hf-form-label">{{__('Priority')}}</label>
                    <select name="priority" class="hf-form-select">
                        <option value="low">{{__('Low')}}</option>
                        <option value="medium">{{__('Medium')}}</option>
                        <option value="high">{{__('High')}}</option>
                        <option value="urgent">{{__('Urgent')}}</option>
                    </select>
                </div>
                <div class="hf-form-group">
                    <label class="hf-form-label">{{__('Departments')}}</label>
                    <select name="departments" class="hf-form-select">
                        @foreach($departments as $dep)
                            <option value="{{$dep->id}}">{{$dep->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="hf-form-group mb-4">
                <label class="hf-form-label">{{__('Description')}}</label>
                <textarea name="description" class="hf-form-textarea" rows="6" placeholder="{{__('Description')}}"></textarea>
            </div>
            <button class="hf-btn hf-btn-primary" type="submit">{{get_static_option('support_ticket_button_text') ?? 'Submit'}}</button>
        </form>
    @else
        @include('tenant.frontend.partials.ajax-login-form',['title' => get_static_option('support_ticket_login_notice')])
    @endif
</div>
@endsection

@section('scripts')
    <script>
        <x-btn.save/>
    </script>
@endsection
