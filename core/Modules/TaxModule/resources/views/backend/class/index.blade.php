@extends("tenant.admin.admin-master")

@section("title", __("Tax Class"))
@section('style')
    <style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection
@section("content")

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Tax Class List --}}
    <div class="lg:col-span-7">
        <div class="bg-surface rounded-xl shadow-main border border-main mb-6">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-format-list-bulleted text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Manage Tax Class')}}</h3>
                    <p class="text-xs text-muted">{{__('Delete all options first before removing a class, or use force delete')}}</p>
                </div>
            </div>
            <div class="tw-table-wrap">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-main">
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-16">{{__('SL')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__('Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($classes as $class)
                        <tr class="border-b border-main hover:bg-muted transition-colors">
                            <td class="px-4 py-3.5"><span class="text-[11px] font-bold text-primary">{{__('#')}} {{ $loop->iteration }}</span></td>
                            <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{ $class->name }}</span></td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('tenant.admin.tax-module.tax-class-option', $class->id) }}"
                                       class="w-9 h-9 rounded-lg bg-info-soft border border-main flex items-center justify-center text-info hover:text-white hover:bg-info hover:border-info transition-all"
                                       title="{{__('View Options')}}">
                                        <i class="mdi mdi-eye-outline text-sm"></i>
                                    </a>
                                    <button type="button"
                                       class="w-9 h-9 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:text-white hover:bg-primary hover:border-primary transition-all updateTaxClassButton"
                                       data-id="{{ $class->id }}" data-name="{{ $class->name }}"
                                       title="{{__('Edit')}}">
                                        <i class="mdi mdi-pencil-outline text-sm"></i>
                                    </button>
                                    <button type="button"
                                       class="w-9 h-9 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:text-white hover:bg-danger hover:border-danger transition-all deleteTaxClassButton"
                                       data-id="{{ $class->id }}" data-option-count="{{ $class->class_option_count }}" data-href="{{ route("tenant.admin.tax-module.tax-class-delete", $class->id) }}"
                                       title="{{__('Delete')}}">
                                        <i class="mdi mdi-delete-outline text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Tax Class --}}
    <div class="lg:col-span-5">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden lg:sticky lg:top-[80px]">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Create Tax Class')}}</h3>
                    <p class="text-xs text-muted">{{__('Add a new tax class')}}</p>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                <form action="{{ route('tenant.admin.tax-module.tax-class') }}" method="post">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-tag-text-outline text-lg text-primary"></i>
                                <input name="name" type="text" placeholder="{{ __('Write tax class name') }}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Create')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Update Modal --}}
<div id="updateTaxClassModal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
    <div id="updateTaxClassBackdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl border border-main w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Update Tax Class')}}</h3>
            <button type="button" class="modal_close w-8 h-8 rounded-lg bg-secondary border border-main flex items-center justify-center text-muted hover:text-danger hover:border-danger transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        <form action="{{ route('tenant.admin.tax-module.tax-class') }}" method="post">
            @csrf
            @method("PUT")
            <input type="hidden" name="id" id="tax-class-id">
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-tag-text-outline text-lg text-primary"></i>
                        <input id="update-tax-class-name" name="name" type="text" placeholder="{{ __('Write tax class name') }}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-main flex items-center justify-end gap-3">
                <button type="button" class="modal_close inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-6 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section("scripts")
    <script>
        (function ($) {
            "use strict";

            function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
            function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }

            $(document).on('click', '.modal_close, #updateTaxClassBackdrop', function () { closeModal('updateTaxClassModal'); });

            $(document).on("click", ".updateTaxClassButton", function () {
                $("#tax-class-id").val($(this).data("id"));
                $("#update-tax-class-name").val($(this).data("name"));
                openModal('updateTaxClassModal');
            });

            $(document).on("click", ".deleteTaxClassButton", function () {
                var el = $(this);
                var countOption = el.data("option-count");
                var formData = new FormData();
                formData.append("_method", "DELETE");
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("id", el.data("id"));

                var warningText = countOption > 0
                    ? '{{ __("If you delete this tax class then all tax class options will be deleted and you won\'t be able to revert those!") }}'
                    : '{!! __("You won\'t be able to revert this!") !!}';

                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: warningText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{ __("Yes, delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: el.data("href"),
                            type: countOption > 0 ? "GET" : "POST",
                            processData: false,
                            contentType: false,
                            data: formData,
                            success: function () {
                                toastr.success('{{ __("Deleted successfully") }}');
                                el.closest('tr').remove();
                            },
                            error: function (data) {
                                var errors = data.responseJSON;
                                if (errors && errors.errors) {
                                    $.each(errors.errors, function (i, v) { toastr.error(v[0]); });
                                }
                                if (errors && errors.custom_msg) { toastr.error(errors.custom_msg); }
                            }
                        });
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
