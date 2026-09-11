@extends(route_prefix().'admin.admin-master')

@section('title')
    {{__('All Custom Form')}}
@endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-file-alt text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Custom Form')}}</h3>
                <p class="text-xs text-muted">{{__('Manage your custom forms')}}</p>
            </div>
        </div>
        <button type="button" onclick="openModal('createForm')"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
            <i class="las la-plus text-base"></i> {{__('New Form')}}
        </button>
    </div>

    {{-- Bulk Action --}}
    <x-bulk-action-v2 permissions="custom-form-builder-delete"/>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="zero_config">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort w-10">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_forms as $data)
                <tr class="border-b border-subtle hover:bg-muted transition">
                    <td class="px-4 sm:px-6 py-3">
                        <div class="bulk-checkbox-wrapper">
                            <input type="checkbox" class="bulk-checkbox" name="bulk_delete[]" value="{{$data->id}}">
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-dark">{{$data->id}}</td>
                    <td class="px-4 py-3 text-sm text-dark">{{$data->title}}</td>
                    <td class="px-4 sm:px-6 py-3 text-right">
                        <div class="inline-flex items-center gap-1">
                            <a href="{{route(route_prefix().'admin.form.builder.edit', $data->id)}}"
                               class="tw-btn-icon tw-btn-icon-edit" title="{{__('Edit')}}">
                                <i class="las la-edit text-sm"></i>
                            </a>
                            <x-delete-popover permissions="custom-form-builder-delete" url="{{route(route_prefix().'admin.form.builder.delete', $data->id)}}"/>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Create New Form Modal --}}
<div class="cm-modal-backdrop" id="createFormBackdrop"></div>
<div class="cm-modal" id="createFormModal">
    <div class="cm-modal-dialog">
        <div class="cm-modal-header">
            <h5 class="cm-modal-title">
                <i class="las la-plus-circle text-primary"></i> {{__('Add New Form')}}
            </h5>
            <button type="button" class="cm-modal-close" onclick="closeModal('createForm')">
                <i class="las la-times"></i>
            </button>
        </div>
        <form action="{{route(route_prefix().'admin.form.builder.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="cm-modal-body">
                <div class="mb-4">
                    <label class="lnd-label">{{__('Title')}}</label>
                    <input type="text" class="lnd-input" name="title" placeholder="{{__('Enter Title')}}">
                </div>
                <div class="mb-4">
                    <label class="lnd-label">{{__('Receiving Email')}}</label>
                    <input type="email" class="lnd-input" name="email" placeholder="{{__('Email')}}">
                    <p class="text-xs text-muted mt-1">{{__('You will get mail with all info of form to this email')}}</p>
                </div>
                <div class="mb-4">
                    <label class="lnd-label">{{__('Button Title')}}</label>
                    <input type="text" class="lnd-input" name="button_title" placeholder="{{__('Enter Button Title')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Success Message')}}</label>
                    <input type="text" class="lnd-input" name="success_message" placeholder="{{__('Form submit success message')}}">
                </div>
            </div>
            <div class="cm-modal-footer">
                <button type="button" onclick="closeModal('createForm')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-muted border border-main hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                    {{__('Submit')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-bulk-action-js :url="route('landlord.admin.form.builder.bulk.action')"/>
    <script>
        (function($){
            "use strict";

            window.openModal = function(name) {
                document.getElementById(name + 'Backdrop').classList.add('active');
                document.getElementById(name + 'Modal').classList.add('active');
            };
            window.closeModal = function(name) {
                document.getElementById(name + 'Backdrop').classList.remove('active');
                document.getElementById(name + 'Modal').classList.remove('active');
            };

            document.querySelectorAll('.cm-modal-backdrop').forEach(function(backdrop) {
                backdrop.addEventListener('click', function() {
                    closeModal(this.id.replace('Backdrop', ''));
                });
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.cm-modal.active').forEach(function(modal) {
                        closeModal(modal.id.replace('Modal', ''));
                    });
                }
            });
        })(jQuery);
    </script>
@endsection
