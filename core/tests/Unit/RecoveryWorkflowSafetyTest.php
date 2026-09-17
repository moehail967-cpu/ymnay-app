<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class RecoveryWorkflowSafetyTest extends TestCase
{
    public function test_recovery_matches_the_failed_timestamp_after_normalizing_to_utc(): void
    {
        $source = file_get_contents(
            dirname(__DIR__, 3).'/.deployment/recover-failed-store-onboarding.php'
        );

        $this->assertStringContainsString("->where('status', 'failed')", $source);
        $this->assertStringContainsString("->utc()", $source);
        $this->assertStringContainsString("->format('Y-m-d\\\\TH:i:s\\\\Z') === \$expectedFailedAt", $source);
        $this->assertStringNotContainsString("->where('updated_at', \$expectedDatabaseTimestamp)", $source);
    }

    public function test_workflow_requires_the_script_success_marker(): void
    {
        $workflow = file_get_contents(
            dirname(__DIR__, 3).'/.github/workflows/recover-failed-store-onboarding-production.yml'
        );

        $this->assertStringContainsString('RECOVERY_OUTPUT="$(ssh', $workflow);
        $this->assertStringContainsString(
            "grep -qx 'RECOVERY_STATUS=success' <<< \"\$RECOVERY_OUTPUT\"",
            $workflow
        );
    }
}
