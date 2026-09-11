<?php

namespace Modules\Blog\Http\Controllers\Landlord\Frontend;

use Illuminate\Http\Request;
use Modules\Blog\Entities\Blog;
use Modules\Blog\Entities\BlogComment;

class NewBlogController extends BlogController
{
    private const NEW_BASE_PATH = 'blog::landlord.frontend.new-blog.';

    public function blog_single($slug)
    {
        $blog_post = Blog::with(['user', 'category', 'comments'])->where(['slug' => $slug, 'status' => 1])->firstOrFail();
        $blog_comments = BlogComment::where(['blog_id' => $blog_post->id, 'parent_id' => null])->orderByDesc('created_at')->take(3)->get();
        $recent_blogs = Blog::where('status', 1)->where('id', '!=', $blog_post->id)->orderByDesc('id')->take(4)->get();

        $this->setMetaDataInfo($blog_post);

        return view(self::NEW_BASE_PATH . 'blog-single', compact('blog_post', 'blog_comments', 'recent_blogs'));
    }

    public function load_more_comments(Request $request)
    {
        if ($request->type == 'load-one') {
            $items = 0;
            $take = 1;
        } else {
            $items = $request->items;
            $take = 5;
        }

        $all_comment = BlogComment::with(['blog', 'user', 'reply'])
            ->where('blog_id', $request->id)
            ->where('parent_id', null)
            ->orderBy('id', 'desc')
            ->skip($items)
            ->take($take)
            ->get();

        $markup = '';
        foreach ($all_comment as $item) {
            $user_image = optional($item->user)->image ?? get_static_option('blog_avatar_image');
            $commented_user_image = render_image_markup_by_attachment_id($user_image, 'w-8 h-8 md:w-10 md:h-10 rounded-full object-cover flex-shrink-0', 'thumb');
            if (empty($commented_user_image)) {
                $commented_user_image = <<<SVG
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
SVG;
            }

            $var_data_parent_name = $item->commented_by;
            $title = $item->commented_by ?? '';
            $created_at = date('d F Y', strtotime($item->created_at ?? ''));
            $comment_content = $item->comment_content;
            $data_id = $item->id;
            $replay_text = __('Reply');

            $replay_mark = <<<REPLA
            <button class="reply-btn btn-replay inline-flex items-center text-sm font-normal text-sectionC transition-colors duration-200 underline" data-comment_id="{$data_id}">{$replay_text}</button>
REPLA;

            $repl = (auth('web')->check() || auth('admin')->check()) ? $replay_mark : '';

            $child_data = '';
            foreach ($item->reply as $repData) {
                $child_user_img = optional($repData->user)->image ?? get_static_option('blog_avatar_image');
                $child_image = render_image_markup_by_attachment_id($child_user_img, 'w-8 h-8 md:w-10 md:h-10 rounded-full object-cover shrink-0', 'thumb');
                if (empty($child_image)) {
                    $child_image = <<<SVG
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
SVG;
                }
                $child_user_name = $repData->commented_by ?? '';
                $child_commented_date = date('d F Y', strtotime($repData->created_at ?? ''));
                $child_comment = $repData->comment_content ?? '';

                $child_data .= <<<CHILDDATA
<article class="comment-item bg-white p-4 border-b border-borderCS ml-8 md:ml-16">
    <div class="flex items-start gap-4">
        {$child_image}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <h3 class="text-xl font-medium text-secondary">{$child_user_name}</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-base font-medium bg-[#F1F6F7] text-quate">{$child_commented_date}</span>
            </div>
            <p class="text-gray-700 font-normal text-base leading-6 mb-1">{$child_comment}</p>
        </div>
    </div>
</article>
CHILDDATA;
            }

            $markup .= <<<MARKUP
<article class="comment-item bg-white p-4 border-b border-borderCS">
    <div class="flex items-start gap-4">
        {$commented_user_image}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <h3 class="text-xl font-medium text-secondary"><a href="javascript:void(0)" data-parent_name="{$var_data_parent_name}">{$title}</a></h3>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-base font-medium bg-[#F1F6F7] text-quate">{$created_at}</span>
            </div>
            <p class="text-gray-700 font-normal text-base leading-6 mb-1">{$comment_content}</p>
            {$repl}
        </div>
    </div>
</article>
{$child_data}
MARKUP;
        }

        return response()->json(['blogComments' => $all_comment, 'markup' => $markup]);
    }
}
