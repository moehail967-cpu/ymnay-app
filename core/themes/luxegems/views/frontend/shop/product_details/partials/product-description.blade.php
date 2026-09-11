{{-- LuxeGems: Product description tab --}}
<div class="lg-prose">
    {!! $product->description !!}
</div>

@if($product->additional_info?->isNotEmpty())
<div class="mt-4">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        @foreach($product->additional_info as $spec)
        <tr style="border-bottom:1px solid var(--lx-border);">
            <td style="padding:10px 16px;color:var(--lx-gold);font-weight:600;width:40%;white-space:nowrap;">{{ $spec->title }}</td>
            <td style="padding:10px 16px;color:var(--lx-muted);">{{ $spec->value }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endif
