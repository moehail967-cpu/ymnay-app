<?php

namespace Modules\MultiLingual\Observers;

use Illuminate\Database\Eloquent\Model;
use Modules\MultiLingual\Services\TranslationService;

/**
 * Generic observer that hydrates translated fields on model retrieval.
 * Register once per model type; pass the model_type key in the constructor.
 *
 * Zero cost for default locale — returns immediately without a DB/cache hit.
 */
class TranslationObserver
{
    private string $modelType;
    private array  $fields;

    public function __construct(string $modelType, array $fields)
    {
        $this->modelType = $modelType;
        $this->fields    = $fields;
    }

    public function retrieved(Model $model): void
    {
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        // Skip entirely for the default locale — no overhead
        if ($locale === $fallback) {
            return;
        }

        $translations = TranslationService::forModel($this->modelType, (int) $model->getKey(), $locale);

        foreach ($this->fields as $field) {
            if (isset($translations[$field]) && $translations[$field] !== '') {
                $model->setAttribute($field, $translations[$field]);
            }
        }
    }
}
