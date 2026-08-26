@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: linear-gradient(135deg, var(--tc-olive-deep) 0%, var(--tc-dark) 100%); position:relative; overflow:hidden;">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div style="font-size:40px; margin-bottom:12px;">🏕️</div>
                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(22px,3vw,30px); font-weight:700; color:#fff; margin-bottom:8px;">{{ $data['title'] }}</h2>
                @endif
                @if(!empty($data['subtitle']))
                    <p style="font-size:14px; color:rgba(255,255,255,.7); margin-bottom:24px; max-width:400px; margin-left:auto; margin-right:auto;">{{ $data['subtitle'] }}</p>
                @endif
                <form class="d-flex gap-2 justify-content-center" onsubmit="tcNewsletterSubmit(event, this)" style="max-width:440px;margin:0 auto;">
                    @csrf
                    <input type="email" name="email" required placeholder="{{ __('Your email address') }}"
                           style="flex:1; padding:12px 18px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:#fff; border-radius:var(--tc-radius); font-size:14px; outline:none;">
                    <button type="submit" style="background:var(--tc-terra); color:#fff; border:none; padding:12px 22px; border-radius:var(--tc-radius); font-size:13px; font-weight:700; cursor:pointer; white-space:nowrap;">
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>
                <p style="font-size:12px; color:rgba(255,255,255,.5); margin-top:12px;">{{ __('No spam. Unsubscribe anytime.') }}</p>
            </div>
        </div>
    </div>
</section>

<script>
function tcNewsletterSubmit(e, form) {
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true;
    btn.textContent = '...';
    fetch('{{ route("tenant.newsletter.store") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({email: form.querySelector('[name="email"]').value})
    }).then(r => r.json()).then(res => {
        if (res.msg || res.message) {
            btn.textContent = '✓ Subscribed';
            form.querySelector('[name="email"]').value = '';
        }
    }).catch(() => { btn.disabled = false; btn.textContent = '{{ $data["button_text"] ?? "Subscribe" }}'; });
}
</script>
