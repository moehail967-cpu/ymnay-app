@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Custom CSS')}} @endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/landlord/common/css/codemirror.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/landlord/common/css/show-hint.css')}}">
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="lab la-css3-alt text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Custom CSS')}}</h3>
            <p class="text-xs text-muted">{{__('Add custom CSS styles to your site')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form action="{{route(route_prefix().'admin.general.custom.css.settings')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-5 rounded-xl overflow-hidden border border-main">
                <textarea name="custom_css_area" id="custom_css_area" cols="30" rows="10">{{isset($custom_css) ? $custom_css : ''}}</textarea>
            </div>

            <div class="pt-4 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/landlord/common/js/codemirror.js')}}"></script>
    <script src="{{global_asset('assets/landlord/common/js/css.js')}}"></script>
    <script src="{{global_asset('assets/landlord/common/js/show-hint.js')}}"></script>
    <script src="{{global_asset('assets/landlord/common/js/css-hint.js')}}"></script>
    <script>
        (function($) {
            "use strict";
            var editor = CodeMirror.fromTextArea(document.getElementById("custom_css_area"), {
                lineNumbers: true,
                mode: "text/css",
                matchBrackets: true
            });
        })(jQuery);
    </script>
@endsection
