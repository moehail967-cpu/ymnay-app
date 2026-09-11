<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DashboardMarkupTest extends TestCase
{
    public function test_legacy_subscription_form_remains_balanced_and_hidden(): void
    {
        $view = file_get_contents(__DIR__.'/../../resources/views/landlord/frontend/dashboard/user-home.blade.php');

        preg_match_all('/<form\b/i', $view, $openingForms);
        preg_match_all('/<\/form>/i', $view, $closingForms);

        $this->assertSame(count($openingForms[0]), count($closingForms[0]));
        $this->assertStringContainsString('id="user_add_subscription" style="display:none!important" aria-hidden="true"', $view);
        $this->assertStringContainsString('id="user_add_subscription_form"', $view);
    }
}
