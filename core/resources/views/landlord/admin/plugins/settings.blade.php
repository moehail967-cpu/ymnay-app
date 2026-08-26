@extends(route_prefix().'admin.admin-master')
@section('title') {{ $manifest->name }} — {{__('Settings')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

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
                    <a href="{{ route('landlord.admin.plugins.index') }}" class="hover:text-primary transition-colors">{{__('Plugins')}}</a>
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

@if(empty($fields))
    <div class="bg-surface rounded-xl border border-main p-12 text-center">
        <i class="mdi mdi-cog-outline text-5xl text-subtle mb-3 block"></i>
        <p class="text-base font-semibold text-dark mb-1">{{__('No settings registered')}}</p>
        <p class="text-sm text-muted">{{__('This plugin has not declared any configurable settings.')}}</p>
    </div>
@else
    <form method="POST" action="{{ route('landlord.admin.plugin.settings.save', $manifest->id) }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Fields column --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($fields as $field)
                    @include('landlord.admin.plugins.partials.settings-field', ['field' => $field, 'current' => $current])
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
                    <a href="{{ route('landlord.admin.plugins.index') }}"
                       class="mt-2 w-full py-2 px-4 border border-main text-sm font-semibold text-muted rounded-lg hover:bg-secondary transition-colors flex items-center justify-center gap-2">
                        <i class="mdi mdi-arrow-left text-sm"></i>
                        {{__('Back to Plugins')}}
                    </a>
                </div>

                <div class="bg-surface rounded-xl border border-main p-5">
                    <h4 class="text-xs font-bold text-muted uppercase tracking-wide mb-3">{{__('Plugin Info')}}</h4>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <dt class="text-muted">{{__('ID')}}</dt>
                            <dd class="font-mono text-dark">{{ $manifest->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">{{__('Version')}}</dt>
                            <dd class="text-dark">{{ $manifest->version }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">{{__('Type')}}</dt>
                            <dd class="text-dark capitalize">{{ $manifest->type }}</dd>
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

@endsection

@section('scripts')
<script>
// Repeater add/remove rows
document.addEventListener('click', function (e) {
    if (e.target.closest('.repeater-remove')) {
        e.target.closest('.repeater-row').remove();
    }

    var addBtn = e.target.closest('.repeater-add');
    if (addBtn) {
        var wrapper    = addBtn.closest('.repeater-wrapper');
        var fieldName  = wrapper.dataset.field;
        var subFields  = JSON.parse(addBtn.dataset.template);
        var rowCount   = wrapper.querySelectorAll('.repeater-row').length;

        var row = document.createElement('div');
        row.className = 'repeater-row flex items-start gap-2 p-3 bg-secondary rounded-lg border border-main';
        row.innerHTML = subFields.map(function (sf) {
            return '<div class="flex-1">'
                + '<label class="block text-xs text-muted mb-1">' + (sf.label || sf.key) + '</label>'
                + '<input type="text" name="' + fieldName + '[' + rowCount + '][' + sf.key + ']" value=""'
                + ' class="w-full px-2 py-1.5 text-sm border border-main rounded-lg bg-surface focus:outline-none focus:border-primary">'
                + '</div>';
        }).join('') + '<button type="button" class="repeater-remove mt-5 text-red-400 hover:text-red-600 transition-colors flex-shrink-0">'
            + '<i class="mdi mdi-minus-circle text-lg"></i></button>';

        addBtn.before(row);
    }
});
</script>
@endsection
