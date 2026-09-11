<?php

namespace App\Theme\Blog;

use Modules\Blog\Entities\BlogComment;

class Comment
{
    public function __construct(private BlogComment $comment) {}

    public function raw(): BlogComment
    {
        return $this->comment;
    }

    public function id(): int
    {
        return $this->comment->id;
    }

    public function author(): string
    {
        return optional($this->comment->user)->name ?? '';
    }

    public function author_avatar(): string
    {
        $id = $this->comment?->user?->image ?? get_static_option('blog_avater_image');
        return render_image_markup_by_attachment_id($id, '', '');
    }

    public function body(): string
    {
        return $this->comment->comment_content ?? '';
    }

    public function date(string $format = 'd F Y'): string
    {
        return date($format, strtotime($this->comment->created_at ?? 'now'));
    }

    public function can_reply(): bool
    {
        $user = auth('web')->user();
        return $user && $user->id !== $this->comment->user_id;
    }

    /** @return Comment[] */
    public function replies(): array
    {
        return ($this->comment->reply ?? collect())->map(fn($r) => new Comment($r))->all();
    }

    public function has_replies(): bool
    {
        return count($this->replies()) > 0;
    }
}
