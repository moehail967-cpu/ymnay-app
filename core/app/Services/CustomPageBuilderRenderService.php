<?php

namespace App\Services;

use Xgenious\PageBuilder\Services\PageBuilderRenderService;

/**
 * Extends the vendor PageBuilderRenderService to fix column width rendering.
 *
 * The editor saves column widths as percentage strings ("33.33%") under the key `width`,
 * but the vendor's renderColumn() reads `$column['size']` (an integer for a 12-col grid).
 * Since renderColumn() is private and cannot be overridden, we instead append per-column
 * width CSS rules keyed by the unique `.pb-column-{id}` selector after rendering.
 */
class CustomPageBuilderRenderService extends PageBuilderRenderService
{
    public function renderPage($page, $css = false): mixed
    {
        // Always get CSS from parent so we can append column width overrides.
        $result = parent::renderPage($page, true);

        if (is_array($result) && $page->use_page_builder && $page->pageBuilderContent) {
            $columnCss = $this->buildColumnWidthCss(
                $page->pageBuilderContent->content ?? []
            );
            if ($columnCss) {
                $result['css'] = $columnCss . ($result['css'] ?? '');
            }
        }

        // Honour the original $css flag — callers that pass false don't expect a css key.
        if (!$css && is_array($result)) {
            unset($result['css']);
        }

        return $result;
    }

    private function buildColumnWidthCss(array $content): string
    {
        $css = '';
        foreach ($content['containers'] ?? [] as $container) {
            foreach ($container['columns'] ?? [] as $column) {
                if (isset($column['width']) && !isset($column['size'])) {
                    $id    = htmlspecialchars($column['id'], ENT_QUOTES, 'UTF-8');
                    $width = htmlspecialchars($column['width'], ENT_QUOTES, 'UTF-8');
                    $css  .= ".pb-column-{$id}{width:{$width}!important;}";
                }
            }
        }
        return $css;
    }
}
