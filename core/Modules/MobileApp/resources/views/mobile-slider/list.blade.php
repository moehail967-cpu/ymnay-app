@extends('tenant.admin.admin-master')
@section('title') {{__('Mobile Sliders')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-image-multiple-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Mobile Sliders')}}</h3>
                <p class="text-xs text-muted">{{__('Manage mobile app sliders')}}</p>
            </div>
        </div>
        <a href="{{ route('tenant.admin.mobile.slider.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
            <i class="mdi mdi-plus text-base"></i>
            {{__('Add New')}}
        </a>
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left" id="sliderTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('#')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Description')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-16">{{__('Image')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Button')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($mobileSliders as $slider)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{ $loop->iteration }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{ $slider->title }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <p class="text-xs text-muted line-clamp-2 max-w-xs">{{ $slider->description }}</p>
                    </td>
                    <td class="px-4 py-3.5">
                        @if($slider->image_id)
                            @php
                                $imgData = get_attachment_image_by_id($slider->image_id);
                                $imgUrl = !empty($imgData['img_url']) ? $imgData['img_url'] : '';
                            @endphp
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" alt="" class="w-12 h-12 rounded-lg object-cover border border-main" loading="lazy">
                            @endif
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-xs text-dark block">{{ $slider->button_text }}</span>
                        @if($slider->url)
                            <a href="{{ $slider->url }}" target="_blank" class="text-[11px] text-primary hover:underline truncate block max-w-[120px]">{{ $slider->url }}</a>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('state-edit')
                            <a href="{{ route('tenant.admin.mobile.slider.edit', $slider->id) }}"
                               class="w-9 h-9 rounded-lg bg-info-soft border border-main flex items-center justify-center text-info hover:text-white hover:bg-info hover:border-info transition-all"
                               title="{{__('Edit')}}">
                                <i class="mdi mdi-pencil-outline text-sm"></i>
                            </a>
                            @endcan
                            @can('state-delete')
                            <button type="button"
                                    class="swal_delete_button w-9 h-9 rounded-lg bg-danger-soft border border-main flex items-center justify-center hover:text-white hover:bg-danger hover:border-danger transition-all"
                                    data-url="{{ route('tenant.admin.mobile.slider.delete', $slider->id) }}"
                                    title="{{__('Delete')}}">
                                <i class="mdi mdi-delete-outline text-sm"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-muted">{{__('No Data Available')}}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>

    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#sliderTable')) {
                $('#sliderTable').DataTable({
                    "order": [[0, "asc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                var btn = $(this);
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You would not be able to revert this!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: '{{ __("Yes, delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: btn.data('url'),
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (res) {
                                if (res.success) {
                                    btn.closest('tr').fadeOut(300, function(){ $(this).remove(); });
                                    toastr.success('{{ __("Deleted successfully") }}');
                                }
                            },
                            error: function () {
                                toastr.error('{{ __("Something went wrong") }}');
                            }
                        });
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
