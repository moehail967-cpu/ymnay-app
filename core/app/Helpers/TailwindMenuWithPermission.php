<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class TailwindMenuWithPermission
{
    private array $parents = [];
    private array $menus_items = [];

    // Stored but not used — the Tailwind renderer uses its own markup.
    // This method exists so SidebarMenuHelper can call it for any theme.
    public function init(
        string $ul_class = '',
        string $li_class = '',
        string $anchor_class = '',
        string $sub_menu_class = '',
        string $anchor_title_class = ''
    ): void {
        // no-op for Tailwind; config is hardcoded in render methods
    }

    public function add_menu_item(string $id, array $args = []): void
    {
        if (empty($args)) {
            return;
        }

        $this->menus_items[$id] = [
            'route' => $args['route'],
            'label' => $args['label'],
            'parent' => $args['parent'],
            'class' => $args['class'] ?? '',
            'permissions' => $args['permissions'] ?? [],
            'icon' => $args['icon'] ?? '',
        ];

        if (isset($args['parent']) && !empty($args['parent'])) {
            $this->parents[] = $args['parent'];
        }
    }

    public function render_menu_items(): string
    {
        $output = '';

        foreach ($this->menus_items as $id => $item) {
            if (!$this->has_permission_to_view($item)) {
                continue;
            }

            if (!$this->has_route($item) && !$this->has_sub_menu_item($id)) {
                abort(405, 'Route name required to render menu');
            }

            if ($this->is_submenu_item($item)) {
                continue;
            }

            $output = $this->render_menu_item($id, $item, $output);
        }

        return $output;
    }

    private function render_menu_item(string $id, array $item, string $output): string
    {
        $searchableText = strtolower($item['label']);
        $isActive = $this->is_active_menu_item($item);
        $hasChildren = $this->has_sub_menu_item($id);
        $slug = Str::slug(strip_tags($id));

        $hasActiveChild = false;
        if ($hasChildren) {
            foreach ($this->get_all_submenu_by_parent_id($id) as $sub) {
                if ($this->is_active_menu_item($sub)) {
                    $hasActiveChild = true;
                    break;
                }
            }
        }

        $isParentActive = $isActive || $hasActiveChild;

        $output .= '<li class="nav-item menu-item ' . ($item['class'] ?? '') . '"';
        $output .= ' data-menu-text="' . htmlspecialchars($searchableText) . '">';

        try {
            $route = route($item['route']);
        } catch (\Throwable) {
            $route = '#';
        }

        if ($hasChildren) {
            $btnClasses = 'w-[calc(100%-1.5rem)] mx-3 mb-1 flex items-center gap-3 py-1 px-4 text-[13px] transition-all duration-200 rounded-lg relative';
            if ($isParentActive) {
                $btnClasses .= ' text-brand bg-primary-soft font-semibold';
            } else {
                $btnClasses .= ' text-muted font-medium hover:text-brand hover:bg-primary-soft';
            }

            $output .= '<button type="button" onclick="toggleSubmenu(this)" class="' . $btnClasses . '">';

            if ($this->has_icon($item)) {
                $iconClass = $isParentActive ? 'text-brand' : 'text-subtle';
                $output .= '<i class="' . $item['icon'] . ' text-[18px] ' . $iconClass . '"></i>';
            }

            $output .= '<span class="menu-title sidebar-label flex-1 text-left">' . $item['label'] . '</span>';

            $arrowRotate = $hasActiveChild ? ' rotate-180' : '';
            $output .= '<i class="mdi mdi-chevron-down text-xs text-subtle transition-transform duration-200 submenu-arrow' . $arrowRotate . '"></i>';
            $output .= '</button>';

            $output = $this->render_sub_menus($id, $output, $hasActiveChild);

        } else {
            $linkClasses = 'w-[calc(100%-1.5rem)] mx-3 mb-1 flex items-center gap-3 py-1 px-4 text-[13px] transition-all duration-200 rounded-lg relative';
            if ($isActive) {
                $linkClasses .= ' text-brand bg-primary-soft font-semibold';
            } else {
                $linkClasses .= ' text-muted font-medium hover:text-brand hover:bg-primary-soft';
            }

            $output .= '<a href="' . $route . '" class="' . $linkClasses . '">';

            if ($this->has_icon($item)) {
                $iconClass = $isActive ? 'text-brand' : 'text-subtle';
                $output .= '<i class="' . $item['icon'] . ' text-[18px] ' . $iconClass . '"></i>';
            }

            $output .= '<span class="menu-title sidebar-label">' . $item['label'] . '</span>';

            $output .= '</a>';
        }

        $output .= '</li>';

        return $output;
    }

    private function render_sub_menus(string $id, string $output, bool $hasActiveChild): string
    {
        $all_menu_items = $this->get_all_submenu_by_parent_id($id);

        $showClass = $hasActiveChild ? '' : ' hidden';
        $output .= '<div class="submenu-collapse ml-[52px] space-y-0.5' . $showClass . '" id="' . Str::slug(strip_tags($id)) . '">';
        $output .= '<ul class="space-y-0.5 border-l border-main pl-3">';

        foreach ($all_menu_items as $submenu_id => $sub_menu) {
            if (!$this->has_permission_to_view($sub_menu)) {
                continue;
            }
            if (!$this->has_route($sub_menu)) {
                continue;
            }

            $isSubActive = $this->is_active_menu_item($sub_menu);
            try {
                $subRoute = route($sub_menu['route']);
            } catch (\Throwable) {
                $subRoute = '#';
            }

            $subClasses = 'flex items-center gap-2 px-3 py-2 text-[13px] transition-all duration-200 rounded-md';
            if ($isSubActive) {
                $subClasses .= ' text-brand font-semibold';
            } else {
                $subClasses .= ' text-muted hover:text-brand hover:bg-primary-soft';
            }

            $output .= '<li class="nav-item menu-item" data-menu-text="' . htmlspecialchars(strtolower($sub_menu['label'])) . '">';

            if ($isSubActive) {
                $output .= '<a href="' . $subRoute . '" class="' . $subClasses . '">';
                $output .= '<span class="w-1.5 h-1.5 rounded-full bg-brand flex-shrink-0"></span>';
            } else {
                $output .= '<a href="' . $subRoute . '" class="' . $subClasses . '">';
                $output .= '<span class="w-1 h-1 rounded-full bg-subtle flex-shrink-0"></span>';
            }

            $output .= '<span class="menu-title">' . $sub_menu['label'] . '</span>';
            $output .= '</a>';
            $output .= '</li>';
        }

        $output .= '</ul></div>';

        return $output;
    }

    // ===== Helper Methods =====

    private function is_active_menu_item(array $item): bool
    {
        if (!$this->has_route($item)) {
            return false;
        }
        return (bool) request()->routeIs($item['route']);
    }

    private function has_permission_to_view(array $item): bool
    {
        $permissions = $item['permissions'];
        $admin_details = auth('admin')->user();

        if (empty($permissions)) {
            return true;
        }

        if (is_array($permissions)) {
            $filtered = array_filter($permissions, fn($p) => !empty($p));
            if (empty($filtered)) {
                return true;
            }
            return (bool) optional($admin_details)->canany($filtered);
        }
        if (is_string($permissions)) {
            return (bool) optional($admin_details)->can($permissions);
        }
        return false;
    }

    private function has_route(array $item): bool
    {
        return isset($item['route']) && !empty($item['route']);
    }

    private function has_icon(array $item): bool
    {
        return isset($item['icon']) && !empty($item['icon']);
    }

    private function has_sub_menu_item(string $id): bool
    {
        return in_array($id, $this->parents);
    }

    private function is_submenu_item(array $item): bool
    {
        return isset($item['parent']) && !is_null($item['parent']);
    }

    private function get_all_submenu_by_parent_id(string $id): array
    {
        $result = [];
        foreach ($this->menus_items as $item_id => $item) {
            if (isset($item['parent']) && $item['parent'] === $id) {
                $result[$item_id] = $item;
            }
        }
        return $result;
    }
}
