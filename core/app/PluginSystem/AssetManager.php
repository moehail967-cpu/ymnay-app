<?php

namespace App\PluginSystem;

class AssetManager
{
    private array $styles        = ['admin' => [], 'frontend' => []];
    private array $scripts       = ['admin' => [], 'frontend' => []];
    private array $inlineStyles  = ['admin' => [], 'frontend' => []];
    private array $inlineScripts = ['admin' => [], 'frontend' => []];

    /** Platform handles available as dependencies */
    private array $platformHandles = ['jquery', 'alpine', 'toastr', 'sweetalert2', 'tailwind'];

    public function enqueueStyle(string $context, string $handle, string $src, array $deps = []): void
    {
        if (isset($this->styles[$context][$handle])) {
            return; // first registration wins
        }
        $this->styles[$context][$handle] = compact('handle', 'src', 'deps');
    }

    public function enqueueScript(string $context, string $handle, string $src, array $deps = [], bool $in_footer = true): void
    {
        if (isset($this->scripts[$context][$handle])) {
            return;
        }
        $this->scripts[$context][$handle] = compact('handle', 'src', 'deps', 'in_footer');
    }

    /**
     * Enqueue an inline CSS snippet.
     * Rendered inside a <style> tag via @pluginFrontendStyles / @pluginAdminStyles.
     */
    public function enqueueInlineStyle(string $context, string $handle, string $css): void
    {
        if (isset($this->inlineStyles[$context][$handle])) {
            return;
        }
        $this->inlineStyles[$context][$handle] = $css;
    }

    /**
     * Enqueue an inline JS snippet.
     * Pass $in_footer = false to render in <head>, true (default) to render before </body>.
     * Rendered via @pluginFrontendScripts / @pluginAdminScripts.
     */
    public function enqueueInlineScript(string $context, string $handle, string $js, bool $in_footer = true): void
    {
        if (isset($this->inlineScripts[$context][$handle])) {
            return;
        }
        $this->inlineScripts[$context][$handle] = compact('js', 'in_footer');
    }

    public function renderStyles(string $context): string
    {
        $output = '';
        foreach ($this->getSortedAssets($this->styles[$context] ?? []) as $asset) {
            $output .= '<link rel="stylesheet" href="' . e($asset['src']) . '">' . "\n";
        }
        if (!empty($this->inlineStyles[$context])) {
            $output .= "<style>\n";
            foreach ($this->inlineStyles[$context] as $css) {
                $output .= $css . "\n";
            }
            $output .= "</style>\n";
        }
        return $output;
    }

    public function renderScripts(string $context, bool $footer = false): string
    {
        $output = '';
        foreach ($this->getSortedAssets($this->scripts[$context] ?? []) as $asset) {
            if ($asset['in_footer'] !== $footer) {
                continue;
            }
            $output .= '<script src="' . e($asset['src']) . '"></script>' . "\n";
        }
        $inlines = array_filter(
            $this->inlineScripts[$context] ?? [],
            fn($s) => $s['in_footer'] === $footer
        );
        if (!empty($inlines)) {
            $output .= "<script>\n";
            foreach ($inlines as $snippet) {
                $output .= $snippet['js'] . "\n";
            }
            $output .= "</script>\n";
        }
        return $output;
    }

    private function getSortedAssets(array $assets): array
    {
        $sorted = [];
        $visited = [];

        $visit = function (string $handle) use (&$visit, &$sorted, &$visited, $assets): void {
            if (isset($visited[$handle])) {
                return;
            }
            $visited[$handle] = true;

            if (!isset($assets[$handle])) {
                return; // platform handle or missing dep — skip
            }

            foreach ($assets[$handle]['deps'] as $dep) {
                if (!in_array($dep, $this->platformHandles)) {
                    $visit($dep);
                }
            }

            $sorted[] = $assets[$handle];
        };

        foreach (array_keys($assets) as $handle) {
            $visit($handle);
        }

        return $sorted;
    }
}
