@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:linear-gradient(135deg, var(--bk-dark) 0%, #5C2D1A 100%); position:relative; overflow:hidden;">

    {{-- Subtle dot pattern overlay --}}
    <div style="position:absolute;inset:0;background:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">

                <div style="font-size:48px; margin-bottom:14px;">🥐</div>

                @if(!empty($data['title']))
                    <h2 style="font-size:clamp(22px,3vw,32px); font-weight:900; color:#fff; font-family:var(--bk-font-head); margin-bottom:10px;">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:rgba(255,255,255,.75); margin-bottom:28px; max-width:480px; margin-left:auto; margin-right:auto;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="bk-newsletter-form d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      onsubmit="bkNewsletterSubmit(event, this)">
                    @csrf
                    <input type="email"
                           name="email"
                           required
                           placeholder="{{ __('Your email address') }}"
                           style="flex:1; max-width:360px; padding:14px 22px; border:none; border-radius:50px; font-size:14px; outline:none; font-family:inherit;">
                    <button type="submit"
                            class="bk-btn bk-btn-rose"
                            style="white-space:nowrap; border-radius:50px; padding:14px 30px; border:none; cursor:pointer;">
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
function bkNewsletterSubmit(e, form) {
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
            btn.style.background = 'var(--bk-gold)';
            form.querySelector('[name="email"]').value = '';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '{{ $data["button_text"] ?? __("Subscribe") }}';
    });
}
</script>
