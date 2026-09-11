<?php

namespace App\Theme\Blog;

use Illuminate\Support\Str;
use Modules\Blog\Entities\Blog;

/**
 * Wraps a Blog model with a clean, stable method API for theme templates.
 * Theme developers use this instead of accessing model internals directly.
 */
class Post
{
    public function __construct(private Blog $blog) {}

    public function raw(): Blog
    {
        return $this->blog;
    }

    // ── Identity ──────────────────────────────────────────────────────────────

    public function id(): int
    {
        return $this->blog->id;
    }

    public function title(): string
    {
        return purify_html($this->blog->title ?? '');
    }

    public function url(): string
    {
        return dynamicRoute($this->blog->slug);
    }

    public function slug(): string
    {
        return $this->blog->slug ?? '';
    }

    // ── Content ───────────────────────────────────────────────────────────────

    public function content(): string
    {
        return $this->blog->blog_content ?? '';
    }

    public function excerpt(int $words = 20): string
    {
        $text = $this->blog->excerpt ?? strip_tags($this->blog->blog_content ?? '');
        return purify_html(Str::words($text, $words));
    }

    // ── Media ─────────────────────────────────────────────────────────────────

    public function image_url(string $size = 'grid'): ?string
    {
        $data = get_attachment_image_by_id($this->blog->image, $size);
        return !empty($data) ? $data['img_url'] : null;
    }

    public function image_tag(string $size = 'grid', string $class = '', string $alt = ''): string
    {
        return render_image_markup_by_attachment_id(
            $this->blog->image,
            $class,
            $size,
            true
        );
    }

    // ── Meta ──────────────────────────────────────────────────────────────────

    public function author(): string
    {
        return optional($this->blog->user)->name ?? optional($this->blog->admin)->name ?? '';
    }

    public function date(string $format = 'M d, Y'): string
    {
        return $this->blog->created_at?->format($format) ?? '';
    }

    public function comment_count(): int
    {
        return $this->blog->comments ? count($this->blog->comments) : 0;
    }

    public function comment_url(): string
    {
        return url(tenant_blog_single_route($this->blog->slug) . '/#comment-area');
    }

    // ── Taxonomy ──────────────────────────────────────────────────────────────

    public function category(): ?Category
    {
        return $this->blog->category ? new Category($this->blog->category) : null;
    }

    /** @return Tag[] */
    public function tags(): array
    {
        if (empty($this->blog->tags)) {
            return [];
        }
        return array_values(array_filter(
            array_map(fn($t) => new Tag(trim($t)), explode(',', $this->blog->tags)),
            fn($t) => $t->name() !== ''
        ));
    }

    // ── Comments ──────────────────────────────────────────────────────────────

    /** @return Comment[] */
    public function comments(): array
    {
        return ($this->blog->comments ?? collect())->map(fn($c) => new Comment($c))->all();
    }

    // ── Sharing ───────────────────────────────────────────────────────────────

    public function share_links(mixed $image = null): string
    {
        return single_post_share($this->url(), $this->blog->title ?? '', $image);
    }

    // ── Conditional ───────────────────────────────────────────────────────────

    public function has_image(): bool
    {
        return !empty($this->blog->image);
    }

    public function has_comments(): bool
    {
        return $this->comment_count() > 0;
    }

    public function has_tags(): bool
    {
        return !empty($this->blog->tags);
    }
}
