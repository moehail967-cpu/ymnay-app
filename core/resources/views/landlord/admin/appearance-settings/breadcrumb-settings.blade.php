@extends(route_prefix().'admin.admin-master')

@section('title')
    {{__('Breadcrumb Settings')}}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-image text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Breadcrumb Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Manage breadcrumb background images')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form method="post" action="{{route(route_prefix().'admin.breadcrumb.update')}}">
            @csrf

            @tenant
                <div class="bg-secondary border border-main rounded-xl px-4 py-3 mb-5 flex items-start gap-2">
                    <i class="las la-info-circle text-info text-lg mt-0.5 flex-shrink-0"></i>
                    <p class="text-xs text-muted">{{__('The Breadcrumb will be applicable if the theme contains breadcrumb images')}}</p>
                </div>

                @php
                    $hasValue = false;
                    switch(getSelectedThemeSlug()){
                        case 'aromatic':
                        $image_count = 5;
                        $hasValue = true;
                        break;
                    }
                @endphp

                @if($hasValue)
                    <div class="space-y-5 grid grid-cols-1 lg:grid-cols-5">
                        @for($i=1; $i<=$image_count; $i++)
                            <div>
                                <label class="lnd-label">{{__('Image '.ucfirst(number_to_word($i)))}}</label>
                                <x-fields.tw-media-upload name="background_image_{{number_to_word($i)}}" :id="get_static_option('background_image_'.number_to_word($i))"/>
                            </div>
                        @endfor
                    </div>

                    <div class="pt-4 mt-5 border-t border-main">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="las la-save"></i> {{__('Save Changes')}}
                        </button>
                    </div>
                @endif
            @else
                <div class="space-y-5 grid grid-cols-1 lg:grid-cols-5">
                    <div class="">
                        <label class="lnd-label">{{__('Left Shape Image')}}</label>
                        <x-fields.tw-media-upload name="background_left_shape_image" :id="get_static_option('background_left_shape_image')"/>
                    </div>
                    <div>
                        <label class="lnd-label">{{__('Right Shape Image')}}</label>
                        <x-fields.tw-media-upload name="background_right_shape_image" :id="get_static_option('background_right_shape_image')"/>
                    </div>
                </div>

                <div class="pt-4 mt-5 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="las la-save"></i> {{__('Save Changes')}}
                    </button>
                </div>
            @endtenant
        </form>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
