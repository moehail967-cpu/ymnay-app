@extends(include_theme_path('user.user-master'))
@section('dash-title') {{ __('Support Tickets') }} @endsection

@section('dash-content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div></div>
    <button class="lg-dash-btn lg-dash-btn-gold" data-bs-toggle="modal" data-bs-target="#lg-new-ticket-modal">
        <i class="las la-plus"></i> {{ __('New Ticket') }}
    </button>
</div>

<div class="lg-dash-card">
    <div class="lg-dash-card-title">{{ __('My Tickets') }}</div>
    <div style="overflow-x:auto;">
        <table class="lg-dash-table">
            <thead>
                <tr>
                    <th>{{ __('#') }}</th>
                    <th>{{ __('Subject') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets ?? [] as $ticket)
                <tr>
                    <td>#{{ $ticket->id }}</td>
                    <td>{{ $ticket->subject }}</td>
                    <td>
                        <span class="lg-dash-badge lg-dash-badge-{{ strtolower($ticket->status ?? 'pending') }}">
                            {{ ucfirst($ticket->status ?? 'open') }}
                        </span>
                    </td>
                    <td>{{ $ticket->created_at?->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('tenant.user.dashboard.support.ticket.view', $ticket->id) }}" class="lg-dash-btn lg-dash-btn-outline" style="font-size:9px;padding:6px 12px;">
                            {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--lx-muted);">{{ __('No support tickets yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- New ticket modal --}}
<div class="modal fade" id="lg-new-ticket-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--lx-card);border:1px solid var(--lx-border);border-radius:0;">
            <div class="modal-header" style="border-bottom:1px solid var(--lx-border);padding:20px 24px;">
                <h5 style="font-size:12px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--lx-white);margin:0;">{{ __('Open a Ticket') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <form action="{{ route('tenant.frontend.support.ticket.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="lg-dash-label">{{ __('Subject') }}</label>
                        <input type="text" name="subject" class="lg-dash-input" placeholder="{{ __('Briefly describe your issue') }}">
                    </div>
                    <div class="mb-4">
                        <label class="lg-dash-label">{{ __('Message') }}</label>
                        <textarea name="message" class="lg-dash-input" rows="5" placeholder="{{ __('Describe your issue in detail…') }}" style="resize:vertical;"></textarea>
                    </div>
                    <button type="submit" class="lg-dash-btn lg-dash-btn-gold w-100 justify-content-center" style="padding:12px;">
                        {{ __('Submit Ticket') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
