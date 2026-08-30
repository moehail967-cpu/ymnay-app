@extends('layouts.tenant-admin')

@section('content')
<div class="wallet-payment-proofs" dir="rtl">
    <h3>{{ __('طلبات بانتظار التحقق من الدفع') }}</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('tenant.admin.yemeniwallets.proofs.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('بانتظار التحقق') }}</a>
        <a href="{{ route('tenant.admin.yemeniwallets.proofs.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('مقبولة') }}</a>
        <a href="{{ route('tenant.admin.yemeniwallets.proofs.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('مرفوضة') }}</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('رقم الطلب') }}</th>
                <th>{{ __('المحفظة') }}</th>
                <th>{{ __('إثبات التحويل') }}</th>
                <th>{{ __('التاريخ') }}</th>
                <th>{{ __('إجراءات') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($proofs as $proof)
                <tr>
                    <td>#{{ $proof->order_id }}</td>
                    <td>{{ $proof->wallet_name }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $proof->screenshot_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $proof->screenshot_path) }}" width="60">
                        </a>
                    </td>
                    <td>{{ $proof->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if ($proof->verification_status === 'pending')
                            <form action="{{ route('tenant.admin.yemeniwallets.proofs.approve', $proof) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">{{ __('قبول') }}</button>
                            </form>
                            <form action="{{ route('tenant.admin.yemeniwallets.proofs.reject', $proof) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-danger">{{ __('رفض') }}</button>
                            </form>
                        @else
                            <span class="badge {{ $proof->verification_status === 'approved' ? 'bg-success' : 'bg-danger' }}">
                                {{ $proof->verification_status === 'approved' ? __('مقبول') : __('مرفوض') }}
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('لا توجد طلبات في هذه الحالة.') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $proofs->links() }}
</div>
@endsection
