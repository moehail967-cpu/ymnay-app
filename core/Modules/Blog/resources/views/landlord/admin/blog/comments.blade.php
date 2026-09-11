@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Blog Comments')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-comment-text-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Blog Comments')}}</h3>
                <p class="text-xs text-muted">{{count($blog_comments?->comments) ?? 0}} {{__('total')}}</p>
            </div>
        </div>
        <a href="{{route(route_prefix().'admin.blog')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-soft border border-main text-primary text-sm font-semibold hover:bg-primary hover:text-white hover:border-primary transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{__('Go Back')}}
        </a>
    </div>

    <x-bulk-action/>

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
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Commented By')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Comments')}}</th>
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right w-28">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($blog_comments->comments ?? [] as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 sm:px-6 py-4">
                        <div class="bulk-checkbox-wrapper">
                            <input type="checkbox" class="bulk-checkbox" name="bulk_delete[]" value="{{$data->id}}">
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-dark">{{$data->id}}</td>
                    <td class="px-4 py-4 text-sm font-semibold text-dark">{{$data->commented_by}}</td>
                    @php
                        $url = route(route_prefix().'frontend.blog.single', $data->blog?->slug).'#comment-area';
                    @endphp
                    <td class="px-4 py-4">
                        <a href="{{$url}}" target="_blank" class="text-sm text-primary hover:underline">{{$data->comment_content}}</a>
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-right">
                        <x-delete-popover url="{{route(route_prefix().'admin.blog.comments.delete.all.lang', $data->id)}}"/>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-bulk-action-js :url="route(route_prefix().'admin.blog.comments.bulk.action')" />
    <script>
        $(document).ready(function(){
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
        });
    </script>
@endsection
