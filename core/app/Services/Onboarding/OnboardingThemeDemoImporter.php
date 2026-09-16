<?php

namespace App\Services\Onboarding;

use App\Services\ThemeDemoImporter;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Same importer methods/order, but onboarding must not swallow partial theme-import failures. */
class OnboardingThemeDemoImporter extends ThemeDemoImporter
{
    public function import(): array
    {
        $path = $this->themeBase . '/demo/data.json';
        if (!is_file($path)) throw new RuntimeException('The selected theme has no importable demo data.');
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (empty($data['sections']) || !is_array($data['sections'])) {
            throw new RuntimeException('The selected theme demo data is invalid.');
        }
        $sections = $data['sections'];
        $media = $sections['media'] ?? [];
        foreach ($media as $item) {
            if (!empty($item['import_as']) && (empty($item['path']) || !is_file($this->themeBase . '/' . $item['path']))) {
                throw new RuntimeException('A required theme demo asset is missing.');
            }
        }
        $map = $this->importMedia($media);
        foreach ($media as $item) {
            if (!empty($item['import_as']) && !isset($map[$item['import_as']])) {
                throw new RuntimeException('A required theme demo asset could not be imported.');
            }
        }
        $this->importSettings($sections['settings'] ?? [], $map);
        DB::transaction(function () use ($sections, $map) {
            $this->importCategories($sections['categories'] ?? [], $map);
            $this->importProducts($sections['products'] ?? [], $map);
            $this->importBrands($sections['brands'] ?? [], $map);
            $this->importMenus($sections['menus'] ?? []);
        });
        DB::transaction(fn () => $this->importPages($sections['pages'] ?? [], $map));
        $this->importWidgets($sections['widgets'] ?? []);

        return ['status' => true, 'msg' => __('Theme demo data imported successfully.')];
    }
}
