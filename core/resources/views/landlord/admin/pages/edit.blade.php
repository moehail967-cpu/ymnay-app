@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Edit Page')}} @endsection

@section('style')
    <x-summernote.css/>
    <style>
        .selected {
            border: 3px solid var(--color-primary, #6366f1);
            transition: 0.3s;
            border-radius: 0.75rem;
        }
    </style>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form class="forms-sample" method="post" action="{{route(route_prefix().'admin.pages.update')}}">
    @csrf
    <input type="hidden" name="id" value="{{$page->id}}">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main Content (Left) --}}
        <div class="lg:col-span-9 space-y-6">

            {{-- Title & Slug Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex flex-wrap items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-file-edit-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Page')}}</h3>
                        <p class="text-xs text-muted">{{$page->title}}</p>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        @can('page-create')
                        <a href="{{route(route_prefix().'admin.pages.create')}}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                            <i class="mdi mdi-plus text-base"></i> {{__('Create New')}}
                        </a>
                        @endcan
                        <a href="{{route(route_prefix().'admin.pages')}}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                            <i class="mdi mdi-arrow-left text-base"></i> {{__('All Pages')}}
                        </a>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-format-title text-lg text-primary"></i>
                            <input type="text" name="title" id="title" value="{{$page->title}}"
                                   placeholder="{{__('Enter page title')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Permalink --}}
                    <div class="permalink_label">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Permalink')}}</label>
                        <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-4 py-2.5">
                            <i class="mdi mdi-link-variant text-lg text-primary"></i>
                            <span id="slug_show" class="text-xs text-primary truncate">{{getUserBasedDomain(tenant())}}/{{$page->slug}}</span>
                            <span id="slug_edit" class="flex items-center gap-2 ml-auto">
                                <button type="button" class="slug_edit_button w-7 h-7 rounded-lg bg-warning-soft flex items-center justify-center text-warning hover:bg-warning hover:text-white transition">
                                    <i class="mdi mdi-pencil text-sm"></i>
                                </button>
                                <input type="text" name="slug" value="{{$page->slug}}" class="blog_slug text-sm bg-surface border border-main rounded-lg px-3 py-1.5 outline-none focus:border-primary" style="display:none">
                                <button type="button" class="slug_update_button px-3 py-1.5 rounded-lg bg-info text-white text-xs font-semibold hover:opacity-90 transition" style="display:none">{{__('Update')}}</button>
                            </span>
                        </div>
                    </div>

                    {{-- Page Builder Link --}}
                    @if($page->page_builder === 1)
                    <div>
                        @php
                            $pageBuilderRoute = route_prefix() === 'tenant.'
                                ? route('tenant.admin.page-builder.edit', $page->id)
                                : route(route_prefix().'admin.page-builder.edit', $page->id);
                        @endphp
                        <a href="{{ $pageBuilderRoute }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#f3e8ff] border border-[#e9d5ff] text-[#9333ea] text-sm font-semibold hover:bg-[#e9d5ff] transition">
                            <i class="mdi mdi-view-dashboard-edit-outline text-base"></i>
                            {{__('Edit with Page-Builder')}}
                        </a>
                    </div>
                    @endif

                    {{-- Page Content (Summernote) --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Page Content')}}</label>
                            @if(\App\PluginSystem\PluginManager::isActive('ai-integration'))
                            <button type="button" class="ai-bar-btn" data-ai-target="page_content" data-ai-type="page">
                                <i class="mdi mdi-robot-outline"></i> {{__('Generate with AI')}}
                            </button>
                            @endif
                        </div>
                        <textarea name="page_content" class="summernote" rows="4">{!! $page->page_content !!}</textarea>
                    </div>
                </div>
            </div>

            {{-- Meta SEO Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-search-web text-success text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Meta Information For SEO')}}</h3>
                        <p class="text-xs text-muted">{{__('Optimize for search engines')}}</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    @php
                        $selected_page = get_static_option($page->slug.'_page');
                    @endphp

                    @if($selected_page != $page->id)
                        {{-- Tab Navigation --}}
                        <div class="flex flex-wrap gap-2 mb-6">
                            <button type="button" class="meta-tab-btn active inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold transition" data-tab="general">
                                <i class="mdi mdi-cog-outline text-sm"></i> {{__('General')}}
                            </button>
                            <button type="button" class="meta-tab-btn inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold transition" data-tab="facebook">
                                <i class="mdi mdi-facebook text-sm"></i> {{__('Facebook')}}
                            </button>
                            <button type="button" class="meta-tab-btn inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold transition" data-tab="twitter">
                                <i class="mdi mdi-twitter text-sm"></i> {{__('Twitter')}}
                            </button>
                        </div>

                        {{-- General Meta --}}
                        <div class="meta-tab-content" id="meta-general">
                            <x-fields.input name="meta_title" label="{{__('Meta Title')}}" value="{{optional($page->metainfo)->title}}" />
                            <x-fields.textarea name="meta_description" label="{{__('Meta Description')}}" value="{{optional($page->metainfo)->description}}" />
                            <div class="mt-4">
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Meta Image')}}</label>
                                <x-fields.tw-media-upload name="meta_image" dimentions="1200x1200" :id="optional($page->metainfo)->image"/>
                            </div>
                        </div>

                        {{-- Facebook Meta --}}
                        <div class="meta-tab-content hidden" id="meta-facebook">
                            <x-fields.input name="meta_fb_title" label="{{__('Meta Title')}}" value="{{optional($page->metainfo)->fb_title}}" />
                            <x-fields.textarea name="meta_fb_description" label="{{__('Meta Description')}}" value="{{optional($page->metainfo)->fb_description}}" />
                            <div class="mt-4">
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Meta Image')}}</label>
                                <x-fields.tw-media-upload name="fb_image" dimentions="1200x1200" :id="optional($page->metainfo)->fb_image"/>
                            </div>
                        </div>

                        {{-- Twitter Meta --}}
                        <div class="meta-tab-content hidden" id="meta-twitter">
                            <x-fields.input name="meta_tw_title" label="{{__('Meta Title')}}" value="{{optional($page->metainfo)->tw_title}}" />
                            <x-fields.textarea name="meta_tw_description" label="{{__('Meta Description')}}" value="{{optional($page->metainfo)->tw_description}}" />
                            <div class="mt-4">
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Meta Image')}}</label>
                                <x-fields.tw-media-upload name="tw_image" dimentions="1200x1200" :id="optional($page->metainfo)->tw_image"/>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 mb-4">
                            <i class="mdi mdi-information-outline text-primary text-lg mt-0.5 shrink-0"></i>
                            <span class="text-[12px] text-dark leading-relaxed">{{__('This page is selected for front page. To update SEO please click the button below,')}}</span>
                        </div>
                        <a href="{{route(route_prefix().'admin.general.seo.settings')}}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-cog-outline text-base"></i>
                            {{__('Global SEO Settings')}}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar (Right) --}}
        <div class="lg:col-span-3">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
                <div class="px-4 py-4 border-b border-main">
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Page Settings')}}</h4>
                </div>

                <div class="p-4 space-y-5">

                    {{-- Visibility --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Visibility')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-eye-outline text-lg text-primary"></i>
                            <select name="visibility" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="0" @if($page->visibility === 0) selected @endif>{{__('Everyone')}}</option>
                                <option value="1" @if($page->visibility === 1) selected @endif>{{__('Users Only')}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('If users only, requires login to access.')}}</p>
                    </div>

                    {{-- Breadcrumb Toggle --}}
                    <div class="flex items-center justify-between bg-secondary border border-main rounded-xl px-4 py-3">
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-navigation-outline text-lg text-primary"></i>
                            <span class="text-xs font-semibold text-dark">{{__('Breadcrumb')}}</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="breadcrumb" class="sr-only peer" @if(!empty($page->breadcrumb)) checked @endif>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:rounded-full after:h-5 after:w-5
                                        after:transition-all peer-checked:after:translate-x-full after:shadow-sm"></div>
                        </label>
                    </div>

                    {{-- Page Builder Toggle --}}
                    <div class="flex items-center justify-between bg-secondary border border-main rounded-xl px-4 py-3">
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-view-dashboard-outline text-lg text-primary"></i>
                            <span class="text-xs font-semibold text-dark">{{__('Page Builder')}}</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="page_builder" class="sr-only peer" @if(!empty($page->page_builder)) checked @endif>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:rounded-full after:h-5 after:w-5
                                        after:transition-all peer-checked:after:translate-x-full after:shadow-sm"></div>
                        </label>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                            <select name="status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="1" @if($page->status === 1) selected @endif>{{__('Publish')}}</option>
                                <option value="0" @if($page->status === 0) selected @endif>{{__('Draft')}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
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
    <x-summernote.js/>
    <script>
    $(document).ready(function () {
        // ── Meta Tab Switching ────────────────────────────────────
        $(document).on('click', '.meta-tab-btn', function () {
            $('.meta-tab-btn').removeClass('active bg-primary text-white').addClass('bg-secondary text-dark border border-main');
            $(this).addClass('active bg-primary text-white').removeClass('bg-secondary text-dark border border-main');
            $('.meta-tab-content').addClass('hidden');
            $('#meta-' + $(this).data('tab')).removeClass('hidden');
        });
        // Init first tab
        $('.meta-tab-btn.active').addClass('bg-primary text-white');
        $('.meta-tab-btn:not(.active)').addClass('bg-secondary text-dark border border-main');

        // ── Slug Edit ─────────────────────────────────────────────
        $(document).on('click', '.slug_edit_button', function (e) {
            e.preventDefault();
            $('.blog_slug').show();
            $(this).hide();
            $('.slug_update_button').show();
        });

        function removeTags(str) {
            if ((str === null) || (str === '')) return false;
            return str.toString().replace(/(<([^>]+)>)/ig, '');
        }

        function converToSlug(slug) {
            let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
            finalSlug = slug.replace(/  +/g, ' ');
            finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
            return finalSlug;
        }

        $(document).on('click', '.slug_update_button', function (e) {
            e.preventDefault();
            $(this).hide();
            $('.slug_edit_button').show();
            var update_input = $('.blog_slug').val();
            var slug = converToSlug(update_input);
            var url = `{{getUserBasedDomain(tenant())}}/` + removeTags(slug);
            $('#slug_show').text(url);
            $('.blog_slug').hide();
        });
    });
    </script>
@endsection
