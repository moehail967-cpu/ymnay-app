<?php

namespace Tests\Feature;

use App\Mail\AdminResetEmail;
use App\Mail\BasicMail;
use App\Models\PricePlan;
use App\Models\StoreOnboardingRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class G01OnboardingHttpTest extends TestCase
{
    public function test_onboarding_entry_renders_through_the_full_http_stack(): void
    {
        $response = $this->get(route('landlord.store.onboarding'))
            ->assertOk()
            ->assertSee('name="plan_id"', false);

        $plans = PricePlan::query()->where('status', 1)->orderBy('id')->get();
        $this->assertGreaterThanOrEqual(3, $plans->count());
        foreach ($plans as $plan) {
            foreach (['product_permission_feature', 'page_permission_feature', 'blog_permission_feature'] as $limit) {
                $response->assertSee((int) $plan->{$limit} === -1 ? 'غير محدود' : (string) $plan->{$limit});
            }
        }
        $response->assertSee('ر.س');
    }

    public function test_existing_unverified_account_uses_onboarding_verification_routes(): void
    {
        Mail::fake();
        $user = $this->user('existing-unverified', false);
        $onboarding = $this->onboarding($user);

        $this->actingAs($user, 'web')
            ->withSession(['store_onboarding_request_id' => $onboarding->id])
            ->get(route('landlord.store.onboarding.email.verify'))
            ->assertOk()
            ->assertSee('name="verify_code"', false);

        $token = (string) $user->fresh()->email_verify_token;
        $this->assertNotSame('', $token);
        Mail::assertSent(BasicMail::class, fn (BasicMail $mail) => $mail->hasTo($user->email));

        $this->actingAs($user, 'web')
            ->withSession(['store_onboarding_request_id' => $onboarding->id])
            ->post(route('landlord.store.onboarding.email.verify.submit'), ['verify_code' => 'wrong-code'])
            ->assertRedirect();
        $this->assertSame(0, (int) $user->fresh()->email_verified);

        $this->actingAs($user, 'web')
            ->withSession(['store_onboarding_request_id' => $onboarding->id])
            ->post(route('landlord.store.onboarding.email.verify.submit'), ['verify_code' => $token])
            ->assertRedirect(route('landlord.store.onboarding', ['step' => 5]));

        $this->assertSame(1, (int) $user->fresh()->email_verified);
        $this->assertNull($user->fresh()->email_verify_token);
        $this->assertSame('account_verified', $onboarding->fresh()->status);
    }

    public function test_expired_registration_otp_is_rejected_and_removed_from_session(): void
    {
        $pending = [
            'name' => 'Expired G01 User',
            'email' => 'expired-'.Str::uuid().'@example.test',
            'phone' => '966500000001',
            'username' => 'expired_'.Str::lower(Str::random(8)),
            'password' => Hash::make('G01-Isolated-Password!'),
            'country' => '',
            'city' => '',
            'otp' => '123456',
            'expires_at' => now()->subSecond()->timestamp,
            'attempts' => 0,
            'last_sent' => now()->subMinute()->timestamp,
        ];

        $this->withSession(['pending_registration' => $pending])
            ->postJson(route('landlord.user.register.otp.verify'), ['otp' => '123456'])
            ->assertOk()
            ->assertJson(['status' => 'expired'])
            ->assertSessionMissing('pending_registration');
    }

    public function test_complete_requires_authentication_and_request_ownership(): void
    {
        $this->postJson(route('landlord.store.onboarding.complete'), ['terms_condition' => true])
            ->assertUnauthorized();

        $owner = $this->user('owner', true);
        $other = $this->user('other', true);
        $onboarding = $this->onboarding($owner);

        $this->actingAs($other, 'web')
            ->withSession(['store_onboarding_request_id' => $onboarding->id])
            ->postJson(route('landlord.store.onboarding.complete'), ['terms_condition' => true])
            ->assertNotFound();
    }

    public function test_repeated_password_recovery_replaces_and_consumes_tokens_without_losing_onboarding(): void
    {
        Mail::fake();
        $user = $this->user('password-recovery', true);
        $plan = PricePlan::query()->where('status', 1)->firstOrFail();
        $onboarding = StoreOnboardingRequest::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'theme_slug' => 'hexfashion',
            'store_name' => 'G01 Password Recovery Store',
            'subdomain' => 'g01-recovery-'.Str::lower(Str::random(8)),
            'status' => 'account_verified',
        ]);
        $preservedChoices = $onboarding->only([
            'plan_id', 'theme_slug', 'store_name', 'subdomain', 'status',
        ]);

        $this->post(route('landlord.user.forget.password'), ['username' => $user->email])
            ->assertRedirect();
        $firstToken = (string) DB::table('password_resets')->where('email', $user->email)->value('token');
        $this->assertNotSame('', $firstToken);

        $this->post(route('landlord.user.forget.password'), ['username' => $user->email])
            ->assertRedirect();
        $secondToken = (string) DB::table('password_resets')->where('email', $user->email)->value('token');
        $this->assertNotSame($firstToken, $secondToken);
        $this->assertSame(1, DB::table('password_resets')->where('email', $user->email)->count());

        $this->post(route('landlord.user.reset.password.change'), [
            'token' => $firstToken,
            'username' => $user->username,
            'password' => 'G01-Stale-Recovery!',
            'password_confirmation' => 'G01-Stale-Recovery!',
        ])->assertRedirect();
        $this->assertTrue(Hash::check('G01-Isolated-Password!', $user->fresh()->password));

        $this->post(route('landlord.user.reset.password.change'), [
            'token' => $secondToken,
            'username' => $user->username,
            'password' => 'G01-Recovered-Password-1!',
            'password_confirmation' => 'G01-Recovered-Password-1!',
        ])->assertRedirect(route('landlord.user.login'));
        $this->assertTrue(Hash::check('G01-Recovered-Password-1!', $user->fresh()->password));
        $this->assertSame(0, DB::table('password_resets')->where('email', $user->email)->count());

        $this->post(route('landlord.user.forget.password'), ['username' => $user->email])
            ->assertRedirect();
        $thirdToken = (string) DB::table('password_resets')->where('email', $user->email)->value('token');
        $this->assertNotSame('', $thirdToken);

        $this->post(route('landlord.user.reset.password.change'), [
            'token' => $thirdToken,
            'username' => $user->username,
            'password' => 'G01-Recovered-Password-2!',
            'password_confirmation' => 'G01-Recovered-Password-2!',
        ])->assertRedirect(route('landlord.user.login'));
        $this->assertTrue(Hash::check('G01-Recovered-Password-2!', $user->fresh()->password));
        $this->assertSame(0, DB::table('password_resets')->where('email', $user->email)->count());

        $this->withSession(['store_onboarding_request_id' => $onboarding->id])
            ->postJson(route('landlord.user.ajax.login'), [
                'username' => $user->email,
                'password' => 'G01-Recovered-Password-2!',
            ])
            ->assertOk()
            ->assertJson(['status' => 'valid'])
            ->assertJsonPath('redirect_url', route('landlord.store.onboarding', ['step' => 5]));

        $this->assertSame($preservedChoices, $onboarding->fresh()->only(array_keys($preservedChoices)));
        Mail::assertSent(AdminResetEmail::class, 3);
    }

    private function user(string $prefix, bool $verified): User
    {
        $suffix = Str::lower(Str::random(10));

        return User::create([
            'name' => 'G01 '.$prefix,
            'email' => $prefix.'-'.$suffix.'@example.test',
            'username' => 'g01_'.$suffix,
            'mobile' => '9665'.random_int(10000000, 99999999),
            'password' => Hash::make('G01-Isolated-Password!'),
            'email_verified' => $verified ? 1 : 0,
        ]);
    }

    private function onboarding(User $user): StoreOnboardingRequest
    {
        return StoreOnboardingRequest::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'status' => 'draft',
        ]);
    }
}
