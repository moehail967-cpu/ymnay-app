@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: linear-gradient(135deg, #000 0%, #001A00 100%); border-top: 1px solid var(--fp-border); position:relative; overflow:hidden;">
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 50% 100%,var(--fp-green-glow) 0%,transparent 70%);pointer-events:none;"></div>
    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div style="font-size:40px; margin-bottom:12px;">💪</div>
                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(22px,3vw,30px); font-weight:900; color:var(--fp-text); margin-bottom:8px; letter-spacing:-.5px;">{{ $data['title'] }}</h2>
                @endif
                @if(!empty($data['subtitle']))
                    <p style="font-size:14px; color:var(--fp-muted); margin-bottom:24px; max-width:400px; margin-left:auto; margin-right:auto;">{{ $data['subtitle'] }}</p>
                @endif
                <form class="d-flex gap-2 justify-content-center" onsubmit="fpNewsletterSubmit(event, this)" style="max-width:440px;margin:0 auto;">
                    @csrf
                    <input type="email" name="email" required placeholder="{{ __('Enter your email') }}"
                           style="flex:1; padding:12px 18px; background:var(--fp-surface); border:1px solid var(--fp-border); color:var(--fp-text); border-radius:var(--fp-radius); font-size:14px; outline:none;">
                    <button type="submit" style="background:var(--fp-green); color:#000; border:none; padding:12px 24px; border-radius:var(--fp-radius); font-size:13px; font-weight:800; cursor:pointer; white-space:nowrap; letter-spacing:.5px;">
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>
                <p style="font-size:12px; color:var(--fp-muted); margin-top:14px;">{{ __('No spam. Unsubscribe anytime.') }}</p>
            </div>
        </div>
    </div>
</section>

<script>
function fpNewsletterSubmit(e, form) {
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
            btn.style.background = '#00CC33';
            form.querySelector('[name="email"]').value = '';
        }
    }).catch(() => {
        btn.disabled = false;
        btn.textContent = '{{ $data["button_text"] ?? "Subscribe" }}';
    });
}
</script>
