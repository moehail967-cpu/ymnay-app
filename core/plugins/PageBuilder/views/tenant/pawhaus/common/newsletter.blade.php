@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: linear-gradient(135deg, var(--ph-dark) 0%, #3A2A18 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div style="font-size:40px; margin-bottom:12px;">🐾</div>
                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(22px,3vw,30px); font-weight:800; color:#fff; margin-bottom:8px;">{{ $data['title'] }}</h2>
                @endif
                @if(!empty($data['subtitle']))
                    <p style="font-size:14px; color:rgba(255,255,255,.7); margin-bottom:24px;">{{ $data['subtitle'] }}</p>
                @endif
                <form class="d-flex gap-2" onsubmit="phNewsletterSubmit(event, this)" style="max-width:440px;margin:0 auto;background:#fff;border-radius:50px;overflow:hidden;padding:4px;">
                    @csrf
                    <input type="email" name="email" required placeholder="{{ __('Enter your email') }}"
                           style="flex:1;border:none;padding:10px 18px;font-size:14px;outline:none;color:var(--ph-dark);background:transparent;">
                    <button type="submit" style="background:var(--ph-terra);color:#fff;border:none;padding:10px 22px;border-radius:50px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;">
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
function phNewsletterSubmit(e, form) {
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true;
    fetch('{{ route("tenant.newsletter.store") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({email: form.querySelector('[name="email"]').value})
    }).then(r => r.json()).then(res => {
        if (res.msg || res.message) { btn.textContent = '✓ Subscribed'; form.querySelector('[name="email"]').value = ''; }
    }).catch(() => { btn.disabled = false; });
}
</script>
