@extends(route_prefix().'admin.admin-master')

@section('title') {{__('View Message')}} @endsection

@section('style')
    <x-summernote.css/>
    <link rel="stylesheet" href="{{ global_asset('assets/new-landlord/admin/css/components/support-ticket.css') }}">
@endsection

@section('content')

{{-- Top Bar --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <a href="{{route('tenant.admin.refund.chat.all')}}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:opacity-80 transition">
        <i class="mdi mdi-arrow-left text-base"></i> {{__('All Messages')}}
    </a>
    <span class="ticket-status-btn {{$ticket_details->status === 'open' ? 'is-open' : 'is-close'}}">
        <i class="mdi mdi-circle-small text-base -ml-1"></i> {{ucfirst($ticket_details->status)}}
    </span>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-5">

    {{-- ============ LEFT: Conversation ============ --}}
    <div class="xl:col-span-8 flex flex-col gap-5">

        {{-- Conversation Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden flex flex-col">

            {{-- Chat Header --}}
            <div class="px-5 py-3.5 border-b border-main flex items-center justify-between gap-3">
                <h4 class="text-sm font-bold text-dark font-urbanist flex items-center gap-2">
                    <i class="mdi mdi-forum-outline text-primary text-base"></i>
                    {{__('Conversation')}}
                    <span class="text-[10px] font-bold text-muted bg-secondary px-2 py-0.5 rounded-full">{{count($all_ticket_messages)}}</span>
                </h4>
                @if($q == 'all' && count($all_ticket_messages) > 1)
                    <form action="" method="get">
                        <input type="hidden" value="all" name="q">
                        <button type="submit"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-secondary border border-main text-[11px] font-semibold text-dark hover:bg-primary-soft hover:text-primary transition">
                            <i class="mdi mdi-reload text-xs"></i> {{__('Load All')}}
                        </button>
                    </form>
                @endif
            </div>

            {{-- Messages Thread --}}
            @if(count($all_ticket_messages) > 0)
                <div class="ticket-chat flex-1" style="padding: 1.25rem;">
                    @foreach($all_ticket_messages as $msg)
                        <div class="ticket-msg {{$msg->type !== 'customer' ? 'is-admin' : ''}}">
                            <div class="ticket-msg-avatar">
                                {{substr($msg->user_info()->name ?? 'U', 0, 1)}}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="ticket-msg-meta">
                                    <span class="ticket-msg-name">{{$msg->user_info()->name ?? __('Unknown')}}</span>
                                    <span class="ticket-msg-time">{{$msg->created_at->format('d M Y, H:i')}} &middot; {{$msg->created_at->diffForHumans()}}</span>
                                    @if($msg->notify == 'on')
                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-success bg-success-soft px-1.5 py-0.5 rounded">
                                            <i class="mdi mdi-email-check-outline text-[10px]"></i> {{__('Emailed')}}
                                        </span>
                                    @endif
                                </div>
                                <div class="ticket-msg-bubble">{!! $msg->message !!}</div>
                                @if($msg->attachment && file_exists('assets/uploads/refund_chat/'.$msg->attachment))
                                    <a href="{{asset('assets/uploads/refund_chat/'.$msg->attachment)}}"
                                       download class="ticket-msg-attachment">
                                        <i class="mdi mdi-file-download-outline"></i> {{$msg->attachment}}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center py-16 px-5">
                    <div class="w-14 h-14 rounded-2xl bg-secondary flex items-center justify-center mb-4">
                        <i class="mdi mdi-message-off-outline text-muted text-2xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-dark mb-1">{{__('No messages yet')}}</p>
                    <p class="text-xs text-muted">{{__('Start the conversation by sending a reply below')}}</p>
                </div>
            @endif
        </div>

        {{-- Reply Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-5 py-3.5 border-b border-main">
                <h4 class="text-sm font-bold text-dark font-urbanist flex items-center gap-2">
                    <i class="mdi mdi-reply text-success text-base"></i>
                    {{__('Reply')}}
                </h4>
            </div>

            <div class="p-5">
                <x-error-msg-tw/>
                <x-flash-msg-tw/>

                <form action="{{route('tenant.admin.refund.chat.send.message')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{$ticket_details->id}}" name="ticket_id">
                    <input type="hidden" value="admin" name="user_type">

                    {{-- Summernote Editor --}}
                    <textarea name="message" class="form-control" cols="30" rows="5" hidden></textarea>
                    <div class="summernote mb-4"></div>

                    {{-- Bottom Bar: file + notify + send --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-main">
                        <div class="flex items-center gap-4">
                            {{-- File --}}
                            <label class="inline-flex items-center gap-1.5 text-sm font-medium text-muted cursor-pointer hover:text-primary transition group">
                                <i class="mdi mdi-paperclip text-lg group-hover:text-primary transition"></i>
                                <span id="file_label">{{__('Attach .zip')}}</span>
                                <input type="file" name="file" accept=".zip" class="hidden" id="refund_file_input">
                            </label>

                            {{-- Notify --}}
                            <label class="inline-flex items-center gap-1.5 text-sm font-medium text-muted cursor-pointer hover:text-primary transition select-none">
                                <input type="checkbox" name="send_notify_mail"
                                       class="w-3.5 h-3.5 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0">
                                <span>{{__('Email notify')}}</span>
                            </label>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-send text-base"></i> {{__('Send')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ RIGHT: Ticket Details Sidebar ============ --}}
    <div class="xl:col-span-4">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-20">

            {{-- Sidebar Header --}}
            <div class="px-5 py-4 border-b border-main bg-secondary">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-ticket-outline text-primary text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-dark font-urbanist truncate">{{$ticket_details->title}}</h3>
                        <p class="text-[11px] text-muted font-semibold">#{{$ticket_details->id}}</p>
                    </div>
                </div>
            </div>

            {{-- Info Rows --}}
            <div class="divide-y divide-subtle">
                {{-- Subject --}}
                <div class="px-5 py-3.5">
                    <span class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1">{{__('Subject')}}</span>
                    <span class="text-[13px] font-medium text-dark leading-snug">{{$ticket_details->subject}}</span>
                </div>

                {{-- User --}}
                <div class="px-5 py-3.5">
                    <span class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Requester')}}</span>
                    <div class="flex items-center gap-2.5">
                        <div class="tw-avatar-initials" style="width:2rem;height:2rem;font-size:0.65rem;background:#6366f1;">
                            {{substr(optional($ticket_details->user)->name ?? 'A', 0, 1)}}
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[13px] font-semibold text-dark truncate">{{optional($ticket_details->user)->name ?? __('Anonymous')}}</span>
                            @if(optional($ticket_details->user)->email)
                                <span class="block text-[11px] text-muted truncate">{{$ticket_details->user->email}}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Admin --}}
                @if($ticket_details->admin_id)
                <div class="px-5 py-3.5">
                    <span class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Assigned Admin')}}</span>
                    <div class="flex items-center gap-2.5">
                        <div class="tw-avatar-initials" style="width:2rem;height:2rem;font-size:0.65rem;">
                            {{substr(optional($ticket_details->admin)->name ?? 'A', 0, 1)}}
                        </div>
                        <span class="text-[13px] font-semibold text-dark truncate">{{optional($ticket_details->admin)->name ?? __('Anonymous')}}</span>
                    </div>
                </div>
                @endif

                {{-- Status --}}
                <div class="px-5 py-3.5">
                    <span class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Status')}}</span>
                    <span class="ticket-status-btn {{$ticket_details->status === 'open' ? 'is-open' : 'is-close'}}">
                        {{ucfirst($ticket_details->status)}}
                    </span>
                </div>

                {{-- Description --}}
                @if($ticket_details->description)
                <div class="px-5 py-3.5">
                    <span class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1">{{__('Description')}}</span>
                    <p class="text-[13px] text-dark leading-relaxed">{{$ticket_details->description}}</p>
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
    $(document).ready(function () {
        $('.summernote').summernote({
            height: 180,
            codemirror: { theme: 'monokai' },
            callbacks: {
                onChange: function (contents) {
                    $(this).prev('textarea').val(contents);
                }
            }
        });

        // Show selected file name
        $('#refund_file_input').on('change', function () {
            var name = this.files.length ? this.files[0].name : '{{__("Attach .zip")}}';
            $('#file_label').text(name);
        });
    });
    </script>
@endsection
