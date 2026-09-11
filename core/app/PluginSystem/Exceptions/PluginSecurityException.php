<?php

namespace App\PluginSystem\Exceptions;

class PluginSecurityException extends \RuntimeException
{
    public function __construct(string $reason)
    {
        parent::__construct("Plugin security check failed: {$reason}");
    }
}
