@extends('tenant.admin.admin-master')
@section('title')
    {{__('Breadcrumb Settings')}}
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-8">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-image-filter-hdr text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Breadcrumb Settings')}}</h3>
                    <p class="text-xs text-muted">{{__('Upload shape images for the breadcrumb section')}}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <form method="post" action="{{route('tenant.admin.breadcrumb.update')}}">
                    @csrf
                    <div class="space-y-5">
                        <x-fields.tw-media-upload name="background_left_shape_image" title="{{__('Left Shape Image')}}"/>
                        <x-fields.tw-media-upload name="background_right_shape_image" title="{{__('Right Shape Image')}}"/>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="lg:col-span-4">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden lg:sticky lg:top-[80px]">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-information-outline text-info text-base"></i>
                </div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Info')}}</h3>
            </div>
            <div class="p-4 sm:p-6 space-y-3">
                <div class="flex items-start gap-2.5 text-xs text-muted">
                    <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                    <span>{{__('Left and right shape images appear on the breadcrumb background.')}}</span>
                </div>
                <div class="flex items-start gap-2.5 text-xs text-muted">
                    <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                    <span>{{__('Use transparent PNG images for best results.')}}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
