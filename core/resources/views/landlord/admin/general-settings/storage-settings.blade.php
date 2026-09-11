@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Storage Settings')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-cloud text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Storage Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Configure file storage driver and credentials')}}</p>
            </div>
        </div>
        <form action="{{route(route_prefix().'admin.general.storage.settings')}}" method="post">
            @csrf
            <input type="hidden" name="_action" value="sync_file">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-info text-white text-xs font-semibold hover:opacity-90 transition">
                <i class="las la-sync-alt"></i>
                {{__('Sync Local File To Cloud')}}
            </button>
        </form>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form class="forms-sample" method="post" action="{{route(route_prefix().'admin.general.storage.settings')}}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="lnd-label">{{__('Disk Driver')}}</label>
                    <select name="storage_driver" id="storage-driver" class="lnd-input">
                        <option value="LandlordMediaUploader" @selected(get_static_option_central('storage_driver') == 'LandlordMediaUploader')>{{__('Local')}}</option>
                        <option value="cloudFlareR2" @selected(get_static_option_central('storage_driver') == 'cloudFlareR2')>{{__('Cloudflare R2')}}</option>
                        <option value="wasabi" @selected(get_static_option_central('storage_driver') == 'wasabi')>{{__('Wasabi S3')}}</option>
                        <option value="s3" @selected(get_static_option_central('storage_driver') == 's3')>{{__('AWS S3')}}</option>
                    </select>
                    <p class="text-xs text-muted mt-1.5">{{__('By default it is local. If you have a cloud storage driver, you can set it here.')}}</p>
                </div>

                <div class="credentials_wrapper">
                    {{-- Wasabi --}}
                    <div class="credentials wasabi space-y-4 {{get_static_option_central('storage_driver') != 'wasabi' ? 'hidden' : ''}}">
                        <div class="pt-4 border-t border-main">
                            <h4 class="text-sm font-bold text-dark font-urbanist mb-4">{{__('Wasabi S3 Credentials')}}</h4>
                        </div>
                        <div>
                            <label class="lnd-label">{{__('WAS Access Key ID')}}</label>
                            <input class="lnd-input" type="text" name="wasabi_access_key_id" value="{{get_static_option_central('wasabi_access_key_id')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('WAS Secret Access Key')}}</label>
                            <input class="lnd-input" type="text" name="wasabi_secret_access_key" value="{{get_static_option_central('wasabi_secret_access_key')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('WAS Default Region')}}</label>
                            <input class="lnd-input" type="text" name="wasabi_default_region" value="{{get_static_option_central('wasabi_default_region')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('WAS Bucket')}}</label>
                            <input class="lnd-input" type="text" name="wasabi_bucket" value="{{get_static_option_central('wasabi_bucket')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('WAS Endpoint')}}</label>
                            <input class="lnd-input" type="text" name="wasabi_endpoint" value="{{get_static_option_central('wasabi_endpoint')}}">
                        </div>
                    </div>

                    {{-- Cloudflare R2 --}}
                    <div class="credentials cloudFlareR2 space-y-4 {{get_static_option_central('storage_driver') != 'cloudFlareR2' ? 'hidden' : ''}}">
                        <div class="pt-4 border-t border-main">
                            <h4 class="text-sm font-bold text-dark font-urbanist mb-4">{{__('Cloudflare R2 Credentials')}}</h4>
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Cloudflare R2 Access Key ID')}}</label>
                            <input class="lnd-input" type="text" name="cloudflare_r2_access_key_id" value="{{get_static_option_central('cloudflare_r2_access_key_id')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Cloudflare R2 Secret Access Key')}}</label>
                            <input class="lnd-input" type="text" name="cloudflare_r2_secret_access_key" value="{{get_static_option_central('cloudflare_r2_secret_access_key')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Cloudflare R2 Bucket')}}</label>
                            <input class="lnd-input" type="text" name="cloudflare_r2_bucket" value="{{get_static_option_central('cloudflare_r2_bucket')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Cloudflare R2 URL')}}</label>
                            <input class="lnd-input" type="text" name="cloudflare_r2_url" value="{{get_static_option_central('cloudflare_r2_url')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Cloudflare R2 Endpoint')}}</label>
                            <input class="lnd-input" type="text" name="cloudflare_r2_endpoint" value="{{get_static_option_central('cloudflare_r2_endpoint')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Cloudflare R2 Use Path Style Endpoint')}}</label>
                            <input class="lnd-input" type="text" name="cloudflare_r2_use_path_style_endpoint" value="{{get_static_option_central('cloudflare_r2_use_path_style_endpoint')}}">
                        </div>
                    </div>

                    {{-- AWS S3 --}}
                    <div class="credentials s3 space-y-4 {{get_static_option_central('storage_driver') != 's3' ? 'hidden' : ''}}">
                        <div class="pt-4 border-t border-main">
                            <h4 class="text-sm font-bold text-dark font-urbanist mb-4">{{__('AWS S3 Credentials')}}</h4>
                        </div>
                        <div>
                            <label class="lnd-label">{{__('AWS Access Key ID')}}</label>
                            <input class="lnd-input" type="text" name="aws_access_key_id" value="{{get_static_option_central('aws_access_key_id')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('AWS Secret Access Key')}}</label>
                            <input class="lnd-input" type="text" name="aws_secret_access_key" value="{{get_static_option_central('aws_secret_access_key')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('AWS Default Region')}}</label>
                            <input class="lnd-input" type="text" name="aws_default_region" value="{{get_static_option_central('aws_default_region')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('AWS Bucket')}}</label>
                            <input class="lnd-input" type="text" name="aws_bucket" value="{{get_static_option_central('aws_bucket')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('AWS URL')}}</label>
                            <input class="lnd-input" type="text" name="aws_url" value="{{get_static_option_central('aws_url')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('AWS Endpoint')}}</label>
                            <input class="lnd-input" type="text" name="aws_endpoint" value="{{get_static_option_central('aws_endpoint')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('AWS Use Path Style Endpoint')}}</label>
                            <input class="lnd-input" type="text" name="aws_use_path_style_endpoint" value="{{get_static_option_central('aws_use_path_style_endpoint')}}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 mt-5 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('change', '#storage-driver', function () {
                let driver = $(this).val();
                $('.credentials').addClass('hidden');
                $('.credentials.' + driver).removeClass('hidden');
            });
        });
    </script>
@endsection
