<div class="lp-dash-wrap">
<style>
.lp-dash-wrap {
    --lp-card:   var(--lx-card,var(--vl-surface,var(--dk-surface,var(--fp-surface,var(--tz-surface,var(--bk-cream,var(--ch-surface,var(--fm-surface,var(--gl-white,var(--gc-ivory,var(--kv-light,var(--pf-bg,var(--sz-bg,var(--tr-cream,#ffffff))))))))))))))  ;
    --lp-border: var(--lx-border,var(--vl-border,var(--dk-border,var(--fp-border,var(--tz-border,var(--bk-border,var(--ch-border,var(--fm-border,var(--gl-border,var(--gc-border,var(--kv-border,var(--pf-border,var(--sz-border,var(--tr-border,#e5e7eb))))))))))))));
    --lp-text:   var(--lx-white,var(--vl-ivory,var(--dk-white,var(--fp-text,var(--tz-text,var(--bk-dark,var(--ch-dark,var(--fm-dark,var(--gl-dark,var(--gc-dark,var(--kv-dark,var(--pf-dark,var(--sz-dark,var(--tr-bark,#111827))))))))))))));
    --lp-muted:  var(--lx-muted,var(--vl-muted,var(--dk-muted,var(--fp-muted,var(--tz-muted,var(--bk-muted,var(--ch-muted,var(--fm-muted,var(--gl-muted,var(--gc-muted,var(--kv-muted,var(--pf-muted,var(--sz-muted,var(--tr-stone,#6b7280))))))))))))));
    --lp-surface:var(--lx-surface,var(--vl-panel,var(--dk-panel,var(--fp-surface,var(--tz-surface,var(--fm-bg,var(--kv-cream,var(--pf-surface,var(--sz-surface,var(--tr-cream,var(--bk-light,var(--gl-cream,var(--gc-ivory,#f9fafb)))))))))))));
    --lp-gold: #f59e0b;
    --lp-gold-bg: rgba(245,158,11,.10);
    --lp-green: #059669;
    --lp-red: #dc2626;
}

/* ── Card shell ── */
.lp-dash-wrap .lp-card { background:var(--lp-card); border:1px solid var(--lp-border); border-radius:12px; overflow:hidden; margin-bottom:20px; }
.lp-dash-wrap .lp-card-body { padding:24px; }

/* ── Stats row ── */
.lp-dash-wrap .lp-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:16px; margin-bottom:20px; }
.lp-dash-wrap .lp-stat { background:var(--lp-card); border:1px solid var(--lp-border); border-radius:12px; padding:20px; text-align:center; }
.lp-dash-wrap .lp-stat-num { font-size:30px; font-weight:800; color:var(--lp-gold); line-height:1; }
.lp-dash-wrap .lp-stat-label { font-size:12px; color:var(--lp-muted); margin-top:6px; }
.lp-dash-wrap .lp-stat-sub { font-size:13px; color:var(--lp-green); font-weight:600; margin-top:4px; }

/* ── Table ── */
.lp-dash-wrap .lp-table { width:100%; border-collapse:collapse; font-size:13px; }
.lp-dash-wrap .lp-table thead tr { background:var(--lp-surface); border-bottom:1px solid var(--lp-border); }
.lp-dash-wrap .lp-table thead th { padding:11px 16px; text-align:left; color:var(--lp-muted); font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; }
.lp-dash-wrap .lp-table tbody tr { border-bottom:1px solid var(--lp-border); transition:background .1s; }
.lp-dash-wrap .lp-table tbody tr:last-child { border-bottom:none; }
.lp-dash-wrap .lp-table tbody td { padding:13px 16px; color:var(--lp-muted); vertical-align:middle; }

/* ── Badges ── */
.lp-dash-wrap .lp-badge { display:inline-block; padding:3px 10px; font-size:11px; font-weight:600; border-radius:20px; }
.lp-dash-wrap .lp-badge-earn   { background:rgba(5,150,105,.12);  color:var(--lp-green); }
.lp-dash-wrap .lp-badge-redeem { background:rgba(220,38,38,.10);  color:var(--lp-red); }
.lp-dash-wrap .lp-badge-expire { background:rgba(107,114,128,.12); color:var(--lp-muted); }
.lp-dash-wrap .lp-badge-manual { background:rgba(124,58,237,.12);  color:#7c3aed; }
.lp-dash-wrap .lp-badge-refund { background:rgba(245,158,11,.12);  color:var(--lp-gold); }

/* ── Points colored ── */
.lp-dash-wrap .lp-pts-earn   { color:var(--lp-green); font-weight:700; }
.lp-dash-wrap .lp-pts-deduct { color:var(--lp-red);   font-weight:700; }

/* ── Empty ── */
.lp-dash-wrap .lp-empty { padding:56px 24px; text-align:center; color:var(--lp-muted); }
.lp-dash-wrap .lp-empty i { font-size:48px; display:block; margin-bottom:12px; opacity:.25; color:var(--lp-text); }

/* ── How to earn ── */
.lp-dash-wrap .lp-how { background:var(--lp-gold-bg); border:1px solid rgba(245,158,11,.25); border-radius:12px; padding:20px; }
.lp-dash-wrap .lp-how-title { font-size:13px; font-weight:700; color:var(--lp-gold); margin-bottom:14px; display:flex; align-items:center; gap:6px; }
.lp-dash-wrap .lp-how-list { list-style:none; margin:0; padding:0; display:grid; gap:10px; }
.lp-dash-wrap .lp-how-list li { display:flex; align-items:flex-start; gap:10px; font-size:13px; color:var(--lp-muted); }
.lp-dash-wrap .lp-how-list li i { color:var(--lp-gold); font-size:16px; flex-shrink:0; margin-top:1px; }
</style>

    {{-- Stats row --}}
    <div class="lp-stats">
        <div class="lp-stat">
            <div class="lp-stat-num">{{ number_format($balance) }}</div>
            <div class="lp-stat-label">{{ __('Current Balance') }}</div>
            @if($redeemValue > 0)
            <div class="lp-stat-sub">≈ {{ amount_with_currency_symbol($redeemValue) }}</div>
            @endif
        </div>
        <div class="lp-stat">
            <div class="lp-stat-num" style="font-size:22px;padding-top:4px;">{{ $earnRate }}</div>
            <div class="lp-stat-label">{{ __('Points per $1 spent') }}</div>
        </div>
        <div class="lp-stat">
            <div class="lp-stat-num" style="font-size:22px;padding-top:4px;">{{ $redeemRate }}</div>
            <div class="lp-stat-label">{{ __('Points = $1 discount') }}</div>
        </div>
    </div>

    {{-- How to earn --}}
    <div class="lp-how" style="margin-bottom:20px;">
        <div class="lp-how-title">
            <i class="mdi mdi-lightbulb-outline"></i>
            {{ __('How to Earn & Redeem') }}
        </div>
        <ul class="lp-how-list">
            <li><i class="mdi mdi-cart-check"></i> <span>{{ __('Earn :n points for every $1 you spend on any order.', ['n' => $earnRate]) }}</span></li>
            <li><i class="mdi mdi-tag-outline"></i> <span>{{ __('Redeem :n points at checkout to get $1 off your order.', ['n' => $redeemRate]) }}</span></li>
            <li><i class="mdi mdi-star-circle-outline"></i> <span>{{ __('You need at least :n points to redeem. Your balance grows with every purchase.', ['n' => $minRedeem]) }}</span></li>
        </ul>
    </div>

    {{-- Transaction history --}}
    <div class="lp-card">
        <div class="lp-card-body" style="padding:0;">
            <div style="padding:16px 20px; border-bottom:1px solid var(--lp-border); display:flex; align-items:center; gap:8px;">
                <i class="mdi mdi-history" style="font-size:18px; color:var(--lp-gold);"></i>
                <span style="font-size:14px; font-weight:600; color:var(--lp-text);">{{ __('Transaction History') }}</span>
            </div>

            @if($transactions->isEmpty())
                <div class="lp-empty">
                    <i class="mdi mdi-star-circle-outline"></i>
                    <p>{{ __('No transactions yet. Make a purchase to start earning points!') }}</p>
                </div>
            @else
            <div style="overflow-x:auto;">
                <table class="lp-table">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th style="text-align:right;">{{ __('Points') }}</th>
                            <th style="text-align:right;">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $tx)
                        @php
                            $badgeClass = match($tx->type) {
                                'earn'   => 'lp-badge-earn',
                                'redeem' => 'lp-badge-redeem',
                                'expire' => 'lp-badge-expire',
                                'manual' => 'lp-badge-manual',
                                'refund' => 'lp-badge-refund',
                                default  => 'lp-badge-expire',
                            };
                            $sign = $tx->points > 0 ? '+' : '';
                            $ptClass = $tx->points > 0 ? 'lp-pts-earn' : 'lp-pts-deduct';
                        @endphp
                        <tr>
                            <td style="white-space:nowrap;">
                                {{ \Carbon\Carbon::parse($tx->created_at)->format('M d, Y') }}
                            </td>
                            <td>
                                <span class="lp-badge {{ $badgeClass }}">{{ ucfirst($tx->type) }}</span>
                            </td>
                            <td>{{ $tx->note ?: '—' }}</td>
                            <td style="text-align:right;" class="{{ $ptClass }}">
                                {{ $sign }}{{ number_format(abs($tx->points)) }}
                            </td>
                            <td style="text-align:right; color:var(--lp-text); font-weight:600;">
                                {{ number_format($tx->balance_after) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

</div>
