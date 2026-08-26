@extends('tenant.admin.admin-master')
@section('title') {{__('Edit Mobile Slider')}} @endsection

@section('style')
    <x-media-upload.css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form action="{{ route('tenant.admin.mobile.slider.edit', $mobileSlider->id) }}" method="post">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main Form --}}
        <div class="lg:col-span-9">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-image-multiple-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Mobile Slider')}}</h3>
                        <p class="text-xs text-muted">{{__('Update slider details')}}</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('tenant.admin.mobile.slider.all') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                            <i class="mdi mdi-format-list-bulleted text-base"></i> {{__('All Sliders')}}
                        </a>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-format-title text-lg text-primary"></i>
                            <input type="text" name="title" value="{{ $mobileSlider->title }}" placeholder="{{__('Slider title...')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Description')}}</label>
                        <textarea name="description" rows="5" placeholder="{{__('Slider description...')}}"
                                  class="w-full bg-secondary border border-main rounded-xl px-4 py-2.5 text-sm text-dark placeholder-subtle outline-none focus:border-primary transition resize-none">{{ $mobileSlider->description }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Image')}}</label>
                        <x-fields.tw-media-upload :title="__('Image')" :name="'image'" :id="$mobileSlider->image_id" :dimentions="'1280x1280'"/>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Button Text')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-gesture-tap-button text-lg text-primary"></i>
                            <input type="text" name="button_text" value="{{ $mobileSlider->button_text }}" placeholder="{{__('Button text...')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Button URL')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-link-variant text-lg text-primary"></i>
                            <input type="text" name="button_url" value="{{ $mobileSlider->url }}" placeholder="{{__('Button URL...')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="category_toggle" name="category_type" class="sr-only peer" {{ !empty($mobileSlider->category) ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                        <span class="text-sm font-medium text-dark">{{__('Enable Category')}}</span>
                    </div>

                    <div id="campaign-list" {!! !empty($mobileSlider->category) ? 'class="hidden"' : '' !!}>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Select Campaign')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-bullhorn-outline text-lg text-primary"></i>
                            <select name="campaign" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 cursor-pointer">
                                <option value="">{{__('Select Campaign')}}</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" {{ $mobileSlider->campaign == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="category-list" {!! empty($mobileSlider->category) ? 'class="hidden"' : '' !!}>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Select Category')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-shape-outline text-lg text-primary"></i>
                            <select name="category" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 cursor-pointer">
                                <option value="">{{__('Select Category')}}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $mobileSlider->category == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-3">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
                <div class="px-4 py-4 border-b border-main">
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Update')}}</h4>
                </div>
                <div class="p-4 space-y-5">
                    <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <i class="mdi mdi-information-outline text-info text-lg mt-0.5 shrink-0"></i>
                        <span class="text-[11px] text-dark leading-relaxed">{{__('Toggle "Enable Category" to link this slider to a category instead of a campaign.')}}</span>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update')}}
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
    <script>
    (function ($) {
        "use strict";
        $('#category_toggle').on('change', function () {
            if ($(this).is(':checked')) {
                $('#campaign-list').slideUp(300, function(){ $(this).addClass('hidden'); });
                $('#category-list').removeClass('hidden').hide().slideDown(300);
            } else {
                $('#category-list').slideUp(300, function(){ $(this).addClass('hidden'); });
                $('#campaign-list').removeClass('hidden').hide().slideDown(300);
            }
        });
    })(jQuery);
    </script>
@endsection
