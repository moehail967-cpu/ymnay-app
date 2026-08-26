@extends('tenant.admin.admin-master')
@section('title')
    {{ __('Tenant Dashboard Theme Manage') }}
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    $selected_theme = get_static_option('tenant_admin_dashboard_theme') ?? '';
@endphp

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-palette-outline text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Dashboard Theme')}}</h3>
            <p class="text-xs text-muted">{{__('Select a theme to activate for your admin dashboard')}}</p>
        </div>
    </div>

    <div class="p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($all_themes as $theme)
                @php $isActive = $theme->slug == $selected_theme; @endphp
                <div class="theme-card group cursor-pointer" data-slug="{{ $theme->slug }}">
                    <form action="{{ route('tenant.admin.dashboard.theme.update', $theme->slug) }}" method="post">
                        @csrf
                        <div class="relative rounded-xl overflow-hidden border-2 transition-all duration-300
                            {{ $isActive ? 'border-primary shadow-lg ring-2 ring-primary/20' : 'border-main hover:border-primary/50 hover:shadow-md' }}">

                            {{-- Screenshot --}}
                            <div class="relative h-[220px] overflow-hidden bg-secondary">
                                @if($theme?->screenshort)
                                    <div class="absolute inset-0 bg-cover bg-top transition-all duration-[2s] ease-linear group-hover:bg-bottom"
                                         style="background-image: url('{{ $theme->screenshort }}')"></div>
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <i class="mdi mdi-image-outline text-4xl text-muted"></i>
                                    </div>
                                @endif

                                {{-- Active Badge --}}
                                @if($isActive)
                                    <div class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary text-white text-[11px] font-bold shadow-lg">
                                        <i class="mdi mdi-check-circle text-sm"></i> {{__('Active')}}
                                    </div>
                                @endif

                                {{-- Hover Overlay --}}
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                    @if(!$isActive)
                                        <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/90 text-dark text-xs font-bold shadow">
                                            <i class="mdi mdi-cursor-default-click-outline text-sm"></i> {{__('Click to Activate')}}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Theme Name --}}
                            <div class="px-4 py-3 flex items-center justify-between {{ $isActive ? 'bg-primary/5' : 'bg-surface' }}">
                                <span class="text-sm font-semibold text-dark truncate">{{ $theme->name }}</span>
                                @if($isActive)
                                    <span class="w-2.5 h-2.5 rounded-full bg-primary flex-shrink-0 animate-pulse"></span>
                                @else
                                    <i class="mdi mdi-chevron-right text-muted text-base group-hover:text-primary transition-colors"></i>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function ($) {
            "use strict";

            $(document).on('click', '.theme-card', function () {
                let slug = $(this).data('slug');
                let form = $(this).find('form');

                let formAction = form.attr('action').replace(/\/([^\/]+)$/, '/' + slug);
                form.attr('action', formAction);

                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You want to activate this theme!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--color-primary, #6366f1)',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{ __("Yes, activate it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
