@extends('layouts.landlord-admin')

@section('content')
<div class="yemeni-wallets-catalog" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>{{ __('كتالوج المحافظ الإلكترونية اليمنية') }}</h3>
        <a href="{{ route('landlord.yemeniwallets.catalog.create') }}" class="btn btn-primary">
            {{ __('إضافة محفظة جديدة') }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('الشعار') }}</th>
                <th>{{ __('الاسم') }}</th>
                <th>{{ __('عدد الحقول') }}</th>
                <th>{{ __('الحالة') }}</th>
                <th>{{ __('إجراءات') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($wallets as $wallet)
                <tr>
                    <td>
                        @if (!empty($wallet['logo']))
                            <img src="{{ asset('storage/' . $wallet['logo']) }}" width="40">
                        @endif
                    </td>
                    <td>{{ $wallet['name'] }}</td>
                    <td>{{ count($wallet['fields_schema']) }}</td>
                    <td>
                        <form action="{{ route('landlord.yemeniwallets.catalog.toggle', $wallet['id']) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="badge {{ ($wallet['status'] ?? true) ? 'bg-success' : 'bg-secondary' }} border-0">
                                {{ ($wallet['status'] ?? true) ? __('مفعّلة') : __('معطّلة') }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('landlord.yemeniwallets.catalog.edit', $wallet['id']) }}" class="btn btn-sm btn-outline-primary">{{ __('تعديل') }}</a>
                        <form action="{{ route('landlord.yemeniwallets.catalog.destroy', $wallet['id']) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('{{ __('هل أنت متأكد من الحذف؟') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('حذف') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('لا توجد محافظ مضافة بعد.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
