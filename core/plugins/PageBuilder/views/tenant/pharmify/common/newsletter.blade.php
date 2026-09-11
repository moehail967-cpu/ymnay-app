@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:linear-gradient(135deg,#1A2332 0%,#0A3A50 100%); position:relative; overflow:hidden;">

    {{-- Subtle grid overlay --}}
    <div style="position:absolute;inset:0;background:url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\'%3E%3Cg fill=\'%2300897B\' fill-opacity=\'0.05\'%3E%3Crect x=\'0\' y=\'0\' width=\'1\' height=\'40\'/%3E%3Crect x=\'0\' y=\'0\' width=\'40\' height=\'1\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <div style="font-size:40px; margin-bottom:12px;">💊</div>

                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(22px,3vw,32px); font-weight:800; color:#fff; margin-bottom:10px;">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:rgba(255,255,255,.75); margin-bottom:28px; max-width:480px; margin-left:auto; margin-right:auto;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      onsubmit="pfNewsletterSubmit(event, this)">
                    @csrf
                    <input type="email"
                           name="email"
                           required
                           placeholder="{{ __('Enter your email address') }}"
                           style="flex:1; max-width:360px; padding:14px 22px; border:none; font-size:14px; outline:none; font-family:inherit; background:#fff; color:#1A2332;">
                    <button type="submit"
                            style="display:inline-flex; align-items:center; gap:8px; white-space:nowrap; padding:14px 30px; background:#00897B; color:#fff; border:none; font-size:14px; font-weight:600; cursor:pointer; font-family:inherit; transition:background .2s;"
                            onmouseover="this.style.background='#006358'" onmouseout="this.style.background='#00897B'">
                        <i class="mdi mdi-email-outline"></i>
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>

                <p style="font-size:12px; color:rgba(255,255,255,.4); margin-top:16px;">
                    {{ __('No spam. Unsubscribe anytime.') }}
                </p>
            </div>
        </div>
    </div>
</section>

<script>
function pfNewsletterSubmit(e, form) {
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
