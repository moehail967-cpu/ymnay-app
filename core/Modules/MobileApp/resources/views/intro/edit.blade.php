@extends('tenant.admin.admin-master')
@section('title') {{__('Edit Mobile Intro')}} @endsection

@section('style')
    <x-media-upload.css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form action="{{ route('tenant.admin.mobile.intro.edit', $mobileIntro->id) }}" method="post">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main Form --}}
        <div class="lg:col-span-9">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-cellphone-screenshot text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Mobile Intro')}}</h3>
                        <p class="text-xs text-muted">{{__('Update the intro slide details')}}</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('tenant.admin.mobile.intro.all') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                            <i class="mdi mdi-format-list-bulleted text-base"></i> {{__('All Intros')}}
                        </a>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">

                    {{-- Title --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-format-title text-lg text-primary"></i>
                            <input type="text" name="title" value="{{ $mobileIntro->title }}"
                                   placeholder="{{__('Mobile intro title...')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Description')}}</label>
                        <textarea name="description" rows="6" placeholder="{{__('Mobile intro description...')}}"
                                  class="w-full bg-secondary border border-main rounded-xl px-4 py-2.5 text-sm text-dark placeholder-subtle outline-none focus:border-primary transition resize-none">{{ $mobileIntro->description }}</textarea>
                    </div>

                    {{-- Image --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Image')}}</label>
                        <x-fields.tw-media-upload :title="__('Image')" :name="'image_id'" :id="$mobileIntro->image_id" :dimentions="'1280x1280'"/>
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
                        <span class="text-[11px] text-dark leading-relaxed">{{__('Recommended image size: 1280x1280 pixels.')}}</span>
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
@endsection
