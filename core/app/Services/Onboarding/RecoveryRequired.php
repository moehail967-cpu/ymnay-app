<?php

namespace App\Services\Onboarding;

use RuntimeException;

/** An interrupted non-repeatable stage needs inspection, not a blind replay. */
class RecoveryRequired extends RuntimeException
{
    public function __construct(public readonly string $stage)
    {
        parent::__construct('Onboarding recovery requires inspection at stage: ' . $stage);
    }
}
