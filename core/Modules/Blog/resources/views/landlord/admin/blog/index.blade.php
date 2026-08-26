@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Blogs')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

    <x-landlord-flash-msg/>
    <x-landlord-error-msg/>

    {{-- Table Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-6">

        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-post-outline text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Blog Posts')}}</h3>
                    <p class="text-xs text-muted">{{__('Manage your blog content')}}</p>
                </div>
            </div>
            @can('blog-create')
                <a href="{{route(route_prefix().'admin.blog.new')}}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                    <i class="mdi mdi-plus text-base"></i>
                    {{__('Add New')}}
                </a>
            @endcan
        </div>

        <x-bulk-action-v2/>

        {{-- Table --}}
        <div class="tw-table-wrap">
            <table class="w-full text-left" id="all_blog_table">
                <thead>
                <tr class="border-b border-main">
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort w-10">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14 no-sort">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort">{{__('Image')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Category')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>

@endsection

@section('scripts')
    <x-datatable.tw-yajra-js/>
    <x-bulk-action-js :url="route(route_prefix().'admin.blog.bulk.action')" />
    <script>
        (function ($) {
            "use strict";

            // ── Row action dropdown ──────────────────────────────────────────
            window.toggleRowMenu = function (btn) {
                var menu = btn.nextElementSibling;
                var isHidden = menu.classList.contains('hidden');

                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });

                if (isHidden) {
                    var rect       = btn.getBoundingClientRect();
                    var menuHeight = 320;
                    var spaceBelow = window.innerHeight - rect.bottom;
                    var spaceAbove = rect.top;

                    menu.style.right = (window.innerWidth - rect.right) + 'px';
                    menu.style.left  = 'auto';

                    if (spaceBelow >= Math.min(menuHeight, 200) || spaceBelow >= spaceAbove) {
                        menu.style.top    = (rect.bottom + 4) + 'px';
                        menu.style.bottom = 'auto';
                    } else {
                        menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                        menu.style.top    = 'auto';
                    }

                    menu.classList.remove('hidden');
                }
            };

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.row-action-wrap')) {
                    document.querySelectorAll('.row-action-menu').forEach(function (m) {
                        m.classList.add('hidden');
                    });
                }
            });

            window.addEventListener('scroll', function (e) {
                if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            }, true);

            // close on .tw-table-wrap horizontal scroll
            document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
                wrap.addEventListener('scroll', function () {
                    document.querySelectorAll('.row-action-menu').forEach(function (m) {
                        m.classList.add('hidden');
                    });
                });
            });

            $(document).ready(function () {

                $(document).on('change','select[name="lang"]',function (e){
                    $(this).closest('form').trigger('submit');
                    $('input[name="lang"]').val($(this).val());
                });

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
                            btn.closest('.flex, td, li').find('.swal_form_submit_btn').trigger('click');
                        }
                    });
                });

                // ── DataTable init ────────────────────────────────────────
                $('#all_blog_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route(route_prefix().'admin.blog',['lang' => $default_lang]) }}",
                    columns: [
                        {data: 'checkbox', name: '', orderable: false, searchable: false},
                        {data: 'id', name: 'id'},
                        {data: 'title_info', name: '', orderable: false, searchable: false},
                        {data: 'image', name: '', orderable: false, searchable: false},
                        {data: 'category_id', name: ''},
                        {data: 'date', name: ''},
                        {data: 'action', name: '', orderable: false, searchable: false},
                    ],
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    "language": (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            });
        })(jQuery);
    </script>
@endsection
