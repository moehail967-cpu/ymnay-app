<div class="af-dash-wrap">
<style>
.af-dash-wrap {
    --af-card:   var(--lx-card,var(--vl-surface,var(--dk-surface,var(--fp-surface,var(--tz-surface,var(--bk-cream,var(--ch-surface,var(--fm-surface,var(--gl-white,var(--gc-ivory,var(--kv-light,var(--pf-bg,var(--sz-bg,var(--tr-cream,#ffffff))))))))))))))  ;
    --af-border: var(--lx-border,var(--vl-border,var(--dk-border,var(--fp-border,var(--tz-border,var(--bk-border,var(--ch-border,var(--fm-border,var(--gl-border,var(--gc-border,var(--kv-border,var(--pf-border,var(--sz-border,var(--tr-border,#e5e7eb))))))))))))));
    --af-text:   var(--lx-white,var(--vl-ivory,var(--dk-white,var(--fp-text,var(--tz-text,var(--bk-dark,var(--ch-dark,var(--fm-dark,var(--gl-dark,var(--gc-dark,var(--kv-dark,var(--pf-dark,var(--sz-dark,var(--tr-bark,#111827))))))))))))));
    --af-muted:  var(--lx-muted,var(--vl-muted,var(--dk-muted,var(--fp-muted,var(--tz-muted,var(--bk-muted,var(--ch-muted,var(--fm-muted,var(--gl-muted,var(--gc-muted,var(--kv-muted,var(--pf-muted,var(--sz-muted,var(--tr-stone,#6b7280))))))))))))));
    --af-surface:var(--lx-surface,var(--vl-panel,var(--dk-panel,var(--fp-surface,var(--tz-surface,var(--fm-bg,var(--kv-cream,var(--pf-surface,var(--sz-surface,var(--tr-cream,var(--bk-light,var(--gl-cream,var(--gc-ivory,#f9fafb)))))))))))));
    --af-gold: var(--lx-gold,var(--vl-champagne,var(--fp-green,var(--sz-red,var(--gl-gold,var(--dk-red,#f59e0b))))));
}

/* ── Components ── */
.af-dash-wrap .af-card { background:var(--af-card); border:1px solid var(--af-border); border-radius:12px; overflow:hidden; margin-bottom:20px; }
.af-dash-wrap .af-card-body { padding:24px; }
.af-dash-wrap .af-card-header { padding:16px 20px; border-bottom:1px solid var(--af-border); font-size:14px; font-weight:600; color:var(--af-text); }

/* ── Typography & Forms ── */
.af-dash-wrap .af-input { width:100%; background:var(--af-surface); border:1px solid var(--af-border); padding:10px 14px; border-radius:8px; color:var(--af-text); }
.af-dash-wrap .af-btn { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border-radius:8px; cursor:pointer; font-size:12px; font-weight:600; text-decoration:none; border:1px solid transparent; transition:opacity .15s; }
.af-dash-wrap .af-btn-primary { background:var(--af-gold); color:#000; border:1px solid var(--af-gold); }
.af-dash-wrap .af-btn-primary:hover { opacity:0.8; }
.af-dash-wrap .af-btn-outline { background:transparent; border-color:var(--af-border); color:var(--af-text); }
.af-dash-wrap .af-btn-outline:hover { background:var(--af-surface); }

/* ── Stats ── */
.af-dash-wrap .af-stat { background:var(--af-card); border:1px solid var(--af-border); border-radius:12px; padding:20px; display:flex; align-items:center; gap:16px; }
.af-dash-wrap .af-stat-icon { font-size:32px; color:var(--af-gold); opacity:0.8; }
.af-dash-wrap .af-stat-value { font-size:24px; font-weight:700; color:var(--af-text); line-height:1; }
.af-dash-wrap .af-stat-label { font-size:11px; color:var(--af-muted); margin-top:4px; text-transform:uppercase; letter-spacing:1px; }

/* ── Table ── */
.af-dash-wrap .af-table { width:100%; border-collapse:collapse; font-size:13px; }
.af-dash-wrap .af-table thead tr { background:var(--af-surface); border-bottom:1px solid var(--af-border); }
.af-dash-wrap .af-table thead th { padding:11px 16px; text-align:left; color:var(--af-muted); font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; }
.af-dash-wrap .af-table tbody tr { border-bottom:1px solid var(--af-border); }
.af-dash-wrap .af-table tbody tr:last-child { border-bottom:none; }
.af-dash-wrap .af-table tbody td { padding:13px 16px; color:var(--af-muted); vertical-align:middle; }

/* ── Badges ── */
.af-dash-wrap .af-badge { padding:3px 10px; font-size:11px; font-weight:600; display:inline-block; border-radius:12px; }
.af-dash-wrap .af-badge-approved, .af-dash-wrap .af-badge-paid { background:rgba(72,187,120,.1); color:#48BB78; border:1px solid #48BB78; }
.af-dash-wrap .af-badge-pending { background:rgba(214,158,46,.1); color:#D69E2E; border:1px solid #D69E2E; }
.af-dash-wrap .af-badge-cancelled { background:rgba(229,62,62,.1); color:#E53E3E; border:1px solid #E53E3E; }
</style>

@if(!$affiliate)

{{-- Not yet joined --}}
<div class="af-card">
    <div class="af-card-body" style="padding:64px;text-align:center;">
        <i class="mdi mdi-account-multiple-outline" style="font-size:56px;display:block;margin-bottom:16px;color:var(--af-border);"></i>
        <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--af-gold);margin-bottom:12px;">{{ __('Affiliate Programme') }}</div>
        <p style="color:var(--af-muted);font-size:14px;max-width:420px;margin:0 auto 28px;line-height:1.6;">
            {{ __('Earn commission every time someone you refer makes a purchase. Get your unique link instantly.') }}
        </p>
        <form action="{{ route('tenant.user.affiliate.join') }}" method="POST">
            @csrf
            <button type="submit" class="af-btn af-btn-primary">
                <i class="mdi mdi-link-variant"></i> {{ __("Join Now — It's Free") }}
            </button>
        </form>
    </div>
</div>

@else

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
    <div class="af-stat">
        <div class="af-stat-icon"><i class="mdi mdi-wallet-outline"></i></div>
        <div>
            <div class="af-stat-value">{{ amount_with_currency_symbol($affiliate->balance) }}</div>
            <div class="af-stat-label">{{ __('Unpaid Balance') }}</div>
        </div>
    </div>
    <div class="af-stat">
        <div class="af-stat-icon"><i class="mdi mdi-chart-line"></i></div>
        <div>
            <div class="af-stat-value">{{ amount_with_currency_symbol($affiliate->total_earned) }}</div>
            <div class="af-stat-label">{{ __('Total Earned') }}</div>
        </div>
    </div>
    <div class="af-stat">
        <div class="af-stat-icon"><i class="mdi mdi-receipt-outline"></i></div>
        <div>
            <div class="af-stat-value">{{ $commissions->count() }}</div>
            <div class="af-stat-label">{{ __('Total Referrals') }}</div>
        </div>
    </div>
</div>

{{-- Referral link --}}
<div class="af-card" style="margin-bottom:20px;">
    <div class="af-card-header">{{ __('Your Referral Link') }}</div>
    <div class="af-card-body">
        <div style="display:flex;gap:8px;align-items:stretch;">
            <input type="text" id="refLink" value="{{ $referralLink }}" readonly
                   class="af-input" style="flex:1;font-family:monospace;font-size:12px;">
            <button onclick="navigator.clipboard.writeText(document.getElementById('refLink').value);this.innerHTML='<i class=\'mdi mdi-check\'></i> {{ __('Copied!') }}';"
                    class="af-btn af-btn-outline" style="padding:12px 20px;font-size:9px;white-space:nowrap;">
                <i class="mdi mdi-content-copy"></i> {{ __('Copy') }}
            </button>
        </div>
        <p style="color:var(--af-muted);font-size:11px;margin-top:10px;">
            {{ __('Share this link. When someone registers and orders, you earn commission.') }}
        </p>
    </div>
</div>

{{-- Commission history --}}
<div class="af-card" style="margin-bottom:20px;">
    <div class="af-card-header">{{ __('Commission History') }}</div>
    @if($commissions->isEmpty())
    <div class="af-card-body" style="padding:48px;text-align:center;color:var(--af-muted);">
        <i class="mdi mdi-cash-multiple" style="font-size:40px;display:block;margin-bottom:12px;color:var(--af-border);"></i>
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;">{{ __('No commissions yet. Start sharing your link!') }}</div>
    </div>
    @else
    <div style="overflow-x:auto;">
        <table class="af-table">
            <thead>
                <tr>
                    <th>{{ __('Order') }}</th>
                    <th>{{ __('Order Total') }}</th>
                    <th>{{ __('Rate') }}</th>
                    <th>{{ __('Commission') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commissions as $c)
                @php
                    $badgeClass = match($c->status) {
                        'approved' => 'af-badge-approved',
                        'paid'     => 'af-badge-paid',
                        'pending'  => 'af-badge-pending',
                        'cancelled'=> 'af-badge-cancelled',
                        default    => '',
                    };
                @endphp
                <tr>
                    <td style="color:var(--af-gold);">#{{ $c->order_id }}</td>
                    <td>{{ amount_with_currency_symbol($c->order_total) }}</td>
                    <td>{{ $c->rate }}%</td>
                    <td style="color:var(--af-text);font-weight:600;">{{ amount_with_currency_symbol($c->amount) }}</td>
                    <td><span class="af-badge {{ $badgeClass }}">{{ ucfirst($c->status) }}</span></td>
                    <td style="font-size:11px;">{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Payout history --}}
@if($payouts->isNotEmpty())
<div class="af-card">
    <div class="af-card-header">{{ __('Payout History') }}</div>
    <div style="overflow-x:auto;">
        <table class="af-table">
            <thead>
                <tr>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Method') }}</th>
                    <th>{{ __('Reference') }}</th>
                    <th>{{ __('Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payouts as $p)
                <tr>
                    <td style="color:var(--af-text);font-weight:600;">{{ amount_with_currency_symbol($p->amount) }}</td>
                    <td><span style="display:inline-block;padding:2px 10px;border:1px solid var(--af-border);font-size:10px;letter-spacing:1px;text-transform:uppercase;color:var(--af-muted);">{{ ucfirst($p->method) }}</span></td>
                    <td style="font-size:11px;">{{ $p->reference ?? '—' }}</td>
                    <td style="font-size:11px;">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endif
</div>
