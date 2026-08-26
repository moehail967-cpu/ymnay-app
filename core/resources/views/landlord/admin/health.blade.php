@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Admin Health')}}
@endsection

@section('style')
<style>
    @keyframes pulse-scale {
        0%, 100% { transform: scale(1); }
        50%       { transform: scale(1.2); }
    }
    .issue-animation { animation: pulse-scale 1.8s ease-in-out infinite; }
</style>
@endsection

@section('content')
@php
    $display_errors        = "ini_get method not allowed";
    $memory_limit          = "ini_get method not allowed";
    $post_max_size         = "ini_get method not allowed";
    $max_execution_time    = "ini_get method not allowed";
    $upload_max_filesize   = "ini_get method not allowed";

    $issues = 0;
    if (function_exists('ini_get')) {
        $display_errors      = ini_get("display_errors");
        $memory_limit        = ini_get("memory_limit");
        $post_max_size       = ini_get("post_max_size");
        $max_execution_time  = ini_get("max_execution_time");
        $upload_max_filesize = ini_get("upload_max_filesize");

        foreach ([
            [(int)str_replace('M', '', $memory_limit),        512],
            [(int)str_replace('M', '', $post_max_size),       128],
            [(int)$max_execution_time,                         300],
            [(int)str_replace('M', '', $upload_max_filesize), 128],
        ] ?? [] as $item) {
            if (current($item) < last($item)) $issues++;
        }
    }

    $website_has_permission_to_create_database = get_static_option('website_has_permission_to_create_database');
    $website_wildcard_subdomain_working        = get_static_option('website_wildcard_subdomain_working');
    $website_cron_job                          = get_static_option('website_cron_job');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Main Column ── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Issues Alert --}}
        @if($issues > 0)
            <div class="flex items-start gap-3 px-5 py-4 rounded-xl border border-red-200 bg-red-50">
                <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="mdi mdi-alert-circle-outline text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-red-600">
                        {!! __(number_to_word($issues) . ' ' . ($issues > 1 ? 'issues detected!' : 'issue detected!')) !!}
                    </p>
                    <p class="text-xs text-red-500 mt-0.5">
                        {{__('If all necessary values are not configured correctly, it may affect the system\'s functionality.')}}
                    </p>
                </div>
            </div>
        @endif

        {{-- System Versions --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-main">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center shrink-0">
                    <i class="mdi mdi-server-outline text-primary text-base"></i>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('System Information')}}</h5>
                    <p class="text-xs text-muted">{{__('Runtime & framework versions')}}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y divide-main">
                @php
                    $sysItems = [
                        ['label' => 'PHP',      'value' => 'V ' . phpversion(),                                        'icon' => 'mdi-language-php',    'color' => 'text-violet-500', 'bg' => 'bg-violet-50'],
                        ['label' => 'MySQL',    'value' => 'V ' . DB::select("SELECT VERSION() as version")[0]->version,'icon' => 'mdi-database-outline', 'color' => 'text-blue-500',   'bg' => 'bg-blue-50'],
                        ['label' => 'Laravel',  'value' => 'V ' . app()->version(),                                    'icon' => 'mdi-fire',            'color' => 'text-red-500',    'bg' => 'bg-red-50'],
                        ['label' => 'DB Engine','value' => \Config::get('database.connections.mysql.engine') ?? 'N/A', 'icon' => 'mdi-cog-outline',     'color' => 'text-gray-500',   'bg' => 'bg-gray-100'],
                    ];
                @endphp
                @foreach($sysItems as $item)
                    <div class="flex flex-col items-center justify-center gap-2 py-5 px-4 text-center">
                        <div class="w-10 h-10 rounded-full {{ $item['bg'] }} flex items-center justify-center">
                            <i class="mdi {{ $item['icon'] }} {{ $item['color'] }} text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-dark">{{ $item['label'] }}</span>
                        <span class="text-[11px] font-medium text-muted bg-secondary px-2 py-0.5 rounded-full border border-main">{{ $item['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Server Requirements --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-main">
                <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center shrink-0">
                    <i class="mdi mdi-tune-vertical-variant text-warning text-base"></i>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Server Requirements')}}</h5>
                    <p class="text-xs text-muted">{{__('Recommended PHP configuration values')}}</p>
                </div>
            </div>
            <div class="divide-y divide-main">
                @php
                    $requirements = [
                        [
                            'label'   => __('Memory Limit'),
                            'hint'    => __('Recommended: 512M or higher'),
                            'value'   => $memory_limit,
                            'ok'      => (int)str_replace('M', '', $memory_limit) >= 512,
                            'animate' => true,
                        ],
                        [
                            'label'   => __('Maximum Execution Time'),
                            'hint'    => __('Recommended: 300s or higher'),
                            'value'   => $max_execution_time,
                            'ok'      => (int)$max_execution_time >= 300,
                            'animate' => true,
                        ],
                        [
                            'label'   => __('Max File Upload Size'),
                            'hint'    => __('Recommended: 128M or higher'),
                            'value'   => $upload_max_filesize,
                            'ok'      => (int)str_replace('M', '', $upload_max_filesize) >= 128,
                            'animate' => true,
                        ],
                        [
                            'label'   => __('Post Max Size'),
                            'hint'    => __('Recommended: 128M or higher'),
                            'value'   => $post_max_size,
                            'ok'      => (int)str_replace('M', '', $post_max_size) >= 128,
                            'animate' => true,
                        ],
                        [
                            'label'   => __('Display Errors'),
                            'hint'    => __('Should be Off in production'),
                            'value'   => $display_errors ?: 'Off',
                            'ok'      => $display_errors != 'Off' ? false : true,
                            'animate' => false,
                        ],
                    ];
                @endphp
                @foreach($requirements as $row)
                    <div class="flex items-center justify-between gap-4 px-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-dark">{{ $row['label'] }}</p>
                            <p class="text-[11px] text-muted mt-0.5">{{ $row['hint'] }}</p>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $row['ok']
                                ? 'bg-green-50 text-green-700 border border-green-200'
                                : 'bg-red-50 text-red-600 border border-red-200 ' . ($row['animate'] ? 'issue-animation' : '') }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $row['ok'] ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $row['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Server Configuration --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-main">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center shrink-0">
                    <i class="mdi mdi-shield-check-outline text-success text-base"></i>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Server Configuration')}}</h5>
                    <p class="text-xs text-muted">{{__('Hosting & environment permissions')}}</p>
                </div>
            </div>
            <div class="divide-y divide-main">
                @php
                    $configs = [
                        ['label' => __('Database Create Permission'), 'value' => $website_has_permission_to_create_database, 'ok' => $website_has_permission_to_create_database === 'yes'],
                        ['label' => __('Wildcard Subdomain'),         'value' => $website_has_permission_to_create_database, 'ok' => $website_has_permission_to_create_database == 'yes'],
                        ['label' => __('Wildcard SSL'),               'value' => $website_wildcard_subdomain_working,        'ok' => $website_wildcard_subdomain_working == 'yes'],
                        ['label' => __('Cron Job'),                   'value' => $website_cron_job,                          'ok' => $website_cron_job == 'yes'],
                    ];
                @endphp
                @foreach($configs as $row)
                    <div class="flex items-center justify-between gap-4 px-6 py-4">
                        <p class="text-sm font-medium text-dark">{{ $row['label'] }}</p>
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $row['ok']
                                ? 'bg-green-50 text-green-700 border border-green-200'
                                : 'bg-red-50 text-red-600 border border-red-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $row['ok'] ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $row['value'] ?? 'N/A' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── Sidebar: PHP Extensions ── --}}
    <div class="lg:col-span-1">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-5">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main">
                <div class="w-9 h-9 rounded-lg bg-secondary flex items-center justify-center shrink-0 border border-main">
                    <i class="mdi mdi-puzzle-outline text-muted text-base"></i>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('PHP Extensions')}}</h5>
                    <p class="text-xs text-muted">{{ count(get_loaded_extensions()) }} {{__('loaded')}}</p>
                </div>
            </div>
            <div class="px-5 py-4 flex flex-wrap gap-1.5 max-h-[520px] overflow-y-auto">
                @foreach(get_loaded_extensions() ?? [] as $ext)
                    <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-medium bg-secondary border border-main text-muted">
                        {{ $ext }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
    <x-datatable.js/>
    <x-media-upload.tw-js/>
@endsection
