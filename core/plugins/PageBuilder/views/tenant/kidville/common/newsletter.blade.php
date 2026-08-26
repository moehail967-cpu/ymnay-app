@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:linear-gradient(135deg, var(--kv-blue, #1565C0) 0%, #0D47A1 100%); position:relative; overflow:hidden;">

    {{-- Playful floating dots overlay --}}
    <div style="position:absolute;inset:0;background:url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\'%3E%3Cg fill=\'%23FDD835\' fill-opacity=\'0.07\'%3E%3Ccircle cx=\'20\' cy=\'20\' r=\'6\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E') center/40px;pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <div style="font-size:52px; margin-bottom:14px;">🧸</div>

                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(24px,3vw,40px); font-weight:900; color:#fff; margin-bottom:10px;">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:rgba(255,255,255,.8); margin-bottom:30px; max-width:480px; margin-left:auto; margin-right:auto; line-height:1.7;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      onsubmit="kvNewsletterSubmit(event, this)">
                    @csrf
                    <input type="email"
                           name="email"
                           required
                           placeholder="{{ __('Enter your email address') }}"
                           style="flex:1; max-width:380px; padding:15px 24px; border:none; border-radius:50px; font-size:14px; outline:none; font-family:inherit; background:rgba(255,255,255,.15); color:#fff;"
                           onfocus="this.style.background='rgba(255,255,255,0.2)'"
                           onblur="this.style.background='rgba(255,255,255,0.15)'">
                    <button type="submit"
                            style="white-space:nowrap; padding:15px 30px; border-radius:50px; background:var(--kv-yellow,#FDD835); color:#1A1A2E; border:none; font-size:14px; font-weight:900; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:transform .2s, box-shadow .2s; box-shadow:0 4px 16px rgba(0,0,0,.2);"
                            onmouseover="this.style.transform='scale(1.05)'"
                            onmouseout="this.style.transform='scale(1)'">
                        <i class="mdi mdi-email-outline"></i>
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>

                <p style="font-size:12px; color:rgba(255,255,255,.5); margin-top:16px;">
                    {{ __('No spam, unsubscribe anytime.') }}
                </p>
            </div>
        </div>
    </div>
</section>

<script>
function kvNewsletterSubmit(e, form) {
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
            btn.innerHTML = '🎉 {{ __("Subscribed!") }}';
            btn.style.background = '#43A047';
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
