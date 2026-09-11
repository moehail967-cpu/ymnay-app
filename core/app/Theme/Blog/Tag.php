<?php

namespace App\Theme\Blog;

use Illuminate\Support\Str;

class Tag
{
    public function __construct(private string $rawTag) {}

    public function name(): string
    {
        return $this->rawTag;
    }

    public function slug(): string
    {
        return Str::slug($this->rawTag);
    }

    public function url(): string
    {
        return tenant_blog_tag_route($this->slug());
    }
}
