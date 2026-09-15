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
    'site_currency_symbol' => 'ر.س',
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

$planId = DB::table('price_plans')->insertGetId([
    'title' => json_encode(['ar' => 'باقة اختبار G01', 'en_GB' => 'G01 Test Plan'], JSON_UNESCAPED_UNICODE),
    'type' => 0,
    'status' => 1,
    'price' => 149,
    'has_trial' => 1,
    'trial_days' => 37,
    'product_permission_feature' => 100,
    'page_permission_feature' => 10,
    'blog_permission_feature' => 10,
    'created_at' => now(),
    'updated_at' => now(),
]);

DB::table('themes')->updateOrInsert(
    ['slug' => 'hexfashion'],
    ['title' => 'HexFashion', 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
);
DB::table('plan_themes')->insert([
    'plan_id' => $planId,
    'theme_slug' => 'hexfashion',
    'status' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);

$plan = App\Models\PricePlan::findOrFail($planId);
$planSnapshot = [
    'id' => (int) $plan->id,
    'title' => (string) $plan->title,
    'price' => (string) $plan->price,
    'type' => (int) $plan->type,
    'has_trial' => (bool) $plan->has_trial,
    'trial_days' => (int) $plan->trial_days,
    'updated_at' => optional($plan->updated_at)->toISOString(),
];

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

$proofDirectory = storage_path('app/seeder-files/all-media');
if (! is_dir($proofDirectory) && ! mkdir($proofDirectory, 0775, true) && ! is_dir($proofDirectory)) {
    throw new RuntimeException("Could not create the isolated file evidence directory.");
}

file_put_contents(
    $proofDirectory.'/g01-proof.txt',
    "synthetic G01 file; no production data\n"
);

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
    'trial_days' => 37,
    'theme' => 'hexfashion',
    'mail_host' => config('mail.mailers.smtp.host'),
    'onboarding_routes' => $onboardingRoutes,
    'parallel_address_race' => $raceRequests,
    'synthetic_only' => true,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
