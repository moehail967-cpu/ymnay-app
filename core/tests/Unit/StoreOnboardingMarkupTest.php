<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StoreOnboardingMarkupTest extends TestCase
{
    private string $markup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markup = file_get_contents(
            dirname(__DIR__, 2) . '/resources/views/landlord/frontend/onboarding/store-setup.blade.php'
        );
    }

    public function test_the_five_owner_approved_steps_are_kept_in_order(): void
    {
        $positions = array_map(
            fn (string $label) => strpos($this->markup, $label),
            ['الباقة', 'القالب', 'بيانات المتجر', 'الحساب والتحقق', 'المراجعة والإنشاء']
        );

        $this->assertNotContains(false, $positions);
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($positions, $sorted);
    }

    public function test_registration_does_not_expose_the_internal_username(): void
    {
        $this->assertStringNotContainsString('name="username"', $this->markup);
        $this->assertStringNotContainsString('reg_username', $this->markup);
    }

    public function test_sensitive_registration_values_are_not_written_to_browser_storage(): void
    {
        $this->assertStringNotContainsString('localStorage', $this->markup);
        $this->assertStringNotContainsString('sessionStorage', $this->markup);
        $this->assertStringContainsString('autocomplete="one-time-code"', $this->markup);
    }

    public function test_provisioning_uses_an_indeterminate_status_instead_of_fake_progress(): void
    {
        $this->assertStringContainsString('جار إنشاء متجرك وتهيئته', $this->markup);
        $this->assertStringNotContainsString('Estimated:', $this->markup);
        $this->assertStringNotContainsString('opSetProgress', $this->markup);
    }

    public function test_mobile_progress_and_collapsible_summary_are_available(): void
    {
        $this->assertStringContainsString('الخطوة {{$step}} من 5', $this->markup);
        $summary = file_get_contents(
            dirname(__DIR__, 2) . '/resources/views/landlord/frontend/onboarding/summary.blade.php'
        );
        $this->assertStringContainsString('<details', $summary);
    }

    public function test_subdomain_and_password_controls_have_expected_states(): void
    {
        $this->assertStringContainsString("route('landlord.subdomain.check')", $this->markup);
        $this->assertStringContainsString('aria-live="polite"', $this->markup);
        $this->assertStringContainsString('data-password-toggle=', $this->markup);
        $this->assertStringContainsString('pattern="[0-9]{6}"', $this->markup);
    }

    public function test_theme_preview_does_not_select_the_theme(): void
    {
        $this->assertStringContainsString('data-onboarding-theme-preview', $this->markup);
        $this->assertStringContainsString('event.stopPropagation()', $this->markup);
        $this->assertStringContainsString('<dialog', $this->markup);
    }

    public function test_fixed_header_clearance_and_data_driven_plan_limits_are_present(): void
    {
        $this->assertStringContainsString('padding:104px 16px 48px', $this->markup);
        $this->assertStringContainsString('padding:88px 16px 28px', $this->markup);
        $this->assertStringContainsString('product_permission_feature', $this->markup);
        $this->assertStringContainsString('page_permission_feature', $this->markup);
        $this->assertStringContainsString('blog_permission_feature', $this->markup);
        $this->assertStringContainsString('storage_permission_feature', $this->markup);
        $this->assertStringContainsString('data-plan-limit=', $this->markup);
        $this->assertStringContainsString('غير محدود', $this->markup);
    }
}
