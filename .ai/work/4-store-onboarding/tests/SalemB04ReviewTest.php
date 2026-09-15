<?php
/**
 * Salem independent B04/adjacent mail-failure regression.
 * Actual candidate controller/helper + existing isolated Laravel/MySQL fixture.
 * Auth, tenant content and mail transport remain explicit fixture adapters.
 * Does not boot the complete application or contact SMTP/Production.
 */
require_once __DIR__ . '/bootstrap.php';

use App\Http\Controllers\Landlord\Frontend\StoreOnboardingController;
use App\Models\{PaymentLogs, PricePlan, StaticOptionCentral, StoreOnboardingRequest, User};
use Illuminate\Http\Request;
use Illuminate\Session\{ArraySessionHandler, Store};
use Illuminate\Support\Facades\{DB, Mail, Schema};
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

final class SalemB04ReviewTest extends TestCase
{
    private mixed $previousMail;
    private static array $evidence = [];

    public static function setUpBeforeClass(): void
    {
        $root = dirname(__DIR__, 4);
        foreach ([
            'core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php' => 'b4394b4f079dbf4886c0d013c516e0d642e4a914',
            'core/app/Helpers/EmailHelpers/VerifyUserMailSend.php' => '138e3f605d53bd2c810addf27a3a0bfa7ea16c79',
        ] as $path => $expected) {
            $bytes = file_get_contents($root . '/' . $path);
            self::assertSame($expected, sha1('blob ' . strlen($bytes) . "\0" . $bytes), 'Wrong candidate: ' . $path);
        }
    }

    protected function setUp(): void
    {
        // Same disposal boundary as the engineer fixture. Never use an application .env.
        self::assertSame('1', getenv('YMNAY_TEST_ALLOW_DISPOSABLE_DB'));
        self::assertSame('ymnay_onboarding_test', getenv('YMNAY_TEST_DB'));
        self::assertSame('127.0.0.1', getenv('YMNAY_TEST_HOST'));
        tenancy()->end();
        Schema::disableForeignKeyConstraints();
        foreach (['store_onboarding_requests', 'tenant_unique_keys', 'domains', 'tenants', 'payment_logs',
            'plan_themes', 'price_plans', 'users', 'static_option_centrals'] as $table) DB::table($table)->truncate();
        Schema::enableForeignKeyConstraints();
        FixtureState::$user = null;
        $this->previousMail = Mail::getFacadeRoot();
        app('session.store')->flush();
        Mail::swap(new class {
            public function to($email) { return $this; }
            public function send($message) {}
        });
    }

    protected function tearDown(): void
    {
        Mail::swap($this->previousMail);
        FixtureState::$user = null;
        tenancy()->end();
    }

    private function fixture(): array
    {
        $user = User::forceCreate(['id' => 71, 'name' => 'QA Owner', 'username' => 'qa-b04-owner',
            'email' => 'qa-b04@example.invalid', 'email_verified' => 0, 'email_verify_token' => 'FixtureA1']);
        FixtureState::$user = $user;
        $plan = PricePlan::forceCreate(['id' => 1, 'title' => 'QA Plan', 'price' => '30',
            'status' => 1, 'type' => 0, 'has_trial' => 1, 'trial_days' => 17]);
        $controller = new StoreOnboardingController();
        $snapshot = (new ReflectionMethod($controller, 'planSnapshot'))->invoke($controller, $plan->fresh());
        $onboarding = StoreOnboardingRequest::create(['id' => (string) Str::uuid(), 'user_id' => $user->id,
            'plan_id' => $plan->id, 'theme_slug' => 'theme-a', 'store_name' => 'QA Store',
            'subdomain' => 'qa-' . bin2hex(random_bytes(5)), 'status' => 'draft', 'plan_snapshot' => $snapshot]);
        return [$controller, $onboarding, $user];
    }

    private function request(StoreOnboardingRequest $onboarding, array $data = []): Request
    {
        $request = Request::create('https://example.invalid/create-store/verify-email', 'POST', $data);
        $session = new Store('salem-b04-session', new ArraySessionHandler(60));
        $session->start();
        $session->put('store_onboarding_request_id', $onboarding->id);
        $request->setLaravelSession($session);
        $request->headers->set('referer', 'https://example.invalid/create-store/verify-email');
        return $request;
    }

    private function noCreation(): void
    {
        self::assertSame(0, DB::table('tenants')->count());
        self::assertSame(0, DB::table('domains')->count());
        self::assertSame(0, PaymentLogs::count());
    }

    public function testDisabledGeneralFlagStillShowsDedicatedVerification(): void
    {
        [$c, $o, $u] = $this->fixture();
        StaticOptionCentral::create(['option_name' => 'user_email_verify_status', 'option_value' => '']);
        $response = $c->verificationForm($this->request($o));
        self::assertSame('landlord.frontend.dashboard.email-verify', $response->name());
        self::assertSame(route('landlord.store.onboarding.email.verify.submit'), $response->getData()['verifyAction']);
        self::assertSame(4, (new ReflectionMethod($c, 'maxStep'))->invoke($c, $o, $u));
        self::assertSame(0, $u->fresh()->email_verified);
        $this->noCreation();
    }

    public function testSuccessfulCodePreservesEverySelectionWithoutCreatingStore(): void
    {
        [$c, $o, $u] = $this->fixture();
        // Compare two database reads: MySQL may canonicalize JSON object-key ordering.
        $before = $o->fresh()->only(['id', 'user_id', 'plan_id', 'theme_slug', 'store_name', 'subdomain', 'plan_snapshot']);
        $r = $this->request($o, ['verify_code' => 'FixtureA1']);
        $response = $c->verifyEmail($r);
        self::assertStringContainsString('/create-store?step=5', $response->getTargetUrl());
        self::assertSame($before, $o->fresh()->only(array_keys($before)));
        self::assertSame('account_verified', $o->fresh()->status);
        self::assertSame(1, $u->fresh()->email_verified);
        self::assertNull($u->fresh()->email_verify_token);
        self::assertSame($o->id, $r->session()->get('store_onboarding_request_id'));
        $this->noCreation();
    }

    public function testWrongCodeLeavesUnverifiedAccountAndRequestUnchanged(): void
    {
        [$c, $o, $u] = $this->fixture();
        $c->verifyEmail($this->request($o, ['verify_code' => 'incorrect-fixture-code']));
        self::assertSame(0, $u->fresh()->email_verified);
        self::assertSame('FixtureA1', $u->fresh()->email_verify_token);
        self::assertSame('draft', $o->fresh()->status);
        $this->noCreation();
    }

    public function testProvisioningRequestCannotBeAlteredByVerification(): void
    {
        [$c, $o, $u] = $this->fixture();
        $o->update(['status' => 'provisioning']);
        try { $c->verifyEmail($this->request($o, ['verify_code' => 'FixtureA1'])); self::fail('Expected 409'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { self::assertSame(409, $e->getStatusCode()); }
        self::assertSame(0, $u->fresh()->email_verified);
        self::assertSame('provisioning', $o->fresh()->status);
        $this->noCreation();
    }

    public function testResendInvalidatesOldCodeAndNewCodeWorks(): void
    {
        [$c, $o, $u] = $this->fixture();
        $c->resendVerificationEmail($this->request($o));
        $newToken = $u->fresh()->email_verify_token;
        self::assertNotSame('FixtureA1', $newToken);
        $c->verifyEmail($this->request($o, ['verify_code' => 'FixtureA1']));
        self::assertSame(0, $u->fresh()->email_verified);
        $c->verifyEmail($this->request($o, ['verify_code' => $newToken]));
        self::assertSame(1, $u->fresh()->email_verified);
        self::assertNull($u->fresh()->email_verify_token);
        $this->noCreation();
    }

    public function testUnverifiedAccountStillCannotCompleteAfterWrongCode(): void
    {
        [$c, $o] = $this->fixture();
        $c->verifyEmail($this->request($o, ['verify_code' => 'incorrect-fixture-code']));
        $response = $c->complete($this->request($o, ['terms_condition' => '1']));
        self::assertSame(422, $response->getStatusCode());
        self::assertSame('draft', $o->fresh()->status);
        $this->noCreation();
    }

    private function assertMailFailureIsNotReportedAsSuccess(int $transportCode): void
    {
        [$c, $o, $u] = $this->fixture();
        $transport = new class($transportCode) {
            public int $attempts = 0;
            public function __construct(private int $code) {}
            public function to($email) { return $this; }
            public function send($message) {
                $this->attempts++;
                throw new \RuntimeException('Injected test-only mail transport rejection', $this->code);
            }
        };
        Mail::swap($transport);
        $r = $this->request($o);
        $response = $c->resendVerificationEmail($r);
        $feedback = $response->getSession()->get('type');
        $message = $response->getSession()->get('msg');
        $form = $c->verificationForm($r);
        $observed = [
            'transport_exception_code' => $transportCode,
            'attempted_sends' => $transport->attempts,
            'flash_type' => $feedback,
            'flash_message' => $message,
            'token_rotated_despite_failed_send' => $u->fresh()->email_verify_token !== 'FixtureA1',
            'next_form' => $form->name(),
            'email_verified' => $u->fresh()->email_verified,
            'request_status' => $o->fresh()->status,
            'request_preserved' => $r->session()->get('store_onboarding_request_id') === $o->id,
        ];
        self::$evidence[] = $observed;
        self::assertSame(1, $transport->attempts);
        self::assertSame(0, $u->fresh()->email_verified);
        $this->noCreation();
        // Requirement: mail-send failures are visible, not represented as delivery success.
        self::assertNotSame('success', $feedback, json_encode($observed, JSON_UNESCAPED_SLASHES));
    }

    public function testTransport550FailureMustNotShowSuccess(): void
    {
        $this->assertMailFailureIsNotReportedAsSuccess(550);
    }

    public function testTransport553FailureMustNotShowSuccess(): void
    {
        $this->assertMailFailureIsNotReportedAsSuccess(553);
    }

    public static function tearDownAfterClass(): void
    {
        $dir = getenv('RUNNER_TEMP') ?: sys_get_temp_dir();
        file_put_contents($dir . '/salem-b04-observations.json', json_encode([
            'candidate' => '5a7013b04c70fbe2ce474f53b491326b2aca89b1',
            'method' => 'Current application code + Laravel/MySQL fixture, simulated transport exception; not SMTP or browser E2E',
            'observations' => self::$evidence,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    }
}
