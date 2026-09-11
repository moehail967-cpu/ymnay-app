@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Blog Tags')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <style>.hover\:text-white:hover{color:#fff!important}</style>

@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-tag-multiple-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Blog Tags')}}</h3>
                <p class="text-xs text-muted">{{__('Manage blog tags')}}</p>
            </div>
        </div>
        @can('blog-tag-create')
        <button type="button" onclick="document.getElementById('newTagModal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary border border-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
            <i class="mdi mdi-plus text-base"></i>
            {{__('Add New Tag')}}
        </button>
        @endcan
    </div>

    <x-bulk-action permissions="blog-tag-delete"/>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort w-10">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Slug')}}</th>
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right w-28">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($all_tag as $data)
                    <tr class="border-b border-main hover:bg-muted transition-colors">
                        <td class="px-4 sm:px-6 py-4">
                            <x-bulk-delete-checkbox :id="$data->id"/>
                        </td>
                        <td class="px-4 py-4 text-sm text-dark">{{$data->id}}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-dark">{{$data->title}}</td>
                        <td class="px-4 py-4 text-sm text-muted">{{$data->slug}}</td>
                        <td class="px-4 sm:px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                @can('blog-tag-edit')
                                <button type="button"
                                        class="w-8 h-8 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:bg-primary hover:text-white hover:border-primary transition-all tag_edit_btn"
                                        data-id="{{$data->id}}"
                                        data-action="{{route(route_prefix().'admin.blog.tag.update')}}"
                                        data-title="{{$data->title}}"
                                        data-slug="{{$data->slug}}">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </button>
                                @endcan
                                <x-delete-popover permissions="blog-tag-delete" url="{{route(route_prefix().'admin.blog.tag.delete', $data->id)}}"/>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- New Tag Modal --}}
@can('blog-tag-create')
<div id="newTagModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-main">
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('New Blog Tag')}}</h3>
            <button type="button" onclick="this.closest('#newTagModal').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg bg-secondary border border-main flex items-center justify-center text-muted hover:text-danger transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        <form action="{{route(route_prefix().'admin.blog.tag.store')}}" method="post">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                    <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <input type="text" name="title" class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" placeholder="{{__('Tag title')}}">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Slug')}}</label>
                    <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <input type="text" name="slug" class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" placeholder="{{__('tag-slug')}}">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary rounded-b-2xl">
                <button type="button" onclick="this.closest('#newTagModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-semibold text-muted bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- Edit Tag Modal --}}
@can('blog-category-edit')
<div id="editTagModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-main">
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Blog Tag')}}</h3>
            <button type="button" onclick="this.closest('#editTagModal').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg bg-secondary border border-main flex items-center justify-center text-muted hover:text-danger transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        <form action="#" class="category_edit_modal_form" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" class="category_id" value="">
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                    <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <input type="text" name="title" class="edit_title w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Slug')}}</label>
                    <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <input type="text" name="slug" class="edit_slug w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary rounded-b-2xl">
                <button type="button" onclick="this.closest('#editTagModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-semibold text-muted bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

<x-media-upload.tw-markup/>
@endsection

@section('scripts')
    <x-media-upload.tw-js/>
    <x-datatable.tw-js/>
    <x-bulk-action-js :url="route(route_prefix().'admin.blog.tag.bulk.action')"/>
    <script>
        $(document).ready(function($){
            "use strict";

            // ── Delete Confirmation ───────────────────────────────────
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                var btn = $(this);
                Swal.fire({
                    title: '{{ __('Are you sure?') }}',
                    text: '{{ __('You would not be able to revert this item!') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: '{{__("Yes, delete it!")}}',
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.closest('td').find('form:last .swal_form_submit_btn').trigger('click');
                    }
                });
            });

            $(document).on('click', '.tag_edit_btn', function () {
                let el = $(this);
                let form = $('.category_edit_modal_form');
                form.attr('action', el.data('action'));
                form.find('.category_id').val(el.data('id'));
                form.find('.edit_title').val(el.data('title'));
                form.find('.edit_slug').val(el.data('slug'));
                document.getElementById('editTagModal').classList.remove('hidden');
            });
        });
    </script>
@endsection
