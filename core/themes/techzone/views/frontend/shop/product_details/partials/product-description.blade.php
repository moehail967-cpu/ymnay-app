<div style="color:var(--tz-text);line-height:1.8;font-size:14px;">
    {!! $product->description !!}
</div>

@if(!empty($product->product_details_field))
<div style="margin-top:24px;">
    <h4 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--tz-border);">{{ __('Specifications') }}</h4>
    <table style="width:100%;border-collapse:collapse;">
        @foreach($product->product_details_field as $spec)
        <tr style="border-bottom:1px solid var(--tz-border);">
            <td style="padding:10px 12px;font-size:13px;font-weight:600;color:var(--tz-muted);width:40%;background:var(--tz-surface);">{{ $spec['field_title'] ?? '' }}</td>
            <td style="padding:10px 12px;font-size:13px;color:var(--tz-text);">{{ $spec['field_data'] ?? '' }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endif
