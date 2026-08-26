@extends('tenant.admin.admin-master')
@section('title') {{ $manifest->name }} — {{__('Settings')}} @endsection

@section('content')

@php $hasDocsView = view()->exists($manifest->id . '::admin.docs'); @endphp

{{-- Breadcrumb header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-puzzle text-primary text-xl"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-dark font-urbanist">{{ $manifest->name }}</h3>
                <div class="flex items-center gap-1 text-xs text-muted">
                    <a href="{{ route('tenant.admin.plugins.index') }}" class="hover:text-primary transition-colors">{{__('Plugins')}}</a>
                    <i class="mdi mdi-chevron-right text-sm"></i>
                    <span>{{__('Settings')}}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-1.5 text-xs text-subtle">
            <i class="mdi mdi-tag-outline text-xs"></i>
            <span>v{{ $manifest->version }}</span>
        </div>
    </div>
</div>

@if($hasDocsView)
{{-- Tab strip --}}
<div class="flex gap-1 mb-6 border-b border-main" id="plugin-tabs">
    <button type="button" data-tab="settings"
            class="plugin-tab px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px transition-colors border-primary text-primary">
        <i class="mdi mdi-cog-outline me-1"></i>{{__('Settings')}}
    </button>
    <button type="button" data-tab="docs"
            class="plugin-tab px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px transition-colors border-transparent text-muted hover:text-dark">
        <i class="mdi mdi-book-open-variant me-1"></i>{{__('Setup Guide')}}
    </button>
</div>
@endif

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
        <i class="mdi mdi-check-circle text-green-500 text-base"></i>
        {{ session('success') }}
    </div>
@endif

{{-- Settings panel --}}
<div id="panel-settings">
    @if(empty($fields))
        <div class="bg-surface rounded-xl border border-main p-12 text-center">
            <i class="mdi mdi-cog-outline text-5xl text-subtle mb-3 block"></i>
            <p class="text-base font-semibold text-dark mb-1">{{__('No settings registered')}}</p>
            <p class="text-sm text-muted">{{__('This plugin has not declared any configurable settings.')}}</p>
        </div>
    @else
        <form method="POST" action="{{ route('tenant.admin.plugin.settings.save', $manifest->id) }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Fields column --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($fields as $field)
                        @include('tenant.admin.plugins.partials.settings-field', ['field' => $field, 'current' => $current])
                    @endforeach
                </div>

                {{-- Sidebar --}}
                <div class="space-y-4">
                    <div class="bg-surface rounded-xl border border-main p-5 sticky top-4">
                        <h4 class="text-sm font-bold text-dark mb-3">{{__('Save Settings')}}</h4>
                        <button type="submit"
                                class="w-full py-2.5 px-4 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                            <i class="mdi mdi-content-save text-base"></i>
                            {{__('Save Changes')}}
                        </button>
                        <a href="{{ route('tenant.admin.plugins.index') }}"
                           class="mt-2 w-full py-2 px-4 border border-main text-sm font-semibold text-muted rounded-lg hover:bg-secondary transition-colors flex items-center justify-center gap-2">
                            <i class="mdi mdi-arrow-left text-sm"></i>
                            {{__('Back to Plugins')}}
                        </a>
                    </div>

                    <div class="bg-surface rounded-xl border border-main p-5">
                        <h4 class="text-xs font-bold text-muted uppercase tracking-wide mb-3">{{__('Plugin Info')}}</h4>
                        <dl class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-muted">{{__('Version')}}</dt>
                                <dd class="text-dark">{{ $manifest->version }}</dd>
                            </div>
                            @if($manifest->author)
                            <div class="flex justify-between">
                                <dt class="text-muted">{{__('Author')}}</dt>
                                <dd class="text-dark">{{ $manifest->author }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

{{-- Docs panel (only rendered if the plugin provides a docs view) --}}
@if($hasDocsView)
<div id="panel-docs" style="display:none">
    @include($manifest->id . '::admin.docs')
</div>
@endif

@endsection

@section('scripts')
<script>
(function () {
    var tabs   = document.querySelectorAll('.plugin-tab');
    var panels = { settings: document.getElementById('panel-settings'), docs: document.getElementById('panel-docs') };

    if (!tabs.length) return;

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var active = btn.dataset.tab;

            tabs.forEach(function (t) {
                var on = t.dataset.tab === active;
                t.classList.toggle('border-primary', on);
                t.classList.toggle('text-primary', on);
                t.classList.toggle('border-transparent', !on);
                t.classList.toggle('text-muted', !on);
            });

            Object.keys(panels).forEach(function (key) {
                if (panels[key]) panels[key].style.display = key === active ? '' : 'none';
            });
        });
    });
}());

document.addEventListener('click', function (e) {
    if (e.target.closest('.repeater-remove')) {
        e.target.closest('.repeater-row').remove();
    }

    var addBtn = e.target.closest('.repeater-add');
    if (addBtn) {
        var wrapper   = addBtn.closest('.repeater-wrapper');
        var fieldName = wrapper.dataset.field;
        var subFields = JSON.parse(wrapper.dataset.subfields || '[]');
        var rowCount  = wrapper.querySelectorAll('.repeater-row').length;
        var inputCls  = 'w-full px-2 py-1.5 text-sm border border-main rounded-lg bg-surface focus:outline-none focus:border-primary';

        var row = document.createElement('div');
        row.className = 'repeater-row flex items-start gap-2 p-3 bg-secondary rounded-lg border border-main';
        row.innerHTML = subFields.map(function (sf) {
            var inputHtml;
            if (sf.type === 'select' && sf.options) {
                var opts = Object.entries(sf.options).map(function(e) {
                    return '<option value="' + e[0] + '">' + e[1] + '</option>';
                }).join('');
                inputHtml = '<select name="' + fieldName + '[' + rowCount + '][' + sf.key + ']" class="' + inputCls + '">' + opts + '</select>';
            } else if (sf.type === 'number') {
                inputHtml = '<input type="number" name="' + fieldName + '[' + rowCount + '][' + sf.key + ']" value="" class="' + inputCls + '">';
            } else {
                inputHtml = '<input type="text" name="' + fieldName + '[' + rowCount + '][' + sf.key + ']" value="" class="' + inputCls + '">';
            }
            return '<div class="flex-1 min-w-0"><label class="block text-xs text-muted mb-1">' + (sf.label || sf.key) + '</label>' + inputHtml + '</div>';
        }).join('') + '<button type="button" class="repeater-remove mt-5 text-red-400 hover:text-red-600 transition-colors flex-shrink-0"><i class="mdi mdi-minus-circle text-lg"></i></button>';

        addBtn.before(row);
    }
});
</script>
@endsection
