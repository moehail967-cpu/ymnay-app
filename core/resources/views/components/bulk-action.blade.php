@php
    if (isset($permissions) && !auth('admin')->user()->can($permissions)){
        return;
    }
@endphp

<div class="bulk-delete-wrapper flex items-center gap-2">
    <select name="bulk_option" id="bulk_option"
            class="text-xs bg-secondary border border-main rounded-lg px-3 py-1.5 text-dark focus:border-primary focus:outline-none transition">
        <option value="">{{__('Bulk Action')}}</option>
        <option value="delete">{{__('Delete')}}</option>
    </select>
    <button class="btn btn-primary btn-sm inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition"
            id="bulk_delete_btn">
        {{__('Apply')}}
    </button>
</div>
