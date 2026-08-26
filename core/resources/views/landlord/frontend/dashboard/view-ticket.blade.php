@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{__('Ticket View')}}
@endsection

@section('title')
    {{__('Ticket View')}}
@endsection

@section('style')
    <x-summernote.css/>
@endsection

@section('section')
<!-- Main Content -->
    <div class="col-span-full lg:col-span-9 px-4 lg:px-0 ">
    <!-- Top Header -->
    <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-y rounded-tr-3xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between pt-3 pb-4 gap-4 ">
            <div class="flex items-center ">
                <!-- Mobile Menu Button -->
                <button id="menuBtn"
                        class="block ml-5 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                    <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                </button>

                <div class="ml-4 lg:ml-16">
                    <h1 class="text-lg font-medium text-secondary">{{__('Ticket View')}}
                    </h1>
                    <p class=" text-xs lg:text-sm text-sub2Title mt-1">{{__('View and manage your support ticket conversation')}}</p>
                </div>
            </div>
            <div class="flex justify-end pb-2">
                <a href="{{route(route_prefix().'user.home.support.tickets')}}"
                    class=" rounded-xl flex items-center justify-center px-6 py-3 gap-2 text-base font-normal text-white transition-opacity hover:opacity-90 bg-primary w-full lg:w-auto">
                    <i class="icon-base ti tabler-arrow-left"></i>
                    {{__('All Tickets')}}
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="p-4 lg:pl-16 lg:pr-9">

        <div class="flex flex-col gap-4">

            <!-- Ticket Info Card -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6">

                <!-- Left: Ticket Details -->
                <div class="lg:col-span-4 rounded-xl flex-1 px-6 py-5 border border-borderCS" style="background-color: var(--section-bg-1, #ffffff)">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-secondary">{{__('Ticket ID:')}}</span>
                            <span class="text-sm font-normal text-[#414E62]">#{{$ticket_details->id}}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-secondary">{{__('Title:')}}</span>
                            <span class="text-sm font-normal text-[#414E62] text-right">{{$ticket_details->title}}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-secondary">{{__('Subject:')}}</span>
                            <span class="text-sm font-normal text-[#414E62] text-right">{{$ticket_details->subject}}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-secondary">{{__('Status:')}}</span>
                            @php
                                $statusClasses = [
                                    'open' => 'bg-[#F0FDF4] border-green-400 text-[#008236]',
                                    'close' => 'bg-[#FEF2F2] border-red-300 text-[#C10007]',
                                ];
                                $sClass = $statusClasses[$ticket_details->status] ?? 'bg-[#FFFBEB] border-yellow-300 text-[#92400E]';
                            @endphp
                            <span class="inline-block border {{ $sClass }} text-xs font-medium px-5 py-1.5 rounded-full">{{__($ticket_details->status)}}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-secondary">{{__('Priority:')}}</span>
                            @php
                                $priorityClasses = [
                                    'low' => 'bg-[#F0FDF4] border-green-400 text-[#008236]',
                                    'medium' => 'bg-[#F0FDF4] border-green-400 text-[#008236]',
                                    'high' => 'bg-[#FEF2F2] border-red-300 text-[#C10007]',
                                    'urgent' => 'bg-[#FFFBEB] border-yellow-300 text-[#92400E]',
                                ];
                                $pClass = $priorityClasses[$ticket_details->priority] ?? 'bg-gray-50 border-gray-300 text-gray-500';
                            @endphp
                            <span class="inline-block border {{ $pClass }} text-xs font-medium px-5 py-1.5 rounded-full">{{__($ticket_details->priority)}}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Description -->
                <div class="lg:col-span-2 rounded-xl border border-borderCS flex-1 px-6 py-5" style="background-color: var(--section-bg-1, #ffffff)">
                    <p class="text-sm font-medium text-secondary mb-2">{{__('Description:')}}</p>
                    <p class="text-sm text-[#636E7E] leading-relaxed">
                        {{$ticket_details->description}}
                    </p>
                </div>

            </div>

            <!-- Chat Card -->
            <div class="rounded-2xl border border-borderCS shadow-sm overflow-hidden flex flex-col min-h-[520px]" style="background-color: var(--section-bg-1, #ffffff)">

                <!-- Chat Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-borderCS">
                    <div class="flex items-center gap-3">
                        <h3 class="text-base font-medium text-secondary">{{__('All Conversation')}}</h3>
                    </div>
                    @if($q != 'all' && count($all_messages) > 1)
                        <a href="?q=all"
                            class="flex items-center gap-2 text-sm font-medium text-sectionC hover:underline transition">
                            <i class="ti tabler-refresh icon-18px"></i>
                            {{__('Load All Messages')}}
                        </a>
                    @endif
                </div>

                <!-- Messages -->
                <div id="chatMessages" class="flex-1 overflow-y-auto px-5 py-5 flex flex-col gap-5 @if($q == 'all') flex-col-reverse @endif">

                    @forelse($all_messages as $msg)
                        @if($msg->type == 'customer')
                            <!-- Customer Message (Right side) -->
                            <div class="flex flex-col items-end gap-1">
                                <div class="bg-teal-50 rounded-2xl rounded-br-sm px-4 py-3 max-w-lg text-right">
                                    <p class="text-xs font-semibold text-sectionC mb-1">{{$msg->user_info()->name ?? __('Unknown')}}</p>
                                    <div class="text-sm text-gray-700">{!! $msg->message !!}</div>
                                    @if(file_exists('assets/uploads/ticket/'.$msg->attachment))
                                        <a href="{{asset('assets/uploads/ticket/'.$msg->attachment)}}"
                                           download class="text-xs text-sectionC underline mt-2 inline-block">
                                            <i class="ti tabler-paperclip text-xs"></i> {{$msg->attachment}}
                                        </a>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mr-1">
                                    <p class="text-xs text-gray-400 mt-0.5">{{date_format($msg->created_at,'d M Y H:i:s')}}</p>
                                    <span class="text-xs text-gray-400">|</span>
                                    <p class="text-xs text-gray-400">{{$msg->created_at->diffForHumans()}}</p>
                                    @if($msg->notify == 'on')
                                        <i class="ti tabler-mail text-xs text-gray-400" title="{{__('Notified by email')}}"></i>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- Admin Message (Left side) -->
                            <div class="flex items-end gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mb-5">
                                    <span class="text-sm font-semibold text-sectionC">{{substr($msg->user_info()->name ?? __('Unknown'),0,1)}}</span>
                                </div>
                                <div>
                                    <div class="bg-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 max-w-sm">
                                        <p class="text-xs font-semibold text-secondary mb-1">{{$msg->user_info()->name ?? __('Unknown')}}</p>
                                        <div class="text-sm text-gray-700">{!! $msg->message !!}</div>
                                        @if(file_exists('assets/uploads/ticket/'.$msg->attachment))
                                            <a href="{{asset('assets/uploads/ticket/'.$msg->attachment)}}"
                                               download class="text-xs text-sectionC underline mt-2 inline-block">
                                                <i class="ti tabler-paperclip text-xs"></i> {{$msg->attachment}}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 ml-1">
                                        <p class="text-xs text-gray-400 mt-1.5">{{date_format($msg->created_at,'d M Y H:i:s')}}</p>
                                        <span class="text-xs text-gray-400">|</span>
                                        <p class="text-xs text-gray-400 mt-1.5">{{$msg->created_at->diffForHumans()}}</p>
                                        @if($msg->notify == 'on')
                                            <i class="ti tabler-mail text-xs text-gray-400 mt-1.5" title="{{__('Notified by email')}}"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="flex items-center justify-center h-full">
                            <p class="text-sm text-sub2Title">{{__('No messages found')}}</p>
                        </div>
                    @endforelse

                </div>

                <!-- Reply Form / Closed Notice -->
                @if($ticket_details->status != 'close')
                    <div class="border-t border-borderCS px-5 py-5">
                        <h5 class="text-base font-medium text-secondary mb-4">{{__('Reply To Message')}}</h5>
                        <x-error-msg-tw/>
                        <x-flash-msg-tw/>
                        <form action="{{route('landlord.user.dashboard.support.ticket.message')}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{$ticket_details->id}}" name="ticket_id">
                            <input type="hidden" value="customer" name="user_type">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-secondary mb-2">{{__('Message')}}</label>
                                <textarea name="message" class="form-control hidden" cols="30" rows="5"></textarea>
                                <div class="summernote"></div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-secondary mb-2">{{__('File')}}</label>
                                <input type="file" name="file" accept=".zip"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-borderCS file:text-sm file:font-medium file:bg-white file:text-secondary hover:file:bg-gray-50"/>
                                <small class="text-xs text-red-500 mt-1 block">{{__('max file size 200mb, only zip file is allowed')}}</small>
                            </div>
                            <div class="flex items-center gap-2 mb-4">
                                <input type="checkbox" name="send_notify_mail" id="send_notify_mail"
                                       class="w-4 h-4 rounded border-gray-300 text-sectionC focus:ring-sectionC">
                                <label for="send_notify_mail" class="text-sm text-secondary">{{__('Notify Via Mail')}}</label>
                            </div>
                            <div class="flex justify-end">
                                <button class="px-6 py-3 rounded-xl flex items-center gap-2 text-base font-normal text-white transition-opacity hover:opacity-90 bg-primary"
                                        type="submit">
                                    <i class="ti tabler-send text-base"></i>
                                    {{__('Send Message')}}
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="border-t border-borderCS px-5 py-5">
                        <p class="text-center text-sm font-medium text-sub2Title">{{__('The ticket is closed')}}</p>
                    </div>
                @endif

            </div>

        </div>

    </main>
</div>
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('assets/new-landlord/js/active_page.js') }}"></script>

    <x-summernote.js/>
    <script>
        $(document).ready(function () {
            $('.summernote').summernote({
                height: 200,
                codemirror: {
                    theme: 'monokai'
                },
                callbacks: {
                    onChange: function (contents, $editable) {
                        $(this).prev('textarea').val(contents);
                    }
                },
                toolbar: [
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                ],
            });

            // Auto scroll to bottom of chat
            var chatMessages = document.getElementById('chatMessages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
    </script>
@endsection
