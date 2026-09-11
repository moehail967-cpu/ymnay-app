<?php

use App\PluginSystem\HookEngine;

if (!function_exists('do_action')) {
    function do_action(string $hook, mixed ...$args): void
    {
        app(HookEngine::class)->doAction($hook, ...$args);
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters(string $hook, mixed $value, mixed ...$args): mixed
    {
        return app(HookEngine::class)->applyFilters($hook, $value, ...$args);
    }
}

if (!function_exists('add_action')) {
    function add_action(string $hook, callable $callback, int $priority = 10): void
    {
        app(HookEngine::class)->addAction($hook, $callback, $priority);
    }
}

if (!function_exists('add_filter')) {
    function add_filter(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void
    {
        app(HookEngine::class)->addFilter($hook, $callback, $priority, $accepted_args);
    }
}

if (!function_exists('plugin_admin_styles')) {
    function plugin_admin_styles(): string
    {
        return app(\App\PluginSystem\AssetManager::class)->renderStyles('admin');
    }
}

if (!function_exists('plugin_admin_scripts')) {
    function plugin_admin_scripts(bool $footer = false): string
    {
        return app(\App\PluginSystem\AssetManager::class)->renderScripts('admin', $footer);
    }
}

if (!function_exists('plugin_frontend_styles')) {
    function plugin_frontend_styles(): string
    {
        return app(\App\PluginSystem\AssetManager::class)->renderStyles('frontend');
    }
}

if (!function_exists('plugin_frontend_scripts')) {
    function plugin_frontend_scripts(bool $footer = false): string
    {
        return app(\App\PluginSystem\AssetManager::class)->renderScripts('frontend', $footer);
    }
}

if (!function_exists('plugin_enqueue_style')) {
    /** Enqueue a CSS file for a context ('frontend' or 'admin'). */
    function plugin_enqueue_style(string $handle, string $src, string $context = 'frontend', array $deps = []): void
    {
        app(\App\PluginSystem\AssetManager::class)->enqueueStyle($context, $handle, $src, $deps);
    }
}

if (!function_exists('plugin_enqueue_script')) {
    /** Enqueue a JS file for a context ('frontend' or 'admin'). */
    function plugin_enqueue_script(string $handle, string $src, string $context = 'frontend', array $deps = [], bool $in_footer = true): void
    {
        app(\App\PluginSystem\AssetManager::class)->enqueueScript($context, $handle, $src, $deps, $in_footer);
    }
}

if (!function_exists('plugin_enqueue_inline_style')) {
    /** Inject an inline CSS snippet into the page <head>. */
    function plugin_enqueue_inline_style(string $handle, string $css, string $context = 'frontend'): void
    {
        app(\App\PluginSystem\AssetManager::class)->enqueueInlineStyle($context, $handle, $css);
    }
}

if (!function_exists('plugin_enqueue_inline_script')) {
    /** Inject an inline JS snippet. $in_footer=true → before </body>, false → in <head>. */
    function plugin_enqueue_inline_script(string $handle, string $js, string $context = 'frontend', bool $in_footer = true): void
    {
        app(\App\PluginSystem\AssetManager::class)->enqueueInlineScript($context, $handle, $js, $in_footer);
    }
}

if (!function_exists('plugin_asset_url')) {
    /**
     * Return the public URL for a plugin's resource file.
     * Path is relative to the plugin's resources/ directory.
     *
     * Usage in blades:   {{ plugin_asset_url('my-plugin', 'css/style.css') }}
     * Usage in PHP:       plugin_asset_url('cart-discount', 'js/cart-discount.js')
     */
    function plugin_asset_url(string $plugin_id, string $path): string
    {
        $assetPath = ltrim(preg_replace('#^resources/#', '', ltrim($path, '/')), '/');
        return url('/plugins/' . $plugin_id . '/assets/' . $assetPath);
    }
}

if (!function_exists('parse_shortcodes')) {
    function parse_shortcodes(string $content): string
    {
        return app(\App\PluginSystem\ShortcodeRegistry::class)->render($content);
    }
}
