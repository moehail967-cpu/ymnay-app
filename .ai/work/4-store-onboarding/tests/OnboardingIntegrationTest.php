<?php
require_once __DIR__ . '/bootstrap.php';

use App\Http\Controllers\Landlord\Frontend\StoreOnboardingController;
use App\Models\{PaymentLogs, PricePlan, StaticOptionCentral, StoreOnboardingRequest, Tenant, TenantUniqueKey, User};
use App\Services\Onboarding\StoreOnboardingProvisioner;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Session\{ArraySessionHandler, Store};
use Illuminate\Support\Facades\{DB, Schema};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

final class OnboardingIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        tenancy()->end();
        // Only databases created by this guarded fixture are eligible for cleanup.
        foreach (DB::connection('central')->table('tenants')->pluck('id') as $id) {
            $database = 'ymnayqa_' . $id;
            if (!preg_match('/^ymnayqa_qa-[a-f0-9]+$/', $database)) continue;
            DB::connection('central')->statement('DROP DATABASE IF EXISTS `' . $database . '`');
        }
        Schema::disableForeignKeyConstraints();
        foreach (['store_onboarding_requests', 'tenant_unique_keys', 'domains', 'tenants', 'payment_logs',
            'plan_themes', 'price_plans', 'users', 'static_option_centrals'] as $table) DB::table($table)->truncate();
        Schema::enableForeignKeyConstraints();
        FixtureState::$user = null;
        FixtureState::$failDomain = FixtureState::$failSeed = FixtureState::$failMail = false;
        FixtureState::$databaseCalls = FixtureState::$migrationCalls = FixtureState::$seedCalls = FixtureState::$fileCalls = FixtureState::$mailCalls = FixtureState::$verificationMailCalls = 0;
    }

    private function fixture(string $status = 'account_verified', int $userId = 7): array
    {
        $user = User::forceCreate(['id' => $userId, 'name' => 'Fixture Owner', 'username' => 'fixture-owner-' . $userId,
            'email' => 'owner' . $userId . '@example.invalid', 'email_verified' => 1]);
        FixtureState::$user = $user;
        $plan = PricePlan::first() ?? PricePlan::forceCreate(['id' => 1, 'title' => 'Fixture Plan', 'price' => '30',
            'status' => 1, 'type' => 0, 'has_trial' => 1, 'trial_days' => 17]);
        $plan->refresh(); // Match a real selection read, including database timestamp precision.
        $controller = new StoreOnboardingController();
        $snapshot = (new ReflectionMethod($controller, 'planSnapshot'))->invoke($controller, $plan);
        $onboarding = StoreOnboardingRequest::create(['id' => (string) Str::uuid(), 'user_id' => $userId, 'plan_id' => $plan->id,
            'theme_slug' => 'theme-a', 'store_name' => 'Fixture Store ' . $userId, 'subdomain' => 'qa-' . bin2hex(random_bytes(5)),
            'status' => $status, 'plan_snapshot' => $snapshot]);
        return [$controller, $this->request($onboarding), $onboarding, $plan, $user];
    }

    private function request(StoreOnboardingRequest $onboarding, array $data = []): Request
    {
        $request = Request::create('https://example.invalid/create-store/complete', 'POST', $data + ['terms_condition' => '1']);
        $session = new Store('qa-session', new ArraySessionHandler(60));
        $session->start();
        $session->put('store_onboarding_request_id', $onboarding->id);
        $request->setLaravelSession($session);
        return $request;
    }

    private function validation(Closure $action, string $field): void
    {
        try { $action(); self::fail('Expected validation rejection'); }
        catch (ValidationException $e) { self::assertArrayHasKey($field, $e->errors()); }
    }

    private function noSideEffects(): void
    {
        foreach (['tenants', 'domains', 'payment_logs'] as $table) self::assertSame(0, DB::table($table)->count(), $table);
        self::assertSame(0, FixtureState::$databaseCalls);
    }

    public function testFinalSubmissionWithoutStoreDataHasNoSideEffects(): void
    {
        [$c, $r, $o] = $this->fixture();
        $o->update(['store_name' => null, 'subdomain' => null]);
        $this->validation(fn () => $c->complete($r), 'store_name');
        $this->noSideEffects();
        self::assertSame('account_verified', $o->fresh()->status);
    }

    public function testFinalSubmissionRereadsForbiddenAddresses(): void
    {
        [$c, $r, $o] = $this->fixture();
        StaticOptionCentral::create(['option_name' => 'forbidden_subdomains', 'option_value' => strtoupper($o->subdomain)]);
        $this->validation(fn () => $c->complete($r), 'subdomain');
        $this->noSideEffects();
    }

    public function testMalformedSavedDataCannotBypassTheFinalGate(): void
    {
        [$c, $r, $o] = $this->fixture();
        foreach (['a', '-bad', 'bad-', 'bad/address', str_repeat('x', 64), 'admin'] as $value) {
            $o->update(['subdomain' => $value]);
            $this->validation(fn () => $c->complete($r), 'subdomain');
        }
        $o->update(['subdomain' => 'valid-name', 'store_name' => '   ']);
        $this->validation(fn () => $c->complete($r), 'store_name');
        $this->noSideEffects();
    }

    public function testPersistedVerificationIsRecheckedUnderLock(): void
    {
        [$c, $r, $o, $p, $u] = $this->fixture();
        DB::table('users')->where('id', $u->id)->update(['email_verified' => 0]);
        self::assertSame(422, $c->complete($r)->getStatusCode());
        $this->noSideEffects();
    }

    public function testUnverifiedOnboardingCanReachVerificationForEitherGlobalFlagValue(): void
    {
        foreach (['', '1'] as $flag) {
            [$c, $r, $o, $p, $u] = $this->fixture('draft', $flag === '' ? 7 : 8);
            StaticOptionCentral::updateOrCreate(
                ['option_name' => 'user_email_verify_status'],
                ['option_value' => $flag]
            );
            $u->update(['email_verified' => 0, 'email_verify_token' => null]);
            FixtureState::$user = $u->fresh();

            $response = $c->verificationForm($r);

            self::assertSame('landlord.frontend.dashboard.email-verify', $response->name());
            self::assertSame(route('landlord.store.onboarding.email.verify.submit'), $response->getData()['verifyAction']);
            self::assertSame(route('landlord.store.onboarding.email.verify.resend'), $response->getData()['resendUrl']);
            self::assertNotEmpty($u->fresh()->email_verify_token);
        }
        self::assertSame(2, FixtureState::$verificationMailCalls);
    }

    public function testCorrectExistingAccountCodeVerifiesAndReturnsToPreservedRequest(): void
    {
        [$c, $r, $o, $p, $u] = $this->fixture('draft');
        $u->update(['email_verified' => 0, 'email_verify_token' => 'correct-code']);
        FixtureState::$user = $u->fresh();
        $r = $this->request($o, ['verify_code' => 'correct-code']);

        $response = $c->verifyEmail($r);

        self::assertStringContainsString('/create-store?step=5', $response->getTargetUrl());
        self::assertSame(1, $u->fresh()->email_verified);
        self::assertNull($u->fresh()->email_verify_token);
        self::assertSame('account_verified', $o->fresh()->status);
        self::assertSame($o->id, $r->session()->get('store_onboarding_request_id'));
    }

    public function testIncorrectExistingAccountCodeCannotAdvanceOrVerify(): void
    {
        [$c, $r, $o, $p, $u] = $this->fixture('draft');
        $u->update(['email_verified' => 0, 'email_verify_token' => 'correct-code']);
        FixtureState::$user = $u->fresh();
        $r = $this->request($o, ['verify_code' => 'wrong-code']);
        $r->headers->set('referer', 'https://example.invalid/create-store/verify-email');

        $c->verifyEmail($r);

        self::assertSame(0, $u->fresh()->email_verified);
        self::assertSame('correct-code', $u->fresh()->email_verify_token);
        self::assertSame('draft', $o->fresh()->status);
        $this->noSideEffects();
    }

    public function testVerificationResendRotatesCodeWithoutLosingRequest(): void
    {
        [$c, $r, $o, $p, $u] = $this->fixture('draft');
        $u->update(['email_verified' => 0, 'email_verify_token' => 'old-code']);
        FixtureState::$user = $u->fresh();

        $response = $c->resendVerificationEmail($r);

        self::assertSame(route('landlord.store.onboarding.email.verify'), $response->getTargetUrl());
        self::assertNotSame('old-code', $u->fresh()->email_verify_token);
        self::assertSame(1, FixtureState::$verificationMailCalls);
        self::assertSame($o->id, $r->session()->get('store_onboarding_request_id'));
    }

    public function testVerifiedAccountSkipsVerificationAndNoMailIsSent(): void
    {
        [$c, $r] = $this->fixture('account_verified');

        $response = $c->verificationForm($r);

        self::assertStringContainsString('/create-store?step=5', $response->getTargetUrl());
        self::assertSame(0, FixtureState::$verificationMailCalls);
    }

    public function testVerificationRejectsGuestMissingRequestAndForeignRequest(): void
    {
        [$c, $r, $o] = $this->fixture('draft');
        FixtureState::$user = null;
        try { $c->verificationForm($r); self::fail('Guest reached onboarding verification'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { self::assertSame(401, $e->getStatusCode()); }

        FixtureState::$user = User::forceCreate(['id' => 9, 'name' => 'Other Fixture',
            'username' => 'other-verification', 'email' => 'other-verification@example.invalid', 'email_verified' => 0]);
        try { $c->verificationForm($r); self::fail('Foreign request reached onboarding verification'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { self::assertSame(404, $e->getStatusCode()); }

        $sessionless = Request::create('https://example.invalid/create-store/verify-email', 'GET');
        $session = new Store('qa-sessionless', new ArraySessionHandler(60));
        $session->start();
        $sessionless->setLaravelSession($session);
        FixtureState::$user = User::find(9);
        try { $c->verificationForm($sessionless); self::fail('Missing request reached onboarding verification'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { self::assertSame(404, $e->getStatusCode()); }
    }

    public function testProductionRouteAndStepFourUseDedicatedOnboardingVerification(): void
    {
        $root = dirname(__DIR__, 4);
        $routes = file_get_contents($root . '/core/routes/web.php');
        $view = file_get_contents($root . '/core/resources/views/landlord/frontend/onboarding/store-setup.blade.php');

        self::assertStringContainsString("[StoreOnboardingController::class, 'verificationForm']", $routes);
        self::assertStringContainsString("[StoreOnboardingController::class, 'verifyEmail']", $routes);
        self::assertStringContainsString("[StoreOnboardingController::class, 'resendVerificationEmail']", $routes);
        self::assertStringContainsString("middleware(['auth:web', 'throttle:10,1'])", $routes);
        self::assertStringContainsString("route('landlord.store.onboarding.email.verify')", $view);
    }

    public function testChangedPlanRequiresAcknowledgement(): void
    {
        [$c, $r, $o, $p] = $this->fixture();
        $p->update(['price' => '40']);
        $response = $c->complete($r);
        self::assertSame(409, $response->getStatusCode());
        self::assertSame('plan_changed', $response->getData(true)['status']);
        $this->noSideEffects();
    }

    public function testAnotherAccountsClaimedAddressIsRejected(): void
    {
        [$c, $r, $o] = $this->fixture();
        Tenant::withoutEvents(fn () => Tenant::create(['id' => $o->subdomain, 'user_id' => 99, 'theme_slug' => 'theme-a']));
        $this->validation(fn () => $c->complete($r), 'subdomain');
        self::assertSame(1, Tenant::count());
        self::assertSame(0, PaymentLogs::count());
        self::assertSame(0, FixtureState::$databaseCalls);
    }

    public function testTrialOnAnotherStoreStillDeniesCreation(): void
    {
        [$c, $r] = $this->fixture();
        PaymentLogs::create(['user_id' => 7, 'tenant_id' => 'another-store', 'status' => 'trial']);
        self::assertSame(422, $c->complete($r)->getStatusCode());
        self::assertSame(0, Tenant::count());
        self::assertSame(1, PaymentLogs::count());
    }

    public function testAllEarlierStepMutationsPreserveProvisioningAndReadyRequests(): void
    {
        [$c, $r, $o] = $this->fixture();
        $methods = ['selectPlan' => ['plan_id' => 1], 'selectTheme' => ['theme_slug' => 'theme-b'],
            'storeDetails' => ['store_name' => 'Changed', 'subdomain' => 'changed-store'], 'acknowledgePlanChange' => []];
        foreach (['provisioning', 'ready'] as $status) {
            $o->update(['status' => $status]);
            $before = $o->fresh()->getAttributes();
            foreach ($methods as $method => $input) {
                $this->validation(fn () => $c->$method($this->request($o, $input)), 'onboarding');
                self::assertSame($before, $o->fresh()->getAttributes(), $method . '/' . $status);
                self::assertSame(1, StoreOnboardingRequest::count());
            }
        }
    }

    public function testFailedRequestWithoutAnOwnedPartialTenantCanBeCorrected(): void
    {
        [$c, $r, $o] = $this->fixture('failed');
        $c->selectTheme($this->request($o, ['theme_slug' => 'theme-b']));
        self::assertSame('draft', $o->fresh()->status);
        self::assertSame('theme-b', $o->fresh()->theme_slug);
    }

    public function testRepeatedCompletionCreatesOneTenantAndOneTrial(): void
    {
        [$c, $r, $o] = $this->fixture();
        $first = $c->complete($r); $second = $c->complete($r);
        self::assertSame('ready', $first->getData(true)['status'], json_encode($first->getData(true)));
        self::assertSame(200, $second->getStatusCode());
        self::assertSame(1, Tenant::count()); self::assertSame(1, PaymentLogs::count());
        self::assertSame(1, FixtureState::$databaseCalls); self::assertSame(1, FixtureState::$seedCalls);
        self::assertStringContainsString($o->subdomain . '.example.invalid/token-login/', $first->getData(true)['dashboard_url']);
        self::assertFalse(tenancy()->initialized);
    }

    public function testFailureBeforeDomainResumesMissingStagesAndLoginKey(): void
    {
        [$c, $r, $o] = $this->fixture();
        FixtureState::$failDomain = true;
        self::assertSame('failed', $c->complete($r)->getData(true)['status']);
        self::assertSame(1, Tenant::count()); self::assertSame(0, PaymentLogs::count());
        self::assertSame(1, FixtureState::$databaseCalls); self::assertSame(1, FixtureState::$migrationCalls);
        self::assertSame(0, FixtureState::$seedCalls); self::assertFalse(tenancy()->initialized);
        FixtureState::$failDomain = false;
        $response = $c->complete($r);
        self::assertSame('ready', $response->getData(true)['status'], json_encode($response->getData(true)));
        self::assertSame(1, FixtureState::$databaseCalls); self::assertSame(1, FixtureState::$migrationCalls);
        self::assertSame(1, FixtureState::$seedCalls); self::assertSame(1, PaymentLogs::count());
        self::assertSame(1, DB::table('domains')->count()); self::assertSame(1, TenantUniqueKey::count());
        $tenant = Tenant::findOrFail($o->subdomain);
        self::assertNotEmpty($tenant->unique_key);
        self::assertSame($tenant->unique_key, DB::table('tenants')->where('id', $tenant->id)->value('unique_key'));
        self::assertFalse(tenancy()->initialized);
    }

    public function testLegacyUnseededPartialTenantIsSafelyAdopted(): void
    {
        [$c, $r, $o] = $this->fixture('failed');
        $tenant = Tenant::withoutEvents(fn () => Tenant::create([
            'id' => $o->subdomain, 'user_id' => $o->user_id, 'theme_slug' => $o->theme_slug,
        ]));
        app()->call([new \App\Jobs\CreateDatabaseWithFallback($tenant), 'handle']);
        \Illuminate\Support\Facades\Artisan::call('tenants:migrate', ['--tenants' => [$tenant->id]]);
        self::assertNull($tenant->getInternal('onboarding_request_id'));
        $response = $c->complete($r);
        self::assertSame('ready', $response->getData(true)['status'], json_encode($response->getData(true)));
        self::assertSame($o->id, $tenant->fresh()->getInternal('onboarding_request_id'));
        self::assertSame(1, Tenant::count()); self::assertSame(1, PaymentLogs::count());
        self::assertSame(1, FixtureState::$databaseCalls); self::assertSame(1, FixtureState::$migrationCalls);
        self::assertSame(1, FixtureState::$seedCalls);
    }

    public function testAnotherAccountCannotReadTheRequestOrItsLoginLink(): void
    {
        [$c, $r] = $this->fixture();
        FixtureState::$user = User::forceCreate(['id' => 9, 'name' => 'Other Fixture',
            'username' => 'other-fixture', 'email' => 'other@example.invalid', 'email_verified' => 1]);
        try { $c->status($r); self::fail('Foreign request was exposed'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { self::assertSame(404, $e->getStatusCode()); }
        $this->noSideEffects();
    }

    public function testPartialSeedIsNotRepeatedOrFalselyDeclaredReady(): void
    {
        [$c, $r, $o] = $this->fixture();
        FixtureState::$failSeed = true;
        self::assertSame('failed', $c->complete($r)->getData(true)['status']);
        FixtureState::$failSeed = false;
        $response = $c->complete($r)->getData(true);
        self::assertTrue($response['recovery_required']); self::assertSame('failed', $response['status']);
        self::assertSame(1, FixtureState::$seedCalls); self::assertSame(0, PaymentLogs::count());
        Tenant::findOrFail($o->subdomain)->run(fn () => self::assertSame(1, DB::table('admins')->count()));
        self::assertFalse(tenancy()->initialized);
    }

    public function testMissingLoginKeyIsRepairedWithoutRepeatingSeedOrTrial(): void
    {
        [$c, $r, $o] = $this->fixture();
        self::assertSame('ready', $c->complete($r)->getData(true)['status']);
        $expiry = PaymentLogs::first()->getRawOriginal('expire_date');
        $o->update(['status' => 'failed', 'completed_at' => null]);
        DB::table('tenants')->where('id', $o->subdomain)->update(['unique_key' => null, 'data->unique_key' => null]);
        DB::table('tenant_unique_keys')->delete();
        self::assertSame('ready', $c->complete($r)->getData(true)['status']);
        self::assertSame(1, FixtureState::$seedCalls); self::assertSame(1, PaymentLogs::count());
        self::assertSame($expiry, PaymentLogs::first()->getRawOriginal('expire_date'));
        self::assertNotEmpty(Tenant::findOrFail($o->subdomain)->unique_key);
    }

    public function testWelcomeMailFailureDoesNotFailAReadyStore(): void
    {
        [$c, $r] = $this->fixture(); FixtureState::$failMail = true;
        self::assertSame('ready', $c->complete($r)->getData(true)['status']);
        self::assertSame('ready', $c->complete($r)->getData(true)['status']);
        self::assertSame(1, FixtureState::$mailCalls); self::assertSame(1, PaymentLogs::count());
    }

    public function testTwoTenantTitlesAndCentralContextRemainSeparate(): void
    {
        [$c1, $r1, $o1] = $this->fixture();
        self::assertSame('ready', $c1->complete($r1)->getData(true)['status']);
        [$c2, $r2, $o2] = $this->fixture('account_verified', 9);
        self::assertSame('ready', $c2->complete($r2)->getData(true)['status']);
        foreach ([$o1, $o2] as $o) Tenant::findOrFail($o->subdomain)->run(function () use ($o) {
            self::assertSame($o->store_name, DB::table('static_options')->where('option_name', 'site_title')->value('option_value'));
            self::assertFalse(Schema::hasTable('store_onboarding_requests'));
        });
        self::assertSame('central', DB::getDefaultConnection());
        self::assertSame(2, StoreOnboardingRequest::count());
    }

    public function testCentralMigrationUpDownAndForeignKeys(): void
    {
        [, , $o, $p, $u] = $this->fixture();
        DB::table('users')->where('id', $u->id)->delete();
        DB::table('price_plans')->where('id', $p->id)->delete();
        self::assertNull($o->fresh()->user_id); self::assertNull($o->fresh()->plan_id);
        $migration = require dirname(__DIR__, 4) . '/core/database/migrations/2026_09_14_000001_create_store_onboarding_requests_table.php';
        $migration->down(); self::assertFalse(Schema::hasTable('store_onboarding_requests'));
        $migration->up(); self::assertTrue(Schema::hasTable('store_onboarding_requests'));
    }

    public function testCheckpointPreservesUnrelatedDataAndPhysicalLoginKey(): void
    {
        [$c, $r, $o] = $this->fixture();
        self::assertSame('ready', $c->complete($r)->getData(true)['status']);
        $tenant = Tenant::findOrFail($o->subdomain); $key = $tenant->unique_key;
        DB::table('tenants')->where('id', $tenant->id)->update(['data->unrelated_fixture' => 'preserve-me']);
        (new ReflectionMethod(StoreOnboardingProvisioner::class, 'writeTenantData'))->invoke(
            app(StoreOnboardingProvisioner::class), $tenant, ['tenancy_onboarding_probe' => true]
        );
        self::assertSame('preserve-me', $tenant->unrelated_fixture);
        self::assertSame($key, $tenant->unique_key);
        self::assertSame($key, DB::table('tenants')->where('id', $tenant->id)->value('unique_key'));
    }

    public function testNewReservationBlocksRetryBeforeFurtherSideEffects(): void
    {
        [$c, $r, $o] = $this->fixture(); FixtureState::$failDomain = true;
        self::assertSame('failed', $c->complete($r)->getData(true)['status']);
        FixtureState::$failDomain = false;
        StaticOptionCentral::create(['option_name' => 'forbidden_subdomains', 'option_value' => $o->subdomain]);
        $this->validation(fn () => $c->complete($r), 'subdomain');
        self::assertSame(0, FixtureState::$seedCalls); self::assertSame(0, PaymentLogs::count());
        self::assertSame(1, FixtureState::$databaseCalls);
    }

    public function testStaleTabWaitsForRealMysqlLockThenCannotOverwriteProvisioning(): void
    {
        if (!function_exists('pcntl_fork')) self::markTestSkipped('pcntl is required for the real parallel request test');
        [$c, $r, $o] = $this->fixture();
        $dir = sys_get_temp_dir() . '/ymnay-lock-' . bin2hex(random_bytes(8)); mkdir($dir);
        DB::purge('central'); // Never share an inherited PDO socket between processes.
        $pid = pcntl_fork(); self::assertNotSame(-1, $pid);
        if ($pid === 0) {
            try {
                $paused = false;
                DB::listen(function (QueryExecuted $query) use (&$paused, $dir) {
                    if (!$paused && str_starts_with(strtolower($query->sql), 'select')
                        && str_contains($query->sql, 'store_onboarding_requests') && !str_contains($query->sql, 'for update')) {
                        $paused = true; file_put_contents($dir . '/read', 'draft');
                        self::waitFor($dir . '/release');
                    }
                });
                DB::connection('central')->beforeExecuting(function ($sql) use ($dir) {
                    if (str_contains($sql, 'for update')) file_put_contents($dir . '/lock-attempt', '1');
                });
                try { $c->selectTheme($this->request($o, ['theme_slug' => 'theme-b'])); $status = 200; }
                catch (ValidationException $e) { $status = 422; }
                file_put_contents($dir . '/done', (string) $status); exit(0);
            } catch (Throwable $e) { file_put_contents($dir . '/done', 'ERROR:' . $e->getMessage()); exit(1); }
        }
        try {
            self::waitFor($dir . '/read');
            DB::beginTransaction();
            StoreOnboardingRequest::whereKey($o->id)->lockForUpdate()->firstOrFail()->update(['status' => 'provisioning']);
            file_put_contents($dir . '/release', '1');
            self::waitFor($dir . '/lock-attempt'); usleep(150000);
            $blocked = !file_exists($dir . '/done');
            DB::commit(); self::waitFor($dir . '/done'); pcntl_waitpid($pid, $waitStatus);
            self::assertTrue($blocked, 'Writer did not wait on the real row lock');
            self::assertSame('422', file_get_contents($dir . '/done'));
            self::assertSame('provisioning', $o->fresh()->status); self::assertSame('theme-a', $o->fresh()->theme_slug);
        } finally {
            while (DB::transactionLevel() > 0) DB::rollBack();
            if (!file_exists($dir . '/done')) { posix_kill($pid, SIGTERM); pcntl_waitpid($pid, $waitStatus); }
            foreach (glob($dir . '/*') as $file) unlink($file); rmdir($dir);
        }
    }

    private static function waitFor(string $path): void
    {
        $deadline = microtime(true) + 10;
        while (!file_exists($path)) { if (microtime(true) > $deadline) throw new RuntimeException('Fixture barrier timed out'); usleep(10000); }
    }
}
