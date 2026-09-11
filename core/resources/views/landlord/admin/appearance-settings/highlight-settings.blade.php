@extends(route_prefix().'admin.admin-master')

@section('title')
    {{__('Text Highlight Settings')}}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-highlighter text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Text Highlight Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Configure the shape displayed under highlighted text')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form method="post" action="{{route(route_prefix().'admin.highlight.update')}}">
            @csrf

            <div class="bg-secondary border border-main rounded-xl px-4 py-3 mb-5 flex items-start gap-2">
                <i class="las la-info-circle text-info text-lg mt-0.5 flex-shrink-0"></i>
                <p class="text-xs text-muted">{{__('If you used highlighted text anywhere, this image will be shown under the text')}}</p>
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Highlighted Text Shape')}}</label>
                <x-fields.tw-media-upload name="highlight_text_shape" dimentions="~230x26px" :id="get_static_option('highlight_text_shape')"/>
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

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
