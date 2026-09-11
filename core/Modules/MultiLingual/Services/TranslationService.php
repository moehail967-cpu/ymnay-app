<?php

namespace Modules\MultiLingual\Services;

use Illuminate\Support\Facades\Cache;
use Modules\MultiLingual\Models\MlTranslation;

class TranslationService
{
    // Request-level static cache keyed by "tenantId:type:locale"
    private static array $requestCache = [];

    // ── Bulk load ─────────────────────────────────────────────────────────────

    /**
     * Load all translations for {tenant, type, locale} in one query.
     * Result is stored in a static cache for the lifetime of the request,
     * and also persisted in the application cache (Redis/file) for 10 minutes.
     * Default locale: always returns empty (no translations stored = use model value).
     */
    private static function loadAll(int $tenantId, string $type, string $locale): void
    {
        $key = "{$tenantId}:{$type}:{$locale}";
        if (isset(self::$requestCache[$key])) {
            return;
        }

        self::$requestCache[$key] = Cache::remember(
            "ml:{$tenantId}:{$type}:{$locale}",
            600,
            function () use ($tenantId, $type, $locale) {
                $rows = MlTranslation::where('tenant_id', $tenantId)
                    ->where('model_type', $type)
                    ->where('locale', $locale)
                    ->get(['model_id', 'field', 'value']);

                $result = [];
                foreach ($rows as $row) {
                    $result[$row->model_id][$row->field] = $row->value;
                }
                return $result;
            }
        );
    }

    // ── Read API ──────────────────────────────────────────────────────────────

    /**
     * Get all translated fields for a single model in the given locale.
     * Returns [] when locale is the fallback locale (caller should use model values).
     */
    public static function forModel(string $type, int $id, ?string $locale = null): array
    {
        $locale  = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        if ($locale === $fallback) {
            return [];
        }

        try {
            $tenantId = (int) tenant()->id;
        } catch (\Throwable) {
            return [];
        }

        self::loadAll($tenantId, $type, $locale);

        $key = "{$tenantId}:{$type}:{$locale}";
        return self::$requestCache[$key][$id] ?? [];
    }

    /**
     * Get a single translated field with fallback to the provided default value.
     */
    public static function get(string $type, int $id, string $locale, string $field, string $fallback = ''): string
    {
        $translations = self::forModel($type, $id, $locale);
        $value = $translations[$field] ?? null;
        return ($value !== null && $value !== '') ? $value : $fallback;
    }

    // ── Write API ─────────────────────────────────────────────────────────────

    /**
     * Save one locale's fields for a model.
     * Fields with null/empty values are skipped (not saved or deleted).
     */
    public static function saveIfPresent(string $type, int $id, string $locale, array $fields): void
    {
        try {
            $tenantId = (int) tenant()->id;
        } catch (\Throwable) {
            $tenantId = null;
        }

        foreach ($fields as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            MlTranslation::updateOrCreate(
                [
                    'tenant_id'  => $tenantId,
                    'model_type' => $type,
                    'model_id'   => $id,
                    'locale'     => $locale,
                    'field'      => $field,
                ],
                ['value' => $value]
            );
        }

        // Invalidate both cache layers
        Cache::forget("ml:{$tenantId}:{$type}:{$locale}");
        unset(self::$requestCache["{$tenantId}:{$type}:{$locale}"]);
    }

    /**
     * Delete all translations for a model (call on model delete).
     */
    public static function deleteForModel(string $type, int $id): void
    {
        try {
            $tenantId = (int) tenant()->id;
        } catch (\Throwable) {
            $tenantId = null;
        }

        MlTranslation::where('tenant_id', $tenantId)
            ->where('model_type', $type)
            ->where('model_id', $id)
            ->delete();

        // Clear all locale caches for this type
        foreach (self::$requestCache as $cacheKey => $_) {
            if (str_starts_with($cacheKey, "{$tenantId}:{$type}:")) {
                unset(self::$requestCache[$cacheKey]);
            }
        }
    }

    /**
     * Retrieve all translations for a model across all locales.
     * Returns ['en' => ['name' => '...'], 'ar' => ['name' => '...']]
     */
    public static function allLocales(string $type, int $id): array
    {
        try {
            $tenantId = (int) tenant()->id;
        } catch (\Throwable) {
            return [];
        }

        $rows = MlTranslation::where('tenant_id', $tenantId)
            ->where('model_type', $type)
            ->where('model_id', $id)
            ->get(['locale', 'field', 'value']);

        $result = [];
        foreach ($rows as $row) {
            $result[$row->locale][$row->field] = $row->value;
        }
        return $result;
    }
}
