<?php

namespace App\PluginSystem;

use App\PluginSystem\Exceptions\InvalidPluginManifestException;

class PluginManifest
{
    public readonly string $id;
    public readonly string $name;
    public readonly string $version;
    public readonly string $description;
    public readonly string $author;
    public readonly string $authorUrl;
    public readonly string $minPlatformVersion;
    public readonly string $type;       // landlord | tenant | both
    public readonly string $pricing;    // free | paid
    public readonly ?string $updateServer;
    public readonly ?string $purchaseUrl;
    public readonly array $requires;
    public readonly string $main;
    public readonly string $rootPath;   // absolute path to module directory
    public readonly ?string $settingsRoute; // named route to the plugin's settings page

    private function __construct() {}

    public static function fromFile(string $jsonPath): self
    {
        if (!file_exists($jsonPath)) {
            throw new InvalidPluginManifestException($jsonPath, 'file not found');
        }

        $raw = json_decode(file_get_contents($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidPluginManifestException($jsonPath, 'invalid JSON — ' . json_last_error_msg());
        }

        return self::parse($raw, $jsonPath, dirname($jsonPath));
    }

    public static function fromArray(array $data, string $rootPath): self
    {
        return self::parse($data, $rootPath . '/plugin.json', $rootPath);
    }

    private static function parse(array $data, string $jsonPath, string $rootPath): self
    {
        $required = ['id', 'name', 'version', 'type', 'pricing', 'min_platform_version', 'main'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new InvalidPluginManifestException($jsonPath, "missing required field [{$field}]");
            }
        }

        if (!preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $data['id'])) {
            throw new InvalidPluginManifestException($jsonPath, "id must be kebab-case (e.g. acme-seo-plugin)");
        }

        if (!preg_match('/^\d+\.\d+\.\d+/', $data['version'])) {
            throw new InvalidPluginManifestException($jsonPath, "version must be semver (e.g. 1.0.0)");
        }

        if (!in_array($data['type'], ['landlord', 'tenant', 'both'])) {
            throw new InvalidPluginManifestException($jsonPath, "type must be landlord|tenant|both");
        }

        if (!in_array($data['pricing'], ['free', 'paid'])) {
            throw new InvalidPluginManifestException($jsonPath, "pricing must be free|paid");
        }

        if (!empty($data['update_server']) && !str_starts_with(strtolower($data['update_server']), 'https://')) {
            throw new InvalidPluginManifestException($jsonPath, "update_server must use HTTPS");
        }

        $mainPath = rtrim($rootPath, '/') . '/' . ltrim($data['main'], '/');
        if (!file_exists($mainPath)) {
            throw new InvalidPluginManifestException($jsonPath, "main file [{$data['main']}] not found");
        }

        $manifest = new self();
        $manifest->id                 = $data['id'];
        $manifest->name               = $data['name'];
        $manifest->version            = $data['version'];
        $manifest->description        = $data['description'] ?? '';
        $manifest->author             = $data['author'] ?? '';
        $manifest->authorUrl          = $data['author_url'] ?? '';
        $manifest->minPlatformVersion = $data['min_platform_version'];
        $manifest->type               = $data['type'];
        $manifest->pricing            = $data['pricing'];
        $manifest->updateServer       = $data['update_server'] ?? null;
        $manifest->purchaseUrl        = $data['purchase_url'] ?? null;
        $manifest->requires           = $data['requires'] ?? [];
        $manifest->main               = $data['main'];
        $manifest->rootPath           = rtrim($rootPath, '/');
        $manifest->settingsRoute      = $data['settings_route'] ?? null;

        return $manifest;
    }

    public function mainClassPath(): string
    {
        return $this->rootPath . '/' . ltrim($this->main, '/');
    }

    public function isForContext(string $context): bool
    {
        return $this->type === 'both' || $this->type === $context;
    }
}
