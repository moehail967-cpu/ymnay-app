<div style="font-family:var(--sz-font-body);font-size:15px;color:var(--sz-muted);line-height:1.8;">
    {!! $product->description !!}
</div>

@if($product->product_details_field?->isNotEmpty())
<div style="margin-top:24px;border-top:1px solid var(--sz-border);padding-top:24px;">
    <div style="font-family:var(--sz-font-head);font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-dark);margin-bottom:14px;">{{ __('Specifications') }}</div>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        @foreach($product->product_details_field as $field)
        <tr>
            <td style="padding:8px 16px 8px 0;font-family:var(--sz-font-head);font-size:12px;text-transform:uppercase;letter-spacing:1px;color:var(--sz-muted);width:35%;border-bottom:1px solid var(--sz-border);">{{ $field->title }}</td>
            <td style="padding:8px 0;color:var(--sz-dark);border-bottom:1px solid var(--sz-border);">{{ $field->data }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endif
