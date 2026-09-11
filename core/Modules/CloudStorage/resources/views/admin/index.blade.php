@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Storage Settings')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    $currentDriver = get_static_option_central('storage_driver') ?? 'LandlordMediaUploader';
    $drivers = [
        'LandlordMediaUploader' => ['label' => __('Local Storage'), 'icon' => 'mdi-harddisk', 'desc' => __('Files stored on your server')],
        'wasabi'          => ['label' => __('Wasabi S3'), 'icon' => 'mdi-cloud-outline', 'desc' => __('Wasabi cloud object storage')],
        'cloudFlareR2'    => ['label' => __('Cloudflare R2'), 'icon' => 'mdi-cloud-sync-outline', 'desc' => __('Cloudflare R2 storage')],
        's3'              => ['label' => __('AWS S3'), 'icon' => 'mdi-aws', 'desc' => __('Amazon Web Services S3')],
    ];
    $activeDriver = $drivers[$currentDriver] ?? $drivers['LandlordMediaUploader'];
@endphp

{{-- Hero Banner --}}
<div class="featured-card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-cloud-cog-outline text-2xl text-white/80"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">{{__('Cloud Storage')}}</p>
                <p class="text-xl font-extrabold text-white leading-tight mt-0.5">{{__('Storage Settings')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 self-start sm:self-center">
            <button type="button" id="info_toggle_btn"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full bg-white/10 text-white/70 text-xs font-semibold hover:bg-white/20 transition-all border border-white/10">
                <i class="mdi mdi-information-outline text-sm"></i> {{__('Info')}}
            </button>
            <form action="{{route('landlord.admin.cloud.storage.settings')}}" method="post" class="inline">
                @csrf
                <input type="hidden" name="_action" value="sync_file">
                <button type="submit" id="sync_btn"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white/15 backdrop-blur-sm text-white text-sm font-semibold hover:bg-white/25 transition-all border border-white/10">
                    <i class="mdi mdi-sync text-base"></i> {{__('Sync to Cloud')}}
                </button>
            </form>
        </div>
    </div>

    {{-- Current driver strip --}}
    <div class="flex items-center gap-3 mt-5 pt-4 border-t border-white/10">
        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
            <i class="mdi {{$activeDriver['icon']}} text-base text-emerald-300"></i>
        </div>
        <div>
            <p class="text-[10px] text-white/40 uppercase tracking-wider font-bold">{{__('Active Driver')}}</p>
            <p class="text-sm font-bold text-white">{{$activeDriver['label']}}</p>
        </div>
    </div>
</div>

{{-- Info Alert (hidden by default) --}}
<div id="cloud_info_alert" class="hidden mb-6 bg-surface rounded-2xl overflow-hidden" style="box-shadow: var(--shadow-main); border: 1px solid var(--color-border-main);">
    <div class="px-5 py-4 flex items-start gap-3" style="background: var(--color-info-bg); border-bottom: 1px solid rgba(59,130,246,0.1);">
        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="mdi mdi-information-outline text-info text-base"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-dark leading-relaxed">
                {!! __('To effectively utilize the cloud storage service, please carefully choose your preferred cloud provider and ensure the accurate entry of your respective credentials. Once the connection to the chosen cloud storage is established successfully, your media files will be seamlessly uploaded to this cloud storage whenever you utilize our integrated media uploader. Should you desire to synchronize all media files associated with both landlords and tenants from your server to the designated cloud storage, simply click on the <b>Sync to Cloud</b> button. This process will initiate the background upload of all media files. Please be aware that the duration of this operation may vary, potentially taking a considerable amount of time based on factors such as the quantity of files, file sizes, and server performance.') !!}
            </p>
        </div>
        <button type="button" id="close_info_alert" class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-muted hover:text-dark hover:bg-white transition">
            <i class="mdi mdi-close text-sm"></i>
        </button>
    </div>
    <div class="px-5 py-3 flex items-start gap-2" style="background: var(--color-danger-bg);">
        <i class="mdi mdi-alert-circle-outline text-danger text-sm mt-0.5 flex-shrink-0"></i>
        <p class="text-[11px] text-danger leading-relaxed font-medium">
            {{__('Removing this plugin while it is in use may lead to application instability or potential crashes. For further assistance and guidance, please do not hesitate to reach out to our support team.')}}
        </p>
    </div>
</div>

{{-- Settings Form --}}
<div class="max-w-3xl">
    <div class="bg-surface rounded-2xl overflow-hidden" style="box-shadow: var(--shadow-main); border: 1px solid var(--color-border-main);">
        <form method="post" action="{{route('landlord.admin.cloud.storage.settings')}}">
            @csrf

            {{-- Driver Selection --}}
            <div class="p-5 sm:p-6" style="border-bottom: 1px solid var(--color-border-main);">
                <label class="text-[11px] font-bold uppercase tracking-widest text-muted mb-3 block">{{__('Storage Driver')}}</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="driver_selector">
                    @foreach($drivers as $key => $driver)
                        <label class="driver-option group relative cursor-pointer">
                            <input type="radio" name="storage_driver" value="{{$key}}" class="peer hidden"
                                {{ $currentDriver === $key ? 'checked' : '' }}>
                            <div class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all duration-200
                                        peer-checked:border-[var(--color-primary)] peer-checked:bg-[var(--color-primary-soft)]
                                        border-[var(--color-border-main)] hover:border-[var(--color-border-hover)] bg-[var(--color-bg-secondary)]">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all
                                            peer-checked:bg-[var(--color-primary)] group-[&:has(:checked)]:bg-[var(--color-primary)]
                                            bg-[var(--color-bg-muted)]">
                                    <i class="mdi {{$driver['icon']}} text-lg
                                        peer-checked:text-white group-[&:has(:checked)]:text-white text-brand"></i>
                                </div>
                                <span class="text-xs font-bold text-dark text-center leading-tight">{{$driver['label']}}</span>
                                <span class="text-[9px] text-muted text-center leading-tight">{{$driver['desc']}}</span>
                            </div>
                            {{-- Active check --}}
                            <div class="absolute top-2 right-2 w-5 h-5 rounded-full hidden peer-checked:flex items-center justify-center"
                                 style="background: var(--color-primary);">
                                <i class="mdi mdi-check text-white text-[10px]"></i>
                            </div>
                        </label>
                    @endforeach
                </div>
                <p class="text-[11px] text-muted mt-3">
                    <i class="mdi mdi-information-outline text-brand mr-0.5"></i>
                    {{__('By default it is local. If you have a cloud driver you can set it here, otherwise leave as Local.')}}
                </p>
            </div>

            {{-- Credential Sections --}}
            <div id="credentials_wrapper">

                {{-- Wasabi --}}
                <div class="credentials-panel wasabi p-5 sm:p-6" style="display: {{ $currentDriver === 'wasabi' ? 'block' : 'none' }}; border-bottom: 1px solid var(--color-border-main);">
                    <div class="flex items-center gap-2.5 mb-5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--color-primary-soft);">
                            <i class="mdi mdi-cloud-outline text-brand text-sm"></i>
                        </div>
                        <h4 class="text-sm font-bold text-dark">{{__('Wasabi Credentials')}}</h4>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Access Key ID')}}</label>
                            <input type="text" name="wasabi_access_key_id" value="{{get_static_option_central('wasabi_access_key_id')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter access key ID')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Secret Access Key')}}</label>
                            <input type="text" name="wasabi_secret_access_key" value="{{get_static_option_central('wasabi_secret_access_key')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter secret key')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Default Region')}}</label>
                            <input type="text" name="wasabi_default_region" value="{{get_static_option_central('wasabi_default_region')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('e.g. us-east-1')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Bucket')}}</label>
                            <input type="text" name="wasabi_bucket" value="{{get_static_option_central('wasabi_bucket')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter bucket name')}}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Endpoint')}}</label>
                            <input type="text" name="wasabi_endpoint" value="{{get_static_option_central('wasabi_endpoint')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('e.g. https://s3.wasabisys.com')}}">
                        </div>
                    </div>
                </div>

                {{-- Cloudflare R2 --}}
                <div class="credentials-panel cloudFlareR2 p-5 sm:p-6" style="display: {{ $currentDriver === 'cloudFlareR2' ? 'block' : 'none' }}; border-bottom: 1px solid var(--color-border-main);">
                    <div class="flex items-center gap-2.5 mb-5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--color-info-bg);">
                            <i class="mdi mdi-cloud-sync-outline text-info text-sm"></i>
                        </div>
                        <h4 class="text-sm font-bold text-dark">{{__('Cloudflare R2 Credentials')}}</h4>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Access Key ID')}}</label>
                            <input type="text" name="cloudflare_r2_access_key_id" value="{{get_static_option_central('cloudflare_r2_access_key_id')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter access key ID')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Secret Access Key')}}</label>
                            <input type="text" name="cloudflare_r2_secret_access_key" value="{{get_static_option_central('cloudflare_r2_secret_access_key')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter secret key')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Bucket')}}</label>
                            <input type="text" name="cloudflare_r2_bucket" value="{{get_static_option_central('cloudflare_r2_bucket')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter bucket name')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('URL')}}</label>
                            <input type="text" name="cloudflare_r2_url" value="{{get_static_option_central('cloudflare_r2_url')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter R2 URL')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Endpoint')}}</label>
                            <input type="text" name="cloudflare_r2_endpoint" value="{{get_static_option_central('cloudflare_r2_endpoint')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter endpoint URL')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Use Path Style Endpoint')}}</label>
                            <select name="cloudflare_r2_use_path_style_endpoint"
                                    class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all appearance-none"
                                    style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);">
                                <option value="1" {{get_static_option_central('cloudflare_r2_use_path_style_endpoint') == 1 ? 'selected' : ''}}>{{__('Yes')}}</option>
                                <option value="0" {{get_static_option_central('cloudflare_r2_use_path_style_endpoint') == 0 ? 'selected' : ''}}>{{__('No')}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- AWS S3 --}}
                <div class="credentials-panel s3 p-5 sm:p-6" style="display: {{ $currentDriver === 's3' ? 'block' : 'none' }}; border-bottom: 1px solid var(--color-border-main);">
                    <div class="flex items-center gap-2.5 mb-5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--color-warning-bg);">
                            <i class="mdi mdi-aws text-warning text-sm"></i>
                        </div>
                        <h4 class="text-sm font-bold text-dark">{{__('AWS S3 Credentials')}}</h4>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Access Key ID')}}</label>
                            <input type="text" name="aws_access_key_id" value="{{get_static_option_central('aws_access_key_id')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter access key ID')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Secret Access Key')}}</label>
                            <input type="text" name="aws_secret_access_key" value="{{get_static_option_central('aws_secret_access_key')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter secret key')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Default Region')}}</label>
                            <input type="text" name="aws_default_region" value="{{get_static_option_central('aws_default_region')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('e.g. us-east-1')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Bucket')}}</label>
                            <input type="text" name="aws_bucket" value="{{get_static_option_central('aws_bucket')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter bucket name')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('URL')}}</label>
                            <input type="text" name="aws_url" value="{{get_static_option_central('aws_url')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter S3 URL')}}">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Endpoint')}}</label>
                            <input type="text" name="aws_endpoint" value="{{get_static_option_central('aws_endpoint')}}"
                                   class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all"
                                   style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);"
                                   onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 3px rgba(26,92,78,0.08)'"
                                   onblur="this.style.borderColor='var(--color-border-main)';this.style.boxShadow='none'"
                                   placeholder="{{__('Enter endpoint URL')}}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-[11px] font-bold uppercase tracking-wide text-muted mb-1.5 block">{{__('Use Path Style Endpoint')}}</label>
                            <select name="aws_use_path_style_endpoint"
                                    class="w-full px-3.5 py-2.5 rounded-xl text-sm text-dark outline-none transition-all appearance-none"
                                    style="border: 1px solid var(--color-border-main); background: var(--color-bg-secondary);">
                                <option value="1" {{get_static_option_central('aws_use_path_style_endpoint') == 1 ? 'selected' : ''}}>{{__('Yes')}}</option>
                                <option value="0" {{get_static_option_central('aws_use_path_style_endpoint') == 0 ? 'selected' : ''}}>{{__('No')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="px-5 sm:px-6 py-4 flex items-center justify-end" style="background: var(--color-bg-secondary);">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-white text-sm font-semibold hover:opacity-90 transition-all"
                        style="background: var(--color-primary); box-shadow: 0 4px 12px rgba(26,92,78,0.25);">
                    <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function ($) {
    "use strict";

    // ── Driver Selection ─────────────────────────────────────────
    $(document).on('change', 'input[name="storage_driver"]', function () {
        var driver = $(this).val();

        Swal.fire({
            title: '{{ __("Change storage driver?") }}',
            text: '{{ __("If you change your storage driver, you will need to download all the media files before to re-sync on the newly selected driver.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1a5c4e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '{{ __("Yes, change it") }}',
            cancelButtonText: '{{ __("Discard") }}'
        }).then(function (result) {
            if (result.isConfirmed) {
                $('.credentials-panel').slideUp(250);
                if (driver !== 'LandlordMediaUploader') {
                    $('.credentials-panel.' + driver).slideDown(250);
                }
            } else {
                window.location.reload();
            }
        });
    });

    // ── Visual state for radio cards ─────────────────────────────
    function updateDriverCards() {
        $('#driver_selector label').each(function () {
            var radio = $(this).find('input[type="radio"]');
            var card = $(this).find('> div');
            var check = $(this).find('.absolute');
            var icon = card.find('> div:first-child');

            if (radio.is(':checked')) {
                card.css({ borderColor: 'var(--color-primary)', background: 'var(--color-primary-soft)' });
                icon.css({ background: 'var(--color-primary)' });
                icon.find('i').css('color', '#fff');
                check.css('display', 'flex');
            } else {
                card.css({ borderColor: 'var(--color-border-main)', background: 'var(--color-bg-secondary)' });
                icon.css({ background: 'var(--color-bg-muted)' });
                icon.find('i').css('color', '');
                check.css('display', 'none');
            }
        });
    }
    updateDriverCards();
    $(document).on('change', 'input[name="storage_driver"]', updateDriverCards);

    // ── Info toggle ──────────────────────────────────────────────
    $('#info_toggle_btn').on('click', function () {
        $('#cloud_info_alert').slideToggle(250);
    });
    $('#close_info_alert').on('click', function () {
        $('#cloud_info_alert').slideUp(250);
    });

    // ── Sync button highlight on "Sync" text click ───────────────
    $(document).on('click', '#cloud_info_alert b', function () {
        var btn = $('#sync_btn');
        btn.css({ transform: 'scale(1.08)', boxShadow: '0 0 0 4px rgba(255,255,255,0.3)' });
        setTimeout(function () {
            btn.css({ transform: 'scale(1)', boxShadow: 'none' });
        }, 200);
    });

})(jQuery);
</script>
@endsection
