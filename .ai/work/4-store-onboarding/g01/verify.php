<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\PaymentLogs;
use App\Models\StoreOnboardingRequest;
use App\Models\Tenant;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

if (getenv('YMNAY_G01') !== '1' || getenv('APP_ENV') !== 'testing') {
    fwrite(STDERR, "G01 verification is restricted to the disposable testing environment.\n");
    exit(2);
}

require __DIR__.'/../../../../core/vendor/autoload.php';
$app = require __DIR__.'/../../../../core/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$assert = static function (bool $condition, string $message): void {
    if (! $condition) {
        throw new RuntimeException($message);
    }
};

$tenant = Tenant::findOrFail('g01-browser-store');
$onboarding = StoreOnboardingRequest::query()
    ->where('tenant_id', $tenant->id)
    ->where('status', 'ready')
    ->firstOrFail();
$trial = PaymentLogs::query()
    ->where('tenant_id', $tenant->id)
    ->where('status', 'trial')
    ->firstOrFail();

$stages = (array) $tenant->getInternal('onboarding_stages');
foreach (['database', 'migrations', 'domain', 'seed', 'login_key', 'store_title', 'file_dispatch'] as $stage) {
    $assert(($stages[$stage] ?? null) === 'done', "Provisioning stage did not finish: {$stage}");
}

$assert($tenant->theme_slug === 'hexfashion', 'Selected theme was not preserved.');
$assert($tenant->domain?->domain === 'g01-browser-store.localhost', 'Tenant domain was not created.');
$assert(! empty($tenant->unique_key), 'Tenant login key is missing.');
$assert((int) $trial->user_id === (int) $onboarding->user_id, 'Trial belongs to a different user.');
$assert((int) $trial->package_id === (int) $onboarding->plan_id, 'Trial belongs to a different plan.');
$assert($trial->theme_slug === $onboarding->theme_slug, 'Trial theme differs from the request.');
$assert(Storage::exists('g01-browser-store/g01-proof.txt'), 'Delayed tenant file copy did not finish.');
$assert(
    is_file(base_path('assets/tenant/uploads/media-uploader/g01-browser-store/g01-proof.txt')),
    'Legacy tenant media copy did not finish.'
);
$assert(DB::table('file_sync_jobs')->count() === 0, 'Tenant file queue was not drained.');

$raceTenant = Tenant::findOrFail('g01-race-store');
$raceRequests = StoreOnboardingRequest::query()
    ->where('subdomain', 'g01-race-store')
    ->orderBy('created_at')
    ->get();
$assert($raceRequests->count() === 2, 'Parallel address race requests are missing.');
$assert($raceRequests->where('status', 'ready')->count() === 1, 'Parallel address race did not produce exactly one winner.');
$assert($raceRequests->where('status', '!=', 'ready')->count() === 1, 'Parallel address race did not safely reject one loser.');
$raceWinner = $raceRequests->firstWhere('status', 'ready');
$assert((int) $raceTenant->user_id === (int) $raceWinner->user_id, 'Race tenant belongs to the losing account.');
$assert(Storage::exists('g01-race-store/g01-proof.txt'), 'Race tenant file copy did not finish.');
$nativeTenant = Tenant::findOrFail('g01-native-store');
$assert($nativeTenant->domain?->domain === 'g01-native-store.localhost', 'Native tenant domain is missing.');
$assert(! empty($nativeTenant->unique_key), 'Native tenant login key is missing.');
$assert(
    PaymentLogs::where('tenant_id', $nativeTenant->id)->where('status', 'complete')->exists(),
    'Native path payment fixture was not preserved.'
);
$assert(Storage::exists('g01-native-store/g01-proof.txt'), 'Native tenant file copy did not finish.');

try {
    tenancy()->initialize($tenant);
    $tenantDb = DB::connection('tenant');
    $centralDbName = (string) config('database.connections.mysql.database');
    $tenantDbName = $tenantDb->getDatabaseName();
    $assert($tenantDbName !== $centralDbName, 'Tenant and central databases are not isolated.');

    foreach (['migrations', 'admins', 'roles', 'permissions', 'static_options', 'pages'] as $table) {
        $assert($tenantDb->getSchemaBuilder()->hasTable($table), "Tenant table is missing: {$table}");
    }

    $admin = Admin::on('tenant')->first();
    $assert($admin !== null && $admin->hasRole('Super Admin'), 'Tenant administrator or role is missing.');
    $assert(
        $tenantDb->table('static_options')->where('option_name', 'site_title')->value('option_value') === 'متجر G01 المعزول',
        'Tenant store title was not synchronized.'
    );
    $assert($tenantDb->table('pages')->count() > 0, 'Theme/page seed did not create tenant content.');
} finally {
    tenancy()->end();
}

try {
    tenancy()->initialize($raceTenant);
    $raceDb = DB::connection('tenant');
    $raceDbName = $raceDb->getDatabaseName();
    $assert($raceDbName !== $tenantDbName && $raceDbName !== $centralDbName, 'Race tenant database is not isolated.');
    $assert(
        $raceDb->table('static_options')->where('option_name', 'site_title')->value('option_value') === $raceWinner->store_name,
        'The winning request did not own the race tenant title.'
    );
} finally {
    tenancy()->end();
}

try {
    tenancy()->initialize($nativeTenant);
    $nativeDb = DB::connection('tenant');
    $nativeDbName = $nativeDb->getDatabaseName();
    $assert(
        ! in_array($nativeDbName, [$centralDbName, $tenantDbName, $raceDbName], true),
        'Native tenant database is not isolated.'
    );
    $nativeAdmin = Admin::on('tenant')->first();
    $assert($nativeAdmin !== null && $nativeAdmin->hasRole('Super Admin'), 'Native tenant administrator is missing.');
} finally {
    tenancy()->end();
}

echo json_encode([
    'request_reference' => $onboarding->id,
    'tenant' => $tenant->id,
    'tenant_database' => $tenantDbName,
    'central_database' => $centralDbName,
    'theme' => $tenant->theme_slug,
    'trial_days' => (int) $onboarding->plan->trial_days,
    'provisioning_stages' => $stages,
    'file_queue_drained' => true,
    'tenant_admin_ready' => true,
    'parallel_address_race' => [
        'tenant' => $raceTenant->id,
        'tenant_database' => $raceDbName,
        'ready_requests' => 1,
        'rejected_requests' => 1,
    ],
    'native_path' => [
        'tenant' => $nativeTenant->id,
        'tenant_database' => $nativeDbName,
        'gateway_or_charge_used' => false,
        'tenant_admin_ready' => true,
    ],
    'synthetic_only' => true,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;
