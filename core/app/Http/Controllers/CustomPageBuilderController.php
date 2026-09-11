<?php

namespace App\Http\Controllers;

use App\Facades\ThemeDataFacade;
use Xgenious\PageBuilder\Http\Controllers\PageBuilderUIController;

class CustomPageBuilderController extends PageBuilderUIController
{
    public function edit(int $pageId)
    {
        if (!is_null(tenant())) {
            $this->injectTenantThemeCss();
        }

        return parent::edit($pageId);
    }

    private function injectTenantThemeCss(): void
    {
        $slug = tenant()->theme_slug ?? 'default';
        $css = [];
        $imports = [];

        // Base tenant frontend CSS (matches what header.blade.php loads)
        $basePaths = [
            'assets/tenant/frontend/css/bootstrap.min.css',
            'assets/tenant/frontend/css/common.css',
            'assets/tenant/frontend/css/helpers.css',
        ];
        foreach ($basePaths as $path) {
            if (file_exists(public_path($path))) {
                $css[] = $path;
            }
        }

        // Theme-specific CSS files declared in the theme's headerHook
        // getHeaderHookCssFiles() returns names like ['style', 'pages']
        $themeFiles = ThemeDataFacade::getHeaderHookCssFiles();
        foreach ($themeFiles as $name) {
            $path = "themes/{$slug}/css/{$name}.css";
            if (file_exists(public_path($path))) {
                $css[] = $path;
                // Extract @import URLs (Google Fonts, icon CDNs) — vendor JS strips these
                array_push($imports, ...$this->extractImports($path));
            }
        }

        config(['xgpagebuilder.editor_frontend_css' => $css]);

        // Extract :root {} CSS variables from the first theme CSS file.
        // The scoper's rule.style.cssText may not serialize custom properties,
        // so we inject them directly as a non-scoped <style> block so var(--*)
        // references resolve correctly in the canvas.
        $rootVarsCss = '';
        if (!empty($themeFiles)) {
            $firstCssPath = "themes/{$slug}/css/{$themeFiles[0]}.css";
            $rootVarsCss  = $this->extractRootVars($firstCssPath);
        }

        config([
            'xgpagebuilder.editor_head_imports'       => array_unique($imports),
            'xgpagebuilder.editor_root_vars_css'      => $rootVarsCss,
            'xgpagebuilder.editor_css_variables_view' => 'page-builder-tenant-imports',
        ]);

        // Override media routes to tenant-scoped URLs so the image widget
        // doesn't CORS-fail by posting to the landlord domain
        config([
            'xgpagebuilder.media.upload_route'  => 'tenant.admin.upload.media.file',
            'xgpagebuilder.media.library_route' => 'tenant.admin.upload.media.file.all',
            'xgpagebuilder.media.delete_route'  => 'tenant.admin.upload.media.file.delete',
        ]);
    }

    private function extractRootVars(string $relativePath): string
    {
        $file = public_path($relativePath);
        if (!file_exists($file)) {
            return '';
        }

        $css = file_get_contents($file);
        // Match the first :root { ... } block (handles multiline)
        if (preg_match('/:root\s*\{([^}]+)\}/s', $css, $matches)) {
            return ':root {' . $matches[1] . '}';
        }

        return '';
    }

    private function extractImports(string $relativePath): array
    {
        $file = public_path($relativePath);
        if (!file_exists($file)) {
            return [];
        }

        $css = file_get_contents($file);
        preg_match_all('/@import\s+url\(\s*[\'"]?([^\'")\s]+)[\'"]?\s*\)\s*;/i', $css, $matches);

        return $matches[1] ?? [];
    }
}
