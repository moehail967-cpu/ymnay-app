@extends(route_prefix().'admin.admin-master')

@section('title') {{ __('Social Auth Settings') }} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Main Form --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info Banner --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex gap-3">
            <i class="mdi mdi-information-outline text-blue-500 text-xl flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-blue-700 leading-relaxed">
                {{ __('Create OAuth 2.0 credentials on') }}
                <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="font-semibold underline">Google Cloud Console</a>,
                {{ __('then paste your Client ID and Secret below. Add the Callback URL shown on this page as an Authorised Redirect URI.') }}
            </p>
        </div>

        {{-- Google OAuth Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main">

            {{-- Card Header --}}
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('Google OAuth') }}</h3>
                    <p class="text-xs text-muted">{{ __('Let users sign in with their Google account') }}</p>
                </div>
            </div>

            {{-- Form --}}
            <div class="px-4 sm:px-6 py-5 space-y-5">
                <form action="{{ route(route_prefix().'admin.social.auth.update') }}" method="POST">
                    @csrf

                    {{-- Enable Toggle --}}
                    <div class="flex items-center justify-between py-3 border-b border-main mb-5">
                        <div>
                            <p class="text-sm font-semibold text-dark">{{ __('Enable Google Login') }}</p>
                            <p class="text-xs text-muted mt-0.5">{{ __('Shows the "Continue with Google" button on all login pages.') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="google_login_enable" id="google_login_enable" class="sr-only peer"
                                {{ get_static_option('google_login_enable') ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    {{-- Client ID --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{ __('Google Client ID') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="google_client_id"
                               class="lnd-input @error('google_client_id') border-red-400 @enderror"
                               value="{{ get_static_option('google_client_id') }}"
                               placeholder="xxxxxxxxxxxx-xxxx.apps.googleusercontent.com">
                        @error('google_client_id')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Client Secret --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{ __('Google Client Secret') }} <span class="text-danger">*</span>
                        </label>
                        <input type="password"
                               name="google_client_secret"
                               class="lnd-input @error('google_client_secret') border-red-400 @enderror"
                               value="{{ get_static_option('google_client_secret') }}"
                               placeholder="{{ __('GOCSPX-xxxxxxxxxxxxxxxxxxxxxx') }}">
                        @error('google_client_secret')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Save --}}
                    <div class="pt-2">
                        <button type="submit" class="btn-default btn-sm">
                            <i class="mdi mdi-content-save-outline mr-1"></i> {{ __('Save Settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">

        {{-- Callback URL --}}
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-link-variant text-success text-base"></i>
                </div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('Callback URL') }}</h3>
            </div>
            <div class="px-4 py-4 space-y-3">
                <p class="text-xs text-muted leading-relaxed">
                    {{ __('Add this URL as an Authorised Redirect URI in your Google Console OAuth client.') }}
                </p>
                <div class="bg-secondary rounded-lg px-3 py-2.5 flex items-center justify-between gap-2">
                    <code class="text-xs text-dark break-all" id="callback_url">{{ url('/auth/google/callback') }}</code>
                    <button type="button" onclick="copyCallbackUrl()"
                        class="flex-shrink-0 w-7 h-7 rounded-md bg-primary-soft text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-colors"
                        title="{{ __('Copy') }}">
                        <i class="mdi mdi-content-copy text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- MobileApp Note --}}
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cellphone text-warning text-base"></i>
                </div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('Mobile App') }}</h3>
            </div>
            <div class="px-4 py-4">
                <p class="text-xs text-muted leading-relaxed">
                    {{ __('The MobileApp module uses the same credentials for native Google login via API. No extra setup needed.') }}
                </p>
            </div>
        </div>

    </div>

</div>

@endsection

@section('scripts')
<script>
    function copyCallbackUrl() {
        var text = document.getElementById('callback_url').innerText;
        navigator.clipboard.writeText(text).then(function () {
            toastr.success('{{ __("Copied to clipboard!") }}');
        });
    }
</script>
@endsection
