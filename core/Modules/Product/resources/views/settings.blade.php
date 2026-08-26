@extends('tenant.admin.admin-master')
@section('title')
    {{ __('Product Settings') }}
@endsection

@section('content')

<x-flash-msg/>
<x-error-msg/>

{{-- Product Settings Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-cog-outline text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Product Global Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Configure product display limits across your site')}}</p>
        </div>
    </div>

    <div class="p-4 sm:p-6">
        <form class="forms-sample" method="post" action="{{route('tenant.admin.product.settings')}}">
            @csrf

            <div class="space-y-4">
                <x-fields.input type="number" value="{{get_static_option('product_title_length')}}" name="product_title_length"
                                label="{{__('Product Title Length')}}"
                                info="{{__('It will limit every product title word length on the whole site')}}"
                                tooltip="{{__('title')}}" direction="right"/>

                <x-fields.input type="number" value="{{get_static_option('product_description_length')}}" name="product_description_length"
                                label="{{__('Product Description Length')}}"
                                info="{{__('It will limit every product description word length on the whole site')}}"
                                tooltip="{{__('description')}}" direction="right"/>

                <x-fields.input type="number" value="{{get_static_option('phone_screen_products_card')}}" name="phone_screen_products_card"
                                label="{{__('Products on phone screen')}}"
                                info="{{__('The number of products will be shown on the whole site for phone screen')}}"
                                tooltip="{{__('phone_screen_products_card')}}" direction="right"/>
            </div>

            <div class="mt-6">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline"></i>
                    {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
