@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Ymnay Manual Wallets')}} @endsection

@section('content')
<x-landlord-error-msg/><x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main">
        <h3 class="text-sm font-bold text-dark">{{__('Add Manual Wallet')}}</h3>
        <p class="text-xs text-muted mt-1">{{__('Create the wallet name, instructions and logo. No wallet is added automatically.')}}</p>
    </div>
    <form method="post" enctype="multipart/form-data" action="{{route('ymnaycustom.landlord.wallets.store')}}" class="p-4 sm:p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="lnd-label">{{__('Wallet name')}}</label><input class="lnd-input" name="name" required value="{{old('name')}}"></div>
            <div><label class="lnd-label">{{__('Logo')}}</label><input class="lnd-input" type="file" name="logo" accept="image/*" required></div>
        </div>
        <div><label class="lnd-label">{{__('Description and transfer instructions')}}</label><textarea class="lnd-input" name="description" rows="4" required>{{old('description')}}</textarea></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="lnd-label">{{__('Display order')}}</label><input class="lnd-input" type="number" min="0" name="sort_order" value="{{old('sort_order',0)}}"></div>
            <label class="flex items-center gap-2 pt-7"><input type="checkbox" name="status" value="1" checked> {{__('Active')}}</label>
        </div>
        <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white font-semibold"><i class="las la-plus"></i>{{__('Add Wallet')}}</button>
    </form>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
@forelse($wallets as $wallet)
    <div class="bg-surface rounded-xl shadow-main border border-main p-5">
        <form method="post" enctype="multipart/form-data" action="{{route('ymnaycustom.landlord.wallets.update',$wallet)}}" class="space-y-4">
            @csrf @method('PUT')
            <div class="flex items-center gap-3">
                @if($wallet->logo_url)<img src="{{$wallet->logo_url}}" class="w-16 h-16 object-contain rounded-xl border border-main" alt="">@endif
                <div class="flex-1"><label class="lnd-label">{{__('Wallet name')}}</label><input class="lnd-input" name="name" value="{{$wallet->name}}" required></div>
            </div>
            <div><label class="lnd-label">{{__('Description and transfer instructions')}}</label><textarea class="lnd-input" name="description" rows="4" required>{{$wallet->description}}</textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="lnd-label">{{__('Replace logo')}}</label><input class="lnd-input" type="file" name="logo" accept="image/*"></div>
                <div><label class="lnd-label">{{__('Display order')}}</label><input class="lnd-input" type="number" min="0" name="sort_order" value="{{$wallet->sort_order}}"></div>
            </div>
            <label class="flex items-center gap-2"><input type="checkbox" name="status" value="1" @checked($wallet->status)> {{__('Active')}}</label>
            <button class="px-4 py-2 rounded-lg bg-primary text-white">{{__('Save Changes')}}</button>
        </form>
        <form method="post" action="{{route('ymnaycustom.landlord.wallets.destroy',$wallet)}}" class="mt-3" onsubmit="return confirm('{{__('Delete this wallet?')}}')">
            @csrf @method('DELETE')<button class="text-danger text-sm">{{__('Delete Wallet')}}</button>
        </form>
    </div>
@empty
    <div class="bg-surface rounded-xl border border-main p-6 text-muted">{{__('No wallets have been added yet.')}}</div>
@endforelse
</div>
@endsection
