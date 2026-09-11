{{-- DriveKit: Breadcrumb / Page Banner — hidden on homepage --}}
@if(request()->route()?->getName() !== 'tenant.frontend.homepage')
<div style="background:var(--dk-carbon);border-bottom:2px solid rgba(229,48,48,.25);padding:28px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background-image:repeating-linear-gradient(90deg,rgba(255,255,255,.012) 0,rgba(255,255,255,.012) 1px,transparent 1px,transparent 60px);pointer-events:none;"></div>
    <div style="position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(to bottom,transparent,var(--dk-red),transparent);"></div>
    <div class="container" style="position:relative;z-index:1;">
        <h1 style="font-size:clamp(18px,2.5vw,26px);font-weight:900;color:var(--dk-white);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">
            @yield('page-title')
        </h1>
        <nav style="display:flex;align-items:center;gap:6px;font-size:12px;">
            <a href="{{ theme_home_url() }}" style="color:var(--dk-silver);text-decoration:none;transition:color .2s;font-weight:600;letter-spacing:.3px;"
               onmouseover="this.style.color='var(--dk-red)'" onmouseout="this.style.color='var(--dk-silver)'">
                {{ __('Home') }}
            </a>
            <i class="mdi mdi-chevron-right" style="color:var(--dk-border);font-size:14px;"></i>
            <span style="color:var(--dk-white);font-weight:600;letter-spacing:.3px;">@yield('page-title')</span>
        </nav>
    </div>
</div>
@endif
