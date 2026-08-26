<?php

namespace App\Utils;

class XSSProtection
{
    private static array $allowedTagsByLevel = [
        'minimal' => '<br><strong><em><u>',
        'basic'   => '<p><br><strong><em><u><a><span>',
        'rich'    => '<p><br><strong><em><u><a><span><div><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><img>',
        'widget'  => '<p><br><strong><em><u><a><span><div><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><img><table><tr><td><th><thead><tbody><tfoot><section><article><figure><figcaption><pre><code>',
    ];

    public static function sanitizeText(string $text, bool $preserveLineBreaks = false): string
    {
        $sanitized = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($preserveLineBreaks) {
            $sanitized = nl2br($sanitized);
        }

        return $sanitized;
    }

    public static function sanitizeHTML(string $content, string $level = 'widget', array $customOptions = []): string
    {
        $allowed = $customOptions['allowed_tags']
            ?? self::$allowedTagsByLevel[$level]
            ?? self::$allowedTagsByLevel['widget'];

        $content = strip_tags($content, $allowed);

        // Remove javascript: and data: from href/src attributes
        $content = preg_replace('/(<[^>]+(?:href|src|action)\s*=\s*["\'])(?:javascript|data|vbscript):[^"\']*(["\'])/i', '$1#$2', $content);

        // Remove on* event attributes
        $content = preg_replace('/\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $content);

        return $content;
    }

    public static function sanitizeURL(string $url, array $allowedSchemes = []): ?string
    {
        $url = trim($url);

        if (empty($url)) {
            return null;
        }

        if (empty($allowedSchemes)) {
            $allowedSchemes = ['http', 'https', 'mailto', 'tel', '#', '/'];
        }

        // Allow relative URLs and anchors
        if (str_starts_with($url, '/') || str_starts_with($url, '#') || str_starts_with($url, '?')) {
            return $url;
        }

        $parsed = parse_url($url);
        $scheme = strtolower($parsed['scheme'] ?? '');

        if (empty($scheme) || in_array($scheme, $allowedSchemes, true)) {
            return $url;
        }

        return null;
    }

    public static function sanitizeCSS(string $css): string
    {
        // Remove javascript expressions and dangerous CSS
        $css = preg_replace('/expression\s*\(/i', '', $css);
        $css = preg_replace('/javascript\s*:/i', '', $css);
        $css = preg_replace('/@import/i', '', $css);
        $css = preg_replace('/behaviour\s*:/i', '', $css);
        $css = preg_replace('/-moz-binding\s*:/i', '', $css);

        return $css;
    }

    public static function detectThreats(string $content): bool
    {
        $patterns = [
            '/<script/i',
            '/javascript\s*:/i',
            '/on\w+\s*=/i',
            '/data\s*:\s*text\/html/i',
            '/vbscript\s*:/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }
}
