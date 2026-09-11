<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the route prefix and middleware for page builder routes.
    |
    */

    'route_prefix' => env('PAGE_BUILDER_ROUTE_PREFIX', 'page-builder'),

    'route_middleware' => ['web', \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class, 'auth:admin'],

    /*
    |--------------------------------------------------------------------------
    | Navigation Routes
    |--------------------------------------------------------------------------
    |
    | Configure route names for navigation buttons in the page builder editor.
    | These routes must exist in your host application.
    | The package will fallback gracefully if routes are not found.
    |
    */

    'routes' => [
        // Route name for previewing pages (should accept slug parameter)
        // Example: Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');
        'preview' => env('PAGE_BUILDER_PREVIEW_ROUTE', 'page.show'),

        // Route name for returning to pages list
        // Example: Route::get('/admin/pages', [PageController::class, 'index'])->name('admin.pages.index');
        'back_to_pages' => env('PAGE_BUILDER_BACK_ROUTE', 'admin.pages.index'),

        // Direct URL fallback (if route resolution fails)
        'back_to_pages_url' => env('PAGE_BUILDER_BACK_URL', '/admin-home/pages'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which models the package should use. This allows the package
    | to work with different host applications without model conflicts.
    |
    */

    'models' => [
        'page' => \App\Models\Page::class,
        'admin' => \App\Models\Admin::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Tables
    |--------------------------------------------------------------------------
    |
    | Customize the database table names used by the page builder.
    |
    */

    'tables' => [
        'content' => 'page_builder_content',
        'widgets' => 'page_builder_widgets',
        'editing_sessions' => 'page_editing_sessions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Addon Support
    |--------------------------------------------------------------------------
    |
    | Enable automatic discovery and registration of legacy PageBuilder addons.
    | This allows old addons to work with the new system without modification.
    |
    */
    'demo_mode' => env('PAGE_BUILDER_DEMO_MODE', false),
    'enable_legacy_addons' => env('PAGE_BUILDER_LEGACY_ADDONS', true),

    'legacy_addon_paths' => [
        base_path('plugins/PageBuilder/Addons'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Discovery Paths
    |--------------------------------------------------------------------------
    |
    | Paths where the package should look for BaseWidget classes.
    | These are the new-style widgets that extend BaseWidget directly.
    |
    */

    'widget_paths' => [
        [
            'path' => base_path('plugins/WidgetBuilder/Widgets'),
            'namespace' => 'plugins\\WidgetBuilder\\Widgets',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific widgets. Set to false to hide a widget
    | from the page builder interface.
    |
    */

    'widgets' => [
        // Theme Widgets (keep disabled — theme-specific, replaced by custom widgets)
        'header' => false,
        'features' => false,

        // Content Widgets (keep disabled — custom Testimonials widget exists)
        'testimonial' => false,

        // Media Widgets
        'image' => false, // disabled — replaced by custom ImageWidget that handles array IMAGE field values
        'image-gallery' => false,
        'video' => false, // disabled — replaced by custom VideoWidget that flattens group nesting
        'icon' => false,

        // Interactive Widgets
        'tabs' => false, // disabled — replaced by custom TabsWidget that fixes group nesting + adds JS

        // Advanced Widgets
        'code' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Widgets
    |--------------------------------------------------------------------------
    |
    | Register your custom widget classes here. Each widget should extend
    | Xgenious\PageBuilder\Core\BaseWidget.
    |
    */

    'custom_widgets' => [
        // Core vendor widgets (not auto-discovered — must be explicit)
        \Plugins\WidgetBuilder\Widgets\HeadingWidget::class,
        \Plugins\WidgetBuilder\Widgets\ParagraphWidget::class,
        \Plugins\WidgetBuilder\Widgets\ButtonWidget::class,
        \Xgenious\PageBuilder\Core\Widgets\DividerWidget::class,
        \Xgenious\PageBuilder\Core\Widgets\SpacerWidget::class,
        \Xgenious\PageBuilder\Core\Widgets\ListWidget::class,

        \Plugins\WidgetBuilder\Widgets\landlord\Header\HeaderOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Brand\BrandOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Feature\FeatureOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Feature\FeatureTwo::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Feature\FeatureThree::class,
        \Plugins\WidgetBuilder\Widgets\landlord\HowItWorks\HowItWorksOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\CallAction\CallActionOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Accordion\AccordionOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Pos\PosFeatureOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Feedback\FeedbackOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Themes\ThemesOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Themes\ThemesTwo::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Themes\ThemesThree::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Themes\ThemesFour::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Themes\ThemesFive::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Pricing\PricingOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Pricing\PricingDetailOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Blog\BlogOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Blog\BlogDetailOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Contact\ContactOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Contact\ContactTwo::class,
        \Plugins\WidgetBuilder\Widgets\landlord\TermsConditions\TermsConditionOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Header\HeaderTwo::class,
        \Plugins\WidgetBuilder\Widgets\landlord\OurStory\OurStoryOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\OurAchievements\OurAchievementsOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Journey\JourneyOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\MissionVision\MissionVisionOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\Values\ValuesOne::class,
        \Plugins\WidgetBuilder\Widgets\landlord\WhyChooseUs\WhyChooseUsOne::class,

        // Tenant theme widgets (shown only in tenant admin context via enable())
        \Plugins\WidgetBuilder\Widgets\Tenant\HeroSection::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\FeaturedProducts::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\CategoryGrid::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\ServicesStrip::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\Newsletter::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\CampaignBanner::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\Testimonials::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\BrandSlider::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\BrandLogos::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\RecentBlog::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\ProductByCategory::class,

        // New generic tenant widgets
        \Plugins\WidgetBuilder\Widgets\Tenant\BoxWidget::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\IconBoxWidget::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\CarouselWidget::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\ProductSliderWidget::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\ProductGridWidget::class,

        // bakerco theme widgets (tenant context, theme_slug === 'bakerco')
        \themes\bakerco\Widgets\HeroSection::class,
        \themes\bakerco\Widgets\CategoryGrid::class,
        \themes\bakerco\Widgets\FeaturedProducts::class,
        \themes\bakerco\Widgets\Newsletter::class,
        \themes\bakerco\Widgets\PromoSection::class,

        // pharmacy theme widgets (tenant context, theme_slug === 'pharmacy')
        \themes\pharmacy\Widgets\HeroSection::class,
        \themes\pharmacy\Widgets\CategoryGrid::class,
        \themes\pharmacy\Widgets\FeaturedProducts::class,
        \themes\pharmacy\Widgets\BestSellers::class,
        \themes\pharmacy\Widgets\Newsletter::class,

        // pawhaus theme widgets (tenant context, theme_slug === 'pawhaus')
        \themes\pawhaus\Widgets\HeroSection::class,
        \themes\pawhaus\Widgets\CategoryGrid::class,
        \themes\pawhaus\Widgets\FeaturedProducts::class,
        \themes\pawhaus\Widgets\PromoBanner::class,
        \themes\pawhaus\Widgets\Newsletter::class,

        // chefhome theme widgets (tenant context, theme_slug === 'chefhome')
        \themes\chefhome\Widgets\HeroSection::class,
        \themes\chefhome\Widgets\TrustStrip::class,
        \themes\chefhome\Widgets\CategoryGrid::class,
        \themes\chefhome\Widgets\FeaturedDishes::class,
        \themes\chefhome\Widgets\ProductsByCategory::class,
        \themes\chefhome\Widgets\HowItWorks::class,
        \themes\chefhome\Widgets\Testimonials::class,
        \themes\chefhome\Widgets\Newsletter::class,

        // drivekit theme widgets (tenant context, theme_slug === 'drivekit')
        \themes\drivekit\Widgets\HeroSection::class,
        \themes\drivekit\Widgets\CategoryGrid::class,
        \themes\drivekit\Widgets\FeaturedProducts::class,
        \themes\drivekit\Widgets\PromoBanner::class,
        \themes\drivekit\Widgets\LatestBlogPosts::class,
        \themes\drivekit\Widgets\Newsletter::class,

        // freshmart theme widgets (tenant context, theme_slug === 'freshmart')
        \themes\freshmart\Widgets\HeroSection::class,
        \themes\freshmart\Widgets\CategoryGrid::class,
        \themes\freshmart\Widgets\FeaturedProducts::class,
        \themes\freshmart\Widgets\PromoBanner::class,
        \themes\freshmart\Widgets\Newsletter::class,

        // fitpeak theme widgets (tenant context, theme_slug === 'fitpeak')
        \themes\fitpeak\Widgets\HeroSection::class,
        \themes\fitpeak\Widgets\CategoryGrid::class,
        \themes\fitpeak\Widgets\FeaturedProducts::class,
        \themes\fitpeak\Widgets\PromoBanner::class,
        \themes\fitpeak\Widgets\Newsletter::class,

        // glowlab theme widgets (tenant context, theme_slug === 'glowlab')
        \themes\glowlab\Widgets\HeroSection::class,
        \themes\glowlab\Widgets\CategoryGrid::class,
        \themes\glowlab\Widgets\FeaturedProducts::class,
        \themes\glowlab\Widgets\PromoBanner::class,
        \themes\glowlab\Widgets\Newsletter::class,

        // goldcraft theme widgets (tenant context, theme_slug === 'goldcraft')
        \themes\goldcraft\Widgets\HeroSection::class,
        \themes\goldcraft\Widgets\CategoryGrid::class,
        \themes\goldcraft\Widgets\FeaturedProducts::class,
        \themes\goldcraft\Widgets\PromoBanner::class,
        \themes\goldcraft\Widgets\Newsletter::class,

        // luxegems theme widgets (tenant context, theme_slug === 'luxegems')
        \themes\luxegems\Widgets\HeroSection::class,
        \themes\luxegems\Widgets\CategoryGrid::class,
        \themes\luxegems\Widgets\FeaturedProducts::class,
        \themes\luxegems\Widgets\PromoBanner::class,
        \themes\luxegems\Widgets\Newsletter::class,

        // maison theme widgets (tenant context, theme_slug === 'maison')
        \themes\maison\Widgets\HeroSection::class,
        \themes\maison\Widgets\CategoryGrid::class,
        \themes\maison\Widgets\FeaturedProducts::class,
        \themes\maison\Widgets\PromoBanner::class,
        \themes\maison\Widgets\Newsletter::class,

        // KidVille theme widgets (tenant context, theme_slug === 'kidville')
        \themes\kidville\Widgets\HeroSection::class,
        \themes\kidville\Widgets\CategoryGrid::class,
        \themes\kidville\Widgets\FeaturedProducts::class,
        \themes\kidville\Widgets\PromoBanner::class,
        \themes\kidville\Widgets\Newsletter::class,

        // sportzone theme widgets (tenant context, theme_slug === 'sportzone')
        \themes\sportzone\Widgets\HeroSection::class,
        \themes\sportzone\Widgets\CategoryGrid::class,
        \themes\sportzone\Widgets\FeaturedProducts::class,
        \themes\sportzone\Widgets\PromoBanner::class,
        \themes\sportzone\Widgets\Newsletter::class,

        // techzone theme widgets (tenant context, theme_slug === 'techzone')
        \themes\techzone\Widgets\HeroSection::class,
        \themes\techzone\Widgets\CategoryGrid::class,
        \themes\techzone\Widgets\FeaturedProducts::class,
        \themes\techzone\Widgets\Newsletter::class,

        // trailco theme widgets (tenant context, theme_slug === 'trailco')
        \themes\trailco\Widgets\HeroSection::class,
        \themes\trailco\Widgets\CategoryGrid::class,
        \themes\trailco\Widgets\FeaturedProducts::class,
        \themes\trailco\Widgets\LatestProducts::class,
        \themes\trailco\Widgets\PromoBanner::class,
        \themes\trailco\Widgets\Newsletter::class,

        // tinynest theme widgets (tenant context, theme_slug === 'tinynest')
        \themes\tinynest\Widgets\HeroSection::class,
        \themes\tinynest\Widgets\CategoryGrid::class,
        \themes\tinynest\Widgets\FeaturedProducts::class,
        \themes\tinynest\Widgets\PromoBanner::class,
        \themes\tinynest\Widgets\Newsletter::class,

        // velvetlux theme widgets (tenant context, theme_slug === 'velvetlux')
        \themes\velvetlux\Widgets\HeroSection::class,
        \themes\velvetlux\Widgets\CategoryGrid::class,
        \themes\velvetlux\Widgets\FeaturedProducts::class,
        \themes\velvetlux\Widgets\PromoSection::class,
        \themes\velvetlux\Widgets\Newsletter::class,

        // aromatic theme widgets (tenant context, theme_slug === 'aromatic')
        \themes\aromatic\Widgets\HeroSection::class,
        \themes\aromatic\Widgets\PromoBanner::class,
        \themes\aromatic\Widgets\ProductTypeList::class,
        \themes\aromatic\Widgets\NewCollection::class,
        \themes\aromatic\Widgets\Brand::class,

        // electro theme widgets (tenant context, theme_slug === 'electro')
        \themes\electro\Widgets\HeroSection::class,
        \themes\electro\Widgets\CategorySection::class,
        \themes\electro\Widgets\CategoryBanners::class,
        \themes\electro\Widgets\FeaturedCollections::class,
        \themes\electro\Widgets\ServiceFeatures::class,
        \themes\electro\Widgets\BrandLogos::class,
        \themes\electro\Widgets\NewReleaseBanner::class,
        \themes\electro\Widgets\PromoBanners::class,
        \themes\electro\Widgets\OurStore::class,
        \themes\electro\Widgets\UpdatedNews::class,

        // bookpoint theme widgets (tenant context, theme_slug === 'bookpoint')
        \themes\bookpoint\Widgets\HeroSection::class,
        \themes\bookpoint\Widgets\ServiceFeatures::class,
        \themes\bookpoint\Widgets\CategoryGrid::class,
        \themes\bookpoint\Widgets\FeaturedProducts::class,
        \themes\bookpoint\Widgets\PromoBanner::class,
        \themes\bookpoint\Widgets\SinglePromoBanner::class,
        \themes\bookpoint\Widgets\BrandLogos::class,
        \themes\bookpoint\Widgets\TopAuthors::class,
        \themes\bookpoint\Widgets\UpcomingBooks::class,
        \themes\bookpoint\Widgets\RecentNews::class,

        // furnito theme widgets (tenant context, theme_slug === 'furnito')
        \themes\furnito\Widgets\HeroSection::class,
        \themes\furnito\Widgets\FeaturedProducts::class,
        \themes\furnito\Widgets\CategoryGrid::class,
        \themes\furnito\Widgets\ServiceFeatures::class,
        \themes\furnito\Widgets\Newsletter::class,
        \themes\furnito\Widgets\CategoryBanners::class,
        \themes\furnito\Widgets\TrendyProducts::class,
        \themes\furnito\Widgets\OurStore::class,
        \themes\furnito\Widgets\NewArrival::class,
        \themes\furnito\Widgets\BrandLogos::class,
        \themes\furnito\Widgets\Testimonials::class,

        // medicom theme widgets (tenant context, theme_slug === 'medicom')
        \themes\medicom\Widgets\HeroSection::class,
        \themes\medicom\Widgets\FeaturedProducts::class,
        \themes\medicom\Widgets\CategoryGrid::class,
        \themes\medicom\Widgets\ServiceFeatures::class,

        // hexfashion theme widgets (tenant context, theme_slug === 'hexfashion')
        \themes\hexfashion\Widgets\HeroSection::class,
        \themes\hexfashion\Widgets\FeaturedProducts::class,
        \themes\hexfashion\Widgets\CategoryGrid::class,
        \themes\hexfashion\Widgets\ServiceFeatures::class,
        \themes\hexfashion\Widgets\Newsletter::class,
        \themes\hexfashion\Widgets\PromoBanner::class,
        \themes\hexfashion\Widgets\OurStore::class,
        \themes\hexfashion\Widgets\FlashStore::class,
        \themes\hexfashion\Widgets\Testimonials::class,
        \themes\hexfashion\Widgets\BlogSection::class,
        \themes\hexfashion\Widgets\BrandLogos::class,
        \themes\hexfashion\Widgets\DealOfTheWeek::class,

        // Global tenant About & Contact widgets (all themes)
        \Plugins\WidgetBuilder\Widgets\Tenant\About\AboutHero::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\About\StatsCounter::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\Contact\ContactInfoForm::class,
        \Plugins\WidgetBuilder\Widgets\Tenant\Contact\GoogleMapEmbed::class,

        // Custom overrides — registered last so they win over vendor's broken versions
        \Plugins\WidgetBuilder\Widgets\ImageWidget::class,
        \Plugins\WidgetBuilder\Widgets\VideoWidget::class,
        \Plugins\WidgetBuilder\Widgets\TabsWidget::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | CSS Generation
    |--------------------------------------------------------------------------
    |
    | Configure CSS generation behavior.
    |
    */

    'css' => [
        'minify' => env('PAGE_BUILDER_MINIFY_CSS', true),
        'cache' => env('PAGE_BUILDER_CACHE_CSS', true),
        'cache_ttl' => 3600, // Cache TTL in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend CSS Files for Editor
    |--------------------------------------------------------------------------
    |
    | List of frontend CSS files to load in the page builder editor.
    | These are scoped to canvas content only to avoid conflicts with editor UI.
    | Add your host app's CSS files here.
    |
    */

    'editor_frontend_css' => [
        'assets/landlord/frontend/css/bootstrap.min.css',
        'assets/landlord/frontend/css/helpers.css',
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend JS Files for Editor & Frontend
    |--------------------------------------------------------------------------
    |
    | JavaScript files from your host app that should be loaded in:
    | 1. Editor canvas (for interactive widgets)
    | 2. Frontend pages (for widget functionality)
    |
    | These are loaded at the end of <body> to ensure proper execution.
    |
    */

    'editor_frontend_js' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how the page builder integrates with your host app's
    | media library. This allows widgets to use your existing media manager.
    |
    */

    'media' => [
        // Media upload route (host app's admin media upload endpoint)
        'upload_route' => 'landlord.admin.upload.media.file',

        // Media library route (host app's media browser)
        'library_route' => 'landlord.admin.upload.media.file.all',

        // Media delete route
        'delete_route' => 'landlord.admin.upload.media.file.delete',

        // Media base path (where media files are stored)
        'base_path' => 'assets/uploads',

        // Allowed file types
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],

        // Max file size in KB
        'max_size' => 5120, // 5MB

        // Use host app's media manager component
        // Set the component path if your host app has a media manager component
        'manager_component' => null, // e.g., 'admin.media.media-upload'
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Publishing
    |--------------------------------------------------------------------------
    |
    | Configure which assets should be published when running
    | vendor:publish command.
    |
    */

    'publish' => [
        'config' => true,
        'migrations' => true,
        'views' => true,
        'assets' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for sections, columns, and widgets.
    |
    */

    'defaults' => [
        'section' => [
            'contentWidth' => 'boxed',
            'maxWidth' => 1200,
            'padding' => [
                'top' => 60,
                'right' => 15,
                'bottom' => 60,
                'left' => 15,
                'unit' => 'px',
            ],
        ],

        'column' => [
            'padding' => [
                'top' => 0,
                'right' => 15,
                'bottom' => 0,
                'left' => 15,
                'unit' => 'px',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Editing Sessions
    |--------------------------------------------------------------------------
    |
    | Configure editing session behavior for concurrent editing protection.
    |
    */

    'editing_sessions' => [
        'enabled' => true,
        'timeout' => 300, // Session timeout in seconds (5 minutes)
        'heartbeat_interval' => 30, // Heartbeat interval in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Security-related configuration options.
    |
    */

    'security' => [
        'allowed_html_tags' => ['p', 'br', 'strong', 'em', 'u', 'a', 'span', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'img'],
        'allowed_protocols' => ['http', 'https', 'mailto', 'tel'],
        'sanitize_input' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance
    |--------------------------------------------------------------------------
    |
    | Performance optimization settings.
    |
    */

    'performance' => [
        'lazy_load_widgets' => true,
        'optimize_images' => true,
        'defer_css' => false,
    ],
];
