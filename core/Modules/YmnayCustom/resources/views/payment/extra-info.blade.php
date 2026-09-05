@php($ymnayWallets = \Modules\YmnayCustom\Support\WalletRepository::availableForCheckout())

<div class="ymnay_manual_wallet_extra_field" style="display:none;width:100%;margin-top:16px">
    @if(empty($ymnayWallets))
        <div class="alert alert-warning">لا توجد محافظ دفع يدوي متاحة حاليًا.</div>
    @else
        <div class="ymnay-wallet-grid">
            @foreach($ymnayWallets as $wallet)
                <label class="ymnay-wallet-card" data-wallet-card>
                    <input type="radio" name="ymnay_wallet_id" value="{{$wallet['id']}}" class="ymnay-wallet-radio">
                    @if($wallet['logo_url'])
                        <img src="{{$wallet['logo_url']}}" alt="{{$wallet['name']}}">
                    @endif
                    <span class="ymnay-wallet-copy">
                        <strong>{{$wallet['name']}}</strong>
                        @if($wallet['recipient_name'])<span class="ymnay-recipient">اسم المستلم: {{$wallet['recipient_name']}}</span>@endif
                        @if(!empty($wallet['accounts']))
                            <span class="ymnay-account-list">
                            @foreach($wallet['accounts'] as $account)
                                <label class="ymnay-account-option">
                                    <input type="radio" name="ymnay_wallet_account" value="{{$account['key']}}" class="ymnay-wallet-account" disabled>
                                    <span><b>{{$account['currency']}}</b><strong>{{$account['account_number']}}</strong>@if($account['description'])<small>{{$account['description']}}</small>@endif</span>
                                </label>
                            @endforeach
                            </span>
                        @endif
                        @if($wallet['description'])<small>{!! nl2br(e($wallet['description'])) !!}</small>@endif
                    </span>
                </label>
            @endforeach
        </div>
        <div class="form-group mt-3">
            <label class="form-label"><strong>صورة سند التحويل</strong></label>
            <input type="file" name="ymnay_wallet_proof" class="form-control ymnay-wallet-proof" accept="image/jpeg,image/png,image/webp">
            <small class="text-muted">الصيغ المقبولة: JPG أو PNG أو WEBP، وبحد أقصى 4 ميجابايت.</small>
        </div>
        <div class="alert alert-info mt-3 mb-0">
            سيبقى الطلب معلقًا حتى يراجع مالك المتجر سند التحويل ويعتمده.
        </div>
    @endif
</div>

<style>
    .ymnay-wallet-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}
    .ymnay-wallet-card{display:flex;align-items:flex-start;gap:10px;padding:14px;border:1px solid #d8dee6;border-radius:12px;cursor:pointer;background:#fff}
    .ymnay-wallet-card.is-selected{border-color:#0f766e;box-shadow:0 0 0 2px rgba(15,118,110,.13)}
    .ymnay-wallet-card img{width:54px;height:54px;object-fit:contain;border-radius:9px;border:1px solid #edf0f4}
    .ymnay-wallet-copy{display:flex;flex-direction:column;gap:7px;min-width:0}.ymnay-wallet-copy>strong{color:#17212b}.ymnay-wallet-copy small{color:#5f6b76;line-height:1.45}
    .ymnay-recipient{font-weight:800;color:#17212b;font-size:15px}.ymnay-account-list{display:grid;gap:7px}.ymnay-account-option{display:flex;gap:8px;padding:9px;border:1px solid #d8dee6;border-radius:9px;background:#f8fafc;cursor:pointer}.ymnay-account-option input{margin-top:5px}.ymnay-account-option span{display:flex;flex-direction:column;gap:2px}.ymnay-account-option strong{font-size:18px;letter-spacing:.4px;color:#0f172a;font-weight:800}.ymnay-account-option small{font-size:12px}
</style>
<script>
(function(){
    function syncYmnayWalletFields(gateway){
        var box=document.querySelector('.ymnay_manual_wallet_extra_field');
        if(!box)return;
        var active=gateway==='ymnay_manual_wallet';
        box.style.display=active?'block':'none';
        box.querySelectorAll('input').forEach(function(input){
            if(input.type==='file') input.required=active;
            if(input.name==='ymnay_wallet_id') input.required=active;
            if(input.name==='ymnay_wallet_account') { input.disabled=!active; input.required=active; }
        });
        syncWalletAccounts();
    }
    function syncWalletAccounts(){
        document.querySelectorAll('[data-wallet-card]').forEach(function(card){
            var selected=card.querySelector('.ymnay-wallet-radio')?.checked;
            card.querySelectorAll('.ymnay-wallet-account').forEach(function(account){
                account.disabled=!selected;
                account.required=!!selected;
            });
        });
    }
    document.addEventListener('click',function(e){
        var gateway=e.target.closest('.payment-gateway-wrapper li[data-gateway]');
        if(gateway)syncYmnayWalletFields(gateway.getAttribute('data-gateway'));
        var card=e.target.closest('[data-wallet-card]');
        if(card){
            document.querySelectorAll('[data-wallet-card]').forEach(function(item){item.classList.remove('is-selected')});
            card.classList.add('is-selected');
            card.querySelector('.ymnay-wallet-radio').checked=true;
            syncWalletAccounts();
        }
    });
    document.addEventListener('DOMContentLoaded',function(){
        var selected=document.querySelector('.payment-gateway-wrapper li.selected[data-gateway]');
        syncYmnayWalletFields(selected?selected.getAttribute('data-gateway'):null);
    });
})();
</script>
