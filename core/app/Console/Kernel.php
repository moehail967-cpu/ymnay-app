<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        // Plugin-registered scheduled tasks
        app(\App\PluginSystem\PluginScheduler::class)->scheduleAll($schedule);

        $schedule->command('theme:check-updates')->daily();

        $schedule->command('package:expire')
            ->daily();

        $schedule->command('campaigns:suggest --days=60 --min-stock=10')
            ->weekly();

        $schedule->command('account:remove')
            ->daily();

        $schedule->command('package:auto-renew')
            ->daily();

        $schedule->command('queue:work --timeout=60 --tries=1 --once')
            ->everyMinute()
            ->withoutOverlapping()
            ->sendOutputTo(storage_path() . '/logs/queue-jobs.log');

        $schedule->command('queue:work tenant_file_sync --timeout=60 --tries=1 --once')
            ->everyMinute()
            ->withoutOverlapping()
            ->sendOutputTo(storage_path() . '/logs/new-website-file-sync-jobs.log');

        if (app()->environment('local'))
        {
            try {
                $schedule->command('telescope:prune')->everyMinute();
            } catch (\Exception $exception) {}
        }
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
