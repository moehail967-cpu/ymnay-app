<div class="details-comment-content">
    <form action="{{ route(route_prefix().'frontend.blog.comment.store') }}" method="POST"
          class="space-y-5" id="blog-comment-form" novalidate>
        @csrf

        <input type="hidden" name="comment_id"/>
        <input type="hidden" name="blog_id" id="blog_id" value="{{ $blog_post->id }}">
        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
        <input type="hidden" name="commented_by" id="commented_by" value="{{ $user->name }}">

        <!-- User info display -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="commenter-name" class="sr-only">{{ __('Name') }}</label>
                <input type="text" id="commenter-name" value="{{ $user->name }}" disabled
                    class="w-full px-4 py-3 rounded-xl bg-bg-counter border border-gray-200 text-text-primarys placeholder-gray-400 focus:outline-none transition-all duration-200 opacity-70">
            </div>
            <div>
                <label for="commenter-email" class="sr-only">{{ __('Email') }}</label>
                <input type="email" id="commenter-email" value="{{ $user->email }}" disabled
                    class="w-full px-4 py-3 rounded-xl bg-bg-counter border border-gray-200 text-text-primarys placeholder-gray-400 focus:outline-none transition-all duration-200 opacity-70">
            </div>
        </div>

        <!-- Comment Textarea -->
        <div>
            <label for="comment_content" class="sr-only">{{ __('Your Comment') }}</label>
            <textarea id="comment_content" name="comment_content" rows="6" placeholder="{{ __('Enter Your Comment') }}"
                required aria-required="true"
                class="w-full px-4 py-3 rounded-xl bg-bg-counter border border-gray-200 text-text-primarys placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sectionC focus:border-transparent transition-all duration-200 resize-none"></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="submitComment"
            class="w-full bg-primary text-white font-semibold py-4 rounded-full transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-teal-800">
            {{ __('SUBMIT') }}
        </button>
    </form>
</div>
