@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Order Cancel Page Settings')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-9">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cancel text-danger text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Order Cancel Page Settings')}}</h3>
                    <p class="text-xs text-muted">{{__('Configure the cancel page content')}}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <form action="{{route(route_prefix().'admin.product.order.cancel.page')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Main Title')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-format-title text-lg text-primary"></i>
                                <input type="text" name="site_order_cancel_page_title"
                                       value="{{get_static_option('site_order_cancel_page_title')}}"
                                       placeholder="{{__('Enter main title')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subtitle')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-subtitles-outline text-lg text-primary"></i>
                                <input type="text" name="site_order_cancel_page_subtitle"
                                       value="{{get_static_option('site_order_cancel_page_subtitle')}}"
                                       placeholder="{{__('Enter subtitle')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                            <p class="text-[11px] text-muted mt-1.5">{{__('{oid} will be replaced by order id')}}</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Description')}}</label>
                            <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <textarea name="site_order_cancel_page_description" rows="6"
                                          placeholder="{{__('Enter description')}}"
                                          class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-none">{{get_static_option('site_order_cancel_page_description')}}</textarea>
                            </div>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Changes')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
