<?php

namespace Modules\AiIntegration\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    /**
     * Generate content via the configured AI provider.
     *
     * @throws \RuntimeException on API error or bad configuration
     */
    public function generate(
        string $provider,
        string $apiKey,
        string $model,
        float  $temperature,
        string $prompt
    ): string {
        return match ($provider) {
            'openai'   => $this->callOpenAi($apiKey, $model, $temperature, $prompt),
            'deepseek' => $this->callDeepSeek($apiKey, $model, $temperature, $prompt),
            'gemini'   => $this->callGemini($apiKey, $model, $temperature, $prompt),
            default    => throw new \RuntimeException("Unknown provider: {$provider}"),
        };
    }

    // ── Providers ─────────────────────────────────────────────────────────────

    private function callOpenAi(string $key, string $model, float $temperature, string $prompt): string
    {
        $response = Http::timeout(30)
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

        return $response->json('choices.0.message.content', '');
    }

    private function callDeepSeek(string $key, string $model, float $temperature, string $prompt): string
    {
        $response = Http::timeout(30)
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

        return $response->json('choices.0.message.content', '');
    }

    private function callGemini(string $key, string $model, float $temperature, string $prompt): string
    {
        $model    = $model ?: 'gemini-1.5-flash';
        $url      = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";

        $response = Http::timeout(30)
            ->post($url, [
                'contents'          => [['parts' => [['text' => $this->systemPrompt() . "\n\n" . $prompt]]]],
                'generationConfig'  => ['temperature' => $temperature],
            ]);

        $this->assertOk($response, 'Gemini');

        return $response->json('candidates.0.content.parts.0.text', '');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function systemPrompt(): string
    {
        return 'You are a professional e-commerce content writer. '
             . 'Write clear, engaging, well-structured HTML content suitable for a rich-text editor. '
             . 'Use <p>, <ul>, <li>, <strong>, <em> tags where appropriate. '
             . 'Do NOT wrap the output in a markdown code block. Return only the HTML.';
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
