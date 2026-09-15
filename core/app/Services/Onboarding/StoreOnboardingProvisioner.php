<?php

namespace App\Services\Onboarding;

use App\Helpers\GenerateTenantToken;
use App\Jobs\CreateDatabaseWithFallback;
use App\Jobs\NewShopCreatedEmailNotificationJob;
use App\Jobs\TenantCacheClearJob;
use App\Jobs\TenantFileSycnForNewTenant;
use App\Jobs\TenantSeedDatabaseJob;
use App\Models\Admin;
use App\Models\PaymentLogs;
use App\Models\StaticOptionCentral;
use App\Models\StoreOnboardingRequest;
use App\Models\Tenant;
use App\Models\TenantUniqueKey;
use Closure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/** Only W10 opt-in tenants use these checkpoints; the native paid/legacy pipeline is unchanged. */
class StoreOnboardingProvisioner
{
    private const REQUIRED_STAGES = ['database', 'migrations', 'domain', 'seed', 'login_key', 'store_title', 'file_dispatch'];

    public function resume(Tenant $tenant, StoreOnboardingRequest $request): void
    {
        if (!$tenant->getInternal('onboarding_request_id')) {
            // Old candidates had no checkpoints. Only a provably unseeded owned tenant can be adopted.
            if (!$this->canAdoptLegacyPartial($tenant, $request)) throw new RecoveryRequired('legacy_seed_state');
            $tenant->getConnection()->transaction(function () use ($tenant, $request) {
                $locked = Tenant::whereKey($tenant->id)->lockForUpdate()->firstOrFail();
                if ($locked->getInternal('onboarding_request_id')) throw new RecoveryRequired('legacy_ownership');
                $this->writeTenantData($locked, [
                    'tenancy_onboarding_request_id' => $request->id,
                    'tenancy_onboarding_stages' => [],
                ]);
            });
            $tenant->refresh();
        }
        $this->assertOwnership($tenant, $request);
        if (tenancy()->initialized) throw new RuntimeException('Onboarding must start in central context.');
        $request->update(['tenant_id' => $tenant->id]);
        // Native listener also writes these physical central columns. Preserve that contract.
        $this->writeTenantData($tenant, [], [
            'user_id' => $request->user_id, 'theme_slug' => $request->theme_slug,
        ]);
        $stages = new ProvisioningStages((array) $tenant->getInternal('onboarding_stages'), function (array $states) use ($tenant) {
            $this->writeTenantData($tenant, ['tenancy_onboarding_stages' => $states]);
        });

        $cpanel = (bool) StaticOptionCentral::where('option_name', '_cpanel_automation_status')->value('option_value');
        // Local DB creation can be reconciled by existence. An uncertain cPanel user/grant step cannot.
        $stages->run('database', fn () => $this->job(new CreateDatabaseWithFallback($tenant)), !$cpanel,
            fn () => $this->databaseReady($tenant));
        $stages->run('migrations', function () use ($tenant) {
            $code = $this->centralCommand('tenants:migrate', $tenant);
            if ($code !== 0) throw new RuntimeException('Tenant migrations did not finish successfully.');
        }, false, fn () => $this->migrationsReady($tenant));

        $this->job(new TenantCacheClearJob());
        $stages->run('domain', function () use ($tenant) {
            $tenant->domains()->firstOrCreate(['domain' => $this->domainName($tenant)]);
        }, true, fn () => $tenant->domains()->where('domain', $this->domainName($tenant))->exists());

        // Seeds/importers can insert duplicates or replace content. Never rerun an uncertain seed.
        $stages->run('seed', function () use ($tenant) {
            $oldStrict = config('ymnay.onboarding_strict_seed', false);
            $oldDebug = config('app.debug');
            try {
                config(['ymnay.onboarding_strict_seed' => true]);
                $this->job(new TenantSeedDatabaseJob($tenant));
                if (!$this->adminReady($tenant)) throw new RuntimeException('Tenant administrator is not ready.');
            } finally {
                config(['ymnay.onboarding_strict_seed' => $oldStrict, 'app.debug' => $oldDebug]);
            }
        });

        $stages->run('login_key', fn () => $this->ensureLoginKey($tenant), true, fn () => $this->loginReady($tenant));
        $stages->run('store_title', function () use ($tenant, $request) {
            $this->inTenant($tenant, fn () => update_static_option('site_title', $request->store_name));
        }, true, fn () => $this->titleReady($tenant, $request));
        // Preserve the existing asynchronous file-copy contract, not a claim that its queue is drained.
        $stages->run('file_dispatch', fn () => $this->job(new TenantFileSycnForNewTenant($tenant)));
        $this->assertReady($tenant, $request);
    }

    public function assertReady(Tenant $tenant, StoreOnboardingRequest $request): void
    {
        $tenant->refresh();
        $this->assertOwnership($tenant, $request);
        $states = (array) $tenant->getInternal('onboarding_stages');
        foreach (self::REQUIRED_STAGES as $stage) {
            if (($states[$stage] ?? null) !== 'done') throw new RecoveryRequired($stage);
        }
        if (!$this->databaseReady($tenant) || !$this->migrationsReady($tenant)
            || !$this->adminReady($tenant) || !$this->loginReady($tenant)
            || !$this->titleReady($tenant, $request)
            || !$tenant->domains()->where('domain', $this->domainName($tenant))->exists()) {
            throw new RuntimeException('Tenant readiness verification failed.');
        }
    }

    public function notifyReady(Tenant $tenant): void
    {
        // Welcome mail is not provisioning. Never fail or recreate a ready store because of mail.
        try {
            $tenant->refresh();
            if ($tenant->getInternal('onboarding_mail_attempted')) return;
            $this->writeTenantData($tenant, ['tenancy_onboarding_mail_attempted' => true]);
            $this->job(new NewShopCreatedEmailNotificationJob($tenant));
        } catch (Throwable $exception) {
            Log::warning('Onboarding welcome mail was not sent', ['tenant_id' => $tenant->id]);
        }
    }

    public function canAdoptLegacyPartial(Tenant $tenant, StoreOnboardingRequest $request): bool
    {
        if ($tenant->getInternal('onboarding_request_id')
            || !in_array($request->status, ['failed', 'provisioning'], true)
            || $request->completed_at || $tenant->id !== $request->subdomain
            || (int) $tenant->user_id !== (int) $request->user_id
            || $tenant->theme_slug !== $request->theme_slug
            || ($request->tenant_id && $request->tenant_id !== $tenant->id)
            || !$tenant->created_at || !$request->created_at || $tenant->created_at->lt($request->created_at)
            || StoreOnboardingRequest::where('id', '!=', $request->id)
                ->where('tenant_id', $tenant->id)->exists()
            || PaymentLogs::where('tenant_id', $tenant->id)->where('status', '!=', 'trial')->exists()) {
            return false;
        }
        if (!$tenant->database()->manager()->databaseExists($tenant->database()->getName())) return true;
        return $this->inTenant($tenant, function () {
            $connection = DB::connection('tenant');
            // Do not infer that a missing admin means all seeds failed. Inspect every application table.
            foreach ($connection->getSchemaBuilder()->getTables() as $table) {
                if (in_array($table['name'], ['migrations', 'sqlite_sequence'], true)) continue;
                if ($connection->table($table['name'])->exists()) return false;
            }
            return true;
        });
    }

    private function assertOwnership(Tenant $tenant, StoreOnboardingRequest $request): void
    {
        if ($tenant->getInternal('onboarding_request_id') !== $request->id
            || $tenant->id !== $request->subdomain
            || (int) $tenant->user_id !== (int) $request->user_id
            || $tenant->theme_slug !== $request->theme_slug
            || ($request->tenant_id && $request->tenant_id !== $tenant->id)
            || !in_array($request->status, ['provisioning', 'ready'], true)) {
            throw new RuntimeException('Tenant does not belong to this onboarding request.');
        }
    }

    private function domainName(Tenant $tenant): string
    {
        $domain = trim((string) env('CENTRAL_DOMAIN'));
        if ($domain === '' || str_contains($domain, '/')) throw new RuntimeException('Central domain is not configured.');
        return $tenant->id . '.' . $domain;
    }

    private function databaseReady(Tenant $tenant): bool
    {
        $tenant->refresh();
        if (!$tenant->database()->manager()->databaseExists($tenant->database()->getName())) return false;
        return $this->inTenant($tenant, function () {
            DB::connection('tenant')->getPdo();
            return true;
        });
    }

    private function migrationsReady(Tenant $tenant): bool
    {
        return $this->inTenant($tenant, function () {
            $connection = DB::connection('tenant');
            if (!$connection->getSchemaBuilder()->hasTable('migrations')) return false;
            $paths = config('tenancy.migration_parameters.--path', []);
            if (!$paths) throw new RuntimeException('Tenant migration paths are not configured.');
            $files = app('migrator')->getMigrationFiles($paths);
            if (!$files) throw new RuntimeException('No tenant migration files were found.');
            $ran = $connection->table('migrations')->pluck('migration')->all();
            return count(array_diff(array_keys($files), $ran)) === 0;
        });
    }

    private function adminReady(Tenant $tenant): bool
    {
        return $this->inTenant($tenant, function () {
            if (!DB::connection('tenant')->getSchemaBuilder()->hasTable('admins')) return false;
            $admin = Admin::on('tenant')->first();
            return $admin && $admin->hasRole('Super Admin');
        });
    }

    private function titleReady(Tenant $tenant, StoreOnboardingRequest $request): bool
    {
        return $this->inTenant($tenant, fn () => DB::connection('tenant')->table('static_options')
            ->where('option_name', 'site_title')->value('option_value') === $request->store_name);
    }

    private function loginReady(Tenant $tenant): bool
    {
        $tenant->refresh();
        return !empty($tenant->unique_key) && TenantUniqueKey::on($tenant->getConnectionName())
            ->where('tenant_id', $tenant->id)->where('unique_key', $tenant->unique_key)->exists();
    }

    private function ensureLoginKey(Tenant $tenant): void
    {
        $tenant->getConnection()->transaction(function () use ($tenant) {
            $locked = Tenant::whereKey($tenant->id)->lockForUpdate()->firstOrFail();
            $physicalKey = $tenant->getConnection()->table('tenants')->where('id', $tenant->id)->value('unique_key');
            $key = $physicalKey ?: ($locked->unique_key ?: GenerateTenantToken::token());
            $this->writeTenantData($locked, [], ['unique_key' => $key]);
            TenantUniqueKey::on($tenant->getConnectionName())->updateOrCreate(['tenant_id' => $tenant->id], ['unique_key' => $key]);
        });
        $tenant->refresh();
    }

    public function synchronizeTrial(Tenant $tenant, PaymentLogs $trial): void
    {
        // Stancl virtual attributes can shadow physical columns written by the native trial action.
        $this->writeTenantData($tenant, [], [
            'user_id' => $trial->user_id,
            'theme_slug' => $trial->theme_slug,
            'start_date' => $trial->start_date,
            'expire_date' => $trial->getRawOriginal('expire_date'),
            'renewal_payment_log_id' => $trial->id,
        ]);
    }

    private function writeTenantData(Tenant $tenant, array $metadata, array $columns = []): void
    {
        // Do not save a refreshed virtual-column model just to checkpoint: it can copy stale
        // physical-column values into JSON and later mask a repaired key or trial expiry.
        $connection = $tenant->getConnection();
        $connection->transaction(function () use ($tenant, $metadata, $columns, $connection) {
            $row = $connection->table('tenants')->where('id', $tenant->id)->lockForUpdate()->first();
            if (!$row) throw new RuntimeException('Onboarding tenant no longer exists.');
            $data = json_decode($row->data ?? '{}', true, 512, JSON_THROW_ON_ERROR) ?? [];
            foreach ($columns as $key => $value) {
                if (!in_array($key, Tenant::getCustomColumns(), true)) $data[$key] = $value;
            }
            $data = array_replace($data, $metadata);
            $connection->table('tenants')->where('id', $tenant->id)->update(
                $columns + ['data' => json_encode($data, JSON_THROW_ON_ERROR)]
            );
        });
        $tenant->refresh();
    }

    private function inTenant(Tenant $tenant, Closure $operation): mixed
    {
        try {
            tenancy()->initialize($tenant);
            return $operation();
        } finally {
            tenancy()->end();
        }
    }

    private function job(object $job): void
    {
        try {
            app()->call([$job, 'handle']);
        } finally {
            // The legacy jobs can leave tenant context active when an exception interrupts a command.
            tenancy()->end();
        }
    }

    private function centralCommand(string $command, Tenant $tenant): int
    {
        try {
            return Artisan::call($command, ['--tenants' => [$tenant->id], '--force' => true]);
        } finally {
            tenancy()->end();
        }
    }
}
