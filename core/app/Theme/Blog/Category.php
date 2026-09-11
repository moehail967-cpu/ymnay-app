<?php

namespace App\Theme\Blog;

use Modules\Blog\Entities\BlogCategory;

class Category
{
    public function __construct(private BlogCategory $category) {}

    public function raw(): BlogCategory
    {
        return $this->category;
    }

    public function id(): int
    {
        return $this->category->id;
    }

    public function name(): string
    {
        return $this->category->title ?? '';
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
        return $this->category->blogs_count ?? 0;
    }
}
