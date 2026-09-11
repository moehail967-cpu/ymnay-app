@extends('landlord.frontend.frontend-page-master')

@section('title')
    {{__('New Support Ticket')}}
@endsection
@section('page-title')
    {{__('New Support Ticket')}}
@endsection

@section('meta-data')
    <meta name="description" content="{{get_static_option('support_ticket_page_meta_description')}}">
    <meta name="tags" content="{{get_static_option('support_ticket_page_meta_tags')}}">
    {!! render_og_meta_image_by_attachment_id(get_static_option('support_ticket_page_meta_image')) !!}
@endsection

@section('content')
    <section class="py-16 lg:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-center">
                <div class="w-full max-w-2xl">
                    @if(auth()->guard('web')->check())
                        <div class="bg-white rounded-2xl border border-borderCS shadow-sm overflow-hidden">
                            <!-- Header -->
                            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-borderCS">
                                <div>
                                    <h2 class="text-lg font-bold text-secondary">{{get_static_option('support_ticket_form_title') ?? __('New Support Ticket')}}</h2>
                                    <p class="text-sm text-sub2Title mt-1">{{__('Fill out the form below to submit a new support ticket')}}</p>
                                </div>
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: #e8f0f5;">
                                    <i class="ti tabler-ticket text-lg text-sectionC icon-22px"></i>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="px-6 py-6">
                                <x-error-msg-tw/>
                                <x-flash-msg-tw/>

                                <form action="{{route('landlord.frontend.support.ticket.store')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="via" value="{{__('website')}}">

                                    <div class="flex flex-col gap-5">

                                        <!-- Title -->
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-sm font-semibold text-secondary">{{__('Title')}}</label>
                                            <input type="text" name="title" placeholder="{{__('Enter ticket title')}}" value="{{old('title')}}"
                                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition" />
                                        </div>

                                        <!-- Subject -->
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-sm font-semibold text-secondary">{{__('Subject')}}</label>
                                            <input type="text" name="subject" placeholder="{{__('Enter subject')}}" value="{{old('subject')}}"
                                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition" />
                                        </div>

                                        <!-- Priority & Department Row -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <!-- Priority -->
                                            <div class="flex flex-col gap-1.5">
                                                <label class="text-sm font-semibold text-secondary">{{__('Priority')}}</label>
                                                <div class="relative">
                                                    <select name="priority"
                                                        class="w-full appearance-none border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition bg-white pr-10 cursor-pointer">
                                                        <option value="low" {{old('priority') == 'low' ? 'selected' : ''}}>{{__('Low')}}</option>
                                                        <option value="medium" {{old('priority') == 'medium' ? 'selected' : ''}}>{{__('Medium')}}</option>
                                                        <option value="high" {{old('priority') == 'high' ? 'selected' : ''}}>{{__('High')}}</option>
                                                        <option value="urgent" {{old('priority') == 'urgent' ? 'selected' : ''}}>{{__('Urgent')}}</option>
                                                    </select>
                                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                                        <i class="ti tabler-chevron-down text-gray-400 text-base"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Departments -->
                                            <div class="flex flex-col gap-1.5">
                                                <label class="text-sm font-semibold text-secondary">{{__('Department')}}</label>
                                                <div class="relative">
                                                    <select name="departments"
                                                        class="w-full appearance-none border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition bg-white pr-10 cursor-pointer">
                                                        @foreach($departments as $dep)
                                                            <option value="{{$dep->id}}" {{old('departments') == $dep->id ? 'selected' : ''}}>{{$dep->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                                        <i class="ti tabler-chevron-down text-gray-400 text-base"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-sm font-semibold text-secondary">{{__('Description')}}</label>
                                            <textarea name="description" rows="6" placeholder="{{__('Describe your issue in detail...')}}"
                                                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition resize-none">{{old('description')}}</textarea>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="flex items-center gap-3 pt-2">
                                            <button type="submit"
                                                    class="px-6 py-3 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition bg-primary">
                                                {{get_static_option('support_ticket_button_text') ?? __('Submit Ticket')}}
                                            </button>
                                            <a href="{{route('landlord.user.home.support.tickets')}}"
                                               class="px-6 py-3 rounded-xl text-sm font-semibold text-sub2Title border border-gray-200 hover:bg-gray-50 transition">
                                                {{__('Back to Tickets')}}
                                            </a>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        @include('tenant.frontend.partials.ajax-login-form',['title' => get_static_option('support_ticket_login_notice')])
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('landlord.frontend.partials.ajax-login-form-js')
@endsection
