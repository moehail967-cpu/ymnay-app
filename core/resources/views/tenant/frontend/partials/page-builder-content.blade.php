{{--
    Unified page content renderer — handles both the new xgenious page builder
    (use_page_builder + rendered_content) and the legacy addon-based page builder
    (page_builder === 1), with visibility / member-only gating on both paths.

    Usage: @include('tenant.frontend.partials.page-builder-content', ['page_post' => $page_post])
--}}
@if(isset($page_post->rendered_content))
{{-- Base grid CSS for xgenious page builder layout classes --}}
<style>
.pb-section{display:block;position:relative;width:100%}
.section-inner{width:100%;margin:auto;padding-left:15px;padding-right:15px;box-sizing:border-box}
.section-layout-full_width .section-inner{max-width:none;padding-left:0;padding-right:0;margin-left:0;margin-right:0}
.section-layout-full_width .xgp_row{margin-left:0;margin-right:0}
.section-layout-full_width .xgp_column{padding-left:0;padding-right:0}
.section-layout-full_width_contained .section-inner{max-width:1200px}
.xgp_row{display:flex;flex-wrap:wrap;margin-left:-15px;margin-right:-15px}
.xgp_column{padding-left:15px;padding-right:15px;box-sizing:border-box;width:100%}
.xgp_col_1{width:8.333%}.xgp_col_2{width:16.666%}.xgp_col_3{width:25%}
.xgp_col_4{width:33.333%}.xgp_col_5{width:41.666%}.xgp_col_6{width:50%}
.xgp_col_7{width:58.333%}.xgp_col_8{width:66.666%}.xgp_col_9{width:75%}
.xgp_col_10{width:83.333%}.xgp_col_11{width:91.666%}.xgp_col_12{width:100%}
.xgp-widget{display:block;width:100%}
@media(max-width:768px){.xgp_col_1,.xgp_col_2,.xgp_col_3,.xgp_col_4,.xgp_col_5,.xgp_col_6,.xgp_col_7,.xgp_col_8,.xgp_col_9,.xgp_col_10,.xgp_col_11{width:100%}}
</style>
    @if($page_post->visibility === 1)
        @if(auth('web')->check())
            @if(!empty($page_post->pagebuilder_generated_styles))
                <style>{!! $page_post->pagebuilder_generated_styles !!}</style>
            @endif
            {!! $page_post->rendered_content !!}
        @else
            <section class="padding-top-100 padding-bottom-100">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-warning">
                                <p><a class="text-primary" href="{{ theme_login_url() }}">{{ __('Login') }}</a> {{ __('to see this page') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @else
        @if(!empty($page_post->pagebuilder_generated_styles))
            <style>{!! $page_post->pagebuilder_generated_styles !!}</style>
        @endif
        {!! $page_post->rendered_content !!}
    @endif
@elseif($page_post->page_builder === 1)
    @if($page_post->visibility === 1)
        @if(auth('web')->check())
            @include('tenant.frontend.partials.pages-portion.dynamic-page-builder-part', ['page_post' => $page_post])
        @else
            <section class="padding-top-100 padding-bottom-100">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-warning">
                                <p><a class="text-primary" href="{{ theme_login_url() }}">{{ __('Login') }}</a> {{ __('to see this page') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @else
        @include('tenant.frontend.partials.pages-portion.dynamic-page-builder-part', ['page_post' => $page_post])
    @endif
@else
    @include('tenant.frontend.partials.dynamic-content')
@endif
