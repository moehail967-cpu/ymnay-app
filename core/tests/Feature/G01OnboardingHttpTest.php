<?php

namespace Tests\Feature;

use App\Mail\BasicMail;
use App\Models\StoreOnboardingRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class G01OnboardingHttpTest extends TestCase
{
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
