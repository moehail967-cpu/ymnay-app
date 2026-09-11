<?php

namespace App\PluginSystem\Exceptions;

class InvalidPluginManifestException extends \RuntimeException
{
    public function __construct(string $path, string $reason)
    {
        parent::__construct("Invalid plugin manifest at [{$path}]: {$reason}");
    }
}
