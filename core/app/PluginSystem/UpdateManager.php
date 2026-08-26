<?php

namespace App\PluginSystem;

use App\PluginSystem\Exceptions\PluginSecurityException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateManager
{
    // Extensions allowed inside a plugin ZIP
    private const ALLOWED_EXTENSIONS = [
        'php', 'json', 'js', 'css', 'html', 'blade',
        'svg', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'ico',
        'woff', 'woff2', 'ttf', 'eot',
        'md', 'txt', 'xml', 'yaml', 'yml',
        'map', 'lock',
    ];

    // Paths that must never appear inside a plugin ZIP
    private const BLOCKED_PATH_PATTERNS = [
        '/\.env/',
        '/\.htaccess/',
        '/wp-config/',
        '/\.git/',
    ];

    /**
     * Check if a newer version is available on the plugin's update server.
     * Returns update info array or null if up to date / unreachable.
     */
    public function checkForUpdate(PluginManifest $manifest): ?array
    {
        if (!$manifest->updateServer) {
            return null;
        }

        $cacheKey = "plugin_update_check.{$manifest->id}";

        return Cache::remember($cacheKey, 3600, function () use ($manifest) {
            try {
                $response = Http::timeout(8)->get($manifest->updateServer . '/info', [
                    'plugin_id' => $manifest->id,
                    'version'   => $manifest->version,
                ]);

                if (!$response->ok()) {
                    return null;
                }

                $data = $response->json();

                if (empty($data['version']) || !$this->isNewer($data['version'], $manifest->version)) {
                    return null;
                }

                return [
                    'version'      => $data['version'],
                    'changelog'    => $data['changelog'] ?? null,
                    'download_url' => $data['download_url'] ?? null,
                    'sha256'       => $data['sha256'] ?? null,
                ];
            } catch (\Throwable $e) {
                Log::channel('plugin')->warning("Update check failed for [{$manifest->id}]: {$e->getMessage()}");
                return null;
            }
        });
    }

    /**
     * Download and install an update for an already-discovered plugin.
     * $expectedSha256 should come from the checkForUpdate() response.
     */
    public function installUpdate(PluginManifest $manifest, string $downloadUrl, ?string $expectedSha256 = null): bool
    {
        $fromVersion = $manifest->version;

        try {
            // Only allow HTTPS download URLs
            $this->assertHttpsUrl($downloadUrl, 'download_url');

            $tmpZip = tempnam(sys_get_temp_dir(), 'plugin_upd_') . '.zip';
            $zipContent = Http::timeout(120)->get($downloadUrl)->body();
            file_put_contents($tmpZip, $zipContent);

            // Verify SHA256 checksum if provided
            $checksumVerified = false;
            if ($expectedSha256 !== null) {
                $actual = hash('sha256', $zipContent);
                if (!hash_equals(strtolower($expectedSha256), $actual)) {
                    @unlink($tmpZip);
                    throw new PluginSecurityException(
                        "SHA256 mismatch for [{$manifest->id}] — expected [{$expectedSha256}], got [{$actual}]"
                    );
                }
                $checksumVerified = true;
            }

            $result = $this->extractZipSecure($tmpZip, base_path('Modules'));
            @unlink($tmpZip);

            if ($result) {
                $this->writeInstallLog([
                    'plugin_id'         => $manifest->id,
                    'version'           => $manifest->version,
                    'action'            => 'update',
                    'source'            => 'remote',
                    'from_version'      => $fromVersion,
                    'download_url'      => $downloadUrl,
                    'checksum_verified' => $checksumVerified,
                ]);

                $plugin = PluginManager::get($manifest->id);
                if ($plugin) {
                    try {
                        $plugin->on_update($fromVersion);
                    } catch (\Throwable $e) {
                        Log::channel('plugin')->error("Plugin [{$manifest->id}] on_update() failed: {$e->getMessage()}");
                    }
                }

                Cache::forget("plugin_update_check.{$manifest->id}");
                Cache::forget('plugin_manager.manifests');
            }

            return $result;
        } catch (PluginSecurityException $e) {
            Log::channel('plugin')->critical("SECURITY: {$e->getMessage()}");
            throw $e;
        } catch (\Throwable $e) {
            Log::channel('plugin')->error("Update install failed for [{$manifest->id}]: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Install a plugin from a local ZIP file path (admin upload).
     * Returns the plugin id from the manifest inside the zip, or null on failure.
     */
    public function installFromZip(string $zipPath): ?string
    {
        try {
            $za = new ZipArchive();
            if ($za->open($zipPath) !== true) {
                return null;
            }

            // 1. Find and read plugin.json
            $pluginJson = null;
            for ($i = 0; $i < $za->numFiles; $i++) {
                $name = $za->getNameIndex($i);
                if (preg_match('#^[^/]+/plugin\.json$#', $name)) {
                    $pluginJson = json_decode($za->getFromIndex($i), true);
                    break;
                }
            }

            if (!$pluginJson || empty($pluginJson['id'])) {
                $za->close();
                return null;
            }

            // 2. Security scan all ZIP entries before extracting
            $this->validateZipEntries($za, base_path('Modules'));
            $za->close();

            // 3. Extract
            if (!$this->extractZipSecure($zipPath, base_path('Modules'))) {
                return null;
            }

            $this->writeInstallLog([
                'plugin_id'         => $pluginJson['id'],
                'version'           => $pluginJson['version'] ?? 'unknown',
                'action'            => 'install',
                'source'            => 'upload',
                'from_version'      => null,
                'download_url'      => null,
                'checksum_verified' => false,
            ]);

            Cache::forget('plugin_manager.manifests');
            return $pluginJson['id'];
        } catch (PluginSecurityException $e) {
            Log::channel('plugin')->critical("SECURITY: {$e->getMessage()}");
            throw $e;
        } catch (\Throwable $e) {
            Log::channel('plugin')->error("ZIP install failed: {$e->getMessage()}");
            return null;
        }
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    /**
     * Validate every ZIP entry before extraction:
     * - No path traversal (../)
     * - No null bytes
     * - No blocked path patterns (.env, .git, etc.)
     * - Extension whitelist
     */
    private function validateZipEntries(ZipArchive $za, string $destDir): void
    {
        $destDir = rtrim(str_replace('\\', '/', $destDir), '/') . '/';

        for ($i = 0; $i < $za->numFiles; $i++) {
            $name = $za->getNameIndex($i);

            // Null byte injection
            if (str_contains($name, "\0")) {
                throw new PluginSecurityException("Null byte in ZIP entry: [{$name}]");
            }

            // Resolve full path and check it stays inside destDir
            $resolved = $this->resolvePath($destDir, $name);
            if (!str_starts_with($resolved, $destDir)) {
                throw new PluginSecurityException("Path traversal in ZIP entry: [{$name}]");
            }

            // Skip directory entries
            if (str_ends_with($name, '/')) {
                continue;
            }

            // Blocked path patterns
            foreach (self::BLOCKED_PATH_PATTERNS as $pattern) {
                if (preg_match($pattern, $name)) {
                    throw new PluginSecurityException("Blocked file in ZIP: [{$name}]");
                }
            }

            // Extension whitelist
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== '' && !in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                throw new PluginSecurityException("Disallowed file type [{$ext}] in ZIP: [{$name}]");
            }
        }
    }

    /**
     * Validate then extract a ZIP. Never extracts without passing validateZipEntries() first.
     */
    private function extractZipSecure(string $zipPath, string $destDir): bool
    {
        $za = new ZipArchive();
        if ($za->open($zipPath) !== true) {
            return false;
        }

        $this->validateZipEntries($za, $destDir);

        $za->extractTo($destDir);
        $za->close();
        return true;
    }

    /**
     * Resolve a ZIP entry path relative to destDir without relying on realpath()
     * (the file doesn't exist yet at validation time).
     */
    private function resolvePath(string $base, string $entry): string
    {
        $base  = rtrim(str_replace('\\', '/', $base), '/') . '/';
        $entry = str_replace('\\', '/', $entry);

        $segments = [];
        foreach (explode('/', $base . $entry) as $seg) {
            if ($seg === '' || $seg === '.') continue;
            if ($seg === '..') {
                array_pop($segments);
            } else {
                $segments[] = $seg;
            }
        }

        return '/' . implode('/', $segments) . '/';
    }

    private function assertHttpsUrl(string $url, string $field): void
    {
        if (!str_starts_with(strtolower($url), 'https://')) {
            throw new PluginSecurityException("Non-HTTPS URL rejected for [{$field}]: [{$url}]");
        }
    }

    private function writeInstallLog(array $data): void
    {
        try {
            DB::connection(config('tenancy.database.central_connection', config('database.default')))->table('plugin_install_log')->insert(array_merge($data, [
                'installed_by' => auth()->id(),
                'ip_address'   => request()->ip(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]));
        } catch (\Throwable $e) {
            // Non-fatal — log the failure but don't block install
            Log::channel('plugin')->warning("Could not write plugin_install_log: {$e->getMessage()}");
        }
    }

    private function isNewer(string $remote, string $current): bool
    {
        return version_compare($remote, $current, '>');
    }
}
