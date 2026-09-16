<?php

declare(strict_types=1);

use App\Events\TenantRegisterEvent;
use App\Models\MediaUploader;
use App\Models\Page;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Xgenious\PageBuilder\Models\PageBuilderWidget;

if (getenv('YMNAY_G01') !== '1' || getenv('APP_ENV') !== 'testing' || !getenv('G01_VISUAL_DIR')) {
    throw new RuntimeException('Visual fixtures require the explicitly isolated G01 test environment.');
}
require __DIR__.'/../../../../core/vendor/autoload.php';
$app = require __DIR__.'/../../../../core/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
if (DB::connection()->getDatabaseName() !== 'ymnay_g01_central' || tenancy()->initialized) {
    throw new RuntimeException('Refusing to touch any database other than disposable G01 central.');
}
$out = getenv('G01_VISUAL_DIR');
$identity = json_decode(file_get_contents($out.'/identity.json'), true, 512, JSON_THROW_ON_ERROR);
$set = static function (string $key, mixed $value): void {
    update_static_option($key, $value);
    update_static_option_central($key, $value);
};

if (($argv[1] ?? '') === 'preview-image') {
    $set('aromatic_theme_image', url('/assets/landlord/uploads/media-uploader/visual-aromatic.png'));
    exit(0);
}

$set('site_title', 'YMNAY');
$set('site_ar_title', 'يمناي');
$set('main_color_one', '#4338CA');
$set('main_color_two', '#4338CA');
foreach (['body', 'heading'] as $kind) {
    $family = $identity[$kind.'_family'] ?? 'Tajawal';
    if (!preg_match('/^[\p{L}\p{N} _-]{1,100}$/u', $family)) throw new RuntimeException('Invalid public font family.');
    $set($kind.'_font_family', $family);
    $set($kind.'_font_variant', serialize(['0,400', '0,700']));
}
if (!empty($identity['logo_file'])) {
    $logo = MediaUploader::create(['title' => 'YMNAY public logo - review copy', 'alt' => 'YMNAY يمناي', 'path' => $identity['logo_file'], 'load_from' => 0, 'user_type' => 0]);
    $set('site_logo', $logo->id);
    $set('site_white_logo', $logo->id);
}
foreach (['terms_condition' => 'الشروط والأحكام', 'privacy_policy' => 'سياسة الخصوصية'] as $key => $title) {
    $page = Page::create(['title' => $title, 'slug' => 'visual-'.$key, 'page_content' => '<p>صفحة تجريبية لاختبار فتح الرابط وحفظ النموذج. ليست صياغة قانونية أو سياسة منشورة.</p>', 'status' => 1, 'visibility' => 0]);
    $page->slug()->create(['slug' => $page->slug]);
    $set($key, $page->id);
}

// Run only AFTER the original G01 assertions. These are declared representative
// review fixtures, not a Production database copy or changes to live pricing.
DB::table('price_plans')->update(['status' => 0]);
$fixtures = [
    ['الباقة الأساسية', 30, 20, 10, 10, 1000],
    ['الباقة الاحترافية', 60, 50, 20, 50, 2000],
    ['باقة الأعمال', 90, 500, 50, 100, 5000],
    ['باقة المواقع التعريفية', 50, 1, 10, 100, 1000],
];
$ids = [];
foreach ($fixtures as [$title, $price, $products, $pages, $blogs, $storage]) {
    $ids[] = $id = DB::table('price_plans')->insertGetId([
        'title' => json_encode(['ar' => $title, 'en_GB' => $title], JSON_UNESCAPED_UNICODE),
        'price' => $price, 'type' => 0, 'status' => 1, 'has_trial' => 1, 'trial_days' => 60,
        'product_permission_feature' => $products, 'page_permission_feature' => $pages,
        'blog_permission_feature' => $blogs, 'storage_permission_feature' => $storage,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    foreach (['aromatic', 'hexfashion'] as $slug) {
        DB::table('plan_themes')->insert(['plan_id' => $id, 'theme_slug' => $slug, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]);
    }
}
$set('aromatic_theme_name', 'عِطري');
$set('hexfashion_theme_name', 'الأزياء');
$set('aromatic_theme_url', 'http://g01-visual-preview.localhost');

$plan = PricePlan::findOrFail($ids[0]);
$user = User::create(['name' => 'مراجعة القالب العربي', 'email' => 'visual-preview@example.test', 'username' => 'visual_preview', 'mobile' => '967700000040', 'password' => Hash::make(Str::random(32)), 'email_verified' => 1]);
$payment = PaymentLogs::create([
    'email' => $user->email, 'name' => $user->name, 'package_name' => $plan->title, 'package_price' => $plan->price,
    'package_id' => $plan->id, 'user_id' => $user->id, 'tenant_id' => 'g01-visual-preview',
    'status' => 'complete', 'payment_status' => 'complete', 'is_renew' => 0, 'track' => Str::random(10),
    'start_date' => now()->format('d-m-Y H:i:s'), 'expire_date' => now()->addMonth()->format('d-m-Y H:i:s'), 'theme_slug' => 'aromatic',
]);
// A synthetic native pipeline fixture, without a gateway or any charge.
event(new TenantRegisterEvent($user, 'g01-visual-preview', 'aromatic'));
$tenant = Tenant::findOrFail('g01-visual-preview');
try {
    tenancy()->initialize($tenant);
    if (!str_starts_with(DB::connection('tenant')->getDatabaseName(), 'ymnay_g01_tenant_')) throw new RuntimeException('Unexpected preview database.');
    DB::table('languages')->update(['default' => 0]);
    DB::table('languages')->updateOrInsert(['slug' => 'ar'], ['name' => 'Arabic', 'direction' => 1, 'default' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]);
    update_static_option('site_title', 'عِطري — متجر العطور');
    update_static_option('site_announcement_text', 'متجر عربي تجريبي للمراجعة البصرية');
    foreach (\App\Models\Menu::all() as $menu) {
        $content = (string) $menu->content;
        $content = str_replace(['Home', 'Shop', 'About Us', 'Contact Us', 'Categories', 'Blog'], ['الرئيسية', 'المتجر', 'من نحن', 'تواصل معنا', 'الأقسام', 'المدونة'], $content);
        $menu->update(['content' => $content]);
    }
    $widget = PageBuilderWidget::where('widget_type', 'aromatic_hero_section')->firstOrFail();
    $settings = $widget->general_settings ?? [];
    $settings['content'] = array_merge($settings['content'] ?? [], [
        'section_tag' => 'عطور تعبّر عنك', 'title' => 'اكتشف عطرك<br>المميز',
        'subtitle' => 'تشكيلة من العطور والهدايا المختارة لتناسب ذوقك وكل مناسباتك.',
        'button_text' => 'تسوق المجموعة', 'button_url' => '/shop', 'button2_text' => 'تعرف علينا', 'button2_url' => '/about',
    ]);
    $widget->update(['general_settings' => $settings]);
} finally {
    tenancy()->end();
}
file_put_contents($out.'/fixture.json', json_encode([
    'synthetic_only' => true, 'production_mutations' => false,
    'source' => 'Four explicitly representative review plans consistent with the read-only public plan presentation; SAR and the 60-day trial follow owner decisions. Names, prices, limits and legal pages are review inputs, not a Production database snapshot.',
    'plans' => $fixtures, 'plan_ids' => $ids, 'trial_days' => 60,
    'arabic_preview' => 'http://g01-visual-preview.localhost', 'theme' => 'Actual repository aromatic theme, native provisioning, Arabic hero configured in disposable tenant DB.',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
