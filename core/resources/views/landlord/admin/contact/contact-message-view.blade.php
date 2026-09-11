@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Contact Message Details')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    $attachments  = json_decode($message['attachment']);
    $data_content = json_decode($message['fields']);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Main Content --}}
    <div class="lg:col-span-8 space-y-6">

        {{-- Message Data Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-form-textbox text-primary text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Message Details')}}</h3>
                    <p class="text-xs text-muted">{{__('Full content of the submitted form')}}</p>
                </div>
                <a href="{{route(route_prefix().'admin.contact.message.all')}}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                    <i class="mdi mdi-arrow-left text-base"></i> {{__('All Messages')}}
                </a>
            </div>

            <div class="p-4 sm:p-6">
                @if(!empty($data_content) && count((array)$data_content) > 0)
                    <div class="space-y-3">
                        @foreach($data_content ?? [] as $key => $val)
                            <div class="flex items-start gap-3 bg-secondary border border-main rounded-xl px-4 py-3">
                                <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="mdi mdi-text text-primary text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{ucfirst(str_replace('_', ' ', $key))}}</span>
                                    <p class="text-sm text-dark mt-0.5 whitespace-pre-line break-words">{{$val}}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-center gap-3 bg-warning-soft border border-yellow-200 rounded-xl px-4 py-4">
                        <i class="mdi mdi-alert-circle-outline text-warning text-xl"></i>
                        <span class="text-sm text-dark">{{__('No field data available')}}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Attachments Card --}}
        @if(!empty($attachments) && count((array)$attachments) > 0)
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-paperclip text-warning text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Attachments')}}</h3>
                    <p class="text-xs text-muted">{{count((array)$attachments)}} {{__('file(s) attached')}}</p>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                <div class="space-y-2">
                    @foreach($attachments as $key => $val)
                        <a href="{{asset($val)}}" target="_blank" download
                           class="flex items-center gap-3 bg-secondary border border-main rounded-xl px-4 py-3 hover:border-primary hover:bg-primary-soft transition group">
                            <div class="w-8 h-8 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                                <i class="mdi mdi-file-download-outline text-info text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-dark group-hover:text-primary truncate block">{{basename($val)}}</span>
                                <span class="text-[10px] text-muted">{{__('Click to download')}}</span>
                            </div>
                            <i class="mdi mdi-download text-lg text-muted group-hover:text-primary"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-4">
      <div class="sticky top-4 space-y-6">

        {{-- Basic Info --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 py-4 border-b border-main">
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Basic Information')}}</h4>
            </div>
            <div class="p-4 space-y-3">
                {{-- ID --}}
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-identifier text-primary text-sm"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Message ID')}}</span>
                        <span class="text-sm font-bold text-primary">#{{$message->id}}</span>
                    </div>
                </div>

                {{-- Date --}}
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-calendar-outline text-info text-sm"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Received On')}}</span>
                        <span class="text-sm font-semibold text-dark">{{date('d M, Y · h:i A', strtotime($message->created_at))}}</span>
                    </div>
                </div>

                {{-- Status --}}
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg {{ $message->status == 1 ? 'bg-success-soft' : 'bg-[#f3f4f6]' }} flex items-center justify-center flex-shrink-0">
                        <i class="mdi {{ $message->status == 1 ? 'mdi-email-alert-outline text-success' : 'mdi-email-open-outline text-muted' }} text-sm"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Status')}}</span>
                        @if($message->status == 1)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-circle-medium text-xs"></i> {{__('New')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#f3f4f6] text-muted text-[10px] font-bold uppercase">
                                <i class="mdi mdi-circle-medium text-xs"></i> {{__('Read')}}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Form Name --}}
                @if(optional($message->form)->title)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-form-select text-warning text-sm"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Form')}}</span>
                        <span class="text-sm font-semibold text-dark">{{$message->form->title}}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 py-4 border-b border-main">
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Actions')}}</h4>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{route(route_prefix().'admin.contact.message.all')}}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-main hover:border-primary hover:bg-primary-soft text-sm font-medium text-dark hover:text-primary transition group">
                    <i class="mdi mdi-arrow-left text-base text-muted group-hover:text-primary"></i>
                    {{__('Back to All Messages')}}
                </a>
                <form method="post" action="{{route(route_prefix().'admin.contact.message.delete', $message->id)}}" id="deleteForm">
                    @csrf
                    <button type="button" id="deleteBtn"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-main hover:border-red-300 hover:bg-danger-soft text-sm font-medium text-dark hover:text-danger transition group">
                        <i class="mdi mdi-delete-outline text-base text-muted group-hover:text-danger"></i>
                        {{__('Delete Message')}}
                    </button>
                </form>
            </div>
        </div>
      </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
(function ($) {
    "use strict";
    $(document).ready(function () {
        $('#deleteBtn').on('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("This message will be permanently deleted!") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __("Yes, delete it!") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then(function (result) {
                if (result.isConfirmed) {
                    var form = $('#deleteForm');
                    $.ajax({
                        type: 'POST',
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (res) {
                            toastr.success(res.msg || '{{ __("Deleted successfully") }}');
                            setTimeout(function () {
                                window.location.href = '{{ route(route_prefix()."admin.contact.message.all") }}';
                            }, 800);
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
