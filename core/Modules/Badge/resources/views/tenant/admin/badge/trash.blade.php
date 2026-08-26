@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Trash Badges')}}
@endsection
@section('style')
    <x-datatable.tw-css/>
@endsection
@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-delete-clock-outline text-danger text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Trash Badges')}}</h3>
                <p class="text-xs text-muted">{{__('Deleted badges that can be restored')}}</p>
            </div>
        </div>
        <a href="{{route('tenant.admin.badge.all')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i> {{__('Back')}}
        </a>
    </div>

    @can('badge-delete')
        <div class="px-4 sm:px-6 py-3 border-b border-main flex items-center gap-3">
            <select id="bulk_action_select" class="text-xs bg-secondary border border-main rounded-lg px-3 py-1.5 text-dark focus:border-primary focus:outline-none transition">
                <option value="">{{__('Bulk Action')}}</option>
                <option value="delete">{{__('Permanent Delete')}}</option>
            </select>
            <button type="button" id="bulk_action_apply_btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
                {{__('Apply')}}
            </button>
        </div>
    @endcan

    <div class="tw-table-wrap">
        <table class="w-full text-left" id="all_user_table">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 w-10 no-sort">
                        <input type="checkbox" class="all-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Image')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($badges as $badge)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" name="bulk_delete[]" value="{{$badge->id}}">
                    </td>
                    <td class="px-4 py-3.5"><span class="text-[11px] font-bold text-primary">{{__('#')}} {{$loop->iteration}}</span></td>
                    <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{$badge->name}}</span></td>
                    <td class="px-4 py-3.5">
                        <div class="w-10 h-10 rounded-lg overflow-hidden border border-main bg-secondary flex items-center justify-center">
                            {!! render_image_markup_by_attachment_id($badge->image, 'w-full h-full object-contain') !!}
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        @if($badge->status === 'active')
                            <span class="tw-pill tw-pill-success">{{__('Active')}}</span>
                        @else
                            <span class="tw-pill tw-pill-warning">{{__('Inactive')}}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('badge-edit')
                                <a href="{{route('tenant.admin.badge.trash.restore', $badge->id)}}"
                                   class="w-9 h-9 rounded-lg bg-success-soft border border-main flex items-center justify-center text-success hover:text-white hover:bg-success hover:border-success transition-all"
                                   title="{{__('Restore')}}">
                                    <i class="mdi mdi-backup-restore text-sm"></i>
                                </a>
                            @endcan
                            @can('badge-delete')
                                <button type="button"
                                        class="w-9 h-9 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:text-white hover:bg-danger hover:border-danger transition-all swal_delete_button"
                                        title="{{__('Permanent Delete')}}">
                                    <i class="mdi mdi-delete-forever-outline text-sm"></i>
                                </button>
                                <form method="post" action="{{route('tenant.admin.badge.trash.delete', $badge->id)}}" class="hidden delete-form">
                                    @csrf
                                </form>
                            @endcan
                        </div>
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
    <x-table.btn.swal.js/>
    @can('badge-delete')
        <x-bulk-action.js :route="route('tenant.admin.badge.trash.bulk.action.delete')"/>
    @endcan

    <script>
        (function ($) {
            "use strict";
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                var btn = $(this);
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("This will be permanently deleted!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{ __("Yes, Delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        btn.closest('td').find('.delete-form').trigger('submit');
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
