@php
    if (isset($permissions) && !auth('admin')->user()->can($permissions)){
        return;
    }
@endphp

<a tabindex="0"
   class="tw-btn-icon tw-btn-icon-danger {{$customStrictClass ?? 'swal_delete_button'}}"
   title="{{__('Delete')}}"
   role="button"
>
    <i class="mdi mdi-delete"></i>
</a>
<form method='post' action='{{$url}}' class="hidden d-none">
    @if(isset($method))
        @method($method)
    @endif
    <input type='hidden' name='_token' value='{{csrf_token()}}'>
    <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
</form>
