@extends('layouts.tenant-admin')

@section('content')
<div class="tenant-yemeni-wallets" dir="rtl">
    <h3>{{ __('محافظي الإلكترونية') }}</h3>
    <p class="text-muted">{{ __('فعّل المحافظ التي تريد استقبال الأموال عليها من زبائنك وأدخل بياناتك الخاصة بكل محفظة.') }}</p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse ($catalog as $catalogWallet)
        @php $activation = $activations[$catalogWallet['id']] ?? null; @endphp

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    @if (!empty($catalogWallet['logo']))
                        <img src="{{ asset('storage/' . $catalogWallet['logo']) }}" width="32">
                    @endif
                    <strong>{{ $catalogWallet['name'] }}</strong>
                </div>
                <span class="badge {{ ($activation['is_active'] ?? false) ? 'bg-success' : 'bg-secondary' }}">
                    {{ ($activation['is_active'] ?? false) ? __('مفعّلة') : __('غير مفعّلة') }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.admin.yemeniwallets.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="catalog_wallet_id" value="{{ $catalogWallet['id'] }}">

                    @foreach ($catalogWallet['fields_schema'] as $field)
                        @php $value = $activation['values'][$field['key']] ?? old('values.' . $field['key']); @endphp
                        <div class="mb-2">
                            <label>{{ $field['label'] }} @if($field['required'] ?? false)<span class="required">*</span>@endif</label>
                            @if (($field['type'] ?? 'text') === 'textarea')
                                <textarea name="values[{{ $field['key'] }}]" class="form-control" @if($field['required'] ?? false) required @endif>{{ $value }}</textarea>
                            @else
                                <input type="{{ $field['type'] === 'number' ? 'number' : 'text' }}" name="values[{{ $field['key'] }}]" class="form-control" value="{{ $value }}" @if($field['required'] ?? false) required @endif>
                            @endif
                        </div>
                    @endforeach

                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($activation['is_active'] ?? false)>
                        <label class="form-check-label">{{ __('إظهار هذه المحفظة للزبائن عند الدفع') }}</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">{{ __('حفظ') }}</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-muted">{{ __('لم يقم مالك المنصة بإضافة أي محافظ إلكترونية بعد.') }}</p>
    @endforelse
</div>
@endsection
