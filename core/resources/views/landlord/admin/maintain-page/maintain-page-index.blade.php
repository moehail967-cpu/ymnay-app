@extends(route_prefix().'admin.admin-master')

@section('title')
    {{__('Maintain Page Settings')}}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-tools text-warning text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Maintain Page Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Configure the maintenance mode page')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form action="{{route(route_prefix().'admin.maintains.page.settings')}}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="lnd-label">{{__('Title')}}</label>
                <input type="text" class="lnd-input" name="maintains_page_title"
                       value="{{get_static_option('maintains_page_title')}}">
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Description')}}</label>
                <textarea class="lnd-input" name="maintains_page_description" rows="4">{{get_static_option('maintains_page_description')}}</textarea>
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Coming Back Date')}}</label>
                <input type="date" class="lnd-input" name="mentenance_back_date" id="maintenance_date"
                       value="{{get_static_option('mentenance_back_date') ?? ''}}">
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Maintenance Logo')}}</label>
                <x-fields.tw-media-upload name="maintenance_logo" :id="get_static_option('maintenance_logo')"/>
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Maintenance Background Image')}}</label>
                <x-fields.tw-media-upload name="maintenance_bg_image" :id="get_static_option('maintenance_bg_image')"/>
            </div>

            <div class="pt-4 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Settings')}}
                </button>
            </div>
        </form>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
    <script>
        flatpickr('#maintenance_date', {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d H:i"
        });
    </script>
@endsection
