@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:linear-gradient(135deg, var(--ms-dark, #2A2416) 0%, #4A3820 100%); position:relative; overflow:hidden;">

    {{-- Decorative texture --}}
    <div style="position:absolute;inset:0;background:url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.025\'%3E%3Cpath d=\'M0 40L40 0H20L0 20M40 40V20L20 40\'/%3E%3C/g%3E%3C/svg%3E');pointer-events:none;"></div>

    {{-- Olive accent bar --}}
    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:var(--ms-olive,#7D8C5A);"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div style="font-size:44px; margin-bottom:14px; line-height:1;">🏠</div>

                @if(!empty($data['title']))
                    <h2 style="font-family:var(--ms-font-head,'Lora',Georgia,serif); font-size:clamp(22px,3vw,34px); font-weight:700; color:#fff; margin-bottom:10px; letter-spacing:-.3px;">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:rgba(255,255,255,.7); margin-bottom:30px; max-width:440px; margin-left:auto; margin-right:auto; line-height:1.75;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      onsubmit="msNewsletterSubmit(event, this)">
                    @csrf
                    <input type="email"
                           name="email"
                           required
                           placeholder="{{ __('Enter your email address') }}"
                           style="flex:1; max-width:360px; padding:14px 22px; border:1px solid rgba(255,255,255,.15); border-radius:1px; font-size:14px; outline:none; font-family:inherit; background:rgba(255,255,255,.08); color:#fff;">
                    <button type="submit"
                            style="white-space:nowrap; padding:14px 26px; border-radius:1px; background:var(--ms-olive,#7D8C5A); border:none; color:#fff; font-weight:700; font-size:13px; letter-spacing:.6px; text-transform:uppercase; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all .2s;">
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
function msNewsletterSubmit(e, form) {
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
            btn.style.background = '#A3B07A';
            form.querySelector('[name="email"]').value = '';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '{{ $data["button_text"] ?? __("Subscribe") }}';
    });
}
</script>
