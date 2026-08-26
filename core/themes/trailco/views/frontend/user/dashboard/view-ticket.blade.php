@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('View Ticket') }} @endsection
@section('page-title') {{ __('View Ticket') }} @endsection

@section('style')
@parent
<x-summernote.css/>
@endsection

@section('dashboard-content')

{!! theme_error_msg() !!}
{!! theme_flash_msg() !!}

<div class="tr-dash-card" style="margin-bottom:20px;">
    <div class="tr-dash-card-header">
        <span><i class="mdi mdi-forum-outline" style="margin-right:6px;"></i>{{ $ticket->subject ?? __('Ticket') }}</span>
        <span style="font-size:12px;color:var(--tr-stone);">#{{ $ticket->id }}</span>
    </div>
    <div class="tr-dash-card-body">

        {{-- Chat Thread --}}
        <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:28px;">
            @forelse($messages ?? $ticket->messages ?? [] as $msg)
            @php $isAdmin = $msg->user_type === 'admin'; @endphp
            <div style="display:flex;gap:12px;{{ $isAdmin ? '' : 'flex-direction:row-reverse;' }}align-items:flex-start;">
                {{-- Avatar --}}
                <div style="width:38px;height:38px;border-radius:50%;background:{{ $isAdmin ? 'var(--tr-bark)' : 'var(--tr-olive)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-size:18px;">
                    <i class="mdi mdi-{{ $isAdmin ? 'headset' : 'account' }}"></i>
                </div>
                {{-- Bubble --}}
                <div style="max-width:75%;background:{{ $isAdmin ? 'var(--tr-bark)' : 'var(--tr-olive)' }};border-radius:{{ $isAdmin ? '4px 12px 12px 12px' : '12px 4px 12px 12px' }};padding:14px 16px;">
                    <div style="font-size:11px;color:rgba(255,255,255,.65);margin-bottom:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">
                        {{ $isAdmin ? __('Support Team') : __('You') }} &middot; {{ $msg->created_at->format('d M Y, H:i') }}
                    </div>
                    <div style="font-size:14px;color:#fff;line-height:1.6;">{!! $msg->message !!}</div>
                    @if($msg->file ?? null)
                    <div style="margin-top:10px;">
                        <a href="{{ asset('storage/' . $msg->file) }}"
                           target="_blank"
                           style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,.8);text-decoration:none;">
                            <i class="mdi mdi-paperclip"></i> {{ __('Attachment') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:32px;color:var(--tr-stone);">
                <i class="mdi mdi-chat-outline" style="font-size:40px;display:block;margin-bottom:10px;color:var(--tr-border);"></i>
                {{ __('No messages yet. Start the conversation below.') }}
            </div>
            @endforelse
        </div>

        {{-- Reply Form --}}
        @if(($ticket->status ?? '') !== 'closed')
        <div style="border-top:1px solid var(--tr-border);padding-top:20px;">
            <h5 style="font-size:14px;font-weight:700;color:var(--tr-bark);margin-bottom:14px;">
                <i class="mdi mdi-reply" style="margin-right:4px;"></i>{{ __('Send a Reply') }}
            </h5>
            <form id="tr_ticket_reply_form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;font-weight:700;color:var(--tr-stone);display:block;margin-bottom:6px;">{{ __('Message') }}</label>
                    <textarea name="message" id="summernote" style="width:100%;"></textarea>
                </div>
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;font-weight:700;color:var(--tr-stone);display:block;margin-bottom:6px;">
                        <i class="mdi mdi-paperclip" style="margin-right:4px;"></i>{{ __('Attachment (zip only)') }}
                    </label>
                    <input type="file" name="file" accept=".zip"
                           style="width:100%;padding:9px 12px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:13px;font-family:inherit;background:var(--tr-cream);color:var(--tr-bark);outline:none;"
                           onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                </div>
                <button type="submit" class="tr-btn tr-btn-primary">
                    <i class="mdi mdi-send"></i> {{ __('Send Reply') }}
                </button>
            </form>
        </div>
        @else
        <div style="border-top:1px solid var(--tr-border);padding-top:16px;text-align:center;">
            <span class="tr-dash-badge" style="background:rgba(123,111,99,.12);color:var(--tr-stone);border:1px solid var(--tr-border);font-size:13px;padding:6px 16px;">
                <i class="mdi mdi-lock-outline" style="margin-right:4px;"></i>{{ __('This ticket is closed') }}
            </span>
        </div>
        @endif

    </div>
</div>

<a href="{{ theme_user_tickets_url() }}" class="tr-btn tr-btn-outline">
    <i class="mdi mdi-arrow-left"></i> {{ __('Back to Tickets') }}
</a>

@endsection

@section('dashboard-scripts')
<x-summernote.js/>
<script>
$(function () {
    if ($('#summernote').length) {
        $('#summernote').summernote({
            height: 180,
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['para', ['paragraph']],
                ['insert', ['link']]
            ]
        });
    }

    $(document).on('submit', '#tr_ticket_reply_form', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        if ($('#summernote').length) {
            formData.set('message', $('#summernote').summernote('code'));
        }
        $.ajax({
            url: '{{ theme_user_ticket_reply_url() }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (d) {
                if (d.type === 'success') {
                    toastr.success(d.msg);
                    setTimeout(function () { location.reload(); }, 700);
                } else {
                    toastr.error(d.msg);
                }
            },
            error: function () {
                toastr.error('{{ __("An error occurred. Please try again.") }}');
            }
        });
    });
});
</script>
@endsection
