<div class="gdpr-wrap">
    <div class="gdpr-card">
        <div class="gdpr-card-body">

            <div class="gdpr-header">
                <div>
                    <h5 class="gdpr-page-title">{{ __('My Data Export') }}</h5>
                    <p class="gdpr-subtitle">{{ __('Request a copy of all your personal data stored by this store (GDPR Article 20).') }}</p>
                </div>
                <form action="{{ route('tenant.user.gdpr-export.request') }}" method="POST">
                    @csrf
                    <button type="submit" class="gdpr-btn">
                        <i class="mdi mdi-download"></i> {{ __('Request Export') }}
                    </button>
                </form>
            </div>

            @if(session('success'))
                <div class="gdpr-alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="gdpr-alert-error">{{ session('error') }}</div>
            @endif

            @if($requests->isEmpty())
                <div class="gdpr-empty">
                    <i class="mdi mdi-shield-account-outline gdpr-empty-icon"></i>
                    <p>{{ __('No export requests yet. Click "Request Export" to get started.') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="gdpr-table">
                        <thead>
                            <tr>
                                <th>{{ __('Requested') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Expires') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}</td>
                                <td>
                                    @php
                                        $badge = match($req->status) {
                                            'pending'    => 'pending',
                                            'processing' => 'processing',
                                            'ready'      => 'ready',
                                            'downloaded' => 'downloaded',
                                            default      => 'expired',
                                        };
                                    @endphp
                                    <span class="gdpr-badge gdpr-badge-{{ $badge }}">{{ ucfirst($req->status) }}</span>
                                </td>
                                <td>
                                    @if($req->expires_at)
                                        {{ \Carbon\Carbon::parse($req->expires_at)->format('d M Y H:i') }}
                                    @else
                                        <span style="opacity:.4;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status === 'ready' && $req->download_token)
                                        <a href="{{ route('tenant.user.gdpr-export.download', $req->download_token) }}"
                                           class="gdpr-download-btn">
                                            <i class="mdi mdi-download"></i> {{ __('Download') }}
                                        </a>
                                    @else
                                        <span style="opacity:.4;">—</span>
                                    @endif
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
