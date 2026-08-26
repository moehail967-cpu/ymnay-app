@php
    if(!isset($selectedcat)){
        $selectedcat = null;
    }

    if(!isset($selectedSubCat)){
        $selectedSubCat = null;
    }

    $categories       = !isset($categories)       ? [] : $categories;
    $sub_categories   = !isset($subCategories)    ? [] : $subCategories;
    $child_categories = !isset($childCategories)  ? [] : $childCategories;
    $selected_child   = !isset($selectedChildCat) ? [] : $selectedChildCat;
@endphp

<div class="dashboard-attr-add-wrapper mb-2">
    <h4 class="product-section-title">{{ __("Categories") }}</h4>

    <div class="space-y-4">

        {{-- Category --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
                {{ __("Category") }} <x-fields.mandatory-indicator/>
            </label>
            <select name="category_id" id="category" class="lnd-input">
                <option value="">{{ __("Select Category") }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $selectedcat === $category->id ? "selected" : "" }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <span class="product-error-msg category-error"></span>
        </div>

        {{-- Sub Category --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
                {{ __("Sub Category") }}
            </label>
            <select name="sub_category" id="sub_category" class="lnd-input">
                <option value="">{{ __("Select Sub Category") }}</option>
                @foreach($sub_categories as $item)
                    <option value="{{ $item->id }}" {{ $item->id == $selectedSubCat ? "selected" : "" }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Child Category — reusable multi-select component --}}
        <x-fields.tw-multiselect
            name="child_category"
            id="child_category"
            :options="$child_categories"
            :selected="$selected_child"
            :placeholder="__('Select child categories…')"
            :label="__('Child Category')"
        />

    </div>
</div>
