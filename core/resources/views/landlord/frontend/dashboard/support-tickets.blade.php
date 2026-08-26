@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{__('Support Tickets')}}
@endsection

@section('title')
    {{__('Support Tickets')}}
@endsection

@section('style')
@endsection

@section('section')
    <div class="col-span-full lg:col-span-9">
    <!-- Top Header -->
    <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-b rounded-t-3xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between px-6 py-3 gap-4">
            <div class="flex items-center">
                <!-- Mobile Menu Button -->
                <button id="menuBtn"
                        class="block mr-3 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                    <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                </button>

                <div class="">
                    <h1 class="text-lg font-medium text-secondary">{{__('Support Tickets')}}
                    </h1>
                    <p class=" text-xs lg:text-sm text-sub2Title mt-1">{{__('Manage your support tickets and track their status')}}</p>
                </div>
            </div>
            <div class=" pb-2">
                <a href="{{route('landlord.frontend.support.ticket')}}"
                    class="px-6 py-3 rounded-xl flex items-center justify-center gap-2 text-base font-normal text-white transition-opacity hover:opacity-90 bg-primary">
                    <i class="icon-base ti tabler-plus"></i>
                    {{__('New Ticket')}}
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="p-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">

            <!-- Card 1: Total Tickets -->
            <div class=" rounded-2xl border border-borderCS px-5 py-4" style="background-color: var(--section-bg-1, #ffffff)">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: #e8f0f5;">
                        <i class="ti tabler-ticket text-lg text-sectionC icon-22px"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm text-sub2Title font-normal">{{__('Total Tickets')}}</span>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xl font-medium text-secondary">{{$total_tickets}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Open Tickets -->
            <div class=" rounded-2xl border border-borderCS px-5 py-4" style="background-color: var(--section-bg-1, #ffffff)">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: #e8f0f5;">
                        <i class="ti tabler-ticket text-lg text-sectionC icon-22px"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm text-sub2Title font-normal">{{__('Open Tickets')}}</span>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xl font-medium text-secondary">{{$open_tickets}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Closed Tickets -->
            <div class=" rounded-2xl border border-borderCS px-5 py-4" style="background-color: var(--section-bg-1, #ffffff)">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: #e8f0f5;">
                        <i class="ti tabler-ticket text-lg text-sectionC icon-22px"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm text-sub2Title font-normal">{{__('Closed Tickets')}}</span>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xl font-medium text-secondary">{{$closed_tickets}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Urgent Tickets -->
            <div class=" rounded-2xl border border-borderCS px-5 py-4" style="background-color: var(--section-bg-1, #ffffff)">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: #e8f0f5;">
                        <i class="ti tabler-ticket text-lg text-sectionC icon-22px"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm text-sub2Title font-normal">{{__('Urgent')}}</span>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xl font-medium text-secondary">{{$urgent_tickets}}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="flex flex-col gap-4 mt-6">

            <!-- Table Card -->
            <div class="rounded-2xl border border-borderCS shadow-sm overflow-hidden" style="background-color: var(--section-bg-1, #ffffff)">
                <div class="border-b border-borderC px-4 sm:px-5 py-4" style="background-color: var(--section-bg-1, #ffffff)">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <!-- Search -->
                        <form method="GET" action="" id="ticketSearchForm" class="flex-1 sm:flex-initial">
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            @if(request('priority'))
                                <input type="hidden" name="priority" value="{{ request('priority') }}">
                            @endif
                            <div class="flex items-center gap-2 border border-gray-200 rounded-xl px-3 py-2 bg-white focus-within:ring-2 focus-within:ring-teal-200 focus-within:border-teal-400 transition w-full sm:w-48 md:w-56">
                                <i class="ti tabler-search text-sub2Title text-base flex-shrink-0"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{__('Search tickets...')}}"
                                       class="text-sm text-sectionC placeholder-gray-400 outline-none bg-transparent w-full min-w-0" />
                            </div>
                        </form>
                        
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <!-- Status Filter -->
                            <form method="GET" action="" id="ticketStatusFilter" class="flex-1 sm:flex-initial">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('priority'))
                                    <input type="hidden" name="priority" value="{{ request('priority') }}">
                                @endif
                                <select name="status" onchange="document.getElementById('ticketStatusFilter').submit()"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm sm:text-base font-normal text-sub2Title bg-white hover:bg-gray-50 transition cursor-pointer outline-none">
                                    <option value="">{{__('All Status')}}</option>
                                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{__('Open')}}</option>
                                    <option value="close" {{ request('status') == 'close' ? 'selected' : '' }}>{{__('Closed')}}</option>
                                </select>
                            </form>
                            
                            <!-- Priority Filter -->
                            <form method="GET" action="" id="ticketPriorityFilter" class="flex-1 sm:flex-initial">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                                <select name="priority" onchange="document.getElementById('ticketPriorityFilter').submit()"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm sm:text-base font-normal text-sub2Title bg-white hover:bg-gray-50 transition cursor-pointer outline-none">
                                    <option value="">{{__('All Priority')}}</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{__('Low')}}</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{__('Medium')}}</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{__('High')}}</option>
                                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>{{__('Urgent')}}</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    @if(count($all_tickets) > 0)
                    <table class="w-full whitespace-nowrap">
                        <thead>
                        <tr class="border-b border-borderCS bg-[#F8FAFB]">
                            <th class="text-left text-sm sm:text-base font-medium text-secondary px-4 sm:px-6 py-4">
                                {{__('Ticket ID')}}</th>
                            <th class="text-left text-sm sm:text-base font-medium text-secondary px-4 py-4 min-w-[200px]">
                                {{__('Subject')}}</th>
                            <th class="text-left text-sm sm:text-base font-medium text-secondary px-4 py-4">
                                {{__('Priority')}}</th>
                            <th class="text-left text-sm sm:text-base font-medium text-secondary px-4 py-4">
                                {{__('Status')}}</th>
                            <th class="text-left text-sm sm:text-base font-medium text-secondary px-4 py-4">
                                {{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($all_tickets as $data)
                        <tr class="border-b border-borderCS hover:bg-gray-50 transition-colors">
                            <td class="px-4 sm:px-6 py-4 text-sm sm:text-base font-normal text-sub2Title">#{{$data->id}}</td>
                            <td class="px-4 py-4">
                                <p class="text-sm sm:text-base text-secondary font-normal truncate max-w-[150px] sm:max-w-[200px] md:max-w-none">{{$data->title}}</p>
                                <p class="text-xs sm:text-sm text-sub2Title mt-0.5">{{$data->created_at->format('D, d M Y')}}</p>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $priorityClasses = [
                                        'low' => 'border-green-400 text-green-500 bg-green-50',
                                        'medium' => 'border-green-400 text-green-500 bg-green-50',
                                        'high' => 'border-red-300 text-red-400 bg-red-50',
                                        'urgent' => 'border-yellow-300 text-yellow-500 bg-yellow-50',
                                    ];
                                    $pClass = $priorityClasses[$data->priority] ?? 'border-gray-300 text-gray-500 bg-gray-50';
                                @endphp
                                <span class="inline-block border {{ $pClass }} text-xs font-medium px-3 py-1 rounded-full whitespace-nowrap">{{__($data->priority)}}</span>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $statusClasses = [
                                        'open' => 'border-green-400 text-green-500 bg-green-50',
                                        'close' => 'border-red-300 text-red-400 bg-red-50',
                                    ];
                                    $sClass = $statusClasses[$data->status] ?? 'border-yellow-300 text-yellow-500 bg-yellow-50';
                                @endphp
                                <span class="inline-block border {{ $sClass }} text-xs font-medium px-3 py-1 rounded-full whitespace-nowrap">{{__($data->status)}}</span>
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{route('landlord.user.dashboard.support.ticket.view',$data->id)}}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary/10 border border-gray-200 hover:bg-gray-50 transition-colors">
                                    <i class="ti tabler-eye icon-18px text-sectionC text-base"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="px-4 sm:px-6 py-8 text-center text-sub2Title">
                            {{__('No support tickets found.')}}
                        </div>
                    @endif
                </div>
            </div>

            <div class="{{$all_tickets->hasPages() ? 'p-6' : ''}}">
                {!! $all_tickets->links('vendor.pagination.tailwind') !!}
            </div>

        </div>

    </main>
</div>
@endsection

@section('scripts')
    @parent
    <script src="{{asset('assets/new-landlord/js/support_ticket.js')}}"></script>
    <script>
        (function (){
            "use strict";

            $(document).on('click','.change_priority',function (e){
                e.preventDefault();
                var priority = $(this).data('val');
                var id = $(this).data('id');
                var currentPriority =  $(this).parent().prev('button').text();
                currentPriority = currentPriority.trim();
                $(this).parent().prev('button').removeClass(currentPriority).addClass(priority).text(priority);
                $.ajax({
                    'type': 'post',
                    'url' : "{{route('landlord.user.dashboard.support.ticket.priority.change')}}",
                    'data' : {
                        _token : "{{csrf_token()}}",
                        priority : priority,
                        id : id,
                    },
                    success: function (data){
                        $(this).parent().find('button.'+currentPriority).removeClass(currentPriority).addClass(priority).text(priority);
                    }
                })
            });
            $(document).on('click','.status_change',function (e){
                e.preventDefault();
                var status = $(this).data('val');
                var id = $(this).data('id');
                var currentStatus =  $(this).parent().prev('button').text();
                currentStatus = currentStatus.trim();
                $(this).parent().prev('button').removeClass('status-'+currentStatus).addClass('status-'+status).text(status);
                $.ajax({
                    'type': 'post',
                    'url' : "{{route('landlord.user.dashboard.support.ticket.status.change')}}",
                    'data' : {
                        _token : "{{csrf_token()}}",
                        status : status,
                        id : id,
                    },
                    success: function (data){
                        $(this).parent().prev('button').removeClass(currentStatus).addClass(status).text(status);
                    }
                })
            });

        })(jQuery);
    </script>
    <script>
        $('.close-bars, .body-overlay').on('click', function() {
            $('.dashboard-close, .dashboard-close-main, .body-overlay').removeClass('active');
        });
        $('.sidebar-icon').on('click', function() {
            $('.dashboard-close, .dashboard-close-main, .body-overlay').addClass('active');
        });
    </script>
@endsection
