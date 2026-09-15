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
$assert(DB::table('file_sync_jobs')->count() === 0, 'Tenant file queue was not drained.');

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
    'synthetic_only' => true,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;
