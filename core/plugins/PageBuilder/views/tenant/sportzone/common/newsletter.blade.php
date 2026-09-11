@php
    $pt = !empty($data['padding_top'])    ? (int)$data['padding_top']    : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="background:var(--sz-navy);padding-top:{{$pt}}px;padding-bottom:{{$pb}}px;position:relative;overflow:hidden;">

    {{-- Decorative diagonal stripe --}}
    <div style="position:absolute;top:0;right:0;width:35%;height:100%;background:var(--sz-red);clip-path:polygon(25% 0,100% 0,100% 100%,0% 100%);opacity:.08;pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <div style="font-size:40px;margin-bottom:12px;">⚽</div>

                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(22px,3vw,32px);font-weight:900;color:#fff;margin-bottom:10px;letter-spacing:-.01em;">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:15px;color:rgba(255,255,255,.75);margin-bottom:28px;max-width:480px;margin-left:auto;margin-right:auto;line-height:1.7;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      onsubmit="szNewsletterSubmit(event, this)">
                    @csrf
                    <input type="email"
                           name="email"
                           required
                           placeholder="{{ __('Enter your email address') }}"
                           style="flex:1;max-width:360px;padding:14px 22px;border:2px solid rgba(255,255,255,.2);border-radius:6px;font-size:14px;outline:none;font-family:inherit;background:rgba(255,255,255,.08);color:#fff;">
                    <button type="submit"
                            style="white-space:nowrap;padding:14px 28px;border-radius:6px;background:var(--sz-red);color:#fff;font-weight:700;font-size:14px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:background .2s;"
                            onmouseover="this.style.background='var(--sz-red-deep)'"
                            onmouseout="this.style.background='var(--sz-red)'">
                        <i class="mdi mdi-email-outline"></i>
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>

                <p style="font-size:12px;color:rgba(255,255,255,.45);margin-top:16px;">
                    {{ __('No spam, unsubscribe anytime.') }}
                </p>
            </div>
        </div>
    </div>
</section>

<script>
function szNewsletterSubmit(e, form) {
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
            form.querySelector('[name="email"]').value = '';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="mdi mdi-email-outline"></i> {{ $data["button_text"] ?? __("Subscribe") }}';
    });
}
</script>
