@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Blog Settings')}} @endsection
@section('style')
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="max-w-full">
    <div class="bg-surface rounded-xl shadow-main border border-main mb-6">

        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-cog-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Blog Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Configure blog display options')}}</p>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{route(route_prefix().'admin.blog.settings')}}" method="post">
            @csrf
            <div class="px-4 sm:px-6 py-6 space-y-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Blog Page Item Show')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-view-grid-outline text-lg text-primary"></i>
                            <input type="text" name="blogs_page_item_show" value="{{get_static_option('blogs_page_item_show')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Category Page Item Show')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-folder-outline text-lg text-primary"></i>
                            <input type="text" name="category_page_item_show" value="{{get_static_option('category_page_item_show')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Tag Page Item Show')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-tag-outline text-lg text-primary"></i>
                            <input type="text" name="tag_page_item_show" value="{{get_static_option('tag_page_item_show')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Search Page Item Show')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-magnify text-lg text-primary"></i>
                            <input type="text" name="search_page_item_show" value="{{get_static_option('search_page_item_show')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Blog Comment Avatar')}}</label>
                    <x-fields.tw-media-upload name="blog_avatar_image" :id="get_static_option('blog_avatar_image')"/>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end px-4 sm:px-6 py-4 border-t border-main bg-secondary rounded-b-xl">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Save Changes')}}
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
