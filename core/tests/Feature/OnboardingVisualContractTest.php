<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PricePlan;
use App\Models\StoreOnboardingRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class OnboardingVisualContractTest extends TestCase
{
    public function test_pending_otp_refresh_restores_only_safe_fields_and_correct_heading(): void
    {
        $plan = PricePlan::query()->where('status', 1)->firstOrFail();
        $draft = StoreOnboardingRequest::create([
            'id' => (string) Str::uuid(), 'plan_id' => $plan->id, 'theme_slug' => 'hexfashion',
            'store_name' => 'متجر المراجعة', 'subdomain' => 'visual-'.Str::lower(Str::random(8)), 'status' => 'draft',
        ]);
        $hash = Hash::make('Visual-Only-Secret!');
        $pending = [
            'name' => 'عميل مراجعة تجريبي', 'email' => 'visual@example.test', 'phone' => '967700000041',
            'password' => $hash, 'otp' => '987654', 'expires_at' => now()->addMinutes(5)->timestamp,
            'last_sent' => now()->timestamp, 'attempts' => 0,
        ];
        $response = $this->withSession(['store_onboarding_request_id' => $draft->id, 'pending_registration' => $pending])
            ->get(route('landlord.store.onboarding', ['step' => 4]))->assertOk();
        $response->assertSee('id="auth-title">تحقق من بريدك</h1>', false)
            ->assertSee('id="edit-email-btn"', false)->assertSee('رجوع إلى بيانات المتجر')
            ->assertSee('value="visual@example.test"', false)->assertSee('value="967700000041"', false)
            ->assertDontSee($hash, false)->assertDontSee('987654', false)->assertDontSee('Visual-Only-Secret!', false);
        $this->assertNull($draft->fresh()->user_id);
        $this->assertNull($draft->fresh()->tenant_id);
        $this->get(route('landlord.store.onboarding', ['step' => 3]))->assertOk()->assertSee('متجر المراجعة');
        $this->get(route('landlord.store.onboarding', ['step' => 4]))->assertOk()
            ->assertSee('id="auth-title">تحقق من بريدك</h1>', false);
        $pending['expires_at'] = now()->subSecond()->timestamp;
        $this->withSession(['pending_registration' => $pending])->get(route('landlord.store.onboarding', ['step' => 4]))
            ->assertOk()->assertSee('id="auth-title">أنشئ حسابك</h1>', false);
    }

    public function test_summary_uses_display_name_period_and_pre_creation_edit_links(): void
    {
        $previous = get_static_option_central('hexfashion_theme_name');
        try {
            update_static_option_central('hexfashion_theme_name', 'قالب الأزياء العربي');
            $plan = PricePlan::query()->where('status', 1)->firstOrFail();
            $draft = new StoreOnboardingRequest(['theme_slug' => 'hexfashion', 'store_name' => 'متجر المراجعة', 'subdomain' => 'visual-summary', 'status' => 'draft']);
            $data = ['onboarding' => $draft, 'plan' => $plan, 'themes' => [(object)['slug' => 'hexfashion', 'name' => 'HexFashion']], 'user' => null, 'maxStep' => 5];
            foreach ([0 => 'شهريًا', 1 => 'سنويًا', 2 => 'مدى الحياة'] as $type => $period) {
                $plan->type = $type; // In-memory view fixture; do not mutate the configured plan.
                $html = view('landlord.frontend.onboarding.summary', $data)->render();
                $this->assertStringContainsString('قالب الأزياء العربي', $html);
                $this->assertStringNotContainsString('>hexfashion<', $html);
                $this->assertStringContainsString($period, $html);
                foreach ([1, 2, 3] as $step) $this->assertStringContainsString('data-edit-step="'.$step.'"', $html);
            }
            foreach (['provisioning', 'ready', 'failed'] as $status) {
                $draft->status = $status;
                $this->assertStringNotContainsString('data-edit-step=', view('landlord.frontend.onboarding.summary', $data)->render());
            }
        } finally {
            update_static_option_central('hexfashion_theme_name', $previous ?? '');
        }
    }

    public function test_policy_links_use_configured_pages_and_safe_new_windows(): void
    {
        $old = ['terms_condition' => get_static_option('terms_condition'), 'privacy_policy' => get_static_option('privacy_policy')];
        $pages = [];
        try {
            foreach ($old as $key => $value) {
                $pages[] = $page = Page::create(['title' => 'صفحة مراجعة', 'slug' => 'visual-'.$key.'-'.Str::lower(Str::random(6)), 'page_content' => 'محتوى تجريبي للمراجعة فقط', 'status' => 1, 'visibility' => 1]);
                update_static_option($key, $page->id);
            }
            $html = view('landlord.frontend.onboarding.policy-links')->render();
            foreach ($pages as $page) $this->assertStringContainsString(url('/'.$page->slug), $html);
            $this->assertSame(2, substr_count($html, 'target="_blank"'));
            $this->assertSame(2, substr_count($html, 'rel="noopener noreferrer"'));
            update_static_option('terms_condition', '');
            update_static_option('privacy_policy', '');
            $html = view('landlord.frontend.onboarding.policy-links')->render();
            $this->assertStringNotContainsString('<a ', $html);
            $this->assertStringContainsString('الرابط غير متاح حاليًا', $html);
        } finally {
            foreach ($old as $key => $value) update_static_option($key, $value ?? '');
            foreach ($pages as $page) $page->delete();
        }
    }
}
