@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Support Tickets') }} @endsection
@section('page-title') {{ __('Support Tickets') }} @endsection

@section('dashboard-content')

{!! theme_error_msg() !!}
{!! theme_flash_msg() !!}

<div class="tr-dash-card">
    <div class="tr-dash-card-header">
        <span><i class="mdi mdi-headset" style="margin-right:6px;"></i>{{ __('Support Tickets') }}</span>
        <a href="{{ theme_user_new_ticket_url() }}" class="tr-btn tr-btn-primary tr-btn-sm">
            <i class="mdi mdi-plus"></i> {{ __('New Ticket') }}
        </a>
    </div>
    <div class="tr-dash-card-body" style="padding:0;">
        @if(isset($all_ticket) && $all_ticket->count())
        <div class="table-responsive">
            <table class="tr-dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($all_ticket as $ticket)
                    @php
                        $pStyle = match($ticket->priority ?? '') {
                            'high'   => 'color:var(--tr-terra);border:1px solid var(--tr-terra);',
                            'medium' => 'color:#9a7a30;border:1px solid #c9a84c;',
                            default  => 'color:var(--tr-olive);border:1px solid var(--tr-olive);'
                        };
                        $sStyle = match($ticket->status ?? '') {
                            'open'    => 'color:var(--tr-olive);border:1px solid var(--tr-olive);',
                            'closed'  => 'color:var(--tr-stone);border:1px solid var(--tr-border);',
                            default   => 'color:#9a7a30;border:1px solid #c9a84c;'
                        };
                    @endphp
                    <tr>
                        <td style="font-weight:600;color:var(--tr-bark);">{{ $ticket->subject }}</td>
                        <td>
                            <span class="tr-dash-badge" style="{{ $pStyle }}background:rgba(0,0,0,.03);">
                                {{ ucfirst($ticket->priority ?? 'low') }}
                            </span>
                        </td>
                        <td>
                            <span class="tr-dash-badge" style="{{ $sStyle }}background:rgba(0,0,0,.03);">
                                {{ ucfirst($ticket->status ?? 'open') }}
                            </span>
                        </td>
                        <td style="color:var(--tr-stone);font-size:12px;">{{ $ticket->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ theme_user_ticket_url($ticket->id) }}"
                               class="tr-btn tr-btn-outline tr-btn-sm">
                                <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:60px;text-align:center;color:var(--tr-stone);">
            <i class="mdi mdi-headset" style="font-size:56px;display:block;margin-bottom:16px;color:var(--tr-border);"></i>
            <p style="font-size:16px;margin-bottom:16px;">{{ __('No tickets yet') }}</p>
            <a href="{{ theme_user_new_ticket_url() }}" class="tr-btn tr-btn-primary">
                <i class="mdi mdi-plus-circle-outline"></i> {{ __('Open a Ticket') }}
            </a>
        </div>
        @endif
    </div>
</div>

@endsection
