<!DOCTYPE html>
<html lang="{{ \App\Facades\GlobalLanguage::default_slug() }}" dir="{{ \App\Facades\GlobalLanguage::default_dir() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ get_static_option('site_title') }} — {{ get_static_option('site_tag_line') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ global_asset('assets/common/css/line-awesome.min.css') }}">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#fdf6f0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px}
        .exp-card{background:#fff;border:1px solid #e0d8d0;border-radius:16px;padding:48px 40px;text-align:center;max-width:480px;width:100%}
        .exp-logo{max-width:160px;margin:0 auto 28px}
        .exp-logo img{max-width:100%}
        .exp-icon{font-size:52px;color:#F83A26;margin-bottom:16px}
        .exp-title{font-size:22px;font-weight:800;color:#222;margin-bottom:12px;line-height:1.35}
        .exp-desc{font-size:14px;color:#666;margin-bottom:28px;line-height:1.6}
        .exp-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:#F83A26;color:#fff;border-radius:8px;font-size:14px;font-weight:700;text-decoration:none;transition:background .18s}
        .exp-btn:hover{background:#d42e1c;color:#fff}
        @media(max-width:480px){.exp-card{padding:32px 20px}}
    </style>
    @if(\App\Facades\GlobalLanguage::default_dir() === 'rtl')
        <link rel="stylesheet" href="{{ asset('assets/frontend/css/rtl.css') }}">
    @endif
</head>
<body>
    <div class="exp-card">
        <div class="exp-logo">
            {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
        </div>
        <div class="exp-icon">
            <i class="las la-exclamation-circle"></i>
        </div>
        <h2 class="exp-title">{{ __('Package Plan Expired') }}</h2>
        <p class="exp-desc">{{ __('Your package plan has been expired. Please purchase or renew a package to continue using your store.') }}</p>
        <a href="{{ route('landlord.homepage') . '#price_plan_section' }}" class="exp-btn">
            <i class="las la-tag"></i> {{ __('View Plans') }}
        </a>
    </div>
</body>
</html>
