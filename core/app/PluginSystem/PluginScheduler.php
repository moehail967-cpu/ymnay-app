<?php

namespace App\PluginSystem;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

class PluginScheduler
{
    private array $tasks = [];

    /**
     * Register a scheduled task from a plugin.
     *
     * @param string   $plugin_id  Used for logging
     * @param string   $frequency  Cron expression OR named frequency: everyMinute, everyFiveMinutes,
     *                             hourly, daily, weekly, monthly — or any valid cron string
     * @param callable $callback
     */
    public function add(string $plugin_id, string $frequency, callable $callback): void
    {
        $this->tasks[] = compact('plugin_id', 'frequency', 'callback');
    }

    /**
     * Register all collected tasks into Laravel's scheduler.
     * Called from Console\Kernel::schedule().
     */
    public function scheduleAll(Schedule $schedule): void
    {
        $named = [
            'everyMinute'       => 'everyMinute',
            'everyFiveMinutes'  => 'everyFiveMinutes',
            'everyTenMinutes'   => 'everyTenMinutes',
            'everyThirtyMinutes'=> 'everyThirtyMinutes',
            'hourly'            => 'hourly',
            'daily'             => 'daily',
            'weekly'            => 'weekly',
            'monthly'           => 'monthly',
        ];

        foreach ($this->tasks as $task) {
            try {
                $event = $schedule->call($task['callback']);

                if (isset($named[$task['frequency']])) {
                    $method = $named[$task['frequency']];
                    $event->$method();
                } else {
                    // Treat as a cron expression
                    $event->cron($task['frequency']);
                }
            } catch (\Throwable $e) {
                Log::channel('plugin')->error(
                    "Plugin [{$task['plugin_id']}] failed to register scheduled task: {$e->getMessage()}"
                );
            }
        }
    }
}
