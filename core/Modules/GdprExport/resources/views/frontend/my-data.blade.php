@extends(View::exists('theme::frontend.user.user-master') ? 'theme::frontend.user.user-master' : 'theme::frontend.user.dashboard.user-master')

@section('title') {{ __('My Data (GDPR)') }} @endsection
@section('page-title') {{ __('My Data') }} @endsection

@section('style')
@parent
<style>
/* ── GDPR Export Plugin ─────────────────────────────────────────────────────
   CSS variable chains resolve each theme's own tokens in order:
   Dark  → VelvetLux · DriveKit · FitPeak · TechZone
   Light → BakerCo · ChefHome · Freshmart · GlowLab · GoldCraft
           KidVille · Pharmacy · SportZone · TrailCo
   ──────────────────────────────────────────────────────────────────────── */
.gdpr-wrap {
    --gdpr-card:
        var(--lx-card,
        var(--vl-card,
        var(--dk-card,
        var(--fp-card,
        var(--tz-card,
        var(--fm-white,
        var(--kv-white,
        var(--pf-white,
        var(--sz-white,
        #ffffff)))))))));

    --gdpr-border:
        var(--lx-border,
        var(--vl-border,
        var(--dk-border,
        var(--fp-border,
        var(--tz-border,
        var(--bk-border,
        var(--ch-border,
        var(--fm-border,
        var(--gl-border,
        var(--gc-border,
        var(--kv-border,
        var(--pf-border,
        var(--sz-border,
        var(--tr-border,
        #dee2e6))))))))))))));

    --gdpr-text:
        var(--lx-white,
        var(--vl-ivory,
        var(--dk-white,
        var(--fp-text,
        var(--tz-text,
        var(--bk-dark,
        var(--ch-dark,
        var(--fm-dark,
        var(--gl-dark,
        var(--gc-dark,
        var(--kv-dark,
        var(--pf-dark,
        var(--sz-dark,
        var(--tr-bark,
        #212529))))))))))))));

    --gdpr-muted:
        var(--lx-muted,
        var(--vl-muted,
        var(--dk-muted,
        var(--fp-muted,
        var(--tz-muted,
        var(--bk-muted,
        var(--ch-muted,
        var(--fm-muted,
        var(--gl-muted,
        var(--gc-muted,
        var(--kv-muted,
        var(--pf-muted,
        var(--sz-muted,
        var(--tr-stone,
        #6c757d))))))))))))));

    --gdpr-accent:
        var(--lx-gold,
        var(--vl-champagne,
        var(--dk-red,
        var(--fp-green,
        var(--tz-blue,
        var(--bk-rose,
        var(--ch-red,
        var(--fm-green,
        var(--gl-gold,
        var(--gc-rose,
        var(--kv-blue,
        var(--pf-teal,
        var(--sz-red,
        var(--tr-olive,
        #0d6efd))))))))))))));

    --gdpr-surface:
        var(--lx-surface,
        var(--vl-surface,
        var(--dk-surface,
        var(--fp-surface,
        var(--tz-surface,
        var(--fm-bg,
        var(--kv-light,
        var(--pf-bg,
        var(--sz-bg,
        var(--tr-cream,
        var(--bk-rose-light,
        var(--gl-white,
        var(--gc-ivory,
        #f8f9fa)))))))))))));
}

/* ── Layout ── */
.gdpr-wrap .gdpr-card {
    background: var(--gdpr-card);
    border: 1px solid var(--gdpr-border);
    overflow: hidden;
}
.gdpr-wrap .gdpr-card-body { padding: 24px; }

.gdpr-wrap .gdpr-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

/* ── Typography ── */
.gdpr-wrap .gdpr-page-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 6px;
    color: var(--gdpr-text);
    letter-spacing: 0;
    text-transform: none;
}
.gdpr-wrap .gdpr-subtitle {
    font-size: 13px;
    color: var(--gdpr-muted);
    margin: 0;
    line-height: 1.5;
}

/* ── Button ── */
.gdpr-wrap .gdpr-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 20px;
    background: var(--gdpr-accent);
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-decoration: none;
    white-space: nowrap;
    transition: opacity .15s;
}
.gdpr-wrap .gdpr-btn:hover { opacity: 0.85; color: #fff; }

/* ── Alerts ── */
.gdpr-wrap .gdpr-alert-success {
    padding: 12px 16px;
    border: 1px solid rgba(72,187,120,.4);
    color: #48BB78;
    background: rgba(72,187,120,.08);
    margin-bottom: 20px;
    font-size: 13px;
}
.gdpr-wrap .gdpr-alert-error {
    padding: 12px 16px;
    border: 1px solid rgba(229,62,62,.4);
    color: #E53E3E;
    background: rgba(229,62,62,.08);
    margin-bottom: 20px;
    font-size: 13px;
}

/* ── Table ── */
.gdpr-wrap .gdpr-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.gdpr-wrap .gdpr-table thead tr {
    background: var(--gdpr-surface);
    border-bottom: 1px solid var(--gdpr-border);
}
.gdpr-wrap .gdpr-table thead th {
    padding: 11px 16px;
    text-align: left;
    color: var(--gdpr-muted);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.gdpr-wrap .gdpr-table tbody tr { border-bottom: 1px solid var(--gdpr-border); }
.gdpr-wrap .gdpr-table tbody tr:last-child { border-bottom: none; }
.gdpr-wrap .gdpr-table tbody td {
    padding: 13px 16px;
    color: var(--gdpr-muted);
    vertical-align: middle;
}

/* ── Status badges ── */
.gdpr-wrap .gdpr-badge {
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}
.gdpr-wrap .gdpr-badge-pending,
.gdpr-wrap .gdpr-badge-processing { background: rgba(214,158,46,.15); color: #D69E2E; border: 1px solid rgba(214,158,46,.35); }
.gdpr-wrap .gdpr-badge-ready      { background: rgba(72,187,120,.15);  color: #48BB78; border: 1px solid rgba(72,187,120,.35); }
.gdpr-wrap .gdpr-badge-downloaded { background: rgba(113,128,150,.15); color: #718096; border: 1px solid rgba(113,128,150,.35); }
.gdpr-wrap .gdpr-badge-expired    { background: rgba(229,62,62,.15);   color: #E53E3E; border: 1px solid rgba(229,62,62,.35); }

/* ── Download link ── */
.gdpr-wrap .gdpr-download-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 12px;
    background: rgba(72,187,120,.12);
    color: #48BB78;
    border: 1px solid rgba(72,187,120,.35);
    font-size: 11px;
    text-decoration: none;
    transition: background .15s;
}
.gdpr-wrap .gdpr-download-btn:hover { background: rgba(72,187,120,.22); color: #48BB78; }

/* ── Empty state ── */
.gdpr-wrap .gdpr-empty {
    padding: 64px 24px;
    text-align: center;
    color: var(--gdpr-muted);
}
.gdpr-wrap .gdpr-empty-icon {
    font-size: 52px;
    display: block;
    margin-bottom: 16px;
    opacity: 0.25;
    color: var(--gdpr-text);
}
</style>
@endsection

{{-- 'section' — bakerco, chefhome, drivekit, glowlab, goldcraft, kidville --}}
@section('section')
    @include('gdpr-export::frontend.my-data-body')
@endsection

{{-- 'dashboard-content' — velvetlux, fitpeak, freshmart, pharmacy, trailco --}}
@section('dashboard-content')
    @include('gdpr-export::frontend.my-data-body')
@endsection

{{-- 'dashboard_content' (underscore) — sportzone, techzone --}}
@section('dashboard_content')
    @include('gdpr-export::frontend.my-data-body')
@endsection

{{-- 'dash-content' — luxegems --}}
@section('dash-content')
    @include('gdpr-export::frontend.my-data-body')
@endsection
