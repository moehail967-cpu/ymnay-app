@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: linear-gradient(135deg, #000 0%, #220000 100%); border-top:2px solid var(--dk-red);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div style="font-size:40px; margin-bottom:12px;">🚗</div>
                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(22px,3vw,30px); font-weight:900; color:var(--dk-text); margin-bottom:8px; text-transform:uppercase; letter-spacing:1px;">{{ $data['title'] }}</h2>
                @endif
                @if(!empty($data['subtitle']))
                    <p style="font-size:14px; color:var(--dk-muted); margin-bottom:24px;">{{ $data['subtitle'] }}</p>
                @endif
                <form class="d-flex gap-2" onsubmit="dkNewsletterSubmit(event, this)" style="max-width:440px;margin:0 auto;">
                    @csrf
                    <input type="email" name="email" required placeholder="{{ __('Enter your email') }}"
                           style="flex:1;padding:12px 16px;background:var(--dk-surface);border:1px solid var(--dk-border);color:var(--dk-text);border-radius:var(--dk-radius);font-size:14px;outline:none;">
                    <button type="submit" style="background:var(--dk-red);color:#fff;border:none;padding:12px 22px;border-radius:var(--dk-radius);font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;text-transform:uppercase;letter-spacing:.5px;">
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
function dkNewsletterSubmit(e, form) {
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true;
    fetch('{{ route("tenant.newsletter.store") }}', {
        method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({email: form.querySelector('[name="email"]').value})
    }).then(r => r.json()).then(res => {
        if (res.msg || res.message) { btn.textContent = '✓'; form.querySelector('[name="email"]').value = ''; }
    }).catch(() => { btn.disabled = false; });
}
</script>
