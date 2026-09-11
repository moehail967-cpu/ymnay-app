@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Checkout')}} @endsection

@section('content')

    {{-- Page Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{route('tenant.my.package.order.buy.plan')}}"
           class="w-8 h-8 rounded-lg bg-surface border border-main flex items-center justify-center text-subtle hover:text-dark hover:bg-muted transition-colors">
            <i class="mdi mdi-arrow-left text-base"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-dark font-urbanist">{{__('Checkout')}}</h1>
            <p class="text-sm text-muted mt-0.5">{{__('Review your plan and select a payment method.')}}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Payment Gateway Selector --}}
        <div class="lg:col-span-2">
            <form action="{{route('tenant.my.package.checkout.initiate')}}" method="POST" id="checkoutForm">
                @csrf
                <input type="hidden" name="plan_id" value="{{$plan->id}}">

                <div class="bg-surface rounded-2xl border border-main overflow-hidden">

                    {{-- Section Header --}}
                    <div class="px-6 py-4 border-b border-main">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                                <i class="mdi mdi-credit-card-outline text-primary text-base"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Payment Method')}}</h3>
                                <p class="text-xs text-muted">{{__('Select how you want to pay')}}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Gateway List --}}
                    <div class="p-6">
                        @if($gateways->isEmpty())
                            <p class="text-sm text-muted italic text-center py-8">{{__('No payment methods available. Please contact support.')}}</p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="gatewayGrid">
                                @foreach($gateways as $gw)
                                    <label class="gateway-card flex items-center gap-3 p-4 rounded-xl border-2 border-main bg-secondary cursor-pointer transition-all hover:border-primary/40 hover:bg-muted/30"
                                           for="gw_{{$gw->name}}">
                                        <input type="radio" name="gateway" id="gw_{{$gw->name}}" value="{{$gw->name}}"
                                               class="gateway-radio sr-only" required>
                                        <div class="w-5 h-5 rounded-full border-2 border-main flex-shrink-0 flex items-center justify-center gateway-indicator">
                                            <div class="w-2.5 h-2.5 rounded-full bg-primary hidden gateway-dot"></div>
                                        </div>
                                        <i class="mdi mdi-credit-card-outline text-subtle text-lg flex-shrink-0"></i>
                                        <span class="text-sm font-semibold text-dark capitalize">
                                            {{str_replace('_', ' ', $gw->name)}}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <p id="gatewayError" class="hidden mt-3 text-xs text-danger font-semibold">
                                {{__('Please select a payment method.')}}
                            </p>
                        @endif
                    </div>

                    {{-- Terms note --}}
                    <div class="px-6 pb-5">
                        <p class="text-xs text-muted">
                            {{__('By clicking "Pay Now" you agree to the subscription terms. Your current plan remains active until the new one is confirmed.')}}
                        </p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-4 flex justify-end">
                    <button type="submit" id="payBtn"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primary-hover text-white text-sm font-bold rounded-xl transition-colors">
                        <i class="mdi mdi-lock-outline text-base"></i>
                        {{__('Pay Now')}}
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: Order Summary --}}
        <div>
            <div class="bg-surface rounded-2xl border border-main overflow-hidden sticky top-6">
                <div class="px-6 py-4 border-b border-main">
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Order Summary')}}</h3>
                </div>
                <div class="p-6 space-y-4">

                    {{-- Plan name & type --}}
                    <div>
                        @php
                            $typeBadge = match((int) $plan->type) {
                                0 => ['label' => __('Monthly'), 'class' => 'bg-blue-50 text-blue-600 border-blue-200'],
                                1 => ['label' => __('Yearly'),  'class' => 'bg-violet-50 text-violet-600 border-violet-200'],
                                2 => ['label' => __('Lifetime'),'class' => 'bg-emerald-50 text-emerald-600 border-emerald-200'],
                                default => ['label' => __('Plan'), 'class' => 'bg-gray-50 text-gray-600 border-gray-200'],
                            };
                        @endphp
                        <h4 class="text-base font-bold text-dark font-urbanist">{{$plan->title}}</h4>
                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border {{$typeBadge['class']}}">
                            {{$typeBadge['label']}}
                        </span>
                    </div>

                    {{-- Limits --}}
                    <div class="space-y-1.5">
                        @if(!empty($plan->page_permission_feature))
                            <div class="flex items-center gap-2 text-xs text-muted">
                                <i class="mdi mdi-file-document-outline text-subtle text-sm"></i>
                                {{ $plan->page_permission_feature == -1 ? __('Unlimited') : $plan->page_permission_feature }} {{__('Pages')}}
                            </div>
                        @endif
                        @if(!empty($plan->product_permission_feature))
                            <div class="flex items-center gap-2 text-xs text-muted">
                                <i class="mdi mdi-cube-outline text-subtle text-sm"></i>
                                {{ $plan->product_permission_feature == -1 ? __('Unlimited') : $plan->product_permission_feature }} {{__('Products')}}
                            </div>
                        @endif
                        @if(!empty($plan->blog_permission_feature))
                            <div class="flex items-center gap-2 text-xs text-muted">
                                <i class="mdi mdi-post-outline text-subtle text-sm"></i>
                                {{ $plan->blog_permission_feature == -1 ? __('Unlimited') : $plan->blog_permission_feature }} {{__('Blogs')}}
                            </div>
                        @endif
                        @if(!empty($plan->storage_permission_feature))
                            <div class="flex items-center gap-2 text-xs text-muted">
                                <i class="mdi mdi-folder-outline text-subtle text-sm"></i>
                                {{ $plan->storage_permission_feature == -1 ? __('Unlimited') : $plan->storage_permission_feature }} MB {{__('Storage')}}
                            </div>
                        @endif
                    </div>

                    {{-- Features --}}
                    @if($plan->plan_features->isNotEmpty())
                        <div class="pt-3 border-t border-main">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-muted mb-2">{{__('Includes')}}</p>
                            <ul class="space-y-1.5">
                                @foreach($plan->plan_features as $feature)
                                    <li class="flex items-center gap-2 text-xs text-dark">
                                        <i class="mdi mdi-check-circle text-success text-sm flex-shrink-0"></i>
                                        <span class="capitalize">{{str_replace('_', ' ', $feature->feature_name)}}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Price --}}
                    <div class="pt-4 border-t border-main flex items-center justify-between">
                        <span class="text-sm font-semibold text-muted">{{__('Total')}}</span>
                        <span class="text-xl font-extrabold text-dark font-urbanist">
                            @if($plan->price == 0)
                                {{__('Free')}}
                            @else
                                {{amount_with_currency_symbol($plan->price)}}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
<script>
    // Highlight selected gateway card
    document.querySelectorAll('.gateway-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Reset all
            document.querySelectorAll('.gateway-card').forEach(function(card) {
                card.classList.remove('border-primary', 'bg-primary-soft/10');
                card.classList.add('border-main', 'bg-secondary');
                card.querySelector('.gateway-dot').classList.add('hidden');
                card.querySelector('.gateway-indicator').classList.remove('border-primary');
            });
            // Activate selected
            var label = document.querySelector('label[for="' + this.id + '"]');
            label.classList.add('border-primary', 'bg-primary-soft/10');
            label.classList.remove('border-main', 'bg-secondary');
            label.querySelector('.gateway-dot').classList.remove('hidden');
            label.querySelector('.gateway-indicator').classList.add('border-primary');
            document.getElementById('gatewayError').classList.add('hidden');
        });
    });

    // Validate before submit
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        var selected = document.querySelector('.gateway-radio:checked');
        if (!selected) {
            e.preventDefault();
            document.getElementById('gatewayError').classList.remove('hidden');
        }
    });
</script>
@endsection
