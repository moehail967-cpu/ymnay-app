@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:linear-gradient(135deg, var(--gc-dark, #2C1810) 0%, #4A2A18 100%); position:relative; overflow:hidden;">

    {{-- Decorative ring pattern overlay --}}
    <div style="position:absolute;inset:0;background:url('data:image/svg+xml,%3Csvg width=\'80\' height=\'80\' viewBox=\'0 0 80 80\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\'%3E%3Cg stroke=\'%23C8A870\' stroke-opacity=\'0.06\' stroke-width=\'1\'%3E%3Ccircle cx=\'40\' cy=\'40\' r=\'30\'/%3E%3Ccircle cx=\'40\' cy=\'40\' r=\'20\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E') center/80px;pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div style="font-size:44px; margin-bottom:14px;">💍</div>

                @if(!empty($data['title']))
                    <h2 style="font-family:'Cormorant Garamond',Georgia,serif; font-size:clamp(24px,3vw,38px); font-weight:700; color:#fff; margin-bottom:10px; letter-spacing:.5px;">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:rgba(255,255,255,.7); margin-bottom:30px; max-width:440px; margin-left:auto; margin-right:auto; line-height:1.7;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      onsubmit="gcNewsletterSubmit(event, this)">
                    @csrf
                    <input type="email"
                           name="email"
                           required
                           placeholder="{{ __('Enter your email address') }}"
                           style="flex:1; max-width:360px; padding:14px 24px; border:1px solid rgba(200,168,112,.4); border-radius:4px; font-size:14px; outline:none; font-family:inherit; background:rgba(255,255,255,.08); color:#fff;">
                    <button type="submit"
                            style="white-space:nowrap; padding:14px 30px; border-radius:4px; background:var(--gc-gold,#C8A870); color:#2C1810; border:none; font-size:12px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:opacity .2s;">
                        <i class="mdi mdi-email-outline"></i>
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>

                <p style="font-size:12px; color:rgba(255,255,255,.4); margin-top:16px;">
                    {{ __('No spam, unsubscribe anytime.') }}
                </p>
            </div>
        </div>
    </div>
</section>

<script>
function gcNewsletterSubmit(e, form) {
    e.preventDefault();
    const email = form.querySelector('[name="email"]').value;
    const btn   = form.querySelector('[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> ...';

    fetch('{{ route("tenant.newsletter.store") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({email})
    })
    .then(r => r.json())
    .then(res => {
        if (res.msg || res.message) {
            btn.innerHTML = '<i class="mdi mdi-check"></i> {{ __("Subscribed!") }}';
            btn.style.background = '#27AE60';
            btn.style.color = '#fff';
            form.querySelector('[name="email"]').value = '';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '{{ $data["button_text"] ?? __("Subscribe") }}';
    });
}
</script>
