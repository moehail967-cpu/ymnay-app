@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:linear-gradient(135deg, #000 0%, #1A1200 100%); position:relative; overflow:hidden;">

    {{-- Gold accent lines --}}
    <div style="position:absolute; top:0; left:0; right:0; height:1px; background:linear-gradient(90deg, transparent, rgba(201,168,76,.5), transparent); pointer-events:none;"></div>
    <div style="position:absolute; bottom:0; left:0; right:0; height:1px; background:linear-gradient(90deg, transparent, rgba(201,168,76,.5), transparent); pointer-events:none;"></div>
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:600px; height:600px; border-radius:50%; background:radial-gradient(circle, rgba(201,168,76,.04) 0%, transparent 70%); pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <div style="font-size:40px; margin-bottom:16px;">💎</div>

                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(24px,3vw,36px); font-weight:700; color:#E8E0D0; margin-bottom:12px; font-family:'Cinzel',Georgia,serif; letter-spacing:.06em;">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:rgba(232,224,208,.6); margin-bottom:36px; max-width:480px; margin-left:auto; margin-right:auto; line-height:1.75;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      onsubmit="lgNewsletterSubmit(event, this)">
                    @csrf
                    <input type="email"
                           name="email"
                           required
                           placeholder="{{ __('Enter your email address') }}"
                           style="flex:1; max-width:360px; padding:14px 24px; border:1px solid #2A2410; background:#0E0E0E; color:#E8E0D0; font-size:14px; outline:none; font-family:inherit;"
                           onfocus="this.style.borderColor='rgba(201,168,76,.6)'" onblur="this.style.borderColor='#2A2410'">
                    <button type="submit"
                            style="display:inline-flex; align-items:center; gap:8px; white-space:nowrap; padding:14px 32px; background:#C9A84C; color:#080808; border:none; font-size:11px; font-weight:700; cursor:pointer; font-family:'Cinzel',Georgia,serif; letter-spacing:.1em; text-transform:uppercase; transition:background .2s;"
                            onmouseover="this.style.background='#A88530'" onmouseout="this.style.background='#C9A84C'">
                        {{ $data['button_text'] ?? __('Subscribe') }}
                        <i class="mdi mdi-arrow-right"></i>
                    </button>
                </form>

                <p style="font-size:11px; color:rgba(232,224,208,.3); margin-top:20px; letter-spacing:.04em;">
                    {{ __('Exclusive offers. No spam. Unsubscribe anytime.') }}
                </p>
            </div>
        </div>
    </div>
</section>

<script>
function lgNewsletterSubmit(e, form) {
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
            btn.innerHTML = '<i class="mdi mdi-check"></i> {{ __("Subscribed") }}';
            btn.style.background = '#27AE60';
            btn.style.color = '#fff';
            form.querySelector('[name="email"]').value = '';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '{{ $data["button_text"] ?? __("Subscribe") }} <i class="mdi mdi-arrow-right"></i>';
    });
}
</script>
