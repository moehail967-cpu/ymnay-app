<?php
/** Independent B05 extension; real candidate code + guarded MySQL fixture, no actual SMTP/HTTP/browser. */
require_once __DIR__ . '/bootstrap.php';

use App\Http\Controllers\Landlord\Frontend\StoreOnboardingController;
use App\Helpers\EmailHelpers\VerifyUserMailSend;
use App\Models\{PaymentLogs, StoreOnboardingRequest, User};
use Illuminate\Http\Request;
use Illuminate\Session\{ArraySessionHandler, Store};
use Illuminate\Support\Facades\{DB, Mail, Log, Schema};
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

final class SalemB05RetestTest extends TestCase
{
    private mixed $mail;
    private mixed $logger;
    private static array $observations = [];

    protected function setUp(): void
    {
        self::assertSame('1', getenv('YMNAY_TEST_ALLOW_DISPOSABLE_DB'));
        self::assertSame('ymnay_onboarding_test', getenv('YMNAY_TEST_DB'));
        self::assertSame('127.0.0.1', getenv('YMNAY_TEST_HOST'));
        tenancy()->end();
        Schema::disableForeignKeyConstraints();
        foreach (['store_onboarding_requests','tenant_unique_keys','domains','tenants','payment_logs',
            'plan_themes','price_plans','users','static_option_centrals'] as $table) DB::table($table)->truncate();
        Schema::enableForeignKeyConstraints();
        app('session.store')->flush();
        $this->mail = Mail::getFacadeRoot();
        $this->logger = Log::getFacadeRoot();
        FixtureState::$user = null;
    }

    protected function tearDown(): void
    {
        Mail::swap($this->mail);
        Log::swap($this->logger);
        FixtureState::$user = null;
        tenancy()->end();
    }

    private function fixture(?string $token): array
    {
        $user = User::forceCreate(['id'=>901,'name'=>'QA B05 Owner','username'=>'qa-b05',
            'email'=>'b05@example.invalid','email_verified'=>0,'email_verify_token'=>$token]);
        FixtureState::$user = $user;
        $o = StoreOnboardingRequest::create(['id'=>(string) Str::uuid(),'user_id'=>$user->id,
            'theme_slug'=>'theme-a','store_name'=>'QA B05','subdomain'=>'qa-b05','status'=>'draft']);
        $r = Request::create('https://example.invalid/create-store/verify-email','POST');
        $s = new Store('salem-b05',new ArraySessionHandler(60)); $s->start();
        $s->put('store_onboarding_request_id',$o->id); $r->setLaravelSession($s);
        $r->headers->set('referer','https://example.invalid/create-store/verify-email');
        return [new StoreOnboardingController(),$o,$user,$r];
    }

    private function transport(?int $failure): object
    {
        $fake = new class($failure) {
            public int $attempts = 0;
            public function __construct(public ?int $failure) {}
            public function to($email) { return $this; }
            public function send($message) {
                $this->attempts++;
                if ($this->failure !== null) throw new RuntimeException('TEST_ONLY_PRIVATE_TRANSPORT_DETAIL',$this->failure);
            }
        };
        Mail::swap($fake); return $fake;
    }

    private function noCreation(): void
    {
        foreach (['tenants','domains','payment_logs'] as $table) self::assertSame(0,DB::table($table)->count());
        self::assertFalse(tenancy()->initialized);
        self::assertSame(0,DB::connection('central')->transactionLevel());
    }

    public function testInitialFailureIsRetryableAndDoesNotPersistAnUnsentToken(): void
    {
        [$c,$o,$u,$r] = $this->fixture(null);
        $mail = $this->transport(553);
        $before = $o->fresh()->getAttributes();
        $v = $c->verificationForm($r);
        self::assertTrue($v->getData()['verificationMailFailed']);
        self::assertNull($u->fresh()->email_verify_token);
        self::assertSame($before,$o->fresh()->getAttributes());
        self::assertSame($o->id,$r->session()->get('store_onboarding_request_id'));
        self::assertSame(1,$mail->attempts); $this->noCreation();
        self::$observations[] = ['case'=>'initial-send-rejected','failure_visible'=>true,'token_null'=>true,'request_unchanged'=>true];
        $mail->failure = null;
        FixtureState::$user = $u->fresh();
        $success = $c->resendVerificationEmail($r);
        self::assertSame('success',$success->getSession()->get('type'));
        $token = $u->fresh()->email_verify_token;
        self::assertNotEmpty($token);
        $r->merge(['verify_code'=>$token]); $c->verifyEmail($r);
        self::assertSame(1,$u->fresh()->email_verified);
        self::assertSame('account_verified',$o->fresh()->status); $this->noCreation();
    }

    public function testRejectedResendPreservesUsableCodeAndReturnsSafeFeedback(): void
    {
        [$c,$o,$u,$r] = $this->fixture('DeliveredCode');
        $mail = $this->transport(550);
        $before = $o->fresh()->getAttributes();
        $response = $c->resendVerificationEmail($r);
        self::assertSame('danger',$response->getSession()->get('type'));
        self::assertSame('DeliveredCode',$u->fresh()->email_verify_token);
        self::assertSame($before,$o->fresh()->getAttributes());
        self::assertStringNotContainsString('TEST_ONLY_PRIVATE_TRANSPORT_DETAIL',$response->getSession()->get('msg'));
        self::assertSame(1,$mail->attempts);
        self::$observations[] = ['case'=>'resend-rejected','feedback'=>'danger','previous_code_preserved'=>true,'private_details_exposed'=>false];
        $r->merge(['verify_code'=>'DeliveredCode']); $c->verifyEmail($r);
        self::assertSame(1,$u->fresh()->email_verified);
        self::assertNull($u->fresh()->email_verify_token); $this->noCreation();
    }

    public function testLoggerFailureCannotMaskMailFailureOrCommitTheReplacementToken(): void
    {
        [$c,$o,$u,$r] = $this->fixture('DeliveredCode');
        $this->transport(550);
        Log::swap(new class { public function warning(...$args) { throw new RuntimeException('TEST_LOGGER_FAILURE'); } });
        self::assertFalse(VerifyUserMailSend::sendMailForOnboarding($u));
        self::assertSame('DeliveredCode',$u->fresh()->email_verify_token);
        self::assertSame(0,$u->fresh()->email_verified); $this->noCreation();
    }

    public function testFailureNoticeUsesActualBladeBranchWithoutSentClaim(): void
    {
        // Render the actual delivery-notice fragment, not a reimplemented template.
        // Layout/components outside this fragment are not certified by this check.
        $file = dirname(__DIR__,4).'/core/resources/views/landlord/frontend/dashboard/email-verify.blade.php';
        $source = file_get_contents($file);
        self::assertSame('4dc505f420e3c27e41f20fa156033b07dbc7f7fe',sha1('blob '.strlen($source)."\0".$source));
        $start = strpos($source,'<!-- Delivery Alert -->');
        $end = strpos($source,'<x-error-msg-tw/>',$start);
        self::assertNotFalse($start); self::assertNotFalse($end);
        $fragment = str_replace('<x-flash-msg-tw/>','',substr($source,$start,$end-$start));
        $compiled = app('blade.compiler')->compileString($fragment);
        $render = static function(bool $verificationMailFailed) use ($compiled): string {
            ob_start(); try { eval('?>'.$compiled); return ob_get_contents(); } finally { ob_end_clean(); }
        };
        $failure = $render(true); $success = $render(false);
        self::assertStringContainsString('We could not send the verification code. Please try again.',$failure);
        self::assertStringContainsString('role="alert"',$failure);
        self::assertStringNotContainsString('A verification code has been sent to your email address.',$failure);
        self::assertStringContainsString('A verification code has been sent to your email address.',$success);
        self::assertStringNotContainsString('We could not send the verification code.',$success);
    }

    public static function tearDownAfterClass(): void
    {
        file_put_contents((getenv('RUNNER_TEMP') ?: sys_get_temp_dir()).'/salem-b05-observations.json',json_encode([
            'candidate'=>'5663407c3c83ad51210312e80beeb6ba1a6fa1a2',
            'method'=>'Real controller/helper + MySQL fixture; injected transport; actual Blade notice fragment only; not full HTTP/SMTP/browser',
            'observations'=>self::$observations,
        ],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n");
    }
}
