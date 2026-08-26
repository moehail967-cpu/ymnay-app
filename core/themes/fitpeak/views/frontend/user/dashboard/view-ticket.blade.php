@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('View Ticket') }} @endsection
@section('page-title') {{ __('View Ticket') }} @endsection

@section('style')
@parent
<x-summernote.css/>
@endsection

@section('dashboard-content')
<div class="fp-dash-card" style="margin-bottom:20px;">
    <div class="fp-dash-card-header">
        {{ $ticket->subject ?? __('Ticket') }}
        <span style="font-size:12px;color:var(--fp-muted);">#{{ $ticket->id }}</span>
    </div>
    <div class="fp-dash-card-body">
        {{-- Messages --}}
        <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:24px;">
            @foreach($ticket->messages ?? [] as $msg)
                @php $isAdmin = $msg->user_type === 'admin'; @endphp
                <div style="display:flex;gap:12px;{{ $isAdmin ? '' : 'flex-direction:row-reverse;' }}">
                    <div style="width:36px;height:36px;border-radius:var(--fp-radius);background:{{ $isAdmin ? 'var(--fp-green-glow)' : 'var(--fp-mid)' }};border:1px solid {{ $isAdmin ? 'var(--fp-green)' : 'var(--fp-border)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;color:{{ $isAdmin ? 'var(--fp-green)' : 'var(--fp-muted)' }};font-size:18px;">
                        <i class="mdi mdi-{{ $isAdmin ? 'headset' : 'account' }}"></i>
                    </div>
                    <div style="max-width:75%;background:var(--fp-card);border:1px solid var(--fp-border);border-radius:var(--fp-radius);padding:14px 16px;">
                        <div style="font-size:11px;color:var(--fp-muted);margin-bottom:8px;font-family:var(--fp-font-head);text-transform:uppercase;letter-spacing:1px;">
                            {{ $isAdmin ? __('Support') : __('You') }} · {{ $msg->created_at->format('d M Y, H:i') }}
                        </div>
                        <div style="font-size:14px;color:var(--fp-text);line-height:1.6;">{!! $msg->message !!}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Reply form --}}
        @if(($ticket->status ?? '') !== 'closed')
        <form id="ticket_reply_form">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
            <div style="margin-bottom:12px;">
                <label class="fp-dash-label">{{ __('Your Reply') }}</label>
                <textarea name="message" id="ticket_message" class="summernote" style="width:100%;"></textarea>
            </div>
            <button type="submit" class="fp-btn fp-btn-primary">
                <i class="mdi mdi-send"></i> {{ __('Send Reply') }}
            </button>
        </form>
        @endif
    </div>
</div>

<a href="{{ theme_user_tickets_url() }}" class="fp-btn fp-btn-dark">
    <i class="mdi mdi-arrow-left"></i> {{ __('Back to Tickets') }}
</a>
@endsection

@section('dashboard-scripts')
<x-summernote.js/>
<script>
$(function () {
    if ($('.summernote').length) { $('.summernote').summernote({ height: 160, toolbar: [['style', ['bold','italic','underline']],['para', ['paragraph']],['insert', ['link']]] }); }

    $(document).on('submit', '#ticket_reply_form', function (e) {
        e.preventDefault();
        var msg = $('#ticket_message').summernote ? $('#ticket_message').summernote('code') : $('#ticket_message').val();
        $.ajax({
            url: '{{ theme_user_ticket_reply_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', ticket_id: '{{ $ticket->id }}', message: msg },
            success: function (d) {
                if (d.type === 'success') { toastr.success(d.msg); setTimeout(() => location.reload(), 500); }
                else { toastr.error(d.msg); }
            },
            error: function () { toastr.error('{{ __("An error occurred") }}'); }
        });
    });
});
</script>
@endsection
