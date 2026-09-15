<?php
/**
 * QA-only control-flow repro for Issue #4 candidate 88a1b136.
 * Executes verbatim method excerpts, not Laravel, routes, mail, or a database.
 * Source: LandlordFrontendController blob ba1b4afbafed30f0bcd5b4ee24b4259137235e7b,
 * verify_user_email(); StoreOnboardingController blob
 * 0044044738d14d0cfa73235cb97fd8245d02b378, maxStep().
 * Auth, session, config, redirect and view are explicit in-memory doubles.
 */
class Auth { public static $user; public static function guard($name) { return new self; } public function user() { return self::$user; } }
$GLOBALS['flag'] = true;
$GLOBALS['requestId'] = 'qa-request';
function get_static_option($name) { return $GLOBALS['flag']; }
function session($key) { return $GLOBALS['requestId']; }
function redirect() { return new class { public function route($name, $args = []) { return ['kind'=>'redirect', 'route'=>$name, 'args'=>$args]; } }; }
function view($name) { return ['kind'=>'view', 'view'=>$name]; }
class StoreOnboardingRequest { public $plan_id=1, $theme_slug='fixture', $store_name='Fixture Store', $subdomain='qa-store', $user_id=7; }
class VerificationExcerpt {
    public function verify_user_email()
    {
        $user = Auth::guard('web')->user();
        if (!$user) {
            return redirect()->route('landlord.user.login');
        }
        if (empty(get_static_option('user_email_verify_status')) || $user->email_verified == 1) {
            return session('store_onboarding_request_id')
                ? redirect()->route('landlord.store.onboarding', ['step' => 5])
                : redirect()->route('landlord.user.home');
        }

        //return view('landlord.frontend.auth.email-verify');
        return view('landlord.frontend.dashboard.email-verify');
    }
}
class StepsExcerpt {
    private function maxStep(?StoreOnboardingRequest $onboarding, $user): int
    {
        if (!$onboarding?->plan_id) return 1;
        if (!$onboarding->theme_slug) return 2;
        if (!$onboarding->store_name || !$onboarding->subdomain) return 3;
        if (!$user || !$user->email_verified || (int) $onboarding->user_id !== (int) $user->id) return 4;
        return 5;
    }
}
$cases = [
    ['guest','on',null,'qa-request','redirect'],
    ['verified onboarding, global verify on','on',1,'qa-request','redirect'],
    ['unverified onboarding, global verify on','on',0,'qa-request','view'],
    ['unverified onboarding, global verify off','',0,'qa-request','view'],
    ['unverified outside onboarding, global verify off','',0,null,'redirect'],
];
$results=[]; $errors=0;
foreach($cases as [$case,$flag,$verified,$id,$expectedKind]) {
    $GLOBALS['flag']=$flag; $GLOBALS['requestId']=$id;
    Auth::$user=$verified === null ? null : (object)['id'=>7,'email_verified'=>$verified];
    $actual=(new VerificationExcerpt)->verify_user_email();
    $trace=[];
    if ($case === 'unverified onboarding, global verify off') {
        for ($i=0;$i<3;$i++) {
            $redirect=(new VerificationExcerpt)->verify_user_email();
            $max=(new ReflectionMethod(StepsExcerpt::class,'maxStep'))->invoke(new StepsExcerpt,new StoreOnboardingRequest,Auth::$user);
            $rendered=max(1,min($redirect['args']['step'],$max));
            $trace[]=['verify_result'=>$redirect,'rendered_step'=>$rendered,'next_ui_action'=>'landlord.user.email.verify'];
        }
    }
    $ok=$actual['kind']===$expectedKind;
    $results[]=['case'=>$case,'result'=>$ok?'PASS':'FAIL','expected_kind'=>$expectedKind,'actual'=>$actual,'loop_trace'=>$trace];
}
$failures=count(array_filter($results, fn($r)=>$r['result']==='FAIL'));
echo json_encode(['candidate'=>'88a1b1368b82406cafccb6b8309938494a847e5e',
    'scope'=>'Verbatim PHP method excerpts with dependency doubles, not HTTP/Laravel/E2E',
    'php'=>PHP_VERSION,'passed'=>count($results)-$failures,'failed'=>$failures,'results'=>$results],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).PHP_EOL;
exit($failures?1:0);
