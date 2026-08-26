@php
    $p = $prefix ?? '';  // '' for add, 'edit_' for edit
    $isEdit = $p === 'edit_';
@endphp

{{-- ── Title ── --}}
<div class="cp-field mb-4">
    <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Coupon Title') }} <span class="text-danger">*</span></label>
    <input type="text" name="{{ $isEdit ? 'title' : 'title' }}" id="{{ $p }}title"
        class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark placeholder-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
        placeholder="{{ __('e.g. Summer Sale 20%') }}">
</div>

{{-- ── Code ── --}}
<div class="cp-field mb-4">
    <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Coupon Code') }} <span class="text-danger">*</span></label>
    <input type="text" name="code" id="{{ $p }}code"
        class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark placeholder-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition font-mono"
        placeholder="{{ __('e.g. SUMMER20') }}"
        style="text-transform: uppercase;">
</div>

{{-- ── Discount On ── --}}
<div class="cp-field mb-4">
    <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Discount On') }} <span class="text-danger">*</span></label>
    <select name="discount_on" id="{{ $p }}discount_on"
        class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
        <option value="">{{ __('Select an option') }}</option>
        @foreach ($coupon_apply_options as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
</div>

{{-- ── Conditional: Category ── --}}
<div class="cp-field cp-conditional mb-4" id="{{ $p }}form_category" style="display:none;">
    <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Category') }}</label>
    <select name="category" id="{{ $p }}category"
        class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
        <option value="">{{ __('Select a Category') }}</option>
        @foreach ($all_categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
</div>

{{-- ── Conditional: Sub-Category ── --}}
<div class="cp-field cp-conditional mb-4" id="{{ $p }}form_subcategory" style="display:none;">
    <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Sub-Category') }}</label>
    <select name="subcategory" id="{{ $p }}subcategory"
        class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
        <option value="">{{ __('Select a Sub-Category') }}</option>
        @foreach ($all_subcategories as $subcategory)
            <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
        @endforeach
    </select>
</div>

{{-- ── Conditional: Child Category ── --}}
<div class="cp-field cp-conditional mb-4" id="{{ $p }}form_childcategory" style="display:none;">
    <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Child Category') }}</label>
    <select name="childcategory" id="{{ $p }}childcategory"
        class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
        <option value="">{{ __('Select a Child Category') }}</option>
        @foreach ($all_childcategories as $childcategory)
            <option value="{{ $childcategory->id }}">{{ $childcategory->name }}</option>
        @endforeach
    </select>
</div>

{{-- ── Conditional: Products ── --}}
<div class="cp-field cp-conditional mb-4" id="{{ $p }}form_products" style="display:none;">
    <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Products') }}</label>
    <select name="products[]" id="{{ $p }}products" multiple
        class="w-full px-3 py-2 text-sm border border-main rounded-xl bg-surface text-dark focus:outline-none focus:border-primary transition">
    </select>
    <p class="text-xs text-muted mt-1">{{ __('Hold Ctrl / Cmd to select multiple products') }}</p>
</div>

{{-- ── Discount Amount + Type (side by side) ── --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    <div class="cp-field">
        <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Discount') }} <span class="text-danger">*</span></label>
        <input type="number" name="discount" id="{{ $p }}discount" min="0" step="0.01"
            class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark placeholder-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
            placeholder="0">
    </div>
    <div class="cp-field">
        <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Type') }}</label>
        <select name="discount_type" id="{{ $p }}discount_type"
            class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
            <option value="percentage">{{ __('Percentage %') }}</option>
            <option value="amount">{{ __('Fixed Amount') }}</option>
        </select>
    </div>
</div>

{{-- ── Divider: Usage Restrictions ── --}}
<div class="flex items-center gap-3 mb-4">
    <span class="flex-1 h-px bg-main"></span>
    <span class="text-[10px] font-bold text-subtle uppercase tracking-widest">{{ __('Usage Restrictions') }}</span>
    <span class="flex-1 h-px bg-main"></span>
</div>

{{-- ── Minimum Quantity ── --}}
<div class="cp-field mb-4">
    <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Minimum Quantity') }}</label>
    <input type="number" name="minimum_quantity" id="{{ $p }}minimum_quantity" min="0" step="1"
        class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark placeholder-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
        placeholder="{{ __('0 = no limit') }}">
    <p class="text-xs text-muted mt-1">{{ __('Minimum items in cart') }}</p>
</div>

{{-- ── Min / Max Spend (side by side) ── --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    <div class="cp-field">
        <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Min Spend') }}</label>
        <input type="number" name="minimum_spend" id="{{ $p }}minimum_spend" min="0" step="0.01"
            class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark placeholder-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
            placeholder="0.00">
        <p class="text-xs text-muted mt-1">{{ __('Min cart subtotal') }}</p>
    </div>
    <div class="cp-field">
        <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Max Spend') }}</label>
        <input type="number" name="maximum_spend" id="{{ $p }}maximum_spend" min="0" step="0.01"
            class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark placeholder-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
            placeholder="0.00">
        <p class="text-xs text-muted mt-1">{{ __('Max cart subtotal') }}</p>
    </div>
</div>

{{-- ── Usage Limits (side by side) ── --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    <div class="cp-field">
        <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Total Use Limit') }}</label>
        <input type="number" name="usage_limit_per_coupon" id="{{ $p }}usage_limit_per_coupon" min="0" step="1"
            class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark placeholder-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
            placeholder="{{ __('0 = unlimited') }}">
    </div>
    <div class="cp-field">
        <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Limit Per User') }}</label>
        <input type="number" name="usage_limit_per_user" id="{{ $p }}usage_limit_per_user" min="0" step="1"
            class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark placeholder-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
            placeholder="{{ __('0 = unlimited') }}">
    </div>
</div>

{{-- ── Divider ── --}}
<div class="flex items-center gap-3 mb-4">
    <span class="flex-1 h-px bg-main"></span>
    <span class="text-[10px] font-bold text-subtle uppercase tracking-widest">{{ __('Schedule & Status') }}</span>
    <span class="flex-1 h-px bg-main"></span>
</div>

{{-- ── Expire Date + Status (side by side) ── --}}
<div class="grid grid-cols-2 gap-3 mb-6">
    <div class="cp-field">
        <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Expire Date') }} <span class="text-danger">*</span></label>
        <input type="text" name="expire_date" id="{{ $p }}expire_date"
            class="flatpickr w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark placeholder-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
            placeholder="{{ __('Pick a date') }}">
    </div>
    <div class="cp-field">
        <label class="block text-xs font-semibold text-dark mb-1.5 uppercase tracking-wide">{{ __('Status') }}</label>
        <select name="status" id="{{ $p }}status"
            class="w-full px-3 py-2.5 text-sm border border-main rounded-xl bg-surface text-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
            <option value="publish">{{ __('Active') }}</option>
            <option value="draft">{{ __('Draft') }}</option>
        </select>
    </div>
</div>

@if(!$isEdit)
{{-- ── Submit Button (Add form only — edit modal has its own footer button) ── --}}
<button type="submit"
    class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold bg-primary text-white rounded-xl hover:opacity-90 active:scale-[.98] transition">
    <i class="mdi mdi-ticket-percent-outline text-base"></i>
    {{ $submit_label ?? __('Add Coupon') }}
</button>
@endif
