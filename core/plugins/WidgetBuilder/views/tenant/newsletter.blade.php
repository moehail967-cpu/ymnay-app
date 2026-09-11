@php
    $bg = !empty($bg_color) ? $bg_color : 'var(--main-color-one, #0d6efd)';
    $newsletter_route = '#';
    try {
        $newsletter_route = route('tenant.admin.newsletter.new.add');
    } catch (\Exception $e) {}
@endphp

<section class="xgpb-newsletter" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px; background:{{ $bg }};">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                @if(!empty($title))
                    <h2 class="xgpb-newsletter-title" style="color:{{ $text_color ?? '#fff' }}">{{ $title }}</h2>
                @endif
                @if(!empty($subtitle))
                    <p class="xgpb-newsletter-subtitle" style="color:{{ $text_color ?? '#fff' }}; opacity:.85;">{{ $subtitle }}</p>
                @endif

                <form class="xgpb-newsletter-form d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4"
                      action="{{ $newsletter_route }}" method="POST">
                    @csrf
                    <input type="email" name="email"
                           class="form-control form-control-lg xgpb-newsletter-input"
                           placeholder="{{ $placeholder ?? __('Enter your email address') }}"
                           required>
                    <button type="submit" class="btn btn-light btn-lg xgpb-newsletter-btn fw-semibold px-5">
                        {{ $button_text ?? __('Subscribe') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.xgpb-newsletter-title { font-size: 1.75rem; font-weight: 700; margin-bottom: .5rem; }
.xgpb-newsletter-subtitle { font-size: 1rem; margin-bottom: 0; }
.xgpb-newsletter-input { border: none; border-radius: 8px; max-width: 360px; flex: 1; }
.xgpb-newsletter-btn { border-radius: 8px; white-space: nowrap; color: var(--main-color-one, #0d6efd); }
</style>
