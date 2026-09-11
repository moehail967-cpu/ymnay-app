<?php

namespace Plugins\WidgetBuilder\Widgets;

use Xgenious\PageBuilder\Widgets\Interactive\TabsWidget as VendorTabsWidget;

class TabsWidget extends VendorTabsWidget
{
    private static bool $cssInjected = false;

    public function render(array $settings = []): string
    {
        // Vendor reads $general['tabs_list'] but fields are stored under group 'tabs'
        // → $general['tabs']['tabs_list']. Flatten before reading.
        if (isset($settings['general']['tabs']) && is_array($settings['general']['tabs'])) {
            $settings['general'] = array_merge($settings['general'], $settings['general']['tabs']);
        }

        $general   = $settings['general'] ?? [];
        $tabsList  = $general['tabs_list'] ?? [];
        $activeTab = (int) ($general['active_tab'] ?? 1);

        // Fall back to defaults when the editor hasn't saved settings yet
        if (empty($tabsList)) {
            $tabsList = [
                ['title' => 'Tab 1', 'content' => 'Content for tab 1'],
                ['title' => 'Tab 2', 'content' => 'Content for tab 2'],
                ['title' => 'Tab 3', 'content' => 'Content for tab 3'],
            ];
        }

        $tabHeaders  = '';
        $tabContents = '';

        foreach ($tabsList as $index => $tab) {
            $tabIndex    = $index + 1;
            $isActive    = $tabIndex === $activeTab;
            $activeClass = $isActive ? ' active' : '';
            $display     = $isActive ? 'block' : 'none';

            $title   = htmlspecialchars($tab['title'] ?? 'Tab ' . $tabIndex, ENT_QUOTES, 'UTF-8');
            $content = $tab['content'] ?? '';

            $tabHeaders  .= "<div class=\"tab-header{$activeClass}\" data-tab=\"{$tabIndex}\">{$title}</div>";
            $tabContents .= "<div class=\"tab-content\" data-tab=\"{$tabIndex}\" style=\"display:{$display}\">{$content}</div>";
        }

        $css = '<style>'
            . '.tabs-widget .tabs-headers{display:flex;flex-wrap:wrap;border-bottom:2px solid #dee2e6;gap:0}'
            . '.tabs-widget .tab-header{padding:10px 20px;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;font-weight:500;color:#555;transition:color .2s,border-color .2s}'
            . '.tabs-widget .tab-header:hover{color:#007cba}'
            . '.tabs-widget .tab-header.active{color:#007cba;border-bottom-color:#007cba}'
            . '.tabs-widget .tabs-contents{padding:20px 0}'
            . '</style>';

        $js = '<script>'
            . '(function(){'
            . 'if(window.__xgpbTabsInit)return;'
            . 'window.__xgpbTabsInit=true;'
            . 'document.addEventListener("click",function(e){'
            . 'var h=e.target.closest(".tab-header");if(!h)return;'
            . 'var w=h.closest(".tabs-widget");if(!w)return;'
            . 'var i=h.getAttribute("data-tab");'
            . 'w.querySelectorAll(".tab-header").forEach(function(x){x.classList.remove("active");});'
            . 'w.querySelectorAll(".tab-content").forEach(function(x){x.style.display="none";});'
            . 'h.classList.add("active");'
            . 'var c=w.querySelector(".tab-content[data-tab=\""+i+"\"]");'
            . 'if(c)c.style.display="block";'
            . '});'
            . '})();'
            . '</script>';

        return $css
            . "<div class=\"tabs-widget\">"
            . "<div class=\"tabs-headers\">{$tabHeaders}</div>"
            . "<div class=\"tabs-contents\">{$tabContents}</div>"
            . "</div>"
            . $js;
    }
}
