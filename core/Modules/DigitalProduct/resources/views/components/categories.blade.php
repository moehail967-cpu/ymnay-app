@php
    if(!isset($selectedcat)){
        $selectedcat = null;
    }
    if(!isset($selectedSubCat)){
        $selectedSubCat = null;
    }
    $categories = !isset($categories) ? [] : $categories;
    $sub_categories = !isset($subCategories) ? [] : $subCategories;
    $child_categories = !isset($childCategories) ? [] : $childCategories;
@endphp

<div>
    <h4 class="product-section-title">{{ __("Categories") }}</h4>
    <div class="space-y-4">
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Category") }} <x-fields.mandatory-indicator/></label>
            <select name="category_id" id="category" class="lnd-input">
                <option value="">{{ __("Select Category") }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $selectedcat === $category->id ? "selected" : "" }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Sub Category") }}</label>
            <select class="lnd-input" name="sub_category" id="sub_category">
                <option value="">{{ __("Select Sub Category") }}</option>
                @foreach($sub_categories as $item)
                    <option value="{{ $item->id }}" {{ $item->id == $selectedSubCat ? "selected" : "" }}>{{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Child Category") }}</label>
            <select name="child_category[]" multiple id="child_category" class="lnd-input select2">
                @foreach($child_categories as $item)
                    <option value="{{ $item->id }}" {{ in_array($item->id, $selectedChildCat) ? "selected" : "" }}>{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
