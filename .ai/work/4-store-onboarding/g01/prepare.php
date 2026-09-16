<?php

declare(strict_types=1);

use App\Models\StaticOption;
use App\Models\StaticOptionCentral;
use App\Models\StoreOnboardingRequest;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

if (getenv('YMNAY_G01') !== '1' || getenv('APP_ENV') !== 'testing') {
    fwrite(STDERR, "G01 preparation is restricted to the disposable testing environment.\n");
    exit(2);
}

require __DIR__.'/../../../../core/vendor/autoload.php';
$app = require __DIR__.'/../../../../core/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

foreach ([
    'site_title' => 'YMNAY G01',
    'site_global_email' => 'support@example.test',
    'site_global_currency' => 'SAR',
    'site_custom_currency_symbol' => 'ر.س',
    'site_currency_symbol_position' => 'right',
    'user_email_verify_status' => 'on',
    'forbidden_subdomains' => 'www,admin,user,tenant,landlord',
    'maintenance_mode' => '',
] as $name => $value) {
    StaticOption::query()->updateOrCreate(['option_name' => $name], ['option_value' => $value]);
    StaticOptionCentral::query()->updateOrCreate(['option_name' => $name], ['option_value' => $value]);
}

DB::table('languages')->updateOrInsert(
    ['slug' => 'ar'],
    [
        'name' => 'Arabic',
        'direction' => 1,
        'default' => 1,
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]
);

$planFixtures = [
    ['title' => 'باقة اختبار G01', 'price' => 149, 'trial_days' => 37, 'products' => 100, 'pages' => 10, 'blogs' => 10, 'storage' => 512],
    ['title' => 'باقة نمو G01', 'price' => 249, 'trial_days' => 45, 'products' => 350, 'pages' => 25, 'blogs' => 40, 'storage' => 2048],
    ['title' => 'باقة أعمال G01', 'price' => 399, 'trial_days' => 60, 'products' => -1, 'pages' => -1, 'blogs' => -1, 'storage' => 5120],
];
$planIds = [];
foreach ($planFixtures as $fixture) {
    $planIds[] = DB::table('price_plans')->insertGetId([
        'title' => json_encode(['ar' => $fixture['title'], 'en_GB' => $fixture['title']], JSON_UNESCAPED_UNICODE),
        'type' => 0,
        'status' => 1,
        'price' => $fixture['price'],
        'has_trial' => 1,
        'trial_days' => $fixture['trial_days'],
        'product_permission_feature' => $fixture['products'],
        'page_permission_feature' => $fixture['pages'],
        'blog_permission_feature' => $fixture['blogs'],
        'storage_permission_feature' => $fixture['storage'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
$planId = $planIds[0];

$themeFixtures = [
    'hexfashion' => 'HexFashion',
    'bakerco' => 'BakerCo',
    'aromatic' => 'Aromatic',
];
foreach ($themeFixtures as $slug => $title) {
    DB::table('themes')->updateOrInsert(
        ['slug' => $slug],
        ['title' => $title, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
    );
    DB::table('plan_themes')->insert([
        'plan_id' => $planId,
        'theme_slug' => $slug,
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
foreach (array_slice($planIds, 1) as $additionalPlanId) {
    DB::table('plan_themes')->insert([
        'plan_id' => $additionalPlanId,
        'theme_slug' => 'hexfashion',
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

$plan = App\Models\PricePlan::findOrFail($planId);
$planSnapshot = [
    'id' => (int) $plan->id,
    'title' => (string) $plan->title,
    'price' => (string) $plan->price,
    'type' => (int) $plan->type,
    'has_trial' => (bool) $plan->has_trial,
    'trial_days' => (int) $plan->trial_days,
    'product_limit' => $plan->product_permission_feature === null ? null : (int) $plan->product_permission_feature,
    'page_limit' => $plan->page_permission_feature === null ? null : (int) $plan->page_permission_feature,
    'blog_limit' => $plan->blog_permission_feature === null ? null : (int) $plan->blog_permission_feature,
    'storage_limit' => $plan->storage_permission_feature === null ? null : (int) $plan->storage_permission_feature,
    'updated_at' => optional($plan->updated_at)->toISOString(),
];

$existingUnverified = User::create([
    'name' => 'G01 Existing Unverified',
    'email' => 'g01-existing-unverified@example.test',
    'username' => 'g01_existing_unverified',
    'mobile' => '966500000009',
    'password' => Hash::make('G01-Isolated-Password!'),
    'email_verified' => 0,
]);
StoreOnboardingRequest::create([
    'id' => (string) Str::uuid(),
    'user_id' => $existingUnverified->id,
    'plan_id' => $plan->id,
    'theme_slug' => 'hexfashion',
    'store_name' => 'G01 Existing Account Store',
    'subdomain' => 'g01-existing-account',
    'status' => 'draft',
    'plan_snapshot' => $planSnapshot,
]);

$passwordRecoveryUser = User::create([
    'name' => 'G01 Password Recovery',
    'email' => 'g01-password-recovery@example.test',
    'username' => 'g01_password_recovery',
    'mobile' => '966500000008',
    'password' => Hash::make('G01-Isolated-Password!'),
    'email_verified' => 1,
]);

$raceRequests = [];
foreach ([1, 2] as $number) {
    $user = User::create([
        'name' => "G01 Race User {$number}",
        'email' => "g01-race-{$number}@example.test",
        'username' => "g01_race_{$number}",
        'mobile' => "96650000000{$number}",
        'password' => Hash::make('G01-Isolated-Password!'),
        'email_verified' => 1,
    ]);
    $request = StoreOnboardingRequest::create([
        'id' => (string) Str::uuid(),
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'theme_slug' => 'hexfashion',
        'store_name' => "G01 Race Store {$number}",
        'subdomain' => 'g01-race-store',
        'status' => 'account_verified',
        'plan_snapshot' => $planSnapshot,
    ]);
    $raceRequests[] = ['email' => $user->email, 'request_reference' => $request->id];
}

$proofContents = "synthetic G01 file; no production data\n";
foreach ([
    // Exercises the asynchronous Storage-based tenant file copy.
    storage_path('app/seeder-files/all-media'),
    // The legacy MediaSeed expects the distribution's optional demo-media bundle.
    // Supply one synthetic fixture so a source checkout can exercise the copy path.
    base_path('assets/tenant/seeder-files/all-media'),
] as $proofDirectory) {
    if (! is_dir($proofDirectory) && ! mkdir($proofDirectory, 0775, true) && ! is_dir($proofDirectory)) {
        throw new RuntimeException("Could not create the isolated file evidence directory: {$proofDirectory}");
    }

    file_put_contents($proofDirectory.'/g01-proof.txt', $proofContents);
}

$onboardingRoutes = collect(app('router')->getRoutes()->getRoutes())
    ->filter(fn ($route) => str_starts_with((string) $route->getName(), 'landlord.store.onboarding'))
    ->map(fn ($route) => [
        'name' => $route->getName(),
        'methods' => $route->methods(),
        'uri' => $route->uri(),
    ])
    ->values()
    ->all();

if (count($onboardingRoutes) !== 10) {
    throw new RuntimeException('The full application did not register all onboarding routes.');
}

echo json_encode([
    'environment' => app()->environment(),
    'central_database' => DB::connection()->getDatabaseName(),
    'plan_id' => $planId,
    'plan_ids' => $planIds,
    'theme_slugs' => array_keys($themeFixtures),
    'existing_unverified_email' => $existingUnverified->email,
    'password_recovery_email' => $passwordRecoveryUser->email,
    'trial_days' => 37,
    'theme' => 'hexfashion',
    'mail_host' => config('mail.mailers.smtp.host'),
    'onboarding_routes' => $onboardingRoutes,
    'parallel_address_race' => $raceRequests,
    'synthetic_only' => true,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
