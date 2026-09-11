@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Edit Price Plan')}} @endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/admin/css/components/price-plan.css')}}">
@endsection

@section('content')

@php
    $features = price_plan_feature_list();
@endphp

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form class="forms-sample" method="post" action="{{route(route_prefix().'admin.price.plan.update')}}">
    @csrf
    <input type="hidden" name="id" value="{{$plan->id}}">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        {{-- Main Content (Left) --}}
        <div class="lg:col-span-8 space-y-5">

            {{-- Basic Info Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-currency-usd text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Price Plan')}}</h3>
                        <p class="text-xs text-muted">{{__('Update plan details and pricing')}}</p>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        <a href="{{route(route_prefix().'admin.price.plan')}}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-main text-xs font-semibold text-dark hover:border-primary hover:text-primary transition">
                            <i class="mdi mdi-arrow-left text-sm"></i> {{__('All Plans')}}
                        </a>
                        @can('price-plan-create')
                        <a href="{{route(route_prefix().'admin.price.plan.create')}}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-plus text-sm"></i> {{__('Create New')}}
                        </a>
                        @endcan
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                            <input type="text" name="title" value="{{$plan->title}}" placeholder="{{__('Enter plan title')}}" class="lnd-input">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Package Badge')}}</label>
                            <input type="text" name="package_badge" value="{{$plan->package_badge}}" placeholder="{{__('e.g. Popular, Best Value')}}" class="lnd-input">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Package Description')}}</label>
                        <textarea name="package_description" rows="3" placeholder="{{__('Describe this plan')}}" class="lnd-input">{{$plan->description}}</textarea>
                    </div>

                    @if(tenant())
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Features')}}</label>
                        <textarea name="features" rows="4" placeholder="{{__('Features')}}" class="lnd-input">{{$plan->getTranslation('features',$lang_slug)}}</textarea>
                        <p class="text-[11px] text-muted mt-1.5">{{__('separate new feature by new line, add {close} for (x) icon add {check} for check icon')}}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Price')}}</label>
                        <input type="number" name="price" value="{{$plan->price}}" placeholder="{{__('Enter price')}}" class="lnd-input">
                    </div>
                </div>
            </div>

            @if(!tenant())
            {{-- Features Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-check-decagram-outline text-success text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Select Features')}}</h3>
                        <p class="text-xs text-muted">{{__('Choose features for this plan')}}</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">
                    <div class="landlord_price_plan_feature">
                        <div class="feature-section">
                            <div class="flex flex-wrap gap-x-5 gap-y-3">
                                @foreach($features as $key => $feat)
                                    <label for="{{$key}}" class="inline-flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="features[]" id="{{$key}}" class="exampleCheck1 w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary"
                                               value="{{$key}}" data-feature="{{$key}}"
                                               @foreach($plan->plan_features as $feat_old)
                                                   {{$feat_old->feature_name == $key ? 'checked' : ''}}
                                               @endforeach
                                        >
                                        <span class="text-sm text-dark group-hover:text-primary transition">
                                            {{splitPascalCase(str_replace('_', ' ', ucfirst($feat)))}}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Permission boxes --}}
                    <div class="page_permission_box {{ empty($plan->page_permission_feature) ? 'hidden' : '' }}">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Page Create Permission')}}
                            <i class="mdi mdi-information-outline text-primary text-xs ml-1" title="{{__('Keep -1 for Unlimited')}}"></i>
                        </label>
                        <input type="text" min="-1" name="page_permission_feature" value="{{$plan->page_permission_feature}}" placeholder="{{__('Page limit')}}" class="lnd-input">
                    </div>

                    <div class="blog_permission_box {{ empty($plan->blog_permission_feature) ? 'hidden' : '' }}">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Blog Create Permission')}}
                            <i class="mdi mdi-information-outline text-primary text-xs ml-1" title="{{__('Keep -1 for Unlimited')}}"></i>
                        </label>
                        <input type="text" min="-1" name="blog_permission_feature" value="{{$plan->blog_permission_feature}}" placeholder="{{__('Blog limit')}}" class="lnd-input">
                    </div>

                    <div class="product_permission_box {{ empty($plan->product_permission_feature) ? 'hidden' : '' }}">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Product Create Permission')}}
                            <i class="mdi mdi-information-outline text-primary text-xs ml-1" title="{{__('Keep -1 for Unlimited')}}"></i>
                        </label>
                        <input type="text" min="-1" name="product_permission_feature" value="{{$plan->product_permission_feature}}" placeholder="{{__('Product limit')}}" class="lnd-input">
                    </div>

                    <div class="storage_permission_box {{ empty($plan->storage_permission_feature) ? 'hidden' : '' }}">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Storage Create Permission')}}
                            <i class="mdi mdi-information-outline text-primary text-xs ml-1" title="{{__('Keep -1 for Unlimited')}}"></i>
                        </label>
                        <input type="text" min="-1" name="storage_permission_feature" value="{{$plan->storage_permission_feature}}" placeholder="{{__('Storage limit (MB)')}}" class="lnd-input">
                    </div>
                </div>
            </div>

            {{-- Payment Gateways Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: var(--color-warning-bg, #fffbeb);">
                        <i class="mdi mdi-credit-card-outline text-base" style="color: var(--color-warning, #f59e0b);"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Payment Gateways')}}</h3>
                        <p class="text-xs text-muted">{{__('Choose payment gateways for this plan')}}</p>
                    </div>
                    <div class="ml-auto">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="select-all-gateway w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-xs font-semibold text-dark">{{__('Select All')}}</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="landlord_price_plan_payment_gateways">
                        <div class="feature-section">
                            @php
                                $replaceable_text = '<input type="hidden" name="selected_payment_gateway" value="paytm">';
                            @endphp
                            {!! str_replace($replaceable_text,'',render_payment_gateway_for_price_plan(is_price_plan: true)) !!}
                            <input type="hidden" name="payment_gateways" value="{{$plan_payment_gateways}}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- FAQ Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[#f3e8ff] flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-help-circle-outline text-[#9333ea] text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('FAQ Section')}}</h3>
                        <p class="text-xs text-muted">{{__('Manage frequently asked questions')}}</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="iconbox-repeater-wrapper space-y-4">
                        @php
                            $faq_items = !empty($plan->faq) ? unserialize($plan->faq,['class' => false]) : ['title' => ['']];
                        @endphp
                        @forelse($faq_items['title'] as $faq)
                            <div class="all-field-wrap bg-secondary border border-main rounded-xl p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Faq Title')}}</label>
                                        <input type="text" name="faq[title][]" value="{{$faq}}" class="lnd-input">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Faq Description')}}</label>
                                        <textarea name="faq[description][]" rows="2" class="lnd-input">{{$faq_items['description'][$loop->index] ?? ''}}</textarea>
                                    </div>
                                </div>
                                <div class="action-wrap">
                                    <span class="add"><i class="mdi mdi-plus"></i></span>
                                    <span class="remove"><i class="mdi mdi-delete-outline"></i></span>
                                </div>
                            </div>
                        @empty
                            <div class="all-field-wrap bg-secondary border border-main rounded-xl p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Faq Title')}}</label>
                                        <input type="text" name="faq[title][]" placeholder="{{__('FAQ title')}}" class="lnd-input">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Faq Description')}}</label>
                                        <textarea name="faq[description][]" rows="2" placeholder="{{__('FAQ description')}}" class="lnd-input"></textarea>
                                    </div>
                                </div>
                                <div class="action-wrap">
                                    <span class="add"><i class="mdi mdi-plus"></i></span>
                                    <span class="remove"><i class="mdi mdi-delete-outline"></i></span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar (Right) --}}
        <div class="lg:col-span-4">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
                <div class="px-4 py-4 border-b border-main flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-cog-outline text-sm text-muted"></i>
                    </div>
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Plan Settings')}}</h4>
                </div>

                <div class="p-4 space-y-5">

                    @if(!tenant())
                    {{-- Type --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Type')}}</label>
                        <select name="type" class="lnd-input">
                            @foreach(\App\Enums\PricePlanTypEnums::getPricePlanTypeList() ?? [] as $key => $value)
                                <option value="{{$key}}" {{$plan->type === $key ? 'selected' : ''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Free Trial Toggle --}}
                    <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-secondary border border-main">
                        <div class="flex items-center gap-2">
                            <i class="mdi mdi-clock-outline text-lg text-primary"></i>
                            <span class="text-xs font-semibold text-dark">{{__('Free Trial')}}</span>
                        </div>
                        <label class="dr-toggle">
                            <input type="checkbox" name="has_trial" {{$plan->has_trial ? 'checked' : ''}}>
                            <span class="dr-toggle-track"></span>
                        </label>
                    </div>

                    {{-- Trial Days --}}
                    <div class="trial_date_box {{empty($plan->trial_days) ? 'hidden' : ''}}">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Trial Days')}}</label>
                        <input type="number" name="trial_days" value="{{$plan->trial_days}}" placeholder="{{__('Days..')}}" class="lnd-input">
                    </div>
                    @endif

                    {{-- Status --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                        <select name="status" class="lnd-input">
                            <option @if($plan->status === \App\Enums\StatusEnums::PUBLISH) selected @endif value="{{\App\Enums\StatusEnums::PUBLISH}}">{{__('Publish')}}</option>
                            <option @if($plan->status === \App\Enums\StatusEnums::DRAFT) selected @endif value="{{\App\Enums\StatusEnums::DRAFT}}">{{__('Draft')}}</option>
                        </select>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                    </button>
                </div>
            </div>
        </div>

    </div>

</form>

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";

        if (typeof flatpickr !== 'undefined') {
            flatpickr('.date', { enableTime: false, dateFormat: "d-m-Y", minDate: "today" });
        }

        $(document).on('change', 'select[name="lang"]', function (e) {
            $(this).closest('form').trigger('submit');
            $('input[name="lang"]').val($(this).val());
        });

        $(document).on('change', 'input[name=has_trial]', function () {
            $('.trial_date_box').toggleClass('hidden');
        });

        $(document).on('change', '.exampleCheck1', function () {
            var feature = $(this).attr('data-feature');
            var checked = this.checked;
            var mapping = {
                'pages':    '.page_permission_box',
                'blog':     '.blog_permission_box',
                'products': '.product_permission_box',
                'storage':  '.storage_permission_box'
            };
            if (mapping[feature]) {
                var $box = $(mapping[feature]);
                if (checked) { $box.removeClass('hidden'); }
                else { $box.addClass('hidden').find('input').val(''); }
            }
        });

        $(document).ready(function () {
            var $gatewayItems = $('.payment-gateway-wrapper ul li');
            var selectedGateways = "{{ $plan_payment_gateways }}";
            var selectedArray = selectedGateways ? selectedGateways.split(',') : [];

            $gatewayItems.removeClass('selected');
            if (selectedArray.length > 0) {
                $.each(selectedArray, function (key, value) {
                    $('.payment-gateway-wrapper ul li[data-gateway=' + value + ']').addClass('selected');
                });
            }

            if (selectedArray.length > 0 && selectedArray.length === $gatewayItems.length) {
                $('.select-all-gateway').prop('checked', true);
            }

            $gatewayItems.on('click', function () {
                $(this).toggleClass('selected');
                updateGatewayInput();
            });

            $('.select-all-gateway').on('change', function () {
                if ($(this).is(':checked')) { $gatewayItems.addClass('selected'); }
                else { $gatewayItems.removeClass('selected'); }
                updateGatewayInput();
            });

            function updateGatewayInput() {
                var gateways = [];
                $('.payment-gateway-wrapper ul li.selected').each(function () {
                    gateways.push($(this).data('gateway'));
                });
                $("input[name='payment_gateways']").val(gateways.join(','));
            }
        });

    })(jQuery);
    </script>
    <x-repeater/>
@endsection
