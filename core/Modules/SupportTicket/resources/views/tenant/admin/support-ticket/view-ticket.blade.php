@extends(route_prefix().'admin.admin-master')
@section('title') {{__('View Ticket')}} @endsection

@section('style')
    <x-summernote.css/>
    <link rel="stylesheet" href="{{ global_asset('assets/new-landlord/admin/css/components/support-ticket.css') }}">
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Main Content --}}
    <div class="lg:col-span-8 space-y-6">

        {{-- Conversation Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-message-text-outline text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Conversation')}}</h3>
                    <p class="text-xs text-muted">{{$ticket_details->title}}</p>
                </div>
                <div class="ml-auto">
                    <a href="{{route(route_prefix().'admin.support.ticket.all')}}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                        <i class="mdi mdi-arrow-left text-base"></i> {{__('All Tickets')}}
                    </a>
                </div>
            </div>

            {{-- Messages --}}
            <div class="ticket-chat @if($q == 'all') flex-col-reverse @endif" id="ticketChat">
                @if($q == 'all' && count($all_ticket_messages) > 1)
                    <div class="text-center py-2">
                        <form action="" method="get">
                            <input type="hidden" value="all" name="q">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
                                <i class="mdi mdi-reload text-sm"></i> {{__('Load All Messages')}}
                            </button>
                        </form>
                    </div>
                @endif

                @forelse($all_ticket_messages as $msg)
                    <div class="ticket-msg @if($msg->type != 'customer') is-admin @endif">
                        <div class="ticket-msg-avatar">
                            {{substr($msg->user_info()->name ?? 'U', 0, 1)}}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="ticket-msg-meta">
                                <span class="ticket-msg-name">{{$msg->user_info()->name ?? __('Unknown')}}</span>
                                <span class="ticket-msg-time">{{$msg->created_at->diffForHumans()}}</span>
                                @if($msg->notify == 'on')
                                    <i class="mdi mdi-email-check-outline text-xs text-primary" title="{{__('Notified by email')}}"></i>
                                @endif
                            </div>
                            <div class="ticket-msg-bubble">
                                {!! $msg->message !!}
                                @if($msg->attachment && file_exists('assets/uploads/ticket/'.$msg->attachment))
                                    <a href="{{asset('assets/uploads/ticket/'.$msg->attachment)}}" download class="ticket-msg-attachment">
                                        <i class="mdi mdi-paperclip"></i> {{$msg->attachment}}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center py-12 text-muted">
                        <div class="text-center">
                            <i class="mdi mdi-message-off-outline text-3xl opacity-40"></i>
                            <p class="text-sm mt-2">{{__('No messages yet')}}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Reply Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-reply text-info text-sm"></i>
                </div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Reply')}}</h4>
            </div>

            <form action="{{route(route_prefix().'admin.support.ticket.send.message')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{$ticket_details->id}}" name="ticket_id">
                <input type="hidden" value="admin" name="user_type">

                <div class="p-4 sm:p-6 space-y-4">
                    {{-- Summernote Editor --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Message')}}</label>
                        <textarea name="message" class="form-control" cols="30" rows="5" style="display:none;"></textarea>
                        <div class="summernote"></div>
                    </div>

                    {{-- File Upload --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Attachment')}}</label>
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-dashed border-main bg-secondary text-sm text-muted hover:border-primary hover:text-primary transition cursor-pointer">
                                <i class="mdi mdi-paperclip text-base"></i>
                                <span>{{__('Choose File')}}</span>
                                <input type="file" name="file" accept=".zip" class="hidden" onchange="this.closest('label').querySelector('span').textContent = this.files[0]?.name || '{{__('Choose File')}}'">
                            </label>
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('Max 200MB, only .zip allowed')}}</p>
                    </div>

                    {{-- Notify Checkbox --}}
                    <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" name="send_notify_mail" id="send_notify_mail"
                               class="rounded border-gray-300 text-primary focus:ring-primary w-4 h-4">
                        <span class="text-sm text-dark font-medium">{{__('Notify Via Mail')}}</span>
                    </label>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end px-4 sm:px-6 py-4 border-t border-main bg-secondary rounded-b-xl">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-send text-base"></i> {{__('Send Reply')}}
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-4 space-y-6">

        {{-- Ticket Info --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
            <div class="px-4 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-ticket-outline text-primary text-sm"></i>
                </div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Ticket Info')}}</h4>
            </div>

            <div class="p-4 space-y-0">
                {{-- Status + Priority Badges --}}
                <div class="flex items-center gap-2 pb-3 mb-3 border-b border-main">
                    @if($ticket_details->status == 'open')
                        <span class="inline-flex items-center gap-0.5 px-2.5 py-1 rounded-full bg-success-soft text-success text-[10px] font-bold uppercase">
                            <i class="mdi mdi-circle-medium text-xs"></i> {{__('Open')}}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-0.5 px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-[10px] font-bold uppercase">
                            <i class="mdi mdi-circle-medium text-xs"></i> {{__('Closed')}}
                        </span>
                    @endif

                    @php
                        $priorityColors = [
                            'low' => 'bg-[#f0fdfa] text-[#0d9488]',
                            'medium' => 'bg-[#eff6ff] text-[#2563eb]',
                            'high' => 'bg-[#fff7ed] text-[#ea580c]',
                            'urgent' => 'bg-[#fef2f2] text-[#dc2626]',
                        ];
                        $pClass = $priorityColors[$ticket_details->priority] ?? 'bg-gray-100 text-gray-500';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full {{ $pClass }} text-[10px] font-bold uppercase">
                        {{__($ticket_details->priority)}}
                    </span>
                </div>

                {{-- Details --}}
                <div class="space-y-0">
                    <div class="flex items-center justify-between py-2.5 border-b border-main">
                        <span class="text-[11px] text-muted font-medium">{{__('Ticket ID')}}</span>
                        <span class="text-xs font-bold text-primary">#{{$ticket_details->id}}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-main">
                        <span class="text-[11px] text-muted font-medium">{{__('Subject')}}</span>
                        <span class="text-xs font-semibold text-dark text-right max-w-[60%] truncate">{{$ticket_details->subject}}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-main">
                        <span class="text-[11px] text-muted font-medium">{{__('Department')}}</span>
                        <span class="text-xs font-semibold text-dark">{{optional($ticket_details->department)->name ?? __('N/A')}}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-main">
                        <span class="text-[11px] text-muted font-medium">{{__('User')}}</span>
                        <span class="text-xs font-semibold text-dark">{{optional($ticket_details->user)->name ?? __('Anonymous')}}</span>
                    </div>
                    @if($ticket_details->admin_id)
                    <div class="flex items-center justify-between py-2.5 border-b border-main">
                        <span class="text-[11px] text-muted font-medium">{{__('Admin')}}</span>
                        <span class="text-xs font-semibold text-dark">{{optional($ticket_details->admin)->name ?? __('N/A')}}</span>
                    </div>
                    @endif
                </div>

                {{-- Description --}}
                @if($ticket_details->description)
                <div class="pt-3">
                    <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}}</span>
                    <p class="text-xs text-dark leading-relaxed">{{$ticket_details->description}}</p>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>

<x-media-upload.tw-markup/>
@endsection

@section('scripts')
    <x-media-upload.tw-js/>
    <x-summernote.js/>
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            $('.summernote').summernote({
                height: 200,
                codemirror: { theme: 'monokai' },
                callbacks: {
                    onChange: function (contents) {
                        $(this).prev('textarea').val(contents);
                    }
                }
            });
        });
    })(jQuery);
    </script>
@endsection
