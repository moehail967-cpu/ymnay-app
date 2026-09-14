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
}
