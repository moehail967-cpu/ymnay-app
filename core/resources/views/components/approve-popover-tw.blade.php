@php
    if (isset($permissions) && !auth('admin')->user()->can($permissions)){
        return;
    }
@endphp

<button type="button"
        class="tw-btn-icon tw-btn-icon-approve swal_change_approve_payment_button"
        title="{{__('Approve')}}">
    <i class="mdi mdi-check-bold"></i>
</button>
<form method='post' action='{{$url}}' class="hidden">
    <input type='hidden' name='_token' value='{{csrf_token()}}'>
    <button type="submit" class="swal_form_submit_btn hidden"></button>
</form>
