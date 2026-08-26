<?php

namespace App\PluginSystem;

class ShortcodeRegistry
{
    /** @var array<string, callable> */
    private array $handlers = [];

    public function register(string $tag, callable $handler): void
    {
        $this->handlers[$tag] = $handler;
    }

    public function has(string $tag): bool
    {
        return isset($this->handlers[$tag]);
    }

    /**
     * Parse and execute shortcodes in content.
     * Supports:
     *   [tag attr="value" key='val'] — self-closing
     *   [tag attr="value"]inner[/tag] — wrapping
     */
    public function render(string $content): string
    {
        if (empty($this->handlers) || !str_contains($content, '[')) {
            return $content;
        }

        $tags = implode('|', array_map('preg_quote', array_keys($this->handlers)));

        // Wrapping: [tag attrs]content[/tag]
        $content = preg_replace_callback(
            '/\[(' . $tags . ')(\s[^\]]*?)?\](.*?)\[\/\1\]/s',
            function ($m) {
                return $this->dispatch($m[1], $this->parseAttrs($m[2] ?? ''), trim($m[3]));
            },
            $content
        );

        // Self-closing: [tag attrs] or [tag attrs /]
        $content = preg_replace_callback(
            '/\[(' . $tags . ')(\s[^\]\/]*)?\s*\/?\]/',
            function ($m) {
                return $this->dispatch($m[1], $this->parseAttrs($m[2] ?? ''), '');
            },
            $content
        );

        return $content;
    }

    private function dispatch(string $tag, array $attrs, string $content): string
    {
        $handler = $this->handlers[$tag] ?? null;
        if (!$handler) {
            return '';
        }

        try {
            return (string) $handler($attrs, $content);
        } catch (\Throwable) {
            return '';
        }
    }

    private function parseAttrs(string $raw): array
    {
        $attrs = [];
        // Match key="value", key='value', or key=value
        preg_match_all('/(\w+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|(\S+))/', $raw, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $attrs[$m[1]] = $m[2] !== '' ? $m[2] : ($m[3] !== '' ? $m[3] : $m[4]);
        }

        return $attrs;
    }
}
