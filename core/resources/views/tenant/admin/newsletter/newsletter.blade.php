@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Newsletter')}} @endsection

@section('style')
    <x-summernote.css/>
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
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
                <i class="mdi mdi-email-multiple-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Newsletter Subscriber')}}</h3>
                <p class="text-xs text-muted">{{__('Manage your newsletter subscribers')}}</p>
            </div>
        </div>
        @can('newsletter-create')
        <button type="button" onclick="openModal('new_subscribe_modal')"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
            <i class="mdi mdi-plus text-base"></i>
            {{__('Add New Subscriber')}}
        </button>
        @endcan
    </div>

    {{-- Bulk Action --}}
    @can('newsletter-delete')
    <div class="px-4 sm:px-6 py-3 border-b border-main flex items-center gap-3">
        <select name="bulk_option" id="bulk_option"
                class="text-xs bg-secondary border border-main rounded-lg px-3 py-1.5 text-dark focus:border-primary focus:outline-none transition">
            <option value="">{{__('Bulk Action')}}</option>
            <option value="delete">{{__('Delete')}}</option>
        </select>
        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition"
                id="bulk_delete_btn">
            {{__('Apply')}}
        </button>
    </div>
    @endcan

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="newsletterTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <input type="checkbox" class="all-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Email')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_newsletter as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- Checkbox --}}
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-primary focus:ring-primary" name="bulk_delete[]" value="{{$data->id}}">
                    </td>

                    {{-- ID --}}
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                    </td>

                    {{-- Email --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-dark">{{$data->email}}</span>
                            @if($data->verified > 0)
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-success-soft text-success text-[9px] font-bold uppercase">
                                    <i class="mdi mdi-check-circle text-[9px]"></i> {{__('Verified')}}
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('newsletter-edit')
                            <button type="button"
                                    class="send_mail_modal_btn w-9 h-9 rounded-lg bg-info-soft border border-main flex items-center justify-center hover:text-white hover:bg-info hover:border-info transition-all"
                                    data-email="{{$data->email}}"
                                    title="{{__('Send Mail')}}">
                                <i class="mdi mdi-email-outline text-sm"></i>
                            </button>
                            @endcan

                            @if($data->verified < 1)
                            <button type="button"
                                    class="verify_mail_btn w-9 h-9 rounded-lg bg-warning-soft border border-main flex items-center justify-center text-warning hover:text-white hover:bg-warning hover:border-warning transition-all"
                                    data-id="{{$data->id}}"
                                    data-email="{{$data->email}}"
                                    title="{{__('Send Verify Mail')}}">
                                <i class="mdi mdi-shield-check-outline text-sm"></i>
                            </button>
                            @endif

                            @can('newsletter-delete')
                            <button type="button"
                                    class="swal_delete_button w-9 h-9 rounded-lg bg-danger-soft border border-main flex items-center justify-center hover:text-white hover:bg-danger hover:border-danger transition-all"
                                    title="{{__('Delete')}}">
                                <i class="mdi mdi-delete-outline text-sm"></i>
                            </button>
                            <form method="post" action="{{route(route_prefix().'admin.newsletter.delete',$data->id)}}" class="hidden d-none">
                                @csrf
                                <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
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

{{-- Add New Subscriber Modal --}}
<div id="new_subscribe_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('new_subscribe_modal')"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-account-plus-outline text-primary text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Add New Subscriber')}}</h5>
                <p class="text-[11px] text-muted">{{__('Manually add an email to the list')}}</p>
            </div>
            <button type="button" onclick="closeModal('new_subscribe_modal')"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-primary-soft hover:text-primary transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        {{-- Body --}}
        <form action="{{route(route_prefix().'admin.newsletter.new.add')}}" method="post">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Email')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-email-outline text-lg text-primary"></i>
                        <input type="email" name="email" placeholder="{{__('Enter email address')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" onclick="closeModal('new_subscribe_modal')"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Submit')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Send Mail Modal --}}
@can('newsletter-edit')
<div id="send_mail_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('send_mail_modal')"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-2xl overflow-hidden border border-main">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-email-fast-outline text-info text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Send Mail To Subscriber')}}</h5>
                <p class="text-[11px] text-muted">{{__('Compose and send an email')}}</p>
            </div>
            <button type="button" onclick="closeModal('send_mail_modal')"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-info-soft hover:text-info transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        {{-- Body --}}
        <form action="{{route(route_prefix().'admin.newsletter.single.mail')}}" id="send_mail_form" method="post">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <input type="hidden" id="mail_email" name="email">

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subject')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-text-short text-lg text-primary"></i>
                        <input type="text" name="subject" placeholder="{{__('Enter email subject')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Message')}}</label>
                    <input type="hidden" name="message">
                    <div class="summernote"></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" onclick="closeModal('send_mail_modal')"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-send text-base"></i>
                    {{__('Send Mail')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@endsection

@section('scripts')
    <x-summernote.js/>
    <x-datatable.tw-js/>

    <x-bulk-action-js :url="route(route_prefix().'admin.newsletter.bulk.action')" />

    <script>
    (function ($) {
        "use strict";

        // ── Modal helpers ────────────────────────────────────────────
        window.openModal = function (id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };
        window.closeModal = function (id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        $(document).ready(function () {

            // ── DataTable init ────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#newsletterTable')) {
                $('#newsletterTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── Send Mail Modal ───────────────────────────────────────
            $(document).on('click', '.send_mail_modal_btn', function () {
                var email = $(this).data('email');
                $('#send_mail_form #mail_email').val(email);
                openModal('send_mail_modal');
            });

            // ── Verify Mail (AJAX) ───────────────────────────────────
            $(document).on('click', '.verify_mail_btn', function (e) {
                e.preventDefault();
                var btn = $(this);
                $.ajax({
                    url: '{{ route(route_prefix()."admin.newsletter.verify.mail.send") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: btn.data('id'),
                        email: btn.data('email')
                    },
                    success: function (res) {
                        toastr.success('{{ __("Verification mail sent successfully") }}');
                        btn.closest('td').find('.verify_mail_btn').remove();
                    },
                    error: function () {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                });
            });

            // ── Delete Confirmation ───────────────────────────────────
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                var btn = $(this);
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You would not be able to revert this item!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: '{{ __("Yes, delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}',
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.next('form').find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            // ── Summernote init ───────────────────────────────────────
            $('.summernote').summernote({
                height: 300,
                codemirror: {
                    theme: 'monokai'
                },
                callbacks: {
                    onChange: function (contents, $editable) {
                        $(this).prev('input').val(contents);
                    }
                }
            });

            // ── Close modals on Escape ────────────────────────────────
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeModal('new_subscribe_modal');
                    closeModal('send_mail_modal');
                }
            });
        });
    })(jQuery);
    </script>
@endsection
