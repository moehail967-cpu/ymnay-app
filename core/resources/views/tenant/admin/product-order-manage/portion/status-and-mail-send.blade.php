{{-- ── Send Mail Modal ──────────────────────────────────────────── --}}
<div id="send_mail_modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="send_mail_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden border border-main">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary flex-shrink-0">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-email-fast-outline text-info text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Send Mail To Order Sender')}}</h5>
                <p class="text-[11px] text-muted">{{__('Compose and send email')}}</p>
            </div>
            <button type="button" class="send_mail_close w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-danger-soft hover:text-danger transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <form action="{{route(route_prefix().'admin.product.order.manage.send.mail')}}" method="post" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden">
            @csrf

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-account-outline text-lg text-primary"></i>
                        <input type="text" name="name" placeholder="{{__('Enter name')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 order_manage_user_name">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Email')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-email-outline text-lg text-primary"></i>
                        <input type="text" name="email" placeholder="{{__('Email')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 order_manage_user_email">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subject')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-text-short text-lg text-primary"></i>
                        <input type="text" name="subject" value="{{__('Your order Replay From {site}')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                    <p class="text-[11px] text-muted mt-1.5">{{__('{site} will be replaced by site title')}}</p>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Message')}}</label>
                    <input type="hidden" name="message">
                    <div class="summernote"></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary flex-shrink-0">
                <button type="button" class="send_mail_close px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-send text-base"></i> {{__('Send Mail')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Order Status Change Modal ─────────────────────────────────── --}}
<div id="order_status_change_modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="order_status_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-swap-horizontal text-warning text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Order Status Change')}}</h5>
                <p class="text-[11px] text-muted">{{__('Update order and payment status')}}</p>
            </div>
            <button type="button" class="order_status_close w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-danger-soft hover:text-danger transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        @if($errors->any())
            <div class="mx-6 mt-4 bg-danger-soft border border-red-200 rounded-xl px-4 py-3">
                <ul class="list-disc list-inside text-sm text-danger space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{route(route_prefix().'admin.product.order.manage.change.status')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-5 space-y-5">
                <input type="hidden" name="order_id" id="order_id">

                {{-- Order Status --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Order Status')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-list-status text-lg text-primary"></i>
                        <select name="order_status" id="order_status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option value="pending">{{__('Pending')}}</option>
                            <option value="in_progress">{{__('In Progress')}}</option>
                            <option value="cancel">{{__('Cancel')}}</option>
                            <option value="complete">{{__('Complete')}}</option>
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                    <p class="text-[11px] text-warning mt-1.5">{{__('If you cancel order then stock will go to its previous state')}}</p>
                </div>

                {{-- Payment Status --}}
                <div id="payment_status_wrap">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Payment Status')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-cash-check text-lg text-primary"></i>
                        <select name="payment_status" id="payment_status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option value="pending">{{__('Pending')}}</option>
                            <option value="success">{{__('Success')}}</option>
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" class="order_status_close px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i> {{__('Change Status')}}
                </button>
            </div>
        </form>
    </div>
</div>
