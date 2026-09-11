<?php

namespace Modules\AiIntegration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AiIntegration\Services\AiService;

class AiIntegrationController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────────

    private function plugin(): mixed
    {
        return app(\App\PluginSystem\PluginManager::class)->get('ai-integration');
    }

    private function view(string $view, array $data = [])
    {
        return view('ai-integration::admin.settings', $data);
    }

    // ── Tenant ────────────────────────────────────────────────────────────────

    public function tenantSettings()
    {
        $plugin = $this->plugin();

        return view('ai-integration::admin.settings', array_merge($this->viewData($plugin), [
            'context'     => 'tenant',
            'saveRoute'   => route('tenant.admin.ai-integration.settings.save'),
            'generateUrl' => route('tenant.admin.ai-integration.generate'),
            'layout'      => 'tenant.admin.admin-master',
        ]));
    }

    public function tenantSaveSettings(Request $request)
    {
        $plugin = $this->plugin();
        $this->saveOptions($plugin, $request);
        return redirect()->route('tenant.admin.ai-integration.settings')
            ->with('success', __('AI Integration settings saved.'));
    }

    public function tenantGenerate(Request $request)
    {
        return $this->handleGenerate($request);
    }

    // ── Landlord ──────────────────────────────────────────────────────────────

    public function landlordSettings()
    {
        $plugin = $this->plugin();

        return view('ai-integration::admin.settings', array_merge($this->viewData($plugin), [
            'context'     => 'landlord',
            'saveRoute'   => route('landlord.admin.ai-integration.settings.save'),
            'generateUrl' => route('landlord.admin.ai-integration.generate'),
            'layout'      => 'landlord.admin.admin-master',
        ]));
    }

    public function landlordSaveSettings(Request $request)
    {
        $plugin = $this->plugin();
        $this->saveOptions($plugin, $request);
        return redirect()->route('landlord.admin.ai-integration.settings')
            ->with('success', __('AI Integration settings saved.'));
    }

    public function landlordGenerate(Request $request)
    {
        return $this->handleGenerate($request);
    }

    // ── Shared ────────────────────────────────────────────────────────────────

    private function viewData(mixed $plugin): array
    {
        return [
            'provider'          => $plugin->get_option('provider',          'openai'),
            'fallback_provider' => $plugin->get_option('fallback_provider', ''),
            'openai_key'        => $plugin->get_option('openai_key',        ''),
            'openai_model'      => $plugin->get_option('openai_model',      'gpt-4o-mini'),
            'deepseek_key'      => $plugin->get_option('deepseek_key',      ''),
            'deepseek_model'    => $plugin->get_option('deepseek_model',    'deepseek-chat'),
            'gemini_key'        => $plugin->get_option('gemini_key',        ''),
            'gemini_model'      => $plugin->get_option('gemini_model',      'gemini-1.5-flash'),
            'temperature'       => $plugin->get_option('temperature',       '0.7'),
        ];
    }

    private function saveOptions(mixed $plugin, Request $request): void
    {
        $plugin->update_option('provider',          $request->input('provider',          'openai'));
        $plugin->update_option('fallback_provider', $request->input('fallback_provider', ''));
        $plugin->update_option('openai_key',        $request->input('openai_key',        ''));
        $plugin->update_option('openai_model',      $request->input('openai_model',      'gpt-4o-mini'));
        $plugin->update_option('deepseek_key',      $request->input('deepseek_key',      ''));
        $plugin->update_option('deepseek_model',    $request->input('deepseek_model',    'deepseek-chat'));
        $plugin->update_option('gemini_key',        $request->input('gemini_key',        ''));
        $plugin->update_option('gemini_model',      $request->input('gemini_model',      'gemini-1.5-flash'));
        $plugin->update_option('temperature',       $request->input('temperature',       '0.7'));
    }

    private function handleGenerate(Request $request)
    {
        $request->validate([
            'hint'         => 'required|string|max:500',
            'content_type' => 'required|in:product,blog,page',
        ]);

        $plugin      = $this->plugin();
        $provider    = $plugin->get_option('provider', 'openai');
        $fallback    = $plugin->get_option('fallback_provider', '');
        $temperature = (float) $plugin->get_option('temperature', '0.7');
        $prompt      = $this->buildPrompt(
            $request->input('hint'),
            $request->input('content_type'),
            $request->input('tone', 'professional')
        );

        // Try primary, then fallback
        $attempts = array_filter([$provider, $fallback ?: null]);

        $lastError = null;
        foreach ($attempts as $attempt) {
            $apiKey = $this->resolveApiKey($plugin, $attempt);
            $model  = $this->resolveModel($plugin, $attempt);

            if (!$apiKey) {
                $lastError = __('No API key configured for :p. Please check AI Integration settings.', ['p' => ucfirst($attempt)]);
                continue;
            }

            try {
                $content = (new AiService())->generate($attempt, $apiKey, $model, $temperature, $prompt);
                return response()->json(['success' => true, 'content' => $content]);
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        return response()->json(['success' => false, 'message' => $lastError ?? __('Generation failed.')], 500);
    }

    private function resolveApiKey(mixed $plugin, string $provider): string
    {
        return match ($provider) {
            'openai'   => $plugin->get_option('openai_key',   ''),
            'deepseek' => $plugin->get_option('deepseek_key', ''),
            'gemini'   => $plugin->get_option('gemini_key',   ''),
            default    => '',
        };
    }

    private function resolveModel(mixed $plugin, string $provider): string
    {
        return match ($provider) {
            'openai'   => $plugin->get_option('openai_model',   'gpt-4o-mini'),
            'deepseek' => $plugin->get_option('deepseek_model', 'deepseek-chat'),
            'gemini'   => $plugin->get_option('gemini_model',   'gemini-1.5-flash'),
            default    => '',
        };
    }

    private function buildPrompt(string $hint, string $contentType, string $tone): string
    {
        $typeLabel = match ($contentType) {
            'product' => 'product description',
            'blog'    => 'blog post',
            'page'    => 'page description',
            default   => 'content',
        };

        $instructions = match ($contentType) {
            'product' => 'Include key features, benefits, and a call-to-action. Keep it under 250 words.',
            'blog'    => 'Write an engaging introduction, 2-3 body paragraphs, and a conclusion. Around 400 words.',
            'page'    => 'Write informative, welcoming copy for the page. Keep it under 200 words.',
            default   => '',
        };

        return "Write a {$tone} {$typeLabel} for: {$hint}\n\n{$instructions}";
    }
}
