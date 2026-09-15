<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LandingPageMarkupTest extends TestCase
{
    private string $markup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markup = file_get_contents(
            dirname(__DIR__, 2) . '/resources/views/landlord/frontend/frontend-home.blade.php'
        );
    }

    public function test_the_owner_approved_landing_sections_are_present_in_order(): void
    {
        $positions = array_map(
            fn (string $marker) => strpos($this->markup, $marker),
            ['ym-hero', 'id="features"', 'id="themes"', 'ym-dashboard-grid', 'id="how-it-works"', 'id="pricing"', 'id="faq"', 'ym-final-cta']
        );

        $this->assertNotContains(false, $positions);
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($positions, $sorted);
    }

    public function test_start_actions_use_the_store_onboarding_route(): void
    {
        $this->assertStringContainsString("route('landlord.store.onboarding')", $this->markup);
        $this->assertStringContainsString("route('landlord.store.onboarding.plan')", $this->markup);
        $this->assertStringNotContainsString('/onboarding', $this->markup);
    }

    public function test_plans_and_themes_are_rendered_from_runtime_data(): void
    {
        $this->assertStringContainsString('@foreach($plans as $plan)', $this->markup);
        $this->assertStringContainsString('@foreach($featuredThemes as $theme)', $this->markup);
        $this->assertStringContainsString('amount_with_currency_symbol($plan->price)', $this->markup);
        $this->assertStringNotContainsString('60 ريال', $this->markup);
        $this->assertStringNotContainsString('الأكثر شعبية', $this->markup);
    }

    public function test_accessible_controls_are_used_for_faq_and_previews(): void
    {
        $this->assertStringContainsString('aria-expanded="false"', $this->markup);
        $this->assertStringContainsString('<dialog', $this->markup);
        $this->assertStringContainsString('data-ym-preview-size="mobile"', $this->markup);
    }
}
