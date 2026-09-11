<?php

namespace App\Http\Controllers;

use App\PluginSystem\PluginManager;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PluginAssetController extends Controller
{
    private static array $mimeMap = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'svg'  => 'image/svg+xml',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
    ];

    public function serve(string $plugin_id, string $path): Response
    {
        /** @var PluginManager $manager */
        $manager  = app(PluginManager::class);
        $manifest = $manager->getManifest($plugin_id);

        abort_if(!$manifest, 404);

        // Only allow files inside the plugin's resources/ directory
        $safePath  = ltrim($path, '/');
        $full      = $manifest->rootPath . '/resources/' . $safePath;
        $realRoot  = realpath($manifest->rootPath . '/resources');
        $realFile  = realpath($full);

        // Path traversal guard
        if (!$realFile || !$realRoot || !Str::startsWith($realFile, $realRoot)) {
            abort(404);
        }

        if (!is_file($realFile)) {
            abort(404);
        }

        $ext      = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));
        $mime     = self::$mimeMap[$ext] ?? 'application/octet-stream';
        $content  = file_get_contents($realFile);
        $etag     = md5($content);
        $lastMod  = filemtime($realFile);

        if (request()->header('If-None-Match') === $etag) {
            return response('', 304);
        }

        return response($content, 200)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'public, max-age=31536000, immutable')
            ->header('ETag', $etag)
            ->header('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', $lastMod));
    }
}
