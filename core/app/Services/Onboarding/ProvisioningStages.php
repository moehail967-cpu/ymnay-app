<?php

namespace App\Services\Onboarding;

use Closure;
use RuntimeException;

/** Durable checkpoints; storage and actual readiness probes are supplied by the tenant adapter. */
final class ProvisioningStages
{
    public function __construct(private array $states, private readonly Closure $persist)
    {
    }

    public function run(string $stage, Closure $operation, bool $repeatable = false, ?Closure $verify = null): void
    {
        $state = $this->states[$stage] ?? null;
        if ($state === 'done') {
            if (!$verify || $verify()) return;
            if (!$repeatable) throw new RecoveryRequired($stage);
        } elseif ($verify && $verify()) {
            // Reconcile a completed side effect whose checkpoint write was interrupted.
            $this->mark($stage, 'done');
            return;
        } elseif ($state !== null && !$repeatable) {
            throw new RecoveryRequired($stage);
        }

        $this->mark($stage, 'running'); // Persist BEFORE side effects. Failure here must stop execution.
        // On an exception 'running' remains durable; uncertain destructive work cannot replay.
        $operation();
        if ($verify && !$verify()) throw new RuntimeException('Onboarding stage is not ready: ' . $stage);
        $this->mark($stage, 'done');
    }

    private function mark(string $stage, string $state): void
    {
        $next = array_replace($this->states, [$stage => $state]);
        ($this->persist)($next);
        $this->states = $next;
    }
}
