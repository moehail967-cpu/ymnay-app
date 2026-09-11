<?php


namespace Plugins\PageBuilder\Fields;


use App\Facades\GlobalLanguage;
use App\Helpers\LanguageHelper;
use Plugins\PageBuilder\Helpers\RepeaterField;
use Plugins\PageBuilder\Helpers\Traits\FieldInstanceHelper;
use Plugins\PageBuilder\Helpers\Traits\LanguageTabs;
use Plugins\PageBuilder\PageBuilderField;

class Repeater extends PageBuilderField
{
    use FieldInstanceHelper;

    /**
     * render field markup
     * */
    public function render()
    {
        $output = '<div class="pb-repeater-wrap iconbox-repeater-wrapper">';
        $all_settings = $this->args['settings'];
        $this->args['settings'] = RepeaterField::remove_default_fields($all_settings);
        $repeater_id = $this->args['settings'][$this->args['id']] ?? [];
        $last_field = array_key_last($repeater_id);
        $last_field = !empty($last_field) ? $repeater_id[$last_field] : [];
        if (!empty($last_field) && is_array($last_field) && count($last_field) > 0) {
            foreach ($last_field as $index => $value) {
                $output .= $this->render_repeater_fields($index);
            }
        } else {
            $output .= $this->render_repeater_fields();
        }

        $output .= '</div>';

        return $output;
    }

    private function render_fields($fields, $settings, $index = '', $lang = null): string
    {
        $output = '';
        foreach ($fields as $field) {
            $class = 'Plugins\PageBuilder\Fields\\' . $field['type'];
            $field_name = $field['name'] . '_' . $lang;
            $value = '';

            if (isset($settings[$this->args['id']][$field_name]) && is_array($settings[$this->args['id']][$field_name])){
                $value = $settings[$this->args['id']][$field_name][$index] ?? '';
            }elseif (isset($settings[$this->args['id']][$field_name])){
                $value = $settings[$this->args['id']][$field_name];
            }

            $instance = new $class(array_merge($field,[
                'name' => $this->args['id'].'['.$field_name . '][]',
                'value' => $value
                ]));
            $output .= $instance->render();
        }
        return $output;
    }

    public function render_repeater_fields( $index = null): string
    {
        $output = '<div class="all-field-wrap pb-repeater-row">';
        $output .= '<div class="action-wrap pb-repeater-actions">'
            . '<button type="button" class="add" title="' . __('Add row') . '"><i class="mdi mdi-plus"></i></button>'
            . '<button type="button" class="remove" title="' . __('Remove row') . '"><i class="mdi mdi-delete-outline"></i></button>'
            . '</div>';

        $language_tab_init = LanguageTabs::init();
        if (isset($this->args['multi_lang']) && $this->args['multi_lang']) {
            $output .= $language_tab_init->language_tab();
            $output .= $language_tab_init->language_tab_start();

            $all_languages = GlobalLanguage::all_languages();

            foreach ($all_languages as $key => $lang) {
                $output .= $language_tab_init->language_tab_content_start([
                    'class' => $key == 0 ? 'tab-pane fade show active' : 'tab-pane fade',
                    'id' => "nav-home-" . $lang->slug
                ]);
                $output .= $this->render_fields($this->args['fields'], $this->args['settings'], $index, $lang->slug);
                $output .= $language_tab_init->language_tab_content_end();
            }

            $output .= $language_tab_init->language_tab_end();
        } else {
            $output .= $this->render_fields($this->args['fields'], $this->args['settings'],$index);
        }
        $output .= '</div>';

        return $output;
    }
}
