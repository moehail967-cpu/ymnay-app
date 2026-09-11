{{-- Maison: product description tab --}}
<div style="font-size:14px;color:var(--ms-charcoal);line-height:1.8;">
    {!! $product->description !!}
</div>

@if($product->tag && $product->tag->isNotEmpty())
<div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--ms-border);display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
    <span style="font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);">
        {{ __('Tags:') }}
    </span>
    @foreach($product->tag as $tag)
        <span style="display:inline-block;background:var(--ms-warm);border:1px solid var(--ms-border);color:var(--ms-muted);padding:4px 12px;border-radius:2px;font-size:11px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;">
            {{ $tag->tag_name }}
        </span>
    @endforeach
</div>
@endif
