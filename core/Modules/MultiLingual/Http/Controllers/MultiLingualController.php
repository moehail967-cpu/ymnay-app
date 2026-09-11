<?php

namespace Modules\MultiLingual\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\PluginSystem\PluginManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\MultiLingual\Services\TranslateAiService;
use Modules\MultiLingual\Services\TranslationService;

class MultiLingualController extends Controller
{
    private function plugin()
    {
        return PluginManager::get('multi-lingual');
    }

    public function settings()
    {
        $plugin   = $this->plugin();
        $enabled  = $plugin ? $plugin->get_option('enabled', '1') : '1';
        $langs    = Language::where('status', 1)->get();
        $fallback = config('app.fallback_locale', 'en');

        return view('multi-lingual::tenant.settings', compact('enabled', 'langs', 'fallback'));
    }

    public function saveSettings(Request $request)
    {
        $plugin = $this->plugin();
        if ($plugin) {
            $plugin->update_option('enabled',        $request->boolean('enabled') ? '1' : '0');
            $plugin->update_option('ai_source',      $request->input('ai_source',      'ai-integration'));
            $plugin->update_option('ai_provider',    $request->input('ai_provider',    'openai'));
            $plugin->update_option('ai_key',         $request->input('ai_key',         ''));
            $plugin->update_option('ai_model',       $request->input('ai_model',       ''));
            $plugin->update_option('ai_temperature', $request->input('ai_temperature', '0.3'));
        }

        return back()->with('success', __('Settings saved.'));
    }

    public function fetchTranslations(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => 'required|string|max:64',
            'model_id'   => 'required|integer|min:1',
        ]);

        $translations = TranslationService::allLocales(
            $request->input('model_type'),
            (int) $request->input('model_id')
        );

        return response()->json($translations);
    }

    public function translate(Request $request): JsonResponse
    {
        $request->validate([
            'text'          => 'required|string|max:50000',
            'source_locale' => 'required|string|max:10',
            'target_locale' => 'required|string|max:10',
        ]);

        try {
            $creds = TranslateAiService::resolveCredentials();

            $translated = (new TranslateAiService)->translate(
                text:         $request->input('text'),
                sourceLocale: $request->input('source_locale'),
                targetLocale: $request->input('target_locale'),
                provider:     $creds['provider'],
                apiKey:       $creds['key'],
                model:        $creds['model'],
                temperature:  $creds['temperature'],
                isHtml:       (bool) $request->input('is_html', false),
            );

            return response()->json(['translated' => $translated]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
