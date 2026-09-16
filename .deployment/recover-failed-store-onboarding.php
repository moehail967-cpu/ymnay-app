<?php

declare(strict_types=1);

use App\Models\PaymentLogs;
use App\Models\StoreOnboardingRequest;
use App\Models\Tenant;
use App\Models\CustomDomain;
use App\Models\TenantException;
use App\Models\TenantUniqueKey;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

if (PHP_SAPI !== 'cli') {
    throw new RuntimeException('This recovery may only run from the command line.');
}

$confirmation = getenv('RECOVERY_CONFIRM') ?: '';
$expectedFailedAt = getenv('EXPECTED_FAILED_AT') ?: '';

if ($confirmation !== 'RECOVER_FAILED_ONBOARDING') {
    throw new RuntimeException('Recovery confirmation did not match.');
}

if (! preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $expectedFailedAt)) {
    throw new RuntimeException('Expected failure timestamp is invalid.');
}

$applicationRoot = getcwd();
if (! is_string($applicationRoot)
    || ! is_file($applicationRoot.'/artisan')
    || ! is_file($applicationRoot.'/vendor/autoload.php')) {
    throw new RuntimeException('Recovery must run from the Laravel application root.');
}

require $applicationRoot.'/vendor/autoload.php';
$app = require $applicationRoot.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (! $app->environment('production')) {
    throw new RuntimeException('Recovery is restricted to Production.');
}

$fail = static function (bool $condition, string $message): void {
    if ($condition) {
        throw new RuntimeException($message);
    }
};

$expectedDatabaseCounts = [
    'admins' => 1,
    'languages' => 8,
    'menus' => 3,
    'pages' => 6,
    'media_uploaders' => 288,
];

$expectedDatabaseTimestamp = gmdate('Y-m-d H:i:s', strtotime($expectedFailedAt));
$requests = StoreOnboardingRequest::query()
    ->where('status', 'failed')
    ->where('updated_at', $expectedDatabaseTimestamp)
    ->get();

$fail($requests->count() !== 1, 'Recovery target was not exactly one failed request.');
$request = $requests->first();
$fail(! $request, 'Recovery request was not found.');
$fail((bool) $request->completed_at, 'A completed onboarding request cannot be recovered this way.');
$fail(! $request->user_id || ! $request->subdomain, 'Recovery request ownership data is incomplete.');

$demoMediaSource = global_assets_path('assets/tenant/seeder-files/all-media');
$fail(! is_dir($demoMediaSource) || ! is_readable($demoMediaSource), 'Corrected demo media source is unavailable.');
$demoMediaFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($demoMediaSource, FilesystemIterator::SKIP_DOTS)
);
$fail(iterator_count($demoMediaFiles) < 1000, 'Corrected demo media source is incomplete.');

$tenant = Tenant::find($request->subdomain);
$fail(! $tenant, 'The guarded partial tenant was not found.');
$fail(! preg_match('/^[a-z0-9-]+$/', $tenant->id), 'Tenant identifier is unsafe.');
$fail($tenant->id !== $request->subdomain, 'Tenant address does not match the request.');
$fail((int) $tenant->user_id !== (int) $request->user_id, 'Tenant owner does not match the request.');
$fail($tenant->theme_slug !== $request->theme_slug, 'Tenant theme does not match the request.');
$fail($tenant->getInternal('onboarding_request_id') !== $request->id, 'Tenant origin does not match the request.');
$fail($request->tenant_id && $request->tenant_id !== $tenant->id, 'Request tenant link does not match.');

$stages = (array) $tenant->getInternal('onboarding_stages');
foreach (['database', 'migrations', 'domain'] as $completedStage) {
    $fail(($stages[$completedStage] ?? null) !== 'done', "Unexpected {$completedStage} recovery stage.");
}
$fail(($stages['seed'] ?? null) !== 'running', 'Seed stage is not the diagnosed interrupted state.');
foreach (['login_key', 'store_title', 'file_dispatch'] as $futureStage) {
    $fail(array_key_exists($futureStage, $stages), "Recovery target already reached {$futureStage}.");
}

$databaseName = $tenant->database()->getName();
$fail(! preg_match('/^[A-Za-z0-9_]+$/', $databaseName), 'Tenant database identifier is unsafe.');
$fail(! $tenant->database()->manager()->databaseExists($databaseName), 'Tenant database is missing.');
$fail(PaymentLogs::query()->where('tenant_id', $tenant->id)->exists(), 'A subscription ledger exists for the tenant.');
$fail(TenantUniqueKey::query()->where('tenant_id', $tenant->id)->exists(), 'A tenant login key already exists.');
$fail(CustomDomain::query()->where('old_domain', $tenant->id)
    ->orWhere('custom_domain', $tenant->id)->exists(), 'A custom domain exists for the tenant.');
$fail(TenantException::query()->where('tenant_id', $tenant->id)->exists(), 'A tenant exception record exists.');
$fail($tenant->domains()->count() !== 1, 'Tenant domain count is outside the diagnosed state.');

try {
    tenancy()->initialize($tenant);
    $tenantConnection = DB::connection('tenant');
    foreach ($expectedDatabaseCounts as $table => $expectedCount) {
        $fail(! $tenantConnection->getSchemaBuilder()->hasTable($table), "Expected tenant table is missing: {$table}.");
        $fail($tenantConnection->table($table)->count() !== $expectedCount, "Tenant row count changed: {$table}.");
    }
} finally {
    tenancy()->end();
}

$mediaDirectory = global_assets_path('assets/tenant/uploads/media-uploader/'.$tenant->id);
$backupToken = gmdate('Ymd_His').'_'.substr(hash('sha256', $request->id), 0, 12);
$backupDatabase = 'ymnay_recovery_'.$backupToken;
$backupDirectory = '/home/ymnay/deploy-backups/onboarding-recovery/'.$backupToken;
$central = DB::connection();

$quoteIdentifier = static function (string $identifier): string {
    if (! preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
        throw new RuntimeException('Unsafe database identifier encountered.');
    }

    return '`'.$identifier.'`';
};

$backupDatabaseQuoted = $quoteIdentifier($backupDatabase);
$databaseQuoted = $quoteIdentifier($databaseName);
$fail((bool) $central->selectOne(
    'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?',
    [$backupDatabase]
), 'Recovery backup database already exists.');

$central->statement("CREATE DATABASE {$backupDatabaseQuoted} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

try {
    $tables = $central->select("SHOW FULL TABLES FROM {$databaseQuoted} WHERE Table_type = 'BASE TABLE'");
    $fail(count($tables) === 0, 'No tenant tables were available for backup.');

    foreach ($tables as $tableRow) {
        $values = array_values((array) $tableRow);
        $tableName = (string) ($values[0] ?? '');
        $tableQuoted = $quoteIdentifier($tableName);
        $central->statement("CREATE TABLE {$backupDatabaseQuoted}.{$tableQuoted} LIKE {$databaseQuoted}.{$tableQuoted}");
        $central->statement("INSERT INTO {$backupDatabaseQuoted}.{$tableQuoted} SELECT * FROM {$databaseQuoted}.{$tableQuoted}");
    }
} catch (Throwable $exception) {
    $central->statement("DROP DATABASE IF EXISTS {$backupDatabaseQuoted}");
    throw $exception;
}

if (is_dir($mediaDirectory)) {
    $mediaBackup = $backupDirectory.'/media-uploader';
    File::ensureDirectoryExists($backupDirectory, 0750, true);
    $fail(! is_dir($backupDirectory), 'Media backup directory could not be created.');
    $fail(! File::copyDirectory($mediaDirectory, $mediaBackup), 'Partial tenant media backup failed.');
}

$tenantId = $tenant->id;
$userId = (int) $request->user_id;
$tenant->domains()->delete();
$tenant->delete();

$fail(Tenant::find($tenantId) !== null, 'Tenant row still exists after deletion.');
$fail((bool) $central->selectOne(
    'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?',
    [$databaseName]
), 'Tenant database still exists after deletion.');

if (is_dir($mediaDirectory)) {
    $fail(! File::deleteDirectory($mediaDirectory), 'Partial tenant media directory could not be removed.');
}

$central->transaction(function () use ($request, $userId): void {
    $locked = StoreOnboardingRequest::query()->whereKey($request->id)->lockForUpdate()->firstOrFail();
    if ($locked->status !== 'failed' || $locked->completed_at) {
        throw new RuntimeException('Onboarding request changed during recovery.');
    }

    $locked->update([
        'tenant_id' => null,
        'status' => 'account_verified',
        'last_error' => null,
        'completed_at' => null,
    ]);

    $hasAnotherTenant = Tenant::query()->where('user_id', $userId)->exists();
    DB::table('users')->where('id', $userId)->update(['has_subdomain' => $hasAnotherTenant]);
});

$request->refresh();
$fail($request->status !== 'account_verified' || $request->tenant_id !== null, 'Onboarding request was not reset.');

echo "RECOVERY_STATUS=success\n";
echo "BACKUP_DATABASE={$backupDatabase}\n";
echo 'MEDIA_BACKUP='.(is_dir($backupDirectory) ? 'created' : 'not_needed')."\n";
echo "REQUEST_STATUS={$request->status}\n";
echo "PARTIAL_TENANT_PRESENT=".(Tenant::find($tenantId) ? 'yes' : 'no')."\n";
