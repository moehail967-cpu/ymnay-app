<button type="button"
        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-primary-soft border border-main text-primary text-[11px] font-semibold hover:bg-primary hover:text-white hover:border-primary transition-all swal_status_change"
        title="{{__('Change Status')}}">
    <i class="mdi mdi-swap-horizontal text-sm"></i>
    {{__('Change')}}
</button>
<form method='post' action='{{$url}}' class="hidden">
    <input type='hidden' name='_token' value='{{csrf_token()}}'>
    <button type="submit" class="swal_form_submit_btn hidden"></button>
</form>
