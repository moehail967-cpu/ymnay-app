@extends('layouts.landlord-admin')

@section('content')
<div class="yemeni-wallets-form" dir="rtl">
    <h3>{{ $wallet ? __('تعديل محفظة') : __('إضافة محفظة جديدة') }}</h3>

    <form action="{{ $wallet ? route('landlord.yemeniwallets.catalog.update', $wallet['id']) : route('landlord.yemeniwallets.catalog.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @if ($wallet) @method('PUT') @endif

        <div class="mb-3">
            <label>{{ __('اسم المحفظة') }} ({{ __('مثال: جوالي، ون كاش، كاش') }})</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $wallet['name'] ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label>{{ __('شعار المحفظة') }}</label>
            <input type="file" name="logo" class="form-control" accept="image/*">
            @if (!empty($wallet['logo']))
                <img src="{{ asset('storage/' . $wallet['logo']) }}" width="60" class="mt-2">
            @endif
        </div>

        <hr>
        <h5>{{ __('الحقول التي يجب على التاجر تعبئتها') }}</h5>
        <p class="text-muted small">{{ __('مثال: اسم صاحب الحساب، رقم المحفظة.') }}</p>

        <div id="fields-wrapper">
            @php $fields = old('fields', $wallet['fields_schema'] ?? [['key' => '', 'label' => '', 'type' => 'text', 'required' => true]]); @endphp
            @foreach ($fields as $i => $field)
                <div class="row g-2 mb-2 field-row">
                    <div class="col-3">
                        <input type="text" name="fields[{{ $i }}][key]" class="form-control" placeholder="{{ __('مفتاح الحقل') }}" value="{{ $field['key'] ?? '' }}" required>
                    </div>
                    <div class="col-4">
                        <input type="text" name="fields[{{ $i }}][label]" class="form-control" placeholder="{{ __('التسمية المعروضة') }}" value="{{ $field['label'] ?? '' }}" required>
                    </div>
                    <div class="col-2">
                        <select name="fields[{{ $i }}][type]" class="form-control">
                            <option value="text" @selected(($field['type'] ?? '') === 'text')>{{ __('نص') }}</option>
                            <option value="textarea" @selected(($field['type'] ?? '') === 'textarea')>{{ __('نص طويل') }}</option>
                            <option value="number" @selected(($field['type'] ?? '') === 'number')>{{ __('رقم') }}</option>
                        </select>
                    </div>
                    <div class="col-2 d-flex align-items-center">
                        <label class="me-1">
                            <input type="checkbox" name="fields[{{ $i }}][required]" value="1" @checked($field['required'] ?? false)>
                            {{ __('إلزامي') }}
                        </label>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-field-row">×</button>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" id="add-field-row" class="btn btn-sm btn-outline-secondary mb-3">{{ __('+ إضافة حقل') }}</button>

        <div class="mb-3">
            <label>
                <input type="checkbox" name="status" value="1" @checked(old('status', $wallet['status'] ?? true))>
                {{ __('مفعّلة على مستوى المنصة') }}
            </label>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('حفظ') }}</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var wrapper = document.getElementById('fields-wrapper');
    var addBtn = document.getElementById('add-field-row');
    var index = wrapper.querySelectorAll('.field-row').length;

    addBtn.addEventListener('click', function () {
        var row = document.createElement('div');
        row.className = 'row g-2 mb-2 field-row';
        row.innerHTML =
            '<div class="col-3"><input type="text" name="fields[' + index + '][key]" class="form-control" placeholder="{{ __('مفتاح الحقل') }}" required></div>' +
            '<div class="col-4"><input type="text" name="fields[' + index + '][label]" class="form-control" placeholder="{{ __('التسمية المعروضة') }}" required></div>' +
            '<div class="col-2"><select name="fields[' + index + '][type]" class="form-control"><option value="text">{{ __('نص') }}</option><option value="textarea">{{ __('نص طويل') }}</option><option value="number">{{ __('رقم') }}</option></select></div>' +
            '<div class="col-2 d-flex align-items-center"><label class="me-1"><input type="checkbox" name="fields[' + index + '][required]" value="1"> {{ __('إلزامي') }}</label></div>' +
            '<div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger remove-field-row">×</button></div>';
        wrapper.appendChild(row);
        index++;
    });

    wrapper.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-field-row')) e.target.closest('.field-row').remove();
    });
});
</script>
@endsection
