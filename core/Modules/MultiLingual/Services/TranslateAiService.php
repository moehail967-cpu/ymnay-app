<?php

namespace Modules\MultiLingual\Services;

use App\PluginSystem\PluginManager;
use Illuminate\Support\Facades\Http;

/**
 * Translates content via OpenAI, DeepSeek, or Gemini.
 * Can pull credentials from the AI Integration plugin or its own settings.
 */
class TranslateAiService
{
    public function translate(
        string $text,
        string $sourceLocale,
        string $targetLocale,
        string $provider,
        string $apiKey,
        string $model,
        float  $temperature = 0.3,
        bool   $isHtml = false
    ): string {
        $prompt = $this->buildPrompt($text, $sourceLocale, $targetLocale, $isHtml);

        return match ($provider) {
            'openai'   => $this->callOpenAi($apiKey, $model, $temperature, $prompt),
            'deepseek' => $this->callDeepSeek($apiKey, $model, $temperature, $prompt),
            'gemini'   => $this->callGemini($apiKey, $model, $temperature, $prompt),
            default    => throw new \RuntimeException("Unknown provider: {$provider}"),
        };
    }

    /**
     * Resolve credentials: use AI Integration plugin if configured and active,
     * otherwise fall back to the Multi-Lingual plugin's own settings.
     *
     * @return array{provider:string, key:string, model:string, temperature:float}
     * @throws \RuntimeException when no usable credentials found
     */
    public static function resolveCredentials(): array
    {
        $mlPlugin = PluginManager::get('multi-lingual');

        $source = $mlPlugin ? $mlPlugin->get_option('ai_source', 'ai-integration') : 'ai-integration';

        if ($source === 'ai-integration') {
            $aiPlugin = PluginManager::get('ai-integration');
            if ($aiPlugin) {
                $provider = $aiPlugin->get_option('provider', 'openai');
                $key      = $aiPlugin->get_option("{$provider}_key", '');
                $model    = $aiPlugin->get_option("{$provider}_model", '');
                $temp     = (float) $aiPlugin->get_option('temperature', '0.3');

                if ($key) {
                    return compact('provider', 'key', 'model', 'temp') + ['temperature' => $temp];
                }
            }
        }

        // Own settings
        if ($mlPlugin) {
            $provider = $mlPlugin->get_option('ai_provider', 'openai');
            $key      = $mlPlugin->get_option('ai_key', '');
            $model    = $mlPlugin->get_option('ai_model', '');
            $temp     = (float) $mlPlugin->get_option('ai_temperature', '0.3');

            if ($key) {
                return ['provider' => $provider, 'key' => $key, 'model' => $model, 'temperature' => $temp];
            }
        }

        throw new \RuntimeException('No AI API key configured. Please set up the AI Integration plugin or configure a key in Multi-Lingual settings.');
    }

    // ── Providers ─────────────────────────────────────────────────────────────

    private function callOpenAi(string $key, string $model, float $temperature, string $prompt): string
    {
        $response = Http::timeout(60)
            ->withToken($key)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $model ?: 'gpt-4o-mini',
                'temperature' => $temperature,
                'messages'    => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

        $this->assertOk($response, 'OpenAI');
        return trim($response->json('choices.0.message.content', ''));
    }

    private function callDeepSeek(string $key, string $model, float $temperature, string $prompt): string
    {
        $response = Http::timeout(60)
            ->withToken($key)
            ->post('https://api.deepseek.com/v1/chat/completions', [
                'model'       => $model ?: 'deepseek-chat',
                'temperature' => $temperature,
                'messages'    => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

        $this->assertOk($response, 'DeepSeek');
        return trim($response->json('choices.0.message.content', ''));
    }

    private function callGemini(string $key, string $model, float $temperature, string $prompt): string
    {
        $model    = $model ?: 'gemini-1.5-flash';
        $url      = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";

        $response = Http::timeout(60)
            ->post($url, [
                'contents'         => [['parts' => [['text' => $this->systemPrompt() . "\n\n" . $prompt]]]],
                'generationConfig' => ['temperature' => $temperature],
            ]);

        $this->assertOk($response, 'Gemini');
        return trim($response->json('candidates.0.content.parts.0.text', ''));
    }

    // ── Prompt ────────────────────────────────────────────────────────────────

    private function buildPrompt(string $text, string $source, string $target, bool $isHtml): string
    {
        $format = $isHtml
            ? 'Preserve all HTML tags exactly (<p>, <ul>, <li>, <strong>, <em>, etc.). Return only the translated HTML.'
            : 'Return only the translated plain text.';

        return "Translate the following text from {$source} to {$target}.\n"
             . "{$format}\n"
             . "Do NOT add explanations, notes, or the original text. Return ONLY the translation.\n\n"
             . $text;
    }

    private function systemPrompt(): string
    {
        return 'You are a professional translator for an e-commerce platform. '
             . 'Translate content accurately while preserving tone, formatting, and HTML structure. '
             . 'Never add commentary or the original text in your response.';
    }

    private function assertOk(\Illuminate\Http\Client\Response $response, string $provider): void
    {
        if (!$response->ok()) {
            $error = $response->json('error.message')
                ?? $response->json('error.status')
                ?? $response->body();
            throw new \RuntimeException("{$provider} API error: {$error}");
        }
    }
}
