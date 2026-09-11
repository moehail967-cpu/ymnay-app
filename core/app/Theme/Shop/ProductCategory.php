<?php

namespace App\Theme\Shop;

class ProductCategory
{
    public function __construct(private object $category) {}

    public function raw(): object
    {
        return $this->category;
    }

    public function id(): int
    {
        return $this->category->id;
    }

    public function name(): string
    {
        return $this->category->name ?? '';
    }

    public function url(): string
    {
        return dynamicRoute($this->category->slug);
    }

    public function slug(): string
    {
        return $this->category->slug ?? '';
    }

    public function count(): int
    {
        return $this->category->product_count ?? $this->category->products_count ?? 0;
    }

    public function image_url(string $size = 'grid'): ?string
    {
        $data = get_attachment_image_by_id($this->category->image ?? null, $size);
        return !empty($data) ? $data['img_url'] : null;
    }
}
