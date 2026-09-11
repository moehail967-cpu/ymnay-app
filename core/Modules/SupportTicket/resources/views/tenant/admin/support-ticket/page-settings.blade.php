@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Ticket Page Settings')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="max-w-3xl">
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-cog-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Ticket Page Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Configure public-facing ticket page text')}}</p>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{route(route_prefix().'admin.support.ticket.page.settings')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-4 sm:p-6 space-y-5">

                {{-- Login Notice --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Login Notice')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-information-outline text-lg text-primary"></i>
                        <input type="text" name="support_ticket_login_notice" value="{{get_static_option('support_ticket_login_notice')}}"
                               placeholder="{{__('Enter login notice text')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                {{-- Form Title --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Form Title')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-format-title text-lg text-primary"></i>
                        <input type="text" name="support_ticket_form_title" value="{{get_static_option('support_ticket_form_title')}}"
                               placeholder="{{__('Enter form title')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                {{-- Button Text --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Button Text')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-cursor-default-click-outline text-lg text-primary"></i>
                        <input type="text" name="support_ticket_button_text" value="{{get_static_option('support_ticket_button_text')}}"
                               placeholder="{{__('Enter button text')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                {{-- Success Message --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Success Message')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-check-circle-outline text-lg text-primary"></i>
                        <input type="text" name="support_ticket_success_message" value="{{get_static_option('support_ticket_success_message')}}"
                               placeholder="{{__('Enter success message')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end px-4 sm:px-6 py-4 border-t border-main bg-secondary rounded-b-xl">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i>
                    {{__('Update Settings')}}
                </button>
            </div>
        </form>

    </div>
</div>

@endsection
