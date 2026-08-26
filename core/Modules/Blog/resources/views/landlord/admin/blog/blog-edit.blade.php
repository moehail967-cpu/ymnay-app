@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Edit Blog Post')}} @endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/landlord/admin/css/bootstrap-tagsinput.css')}}">
    <x-summernote.css/>
    <style>
        .bootstrap-tagsinput {
            width: 100%; border: none; box-shadow: none; padding: 0.25rem 0;
            background: transparent; font-size: 0.8125rem;
        }
        .bootstrap-tagsinput .tag {
            background: var(--color-primary, #1a5c4e); border-radius: 0.375rem;
            padding: 0.125rem 0.5rem; font-size: 0.75rem; font-weight: 600;
        }
        .bootstrap-tagsinput input {
            background: transparent !important; border: none !important;
            outline: none !important; box-shadow: none !important;
            font-size: 0.8125rem; color: var(--color-text-dark, #111827);
        }
        #show-autocomplete {
            margin-top: 0.5rem; padding: 0.5rem;
            background: var(--color-bg-surface, #fff); border: 1px solid var(--color-border-main, #e5e7eb);
            border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(0,0,0,.08);
        }
        ul.autocomplete-warp { margin: 0; list-style: none; padding: 0; }
        li.tag_option {
            padding: 0.375rem 0.75rem; border-radius: 0.5rem; cursor: pointer;
            font-size: 0.8125rem; color: var(--color-text-dark, #111827); transition: background 0.12s;
        }
        li.tag_option:hover { background: var(--color-bg-muted, #f3f4f6); }
    </style>
    <style>.hover\:text-white:hover{color:#fff!important}</style>

@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form method="post" action="{{route(route_prefix().'admin.blog.update',$blog_post->id)}}">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main Content (Left) --}}
        <div class="lg:col-span-9 space-y-6">

            {{-- Title & Slug Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-pencil-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Blog Post')}}</h3>
                        <p class="text-xs text-muted">{{__('Update your article content and SEO settings')}}</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{route(route_prefix().'admin.blog')}}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                            <i class="mdi mdi-arrow-left text-base"></i> {{__('All Posts')}}
                        </a>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-format-title text-lg text-primary"></i>
                            <input type="text" name="title" id="title" value="{!! purify_html($blog_post->title) !!}"
                                   placeholder="{{__('Enter blog title')}}"
                                   class="title flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Permalink --}}
                    <div class="permalink_label">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Permalink')}}</label>
                        <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-4 py-2.5">
                            <i class="mdi mdi-link-variant text-lg text-primary"></i>
                            <span id="slug_show" class="text-xs text-primary truncate"></span>
                            <span id="slug_edit" class="flex items-center gap-2 ml-auto">
                                <button type="button" class="slug_edit_button w-7 h-7 rounded-lg bg-warning-soft flex items-center justify-center text-warning hover:bg-warning hover:text-white transition">
                                    <i class="mdi mdi-pencil text-sm"></i>
                                </button>
                                <input type="text" name="slug" value="{{$blog_post->slug}}" class="form-control blog_slug text-sm bg-surface border border-main rounded-lg px-3 py-1.5 outline-none focus:border-primary" style="display:none">
                                <button type="button" class="slug_update_button px-3 py-1.5 rounded-lg bg-info text-white text-xs font-semibold hover:opacity-90 transition" style="display:none">{{__('Update')}}</button>
                            </span>
                        </div>
                    </div>

                    {{-- Blog Content (Summernote) --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Blog Content')}}</label>
                            @if(\App\PluginSystem\PluginManager::isActive('ai-integration'))
                            <button type="button" class="ai-bar-btn" data-ai-target="blog_content" data-ai-type="blog">
                                <i class="mdi mdi-robot-outline"></i> {{__('Generate with AI')}}
                            </button>
                            @endif
                        </div>
                        <input type="hidden" name="blog_content" value="{{$blog_post->blog_content}}">
                        <div class="summernote" data-content="{{$blog_post->blog_content}}"></div>
                    </div>

                    {{-- Excerpt --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Excerpt')}}</label>
                        <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <textarea name="excerpt" rows="3" class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-y" placeholder="{{__('Short description...')}}">{{$blog_post->excerpt}}</textarea>
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('A brief summary shown in blog listings and search results.')}}</p>
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
                        <x-fields.input name="meta_title" label="{{__('Meta Title')}}" value="{{optional($blog_post->metainfo)->title}}"/>
                        <x-fields.textarea name="meta_description" label="{{__('Meta Description')}}" value="{{optional($blog_post->metainfo)->description}}"/>
                        <div class="mt-4">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Meta Image')}}</label>
                            <x-fields.tw-media-upload name="meta_image" dimentions="1200x1200" :id="optional($blog_post->metainfo)->image"/>
                        </div>
                    </div>

                    {{-- Facebook Meta --}}
                    <div class="meta-tab-content hidden" id="meta-facebook">
                        <x-fields.input name="meta_fb_title" label="{{__('Meta Title')}}" value="{{optional($blog_post->metainfo)->fb_title}}"/>
                        <x-fields.textarea name="meta_fb_description" label="{{__('Meta Description')}}" value="{{optional($blog_post->metainfo)->fb_description}}"/>
                        <div class="mt-4">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Meta Image')}}</label>
                            <x-fields.tw-media-upload name="meta_fb_image" dimentions="1200x1200" :id="optional($blog_post->metainfo)->fb_image"/>
                        </div>
                    </div>

                    {{-- Twitter Meta --}}
                    <div class="meta-tab-content hidden" id="meta-twitter">
                        <x-fields.input name="meta_tw_title" label="{{__('Meta Title')}}" value="{{optional($blog_post->metainfo)->tw_title}}"/>
                        <x-fields.textarea name="meta_tw_description" label="{{__('Meta Description')}}" value="{{optional($blog_post->metainfo)->tw_description}}"/>
                        <div class="mt-4">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Meta Image')}}</label>
                            <x-fields.tw-media-upload name="meta_tw_image" dimentions="1200x1200" :id="optional($blog_post->metainfo)->tw_image"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar (Right) --}}
        <div class="lg:col-span-3">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
                <div class="px-4 py-4 border-b border-main">
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Post Settings')}}</h4>
                </div>

                <div class="p-4 space-y-5">

                    {{-- Post Type --}}
                    @php
                        $check = $blog_post->video_url ? 'checked' : '';
                    @endphp
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Post Type')}}</label>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-4 py-2.5 cursor-pointer flex-1 has-[:checked]:border-primary has-[:checked]:bg-primary-soft transition">
                                <input class="form-check-input post_type sr-only" type="radio" @if(!$blog_post->video_url) checked @endif name="inlineRadioOptions" id="radio_general" value="option1">
                                <i class="mdi mdi-cog-outline text-lg text-primary"></i>
                                <span class="text-xs font-semibold text-dark">{{__('General')}}</span>
                            </label>
                            <label class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-4 py-2.5 cursor-pointer flex-1 has-[:checked]:border-primary has-[:checked]:bg-primary-soft transition">
                                <input class="form-check-input post_type sr-only" type="radio" @if($blog_post->video_url) checked @endif name="inlineRadioOptions" id="radio_video" value="option2">
                                <i class="mdi mdi-video-outline text-lg text-primary"></i>
                                <span class="text-xs font-semibold text-dark">{{__('Video')}}</span>
                            </label>
                        </div>
                    </div>

                    {{-- Video URL (conditional) --}}
                    <div class="video_section" style="display:none">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Video Url')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-youtube text-lg text-primary"></i>
                            <input type="text" name="video_url" value="{{$blog_post->video_url}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                                   placeholder="{{__('https://youtube.com/...')}}">
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('Paste the YouTube or Vimeo video URL.')}}</p>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Category')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-folder-outline text-lg text-primary"></i>
                            <select name="category_id" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                @foreach($all_blog_category as $cat)
                                    <option value="{{$cat->id}}" @if($cat->id == $blog_post->category_id) selected @endif>{{$cat->title}}</option>
                                @endforeach
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div id="blog_tag_list">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Tags')}}</label>
                        <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <input type="text" class="tags_filed w-full bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0"
                                   name="tags" id="datetimepicker1" value="{{$blog_post->tags}}">
                        </div>
                        <div id="show-autocomplete" style="display:none;">
                            <ul class="autocomplete-warp"></ul>
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('Press enter to add a tag.')}}</p>
                    </div>

                    {{-- Featured Toggle --}}
                    <div class="flex items-center justify-between bg-secondary border border-main rounded-xl px-4 py-3">
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-star-outline text-lg text-primary"></i>
                            <span class="text-xs font-semibold text-dark">{{__('Featured')}}</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="featured" value="on" class="sr-only peer" @if($blog_post->featured) checked @endif>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:rounded-full after:h-5 after:w-5
                                        after:transition-all peer-checked:after:translate-x-full after:shadow-sm"></div>
                        </label>
                    </div>

                    {{-- Visibility --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Visibility')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-eye-outline text-lg text-primary"></i>
                            <select name="visibility" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="public" @if($blog_post->visibility == 'public') selected @endif>{{__('Public')}}</option>
                                <option value="logged_user" @if($blog_post->visibility == 'logged_user') selected @endif>{{__('Logged User')}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('If logged user, requires login to access.')}}</p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                            <select name="status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="{{\App\Enums\StatusEnums::DRAFT}}" @if($blog_post->status == \App\Enums\StatusEnums::DRAFT) selected @endif>{{__("Draft")}}</option>
                                <option value="{{\App\Enums\StatusEnums::PUBLISH}}" @if($blog_post->status == \App\Enums\StatusEnums::PUBLISH) selected @endif>{{__("Publish")}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Media --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Featured Image')}}</label>
                        <x-fields.tw-media-upload name="image" :id="$blog_post->image"/>
                    </div>

                    {{-- Gallery --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Image Gallery')}}</label>
                        <x-landlord-others.tw-edit-media-upload-gallery :name="'image_gallery'" :value="$blog_post->image_gallery"/>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Post')}}
                    </button>
                </div>
            </div>
        </div>

    </div>

</form>

<x-media-upload.tw-markup/>
@endsection

@section('scripts')
    <x-summernote.js/>
    <x-media-upload.tw-js/>
    <script src="{{global_asset('assets/landlord/admin/js/bootstrap-tagsinput.js')}}"></script>

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

        // ── Permalink ─────────────────────────────────────────────
        function converToSlug(slug) {
            let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
            finalSlug = slug.replace(/  +/g, ' ');
            finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
            return finalSlug;
        }

        // Show existing slug on load
        var sl = $('.blog_slug').val();
        var url = `{{url('/blog/')}}/` + sl;
        $('#slug_show').text(url);

        $(document).on('click', '.slug_edit_button', function (e) {
            e.preventDefault();
            $('.blog_slug').show();
            $(this).hide();
            $('.slug_update_button').show();
        });

        $(document).on('click', '.slug_update_button', function (e) {
            e.preventDefault();
            $(this).hide();
            $('.slug_edit_button').show();
            var update_input = $('.blog_slug').val();
            var slug = converToSlug(update_input);
            var url = `{{url('/blog/')}}/` + slug;
            $('#slug_show').text(url);
            $('.blog_slug').hide();
        });

        // ── Post Type Toggle ──────────────────────────────────────
        $(document).on('change', '.post_type', function () {
            var val = $(this).val();
            if (val === 'option2') {
                $('.video_section').slideDown();
            } else {
                $('.video_section').slideUp();
            }
        });

        // Show video section if already has video
        @if($blog_post->video_url)
            $('.video_section').show();
        @endif

        // ── Language Change ───────────────────────────────────────
        $(document).on('change', 'select[name="lang"]', function (e) {
            $(this).closest('form').trigger('submit');
            $('input[name="lang"]').val($(this).val());
        });

        // ── Summernote Init ───────────────────────────────────────
        $('.summernote').summernote({
            height: 400,
            codemirror: {
                theme: 'monokai'
            },
            callbacks: {
                onChange: function (contents, $editable) {
                    $(this).prev('input').val(contents);
                }
            }
        });
        if ($('.summernote').length > 0) {
            $('.summernote').each(function (index, value) {
                $(this).summernote('code', $(this).data('content'));
            });
        }
    });
    </script>

    <script>
        //Date Picker
        flatpickr('#tag_data', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today"
        });

        var blogTagInput = $('#blog_tag_list .tags_filed');
        var oldTag = '';
        blogTagInput.tagsinput();
        //For Tags
        $(document).on('keyup', '#blog_tag_list .bootstrap-tagsinput input[type="text"]', function (e) {
            e.preventDefault();
            var el = $(this);
            var inputValue = $(this).val();
            $.ajax({
                type: 'get',
                url: "{{ route('tenant.admin.blog.get.tags.by.ajax') }}",
                async: false,
                data: {
                    query: inputValue
                },

                success: function (data) {
                    oldTag = inputValue;
                    let html = '';
                    var showAutocomplete = '';
                    $('#show-autocomplete').html('<ul class="autocomplete-warp"></ul>');
                    if (el.val() != '' && data.markup != '') {
                        data.result.map(function (tag, key) {
                            html += '<li class="tag_option" data-id="' + key + '"  data-val="' + tag + '">' + tag + '</li>'
                        })

                        $('#show-autocomplete ul').html(html);
                        $('#show-autocomplete').show();
                    } else {
                        $('#show-autocomplete').hide();
                        oldTag = '';
                    }

                },
                error: function (res) {

                }
            });
        });

        $(document).on('click', '.tag_option', function (e) {
            e.preventDefault();

            let id = $(this).data('id');
            let tag = $(this).data('val');
            blogTagInput.tagsinput('add', tag);
            $(this).parent().remove();
            blogTagInput.tagsinput('remove', oldTag);
        });
    </script>
@endsection
