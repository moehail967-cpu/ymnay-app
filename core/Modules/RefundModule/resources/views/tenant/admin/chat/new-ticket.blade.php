@extends(route_prefix().'admin.admin-master')

@section('title') {{__('New Refund Message')}} @endsection

@section('content')

<div class="max-w-2xl mx-auto space-y-5">

    {{-- Back Link --}}
    <a href="{{route(route_prefix().'admin.refund.chat.all')}}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:opacity-80 transition">
        <i class="mdi mdi-arrow-left text-base"></i> {{__('All Refund Messages')}}
    </a>

    <x-error-msg-tw/>
    <x-flash-msg-tw/>

    {{-- Form Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

        {{-- Header --}}
        <div class="px-5 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-message-plus-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('New Refund Message')}}</h3>
                <p class="text-xs text-muted">{{__('Create a new refund support ticket')}}</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="p-5">
            <form action="{{route(route_prefix().'admin.refund.chat.new')}}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-4">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Title')}} <span class="text-danger">*</span></label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-format-title text-primary"></i>
                            <input type="text" name="title" value="{{old('title')}}" placeholder="{{__('Enter ticket title')}}" required
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Subject')}} <span class="text-danger">*</span></label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-text-short text-primary"></i>
                            <input type="text" name="subject" value="{{old('subject')}}" placeholder="{{__('Enter subject')}}" required
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('User')}} <span class="text-danger">*</span></label>
                        <select name="user_id" class="lnd-input" required>
                            <option value="">{{__('Select a user')}}</option>
                            @foreach($all_users as $user)
                                <option value="{{$user->id}}">{{$user->username}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}}</label>
                        <div class="bg-surface border border-main rounded-xl px-3.5 py-2.5 focus-within:border-primary transition">
                            <textarea name="description" rows="5" placeholder="{{__('Describe the refund issue...')}}"
                                      class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-none">{{old('description')}}</textarea>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-send text-base"></i> {{__('Submit Message')}}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
