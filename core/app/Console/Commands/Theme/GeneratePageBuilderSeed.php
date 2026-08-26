<?php

namespace App\Console\Commands\Theme;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Generates demo/page_builder_layout.json for every theme.
 * The JSON is consumed by ThemeDemoImporter to seed page_builder_content
 * and page_builder_widgets when a tenant activates a theme.
 *
 * Usage:
 *   php artisan theme:generate-pb-seed              # all themes
 *   php artisan theme:generate-pb-seed --theme=chefhome
 *   php artisan theme:generate-pb-seed --force      # overwrite existing
 */
class GeneratePageBuilderSeed extends Command
{
    protected $signature = 'theme:generate-pb-seed
                            {--theme= : Generate for a single theme slug}
                            {--force  : Overwrite existing files}';

    protected $description = 'Generate demo/page_builder_layout.json seed files for new visual page builder';

    private array $nicheMap = [
        'food'            => ['hero_title' => 'Fresh, Homemade & Delivered to You',        'hero_subtitle' => 'Order from our kitchen — quality ingredients, every time.',     'category_title' => 'Browse by Cuisine'],
        'grocery'         => ['hero_title' => 'Fresh Groceries at Your Door',               'hero_subtitle' => 'Farm-fresh produce, dairy, and pantry essentials delivered fast.','category_title' => 'Shop by Category'],
        'automotive'      => ['hero_title' => 'Everything Your Vehicle Needs',              'hero_subtitle' => 'Quality parts, accessories, and gear for every make and model.',  'category_title' => 'Shop by Category'],
        'fitness'         => ['hero_title' => 'Train Harder. Recover Faster.',              'hero_subtitle' => 'Premium fitness gear, supplements, and apparel for every goal.',  'category_title' => 'Shop by Sport'],
        'beauty'          => ['hero_title' => 'Glow Naturally, Live Beautifully',           'hero_subtitle' => 'Clean beauty essentials crafted for your skin and lifestyle.',     'category_title' => 'Shop by Concern'],
        'jewelry'         => ['hero_title' => 'Wear What Moves You',                        'hero_subtitle' => 'Handcrafted jewelry for every occasion and every story.',          'category_title' => 'Browse Collections'],
        'kids'            => ['hero_title' => 'Fun, Safe, and Made for Kids',               'hero_subtitle' => 'Toys, clothing, and essentials designed for little adventurers.',  'category_title' => 'Shop by Age'],
        'home-decor'      => ['hero_title' => 'Design Your Dream Space',                    'hero_subtitle' => 'Curated home décor and furniture for every interior style.',       'category_title' => 'Shop by Room'],
        'pet-supplies'    => ['hero_title' => 'Because They Deserve the Best',              'hero_subtitle' => 'Premium food, toys, and care products for happy, healthy pets.',   'category_title' => 'Shop by Pet'],
        'health-pharmacy' => ['hero_title' => 'Your Health, Our Priority',                  'hero_subtitle' => 'Trusted medicines, vitamins, and wellness products delivered.',    'category_title' => 'Browse by Category'],
        'sports'          => ['hero_title' => 'Gear Up. Show Up. Win.',                     'hero_subtitle' => 'Professional-grade sports equipment and activewear for champions.','category_title' => 'Shop by Sport'],
        'electronics'     => ['hero_title' => 'Next-Gen Tech at Your Fingertips',          'hero_subtitle' => 'Laptops, phones, accessories — the latest in consumer electronics.','category_title' => 'Browse by Category'],
        'baby'            => ['hero_title' => 'Gentle Care for Tiny Ones',                  'hero_subtitle' => 'Safe, soft, and certified baby essentials for every milestone.',    'category_title' => 'Shop by Age'],
        'outdoors'        => ['hero_title' => 'Adventure Awaits — Gear Up',                 'hero_subtitle' => 'Outdoor equipment and apparel built for the wild.',                'category_title' => 'Shop by Activity'],
        'luxury-fashion'  => ['hero_title' => 'Luxury Redefined for the Modern Wardrobe',  'hero_subtitle' => 'Curated fashion and accessories for discerning tastes.',            'category_title' => 'Browse Collections'],
        'default'         => ['hero_title' => 'Welcome to Our Store',                       'hero_subtitle' => 'Discover amazing products at great prices.',                       'category_title' => 'Shop by Category'],
    ];

    private array $nicheServices = [
        'food'            => [['las la-motorcycle',    'Fast Delivery',      'Order by noon, delivered by 6pm'],      ['las la-leaf',           'Fresh Daily',        'Ingredients sourced every morning'],    ['las la-star',           'Chef Crafted',       'Recipes from professional kitchens'],   ['las la-headset',        '24/7 Support',       'Always here to help']],
        'grocery'         => [['las la-truck',         'Free Delivery',      'On orders over $50'],                   ['las la-leaf',           'Farm Fresh',         '100% organic options available'],       ['las la-undo',           'Easy Returns',       '30-day hassle-free policy'],            ['las la-headset',        '24/7 Support',       'Always here to help']],
        'automotive'      => [['las la-truck',         'Fast Shipping',      'Parts shipped same day'],               ['las la-shield-alt',     'Guaranteed Fit',     'We verify compatibility before shipping'],['las la-tools',         'Expert Support',     'Talk to a mechanic for free'],         ['las la-undo',           'Easy Returns',       '60-day return policy']],
        'fitness'         => [['las la-shipping-fast', 'Free Shipping',      'On all orders over $75'],               ['las la-medal',          'Pro Quality',        'Used by professional athletes'],        ['las la-undo',           'Easy Returns',       '30-day return policy'],                 ['las la-headset',        'Expert Advice',      'Fitness experts on call']],
        'beauty'          => [['las la-shipping-fast', 'Free Shipping',      'On orders over $40'],                   ['las la-leaf',           'Clean Ingredients',  'No harmful chemicals or parabens'],     ['las la-undo',           'Easy Returns',       '30-day return guarantee'],              ['las la-headset',        '24/7 Support',       'Beauty experts at your service']],
        'jewelry'         => [['las la-gem',           'Certified Quality',  'All pieces come with authenticity cert'],['las la-box',           'Gift Ready',         'Luxury gift packaging included'],       ['las la-undo',           'Free Returns',       '30-day return policy'],                 ['las la-headset',        'Personal Styling',   'Book a free consultation']],
        'kids'            => [['las la-shield-alt',    'Safety Certified',   'All products meet safety standards'],   ['las la-shipping-fast',  'Fast Delivery',      'Express options available'],            ['las la-undo',           'Easy Returns',       '45-day return policy'],                 ['las la-headset',        'Parent Support',     'We are here for your questions']],
        'home-decor'      => [['las la-truck',         'Free Delivery',      'On furniture orders over $200'],        ['las la-ruler-combined', 'Custom Sizing',      'Made-to-measure options available'],    ['las la-undo',           'Easy Returns',       '60-day return policy'],                 ['las la-headset',        'Design Advice',      'Free interior design consultation']],
        'pet-supplies'    => [['las la-shipping-fast', 'Fast Delivery',      'Orders ship within 24 hours'],          ['las la-leaf',           'Vet Approved',       'Trusted by veterinarians nationwide'],  ['las la-undo',           'Easy Returns',       '30-day return policy'],                 ['las la-headset',        '24/7 Support',       'Our pet experts are always available']],
        'health-pharmacy' => [['las la-shipping-fast', 'Fast Dispatch',      'Same-day dispatch before 3pm'],         ['las la-shield-alt',     'Verified Products',  'All products are clinically approved'],  ['las la-undo',           'Easy Returns',       '30-day return policy'],                 ['las la-headset',        'Pharmacist Chat',    'Talk to a pharmacist anytime']],
        'sports'          => [['las la-shipping-fast', 'Free Shipping',      'On orders over $100'],                  ['las la-medal',          'Pro-Grade Gear',     'Equipment used by professionals'],      ['las la-undo',           'Easy Returns',       '60-day return policy'],                 ['las la-headset',        'Expert Support',     'Coaches and athletes on standby']],
        'electronics'     => [['las la-truck',         'Free Shipping',      'On all orders over $99'],               ['las la-shield-alt',     'Warranty Included',  'All products come with full warranty'],  ['las la-undo',           'Easy Returns',       '30-day return policy'],                 ['las la-headset',        'Tech Support',       'Get help from certified technicians']],
        'baby'            => [['las la-shield-alt',    'Safety First',       'All products certified baby-safe'],     ['las la-shipping-fast',  'Fast Delivery',      'Express shipping on essential items'],  ['las la-undo',           'Easy Returns',       '45-day return policy'],                 ['las la-headset',        'Parent Helpline',    'Talk to parenting experts anytime']],
        'outdoors'        => [['las la-truck',         'Free Shipping',      'On orders over $80'],                   ['las la-shield-alt',     'Durability Tested',  'Gear built for extreme conditions'],    ['las la-undo',           'Easy Returns',       '60-day return policy'],                 ['las la-headset',        'Gear Advice',        'Speak with outdoor specialists']],
        'luxury-fashion'  => [['las la-box-open',      'Luxury Packaging',   'Every order arrives gift-wrapped'],     ['las la-shipping-fast',  'Express Delivery',   'White-glove same-day delivery'],        ['las la-undo',           'Easy Returns',       '30-day full refund'],                   ['las la-headset',        'Personal Stylist',   'Book a private styling session']],
        'default'         => [['las la-truck',         'Free Shipping',      'On orders over $50'],                   ['las la-shield-alt',     'Secure Payment',     '100% secure checkout'],                 ['las la-undo',           'Easy Returns',       '30-day return policy'],                 ['las la-headset',        '24/7 Support',       'Always here to help']],
    ];

    public function handle(): int
    {
        $basePath = config('theme.base_path', base_path('themes'));
        $themeArg = $this->option('theme');
        $force    = $this->option('force');

        $slugs = $themeArg
            ? [$themeArg]
            : array_filter(
                array_map('basename', glob($basePath . '/*/theme.json', GLOB_BRACE)),
                fn($s) => !in_array($s, ['_stubs', 'default'])
              );

        // glob returns full paths — extract slugs
        if (!$themeArg) {
            $slugs = [];
            foreach (glob($basePath . '/*/theme.json') as $jsonFile) {
                $slug = basename(dirname($jsonFile));
                if (!in_array($slug, ['_stubs', 'default'])) {
                    $slugs[] = $slug;
                }
            }
        }

        foreach ($slugs as $slug) {
            $themeDir  = $basePath . '/' . $slug;
            $metaFile  = $themeDir . '/theme.json';
            $outputFile = $themeDir . '/demo/page_builder_layout.json';

            if (!file_exists($metaFile)) {
                $this->warn("Skipping {$slug}: theme.json not found");
                continue;
            }

            if (file_exists($outputFile) && !$force) {
                $this->line("<fg=yellow>Skip</> {$slug} — already has page_builder_layout.json (use --force to overwrite)");
                continue;
            }

            $meta  = json_decode(file_get_contents($metaFile));
            $niche = $meta->niche ?? 'default';
            $name  = $meta->name ?? Str::title($slug);

            if (!array_key_exists($niche, $this->nicheMap)) {
                $niche = 'default';
            }

            $layout = $this->buildLayout($slug, $name, $niche);

            if (!is_dir(dirname($outputFile))) {
                mkdir(dirname($outputFile), 0755, true);
            }

            file_put_contents($outputFile, json_encode($layout, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->info("Generated page_builder_layout.json for <fg=green>{$slug}</> ({$niche})");
        }

        $this->newLine();
        $this->info('Done.');
        return 0;
    }

    private function buildLayout(string $slug, string $themeName, string $niche): array
    {
        $copy     = $this->nicheMap[$niche];
        $services = $this->nicheServices[$niche];

        $sections = [
            $this->heroSection($copy),
            $this->servicesSection($services),
            $this->featuredProductsSection($copy),
            $this->categoryGridSection($copy),
            $this->productByCategorySection($copy),
            $this->testimonialSection(),
            $this->newsletterSection($copy),
        ];

        $containers = [];
        $widgets    = [];
        $order      = 0;

        foreach ($sections as $section) {
            $cid = 'c_' . Str::random(8);
            $lid = 'col_' . Str::random(8);
            $wid = 'w_' . Str::random(8);

            $containers[] = [
                'id'      => $cid,
                'columns' => [[
                    'id'      => $lid,
                    'width'   => 12,
                    'widgets' => [['id' => $wid, 'type' => $section['type']]],
                ]],
            ];

            $widgets[] = [
                'widget_id'        => $wid,
                'widget_type'      => $section['type'],
                'container_id'     => $cid,
                'column_id'        => $lid,
                'sort_order'       => $order++,
                'general_settings' => $section['general'],
                'style_settings'   => $section['style'] ?? [],
                'is_visible'       => true,
                'is_enabled'       => true,
            ];
        }

        return [
            'version'     => '1.0',
            'theme'       => $slug,
            'description' => "Default homepage layout for {$themeName}",
            'content'     => ['containers' => $containers],
            'widgets'     => $widgets,
        ];
    }

    // ── Section builders ───────────────────────────────────────────────────

    private function heroSection(array $copy): array
    {
        return [
            'type'    => 'tenant_hero_section',
            'general' => [
                'content' => [
                    'title'                  => $copy['hero_title'],
                    'subtitle'               => $copy['hero_subtitle'],
                    'button_text'            => 'Shop Now',
                    'button_url'             => '#',
                    'secondary_button_text'  => 'View All Products',
                    'secondary_button_url'   => '#',
                ],
                'media' => [
                    'hero_image'       => null,
                    'background_image' => null,
                    'background_color' => '#f8f9fa',
                ],
            ],
            'style' => [
                'spacing' => ['padding_top' => 80, 'padding_bottom' => 80],
            ],
        ];
    }

    private function servicesSection(array $services): array
    {
        return [
            'type'    => 'tenant_services_strip',
            'general' => [
                'items' => [
                    'services' => array_map(fn($s) => [
                        'icon'        => $s[0],
                        'title'       => $s[1],
                        'description' => $s[2],
                    ], $services),
                ],
            ],
            'style' => [
                'style'   => ['background_color' => '#ffffff'],
                'spacing' => ['padding_top' => 50, 'padding_bottom' => 50],
            ],
        ];
    }

    private function featuredProductsSection(array $copy): array
    {
        return [
            'type'    => 'tenant_featured_products',
            'general' => [
                'content' => [
                    'title'         => 'Featured Products',
                    'subtitle'      => '',
                    'view_all_text' => 'View All',
                    'view_all_url'  => '#',
                ],
                'query' => [
                    'item_count' => 8,
                    'sort_by'    => 'latest',
                    'columns'    => '4',
                ],
            ],
            'style' => [
                'spacing' => ['padding_top' => 80, 'padding_bottom' => 80],
            ],
        ];
    }

    private function categoryGridSection(array $copy): array
    {
        return [
            'type'    => 'tenant_category_grid',
            'general' => [
                'content' => [
                    'title'         => $copy['category_title'],
                    'subtitle'      => '',
                    'view_all_text' => 'View All',
                    'view_all_url'  => '#',
                ],
                'query' => [
                    'item_count' => 6,
                    'columns'    => '6',
                    'show_count' => false,
                ],
            ],
            'style' => [
                'spacing' => ['padding_top' => 80, 'padding_bottom' => 80],
            ],
        ];
    }

    private function productByCategorySection(array $copy): array
    {
        return [
            'type'    => 'tenant_products_by_category',
            'general' => [
                'content' => [
                    'title'         => 'Shop by Category',
                    'view_all_text' => 'View All',
                    'view_all_url'  => '#',
                ],
                'query' => [
                    'item_count'     => 8,
                    'show_all_tab'   => true,
                    'max_categories' => 5,
                    'columns'        => '4',
                ],
            ],
            'style' => [
                'spacing' => ['padding_top' => 80, 'padding_bottom' => 80],
            ],
        ];
    }

    private function testimonialSection(): array
    {
        return [
            'type'    => 'tenant_testimonials',
            'general' => [
                'header' => [
                    'title'    => 'What Our Customers Say',
                    'subtitle' => '',
                ],
                'items' => [
                    'testimonials' => [
                        ['name' => 'Sarah M.',  'role' => 'Verified Buyer',   'rating' => '5', 'review' => 'Absolutely love the products! Quality is amazing and delivery was super fast.'],
                        ['name' => 'James K.',  'role' => 'Regular Customer', 'rating' => '5', 'review' => 'Best online store I\'ve shopped at. Will definitely come back again!'],
                        ['name' => 'Emma R.',   'role' => 'New Customer',     'rating' => '4', 'review' => 'Great selection and competitive prices. Very happy with my purchase.'],
                        ['name' => 'David L.',  'role' => 'Verified Buyer',   'rating' => '5', 'review' => 'Fast shipping, excellent product quality, and wonderful customer service!'],
                    ],
                ],
            ],
            'style' => [
                'style'   => ['background_color' => '#f8f9fa'],
                'spacing' => ['padding_top' => 80, 'padding_bottom' => 80],
            ],
        ];
    }

    private function newsletterSection(array $copy): array
    {
        return [
            'type'    => 'tenant_newsletter',
            'general' => [
                'content' => [
                    'title'        => 'Stay in the Loop',
                    'subtitle'     => 'Subscribe for exclusive deals, new arrivals, and insider tips.',
                    'placeholder'  => 'Enter your email address',
                    'button_text'  => 'Subscribe',
                ],
            ],
            'style' => [
                'style'   => ['background_color' => '', 'text_color' => '#ffffff'],
                'spacing' => ['padding_top' => 80, 'padding_bottom' => 80],
            ],
        ];
    }
}
