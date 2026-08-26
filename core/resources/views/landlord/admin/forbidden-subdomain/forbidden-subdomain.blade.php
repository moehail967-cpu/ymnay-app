@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Forbidden Subdomains')}} @endsection

@section('style')
    <style>
        .tag-wrap { display: flex; flex-wrap: wrap; gap: 0.375rem; }
        .tag-item {
            display: inline-flex; align-items: center; gap: 0.375rem;
            padding: 0.25rem 0.625rem; border-radius: 0.5rem;
            background: var(--color-primary-soft, #e0f0f0);
            border: 1px solid var(--color-border-main, #e5e7eb);
            font-size: 0.8125rem; font-weight: 600;
            color: var(--color-primary, #1a5c4e);
        }
        .tag-item .tag-remove {
            width: 1rem; height: 1rem; border-radius: 9999px; border: none;
            background: var(--color-primary, #1a5c4e); color: #fff;
            font-size: 0.625rem; cursor: pointer; display: inline-flex;
            align-items: center; justify-content: center; transition: opacity 0.15s;
            line-height: 1; padding: 0;
        }
        .tag-item .tag-remove:hover { opacity: 0.7; }
    </style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="max-w-full">
    <div class="bg-surface rounded-xl shadow-main border border-main mb-6">

        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-block-helper text-danger text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Forbidden Subdomains')}}</h3>
                <p class="text-xs text-muted">{{__('Words that cannot be used as subdomains')}}</p>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('landlord.admin.store.forbidden.subdomain') }}" method="post" id="forbiddenForm">
            @csrf
            <div class="px-4 sm:px-6 py-6 space-y-4">

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subdomains')}}</label>

                    {{-- Tags display --}}
                    <div class="tag-wrap mb-3" id="tagContainer">
                        {{-- filled by JS --}}
                    </div>

                    {{-- Input --}}
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-web text-lg text-primary"></i>
                        <input type="text" id="tagInput"
                               placeholder="{{__('Type a word and press Enter')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>

                    <input type="hidden" name="forbidden_subdomains" id="hiddenTags" value="{{ get_static_option('forbidden_subdomains') }}">
                </div>

                <p class="text-[11px] text-muted flex items-start gap-1.5">
                    <i class="mdi mdi-information-outline text-primary text-sm flex-shrink-0 mt-0.5"></i>
                    {{__('Add words without spaces. You can use dashes (-). Example: admin, www, super-user')}}
                </p>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end px-4 sm:px-6 py-4 border-t border-main bg-secondary rounded-b-xl">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Save Changes')}}
                </button>
            </div>
        </form>

    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    "use strict";

    var container = document.getElementById('tagContainer');
    var input     = document.getElementById('tagInput');
    var hidden    = document.getElementById('hiddenTags');

    function getTags() {
        return hidden.value ? hidden.value.split(',').map(function (t) { return t.trim(); }).filter(Boolean) : [];
    }

    function setTags(tags) {
        hidden.value = tags.join(',');
    }

    function render() {
        var tags = getTags();
        container.innerHTML = '';
        tags.forEach(function (tag) {
            var el = document.createElement('span');
            el.className = 'tag-item';
            el.textContent = tag;

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tag-remove';
            btn.innerHTML = '&times;';
            btn.addEventListener('click', function () {
                removeTag(tag);
            });

            el.appendChild(btn);
            container.appendChild(el);
        });
    }

    function addTag(val) {
        val = val.trim().toLowerCase().replace(/[^a-z0-9\-]/g, '');
        if (!val) return;
        var tags = getTags();
        if (tags.indexOf(val) === -1) {
            tags.push(val);
            setTags(tags);
            render();
        }
    }

    function removeTag(val) {
        var tags = getTags().filter(function (t) { return t !== val; });
        setTags(tags);
        render();
    }

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag(input.value);
            input.value = '';
        }
        if (e.key === 'Backspace' && !input.value) {
            var tags = getTags();
            if (tags.length) {
                tags.pop();
                setTags(tags);
                render();
            }
        }
    });

    render();
})();
</script>
@endsection
