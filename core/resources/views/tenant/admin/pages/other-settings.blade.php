@extends('tenant.admin.admin-master')
@section('title')
    {{__('Other Settings')}}
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-cog-outline text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Other Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Configure header button text and URLs per language')}}</p>
        </div>
    </div>

    <div class="p-4 sm:p-6">
        <form method="post" action="{{route('tenant.admin.other.settings')}}">
            @csrf

            <x-lang-tab>
                @foreach(\App\Facades\GlobalLanguage::all_languages() as $lang)
                    <x-slot :name="$lang->slug">
                        <div class="space-y-5 pt-3">
                            @foreach(['one','two','three','four','five','six'] as $num)
                                <div class="p-4 bg-secondary rounded-xl border border-main space-y-4">
                                    <h4 class="text-xs font-bold text-dark uppercase tracking-wider flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-primary text-white text-[10px] font-bold">{{$loop->iteration}}</span>
                                        {{__('Home')}} {{ucfirst($num)}}
                                    </h4>
                                    <x-fields.input type="text" value="{{get_static_option('home_'.$num.'_header_button_'.$lang->slug.'_text')}}" name="home_{{$num}}_header_button_{{$lang->slug}}_text" label="{{__('Header Button Text')}}"/>
                                    <x-fields.input type="text" value="{{get_static_option('home_'.$num.'_header_button_'.$lang->slug.'_url')}}" name="home_{{$num}}_header_button_{{$lang->slug}}_url" label="{{__('Header Button URL')}}"/>
                                </div>
                            @endforeach
                        </div>
                    </x-slot>
                @endforeach
            </x-lang-tab>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition mt-5">
                <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
            </button>
        </form>
    </div>
</div>

@endsection
