@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:linear-gradient(135deg, #1A1208 0%, #3D2B0A 100%); position:relative; overflow:hidden;">

    {{-- Gold shimmer orbs --}}
    <div style="position:absolute; top:-60px; right:-60px; width:300px; height:300px; border-radius:50%; background:radial-gradient(circle, rgba(201,164,76,.15) 0%, transparent 70%); pointer-events:none;"></div>
    <div style="position:absolute; bottom:-40px; left:-40px; width:200px; height:200px; border-radius:50%; background:radial-gradient(circle, rgba(201,164,76,.1) 0%, transparent 70%); pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <div style="font-size:40px; margin-bottom:14px;">✨</div>

                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(22px,3vw,34px); font-weight:800; color:#fff; margin-bottom:12px; letter-spacing:-.01em;">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:rgba(255,255,255,.7); margin-bottom:32px; max-width:480px; margin-left:auto; margin-right:auto; line-height:1.7;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      onsubmit="glNewsletterSubmit(event, this)">
                    @csrf
                    <input type="email"
                           name="email"
                           required
                           placeholder="{{ __('Enter your email address') }}"
                           style="flex:1; max-width:360px; padding:14px 22px; border:1px solid rgba(201,164,76,.4); background:rgba(255,255,255,.08); color:#fff; font-size:14px; outline:none; font-family:inherit; border-radius:100px;"
                           onfocus="this.style.borderColor='rgba(201,164,76,.8)'" onblur="this.style.borderColor='rgba(201,164,76,.4)'">
                    <button type="submit"
                            style="display:inline-flex; align-items:center; gap:8px; white-space:nowrap; padding:14px 30px; background:#C9A44C; color:#fff; border:none; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; border-radius:100px; transition:background .2s; letter-spacing:.02em;"
                            onmouseover="this.style.background='#A8852E'" onmouseout="this.style.background='#C9A44C'">
                        <i class="mdi mdi-email-outline"></i>
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>

                <p style="font-size:12px; color:rgba(255,255,255,.35); margin-top:18px;">
                    {{ __('No spam. Unsubscribe anytime.') }}
                </p>
            </div>
        </div>
    </div>
</section>

<script>
function glNewsletterSubmit(e, form) {
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
