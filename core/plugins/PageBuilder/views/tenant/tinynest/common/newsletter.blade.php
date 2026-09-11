@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:linear-gradient(135deg, var(--tn-dark, #2D1B2E) 0%, #4A2040 100%); position:relative; overflow:hidden;">

    {{-- Decorative blobs --}}
    <div style="position:absolute;top:-80px;left:-60px;width:250px;height:250px;background:var(--tn-blush,#F2A7C3);opacity:.08;border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-60px;right:-40px;width:200px;height:200px;background:var(--tn-blue,#A7C8F2);opacity:.08;border-radius:50%;pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div style="font-size:44px; margin-bottom:14px; line-height:1;">🍼</div>

                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(22px,3vw,32px); font-weight:800; color:#fff; margin-bottom:10px; font-family:inherit;">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:rgba(255,255,255,.75); margin-bottom:28px; max-width:460px; margin-left:auto; margin-right:auto; line-height:1.7;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      onsubmit="tnNewsletterSubmit(event, this)">
                    @csrf
                    <input type="email"
                           name="email"
                           required
                           placeholder="{{ __('Enter your email address') }}"
                           style="flex:1; max-width:360px; padding:14px 22px; border:none; border-radius:50px; font-size:14px; outline:none; font-family:inherit; background:#fff; color:#333;">
                    <button type="submit"
                            style="white-space:nowrap; padding:14px 28px; border-radius:50px; background:var(--tn-blush,#F2A7C3); border:none; color:#fff; font-weight:700; font-size:14px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all .2s;">
                        <i class="mdi mdi-email-outline"></i>
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>

                <p style="font-size:12px; color:rgba(255,255,255,.45); margin-top:16px;">
                    {{ __('No spam, unsubscribe anytime.') }}
                </p>
            </div>
        </div>
    </div>
</section>

<script>
function tnNewsletterSubmit(e, form) {
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
            btn.style.background = '#A7E2CE';
            form.querySelector('[name="email"]').value = '';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '{{ $data["button_text"] ?? __("Subscribe") }}';
    });
}
</script>
