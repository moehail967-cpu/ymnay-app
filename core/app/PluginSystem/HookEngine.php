<?php

namespace App\PluginSystem;

use Illuminate\Support\Facades\Log;

class HookEngine
{
    /** @var array<string, array<int, array<array{callback: callable, plugin_id: string, accepted_args: int}>>> */
    private array $actions = [];

    /** @var array<string, array<int, array<array{callback: callable, plugin_id: string, accepted_args: int}>>> */
    private array $filters = [];

    private bool $debug;

    public function __construct()
    {
        $this->debug = (bool) config('app.debug') && env('PLUGIN_LOG_HOOKS', false);
    }

    public function addAction(string $hook, callable $callback, int $priority = 10, string $plugin_id = 'core'): void
    {
        $this->actions[$hook][$priority][] = [
            'callback'  => $callback,
            'plugin_id' => $plugin_id,
        ];
    }

    public function addFilter(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1, string $plugin_id = 'core'): void
    {
        $this->filters[$hook][$priority][] = [
            'callback'      => $callback,
            'plugin_id'     => $plugin_id,
            'accepted_args' => $accepted_args,
        ];
    }

    public function doAction(string $hook, mixed ...$args): void
    {
        if (empty($this->actions[$hook])) {
            return;
        }

        $callbacks = $this->getSorted($this->actions[$hook]);

        foreach ($callbacks as $entry) {
            $start = microtime(true);
            try {
                ($entry['callback'])(...$args);
            } catch (\Throwable $e) {
                Log::channel('plugin')->error("Plugin hook error [{$hook}] from [{$entry['plugin_id']}]: {$e->getMessage()}", [
                    'hook'      => $hook,
                    'plugin_id' => $entry['plugin_id'],
                    'exception' => $e,
                ]);
            }
            $this->logExecution($hook, $entry['plugin_id'], microtime(true) - $start);
        }
    }

    public function applyFilters(string $hook, mixed $value, mixed ...$args): mixed
    {
        if (empty($this->filters[$hook])) {
            return $value;
        }

        $callbacks = $this->getSorted($this->filters[$hook]);

        foreach ($callbacks as $entry) {
            $start = microtime(true);
            try {
                $accepted = $entry['accepted_args'] - 1;
                $extra    = array_slice($args, 0, max(0, $accepted));
                $result   = ($entry['callback'])($value, ...$extra);

                // Only update value if callback returned something non-null
                if ($result !== null) {
                    $value = $result;
                }
            } catch (\Throwable $e) {
                Log::channel('plugin')->error("Plugin filter error [{$hook}] from [{$entry['plugin_id']}]: {$e->getMessage()}", [
                    'hook'      => $hook,
                    'plugin_id' => $entry['plugin_id'],
                    'exception' => $e,
                ]);
            }
            $this->logExecution($hook, $entry['plugin_id'], microtime(true) - $start);
        }

        return $value;
    }

    public function removeAction(string $hook, callable $callback): void
    {
        $this->removeFromRegistry($this->actions, $hook, $callback);
    }

    public function removeFilter(string $hook, callable $callback): void
    {
        $this->removeFromRegistry($this->filters, $hook, $callback);
    }

    public function hasAction(string $hook): bool
    {
        return !empty($this->actions[$hook]);
    }

    public function hasFilter(string $hook): bool
    {
        return !empty($this->filters[$hook]);
    }

    private function getSorted(array $priorityMap): array
    {
        ksort($priorityMap);
        return array_merge(...array_values($priorityMap));
    }

    private function removeFromRegistry(array &$registry, string $hook, callable $callback): void
    {
        if (empty($registry[$hook])) {
            return;
        }

        foreach ($registry[$hook] as $priority => $entries) {
            $registry[$hook][$priority] = array_filter(
                $entries,
                fn($e) => $e['callback'] !== $callback
            );
        }
    }

    private function logExecution(string $hook, string $plugin_id, float $elapsed): void
    {
        if (!$this->debug) {
            return;
        }

        try {
            \DB::connection(config('tenancy.database.central_connection', config('database.default')))->table('plugin_hook_log')->insert([
                'hook'       => $hook,
                'plugin_id'  => $plugin_id,
                'duration_ms' => round($elapsed * 1000, 3),
                'tenant_id'  => function_exists('tenant') && tenant() ? tenant()->id : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // Never crash the app over a log entry
        }
    }
}
