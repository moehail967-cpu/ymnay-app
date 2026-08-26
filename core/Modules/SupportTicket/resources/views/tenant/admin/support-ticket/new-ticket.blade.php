@extends(route_prefix().'admin.admin-master')
@section('title') {{__('New Ticket')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="max-w-3xl">
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-ticket-plus-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('New Support Ticket')}}</h3>
                <p class="text-xs text-muted">{{__('Create a new support ticket on behalf of a user')}}</p>
            </div>
            <div class="ml-auto">
                <a href="{{route(route_prefix().'admin.support.ticket.all')}}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                    <i class="mdi mdi-arrow-left text-base"></i> {{__('All Tickets')}}
                </a>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{route(route_prefix().'admin.support.ticket.new')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-4 sm:p-6 space-y-5">

                {{-- Title --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-format-title text-lg text-primary"></i>
                        <input type="text" name="title" value="{{old('title')}}" placeholder="{{__('Enter ticket title')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                {{-- Subject --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subject')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-text-short text-lg text-primary"></i>
                        <input type="text" name="subject" value="{{old('subject')}}" placeholder="{{__('Enter ticket subject')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                {{-- Priority + Department (2-col) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Priority')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-flag-outline text-lg text-primary"></i>
                            <select name="priority" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="low">{{__('Low')}}</option>
                                <option value="medium">{{__('Medium')}}</option>
                                <option value="high">{{__('High')}}</option>
                                <option value="urgent">{{__('Urgent')}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Department')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-domain text-lg text-primary"></i>
                            <select name="departments" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                @foreach($departments as $dep)
                                    <option value="{{$dep->id}}">{{$dep->name}}</option>
                                @endforeach
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                {{-- User --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('User')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-account-outline text-lg text-primary"></i>
                        <select name="user_id" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            @foreach($all_users as $user)
                                <option value="{{$user->id}}">{{$user->username ?? $user->name}}</option>
                            @endforeach
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Description')}}</label>
                    <textarea name="description" rows="6" placeholder="{{__('Describe the issue in detail...')}}"
                              class="w-full bg-secondary border border-main rounded-xl px-4 py-3 text-sm text-dark placeholder-subtle outline-none focus:border-primary transition resize-y">{{old('description')}}</textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-4 sm:px-6 py-4 border-t border-main bg-secondary rounded-b-xl">
                <a href="{{route(route_prefix().'admin.support.ticket.all')}}"
                   class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-send text-base"></i>
                    {{__('Submit Ticket')}}
                </button>
            </div>
        </form>

    </div>
</div>

@endsection
