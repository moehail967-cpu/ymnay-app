@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Theme Settings')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-cog-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Theme Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Configure theme visibility options')}}</p>
            </div>
        </div>
        <a href="{{route('landlord.admin.theme')}}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
            <i class="mdi mdi-arrow-left text-base"></i> {{__('All Themes')}}
        </a>
    </div>

    {{-- Body --}}
    <form action="{{route('landlord.admin.theme.settings')}}" method="POST">
        @csrf
        <div class="p-4 sm:p-6 space-y-4">

            {{-- Frontend toggle --}}
            <div class="flex items-center justify-between bg-secondary border border-main rounded-xl px-4 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-monitor-eye text-info text-sm"></i>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-dark">{{__('Show upcoming themes on home page')}}</span>
                        <p class="text-[11px] text-muted mt-0.5">{{__('Display upcoming themes to visitors on the frontend.')}}</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 ml-3">
                    <input type="checkbox" name="up_coming_themes_frontend" class="sr-only peer" {{get_static_option('up_coming_themes_frontend') ? 'checked' : ''}}>
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full after:shadow-sm"></div>
                </label>
            </div>

            {{-- Backend toggle --}}
            <div class="flex items-center justify-between bg-secondary border border-main rounded-xl px-4 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-shield-home-outline text-warning text-sm"></i>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-dark">{{__('Show upcoming themes on admin panel')}}</span>
                        <p class="text-[11px] text-muted mt-0.5">{{__('Display upcoming themes in the admin dashboard.')}}</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 ml-3">
                    <input type="checkbox" name="up_coming_themes_backend" class="sr-only peer" {{get_static_option('up_coming_themes_backend') ? 'checked' : ''}}>
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full after:shadow-sm"></div>
                </label>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-4 sm:px-6 py-4 border-t border-main flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                <i class="mdi mdi-content-save-outline text-base"></i>
                {{__('Update Settings')}}
            </button>
        </div>
    </form>
</div>

@endsection
