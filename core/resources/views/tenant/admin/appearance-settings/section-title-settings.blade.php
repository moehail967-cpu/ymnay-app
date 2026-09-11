@extends('tenant.admin.admin-master')
@section('title')
    {{__('Section Title Settings')}}
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-8">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-format-title text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Section Title Settings')}}</h3>
                    <p class="text-xs text-muted">{{__('Upload a shape image for section titles')}}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <form method="post" action="{{route('tenant.admin.section.manage.update')}}">
                    @csrf
                    <div class="space-y-5">
                        <div class="flex items-start gap-2 p-3 rounded-lg bg-info-soft border border-info/20">
                            <i class="mdi mdi-information-outline text-info text-sm mt-0.5 flex-shrink-0"></i>
                            <p class="text-xs text-muted">{{__('This image will be visible under section title if the feature is available in the selected theme')}}</p>
                        </div>

                        <x-fields.tw-media-upload name="shape_image" title="{{__('Shape Image')}}" dimentions="~230x26px"/>

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
                    <span>{{__('Recommended dimension: ~230x26px')}}</span>
                </div>
                <div class="flex items-start gap-2.5 text-xs text-muted">
                    <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                    <span>{{__('Use a transparent PNG for best results.')}}</span>
                </div>
                <div class="flex items-start gap-2.5 text-xs text-muted">
                    <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                    <span>{{__('This feature depends on the active theme supporting it.')}}</span>
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
