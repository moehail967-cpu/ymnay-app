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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="lnd-label">{{__('Wallet number')}}</label><input class="lnd-input" name="wallets[{{$wallet->id}}][account_number]" value="{{$row['account_number'] ?? ''}}"></div>
                    <div><label class="lnd-label">{{__('Recipient name')}}</label><input class="lnd-input" name="wallets[{{$wallet->id}}][recipient_name]" value="{{$row['recipient_name'] ?? ''}}"></div>
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
