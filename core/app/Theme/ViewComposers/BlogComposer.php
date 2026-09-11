<?php

namespace App\Theme\ViewComposers;

use App\Theme\Blog\Category;
use App\Theme\Blog\Comment;
use App\Theme\Blog\Post;
use App\Theme\Blog\Tag;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as IlluminateView;
use Modules\Blog\Entities\BlogCategory;
use Modules\Blog\Entities\BlogTag;

/**
 * Registered for blog::* views.
 * Wraps raw Eloquent objects into typed theme API objects and shares them
 * via View::share() so both theme_*() helpers (view()->shared()) and direct
 * $theme_* blade variables work correctly.
 *
 * NOTE: This composer runs before BlogServiceProvider's view composer (app
 * providers boot before module providers). Therefore sidebar data is fetched
 * here directly instead of relying on $data['all_category'] being present.
 */
class BlogComposer
{
    public function compose(IlluminateView $view): void
    {
        $data = $view->getData();

        // ── Blog list ($blogs paginator) → $theme_blogs ───────────────────────
        if (isset($data['blogs'])) {
            $blogs = collect($data['blogs']->items())->map(fn($b) => new Post($b));
            View::share('theme_blogs', $blogs);
            View::share('theme_blogs_paginator', $data['blogs']);
        }

        // ── Blog single ($blog_post) → $theme_post ────────────────────────────
        if (isset($data['blog_post'])) {
            View::share('theme_post', new Post($data['blog_post']));
        }

        // ── Comments ($blog_comments) → $theme_comments ───────────────────────
        if (isset($data['blog_comments'])) {
            View::share('theme_comments', collect($data['blog_comments'])->map(fn($c) => new Comment($c)));
            View::share('theme_comments_count', $data['blog_comments_count'] ?? 0);
        }

        // ── Sidebar categories — fetch directly so we don't depend on controller
        //    passing $all_category (execution order: app providers boot before modules)
        if (!View::shared('theme_blog_categories')) {
            $rawCats = isset($data['all_category'])
                ? $data['all_category']
                : BlogCategory::withCount('blogs')->has('blogs')->get();
            View::share('theme_blog_categories', collect($rawCats)->map(fn($c) => new Category($c)));
        }

        // ── Sidebar tags — same self-sufficient approach
        if (!View::shared('theme_blog_tags')) {
            try {
                $rawTags = isset($data['all_tags'])
                    ? $data['all_tags']
                    : (tenant() ? BlogTag::orderByDesc('created_at')->select('id', 'title', 'slug')->take(15)->get() : collect());
            } catch (\Exception $e) {
                $rawTags = collect();
            }
            View::share('theme_blog_tags', collect($rawTags)->map(fn($t) => new Tag($t->title ?? '')));
        }
    }
}
