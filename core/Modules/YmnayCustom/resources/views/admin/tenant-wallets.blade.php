@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Manual Wallets')}} @endsection

@section('content')
<x-landlord-error-msg/><x-landlord-flash-msg/>
<div class="bg-surface rounded-xl shadow-main border border-main">
    <div class="px-4 sm:px-6 py-4 border-b border-main">
        <h3 class="text-sm font-bold text-dark">{{__('Manual Wallets')}}</h3>
        <p class="text-xs text-muted mt-1">{{__('Enable the wallets you accept and enter your own receiving details.')}}</p>
    </div>
    <form method="post" action="{{route('ymnaycustom.tenant.wallets.save')}}" class="p-4 sm:p-6 space-y-5">
        @csrf
        @forelse($wallets as $wallet)
            @php($row = $settings[$wallet->id] ?? [])
            <div class="rounded-xl border border-main p-4">
                <div class="flex items-center gap-3 mb-4">
                    @if($wallet->logo_url)<img src="{{$wallet->logo_url}}" class="w-14 h-14 object-contain rounded-lg border border-main" alt="">@endif
                    <div class="flex-1"><h4 class="font-bold text-dark">{{$wallet->name}}</h4><p class="text-xs text-muted">{{$wallet->description}}</p></div>
                    <label class="flex items-center gap-2"><input type="hidden" name="wallets[{{$wallet->id}}][enabled]" value="0"><input type="checkbox" name="wallets[{{$wallet->id}}][enabled]" value="1" @checked(!empty($row['enabled']))> {{__('Enable')}}</label>
                </div>
                <div><label class="lnd-label">{{__('Recipient name')}}</label><input class="lnd-input" name="wallets[{{$wallet->id}}][recipient_name]" value="{{$row['recipient_name'] ?? ''}}"></div>
                <div class="mt-4" data-wallet-accounts data-wallet-id="{{$wallet->id}}">
                    <div class="flex items-center justify-between gap-3 mb-2"><label class="lnd-label mb-0">{{__('أرقام المحفظة حسب العملة')}}</label><button type="button" class="px-3 py-2 rounded-lg border border-main text-sm font-semibold" data-add-account>{{__('إضافة رقم محفظة')}}</button></div>
                    <p class="text-xs text-muted mb-3">{{__('أضف أي اسم عملة تريده. قيمة الطلب لا تتحول تلقائيًا بين العملات.')}}</p>
                    <div class="space-y-3" data-account-list>
                        @foreach(($row['accounts'] ?? (!empty($row['account_number']) ? [['currency' => __('العملة الأساسية'), 'account_number' => $row['account_number'], 'description' => '']] : [])) as $index => $account)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 rounded-lg border border-main p-3" data-account-row>
                                <div><label class="lnd-label">{{__('اسم العملة')}}</label><input class="lnd-input" name="wallets[{{$wallet->id}}][accounts][{{$index}}][currency]" value="{{$account['currency'] ?? ''}}" placeholder="{{__('مثال: ريال يمني')}}"></div>
                                <div><label class="lnd-label">{{__('رقم المحفظة')}}</label><input class="lnd-input" name="wallets[{{$wallet->id}}][accounts][{{$index}}][account_number]" value="{{$account['account_number'] ?? ''}}"></div>
                                <div><label class="lnd-label">{{__('وصف اختياري')}}</label><input class="lnd-input" name="wallets[{{$wallet->id}}][accounts][{{$index}}][description]" value="{{$account['description'] ?? ''}}" placeholder="{{__('مثال: للتحويل عبر التطبيق فقط')}}"></div>
                                <div class="md:col-span-3 text-left"><button type="button" class="text-danger text-sm font-semibold" data-remove-account>{{__('حذف هذا الرقم')}}</button></div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4"><label class="lnd-label">{{__('Customer instructions')}}</label><textarea class="lnd-input" rows="3" name="wallets[{{$wallet->id}}][instructions]">{{$row['instructions'] ?? ''}}</textarea></div>
            </div>
        @empty
            <div class="alert alert-warning">{{__('The Ymnay administrator has not published any wallets yet.')}}</div>
        @endforelse
        @if($wallets->isNotEmpty())<button class="px-5 py-2.5 rounded-xl bg-primary text-white font-semibold">{{__('Save Wallet Settings')}}</button>@endif
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('click', function (event) {
    const add = event.target.closest('[data-add-account]');
    if (add) {
        const box = add.closest('[data-wallet-accounts]');
        const list = box.querySelector('[data-account-list]');
        const walletId = box.dataset.walletId;
        const index = Date.now();
        list.insertAdjacentHTML('beforeend', `<div class="grid grid-cols-1 md:grid-cols-3 gap-3 rounded-lg border border-main p-3" data-account-row>
            <div><label class="lnd-label">اسم العملة</label><input class="lnd-input" name="wallets[${walletId}][accounts][${index}][currency]" placeholder="مثال: ريال يمني"></div>
            <div><label class="lnd-label">رقم المحفظة</label><input class="lnd-input" name="wallets[${walletId}][accounts][${index}][account_number]"></div>
            <div><label class="lnd-label">وصف اختياري</label><input class="lnd-input" name="wallets[${walletId}][accounts][${index}][description]" placeholder="مثال: للتحويل عبر التطبيق فقط"></div>
            <div class="md:col-span-3 text-left"><button type="button" class="text-danger text-sm font-semibold" data-remove-account>حذف هذا الرقم</button></div>
        </div>`);
        return;
    }
    const remove = event.target.closest('[data-remove-account]');
    if (remove) remove.closest('[data-account-row]').remove();
});
</script>
@endsection
