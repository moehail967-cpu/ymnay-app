{{-- Yemeni E-Wallets — Submit Payment Proof Page --}}
@extends(themeLayoutName())

@section('title', __('Submit Payment Proof'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('Complete Your Payment') }}</h5>
                </div>
                <div class="card-body">

                    <div class="alert alert-info">
                        <strong>{{ __('Order') }} #{{ $order->id }}</strong> —
                        {{ __('Amount') }}: {{ amount_with_currency_symbol($order->total_amount) }}
                    </div>

                    @if(session('wallet_proof_pending'))
                        <div class="alert alert-success">{{ session('wallet_proof_pending') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    @if(count($wallets) === 0)
                        <div class="alert alert-warning">
                            {{ __('No wallets are currently activated for this store. Please contact the store owner.') }}
                        </div>
                    @else
                        <p class="mb-3">{{ __('Transfer the exact amount to one of the wallets below, then upload a screenshot of the transfer receipt.') }}</p>

                        <form method="POST"
                              action="{{ route('yemeniwallets.submit-proof') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            {{-- Wallet selector --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ __('Select Wallet') }} <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($wallets as $wallet)
                                    <label class="border rounded p-2 d-flex align-items-center gap-2"
                                           style="cursor:pointer;">
                                        <input type="radio"
                                               name="catalog_wallet_id"
                                               value="{{ $wallet['catalog_wallet_id'] }}"
                                               class="wallet-radio"
                                               data-target="wallet-detail-{{ $wallet['catalog_wallet_id'] }}"
                                               required>
                                        @if($wallet['logo'])
                                            <img src="{{ $wallet['logo'] }}" alt="{{ $wallet['name'] }}" style="height:28px;object-fit:contain;">
                                        @endif
                                        <span>{{ $wallet['name'] }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Account detail panels --}}
                            @foreach($wallets as $wallet)
                            <div id="wallet-detail-{{ $wallet['catalog_wallet_id'] }}"
                                 class="wallet-detail-panel card mb-3 p-3 bg-light"
                                 style="display:none;">
                                <p class="fw-semibold mb-2">{{ __('Transfer to:') }}</p>
                                <ul class="list-group list-group-flush">
                                    @foreach($wallet['values'] as $key => $val)
                                    <li class="list-group-item d-flex justify-content-between bg-transparent">
                                        <span class="text-muted">{{ $key }}</span>
                                        <strong>{{ $val }}</strong>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endforeach

                            {{-- Proof upload — MANDATORY --}}
                            <div class="mb-4">
                                <label for="payment_proof" class="form-label fw-semibold">
                                    {{ __('Transfer Screenshot') }} <span class="text-danger">*</span>
                                </label>
                                <input type="file"
                                       name="payment_proof"
                                       id="payment_proof"
                                       accept="image/jpeg,image/jpg,image/png,image/webp"
                                       class="form-control @error('payment_proof') is-invalid @enderror"
                                       required>
                                @error('payment_proof')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('JPG, PNG or WebP · max 5 MB') }}</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Submit Proof & Complete Order') }}
                            </button>
                        </form>

                        <script>
                        document.querySelectorAll('.wallet-radio').forEach(function(radio){
                            radio.addEventListener('change', function(){
                                document.querySelectorAll('.wallet-detail-panel').forEach(function(p){ p.style.display='none'; });
                                var target = document.getElementById(this.dataset.target);
                                if(target) target.style.display='block';
                            });
                        });
                        </script>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
