<?php

namespace App\PluginSystem;

class MenuRegistry
{
    private array $menus    = [];
    private array $submenus = [];

    public function addMenu(array $config): void
    {
        $config['order']   = $config['order'] ?? 50;
        $config['context'] = $config['context'] ?? 'both';
        $this->menus[$config['id']] = $config;
    }

    public function addSubmenu(string $parent_id, array $config): void
    {
        $this->submenus[$parent_id][] = $config;
    }

    public function getMenus(string $context): array
    {
        $menus = array_filter($this->menus, function ($menu) use ($context) {
            return $menu['context'] === 'both' || $menu['context'] === $context;
        });

        usort($menus, fn($a, $b) => $a['order'] <=> $b['order']);

        foreach ($menus as &$menu) {
            $menu['children'] = $this->submenus[$menu['id']] ?? [];
        }

        return $menus;
    }
}
