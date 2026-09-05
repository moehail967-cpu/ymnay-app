<?php

namespace Tests\Unit;

use App\Console\Commands\PackageExpireCommand;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class PackageExpireCommandTest extends TestCase
{
    public function test_it_notifies_only_on_a_configured_day(): void
    {
        $today = Carbon::parse('2026-09-02');
        $expiresAt = Carbon::parse('2026-09-09 18:30:00');

        $this->assertTrue(
            PackageExpireCommand::shouldSendExpiryNotification($expiresAt, $today, [7, 3, 1])
        );
    }

    public function test_it_does_not_repeat_notification_after_the_configured_day(): void
    {
        $expiresAt = Carbon::parse('2026-09-09');

        $this->assertFalse(
            PackageExpireCommand::shouldSendExpiryNotification(
                $expiresAt,
                Carbon::parse('2026-09-03'),
                [7]
            )
        );
    }

    public function test_it_ignores_invalid_notification_configuration(): void
    {
        $this->assertFalse(
            PackageExpireCommand::shouldSendExpiryNotification(
                Carbon::parse('2026-09-09'),
                Carbon::parse('2026-09-02'),
                null
            )
        );
    }
}
