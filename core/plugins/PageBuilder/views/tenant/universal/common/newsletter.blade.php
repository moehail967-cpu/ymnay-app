@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $bg = !empty($data['bg_color']) ? $data['bg_color'] : 'var(--sectionC,#0d6efd)';
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:{{$bg}};">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                @if(!empty($data['title']))
                    <h2 style="font-size:2rem; font-weight:700; color:#fff; margin-bottom:.75rem;">
                        {{ $data['title'] }}
                    </h2>
                @endif
                @if(!empty($data['subtitle']))
                    <p style="font-size:1.1rem; color:rgba(255,255,255,.85); margin-bottom:2rem;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center" onsubmit="return false;">
                    <input type="email"
                           class="form-control form-control-lg"
                           style="border-radius:8px; border:none; max-width:360px;"
                           placeholder="{{ __('Enter your email address') }}">
                    <button type="submit" class="btn btn-light btn-lg fw-semibold px-5"
                            style="border-radius:8px; white-space:nowrap; color:var(--sectionC,#0d6efd);">
                        {{ $data['button_text'] ?? __('Subscribe') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
