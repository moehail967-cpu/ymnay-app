<?php


namespace Plugins\PageBuilder\Fields;


use Plugins\PageBuilder\Helpers\Traits\FieldInstanceHelper;
use Plugins\PageBuilder\PageBuilderField;

class IconPicker extends PageBuilderField
{
    use FieldInstanceHelper;

    /**
     * render field markup
     * */
    public function render()
    {
        $output = '';
        $output .= $this->field_before();
        $output .= $this->label('d-block');

        $value = $this->value();

        $output .= '<div class="tw-iconpicker-wrap">';
        $output .= '<button type="button" class="tw-icon-preview iconpicker-component" title="' . __('Current icon') . '">'
            . '<i class="' . $value . '"></i>'
            . '</button>';
        $output .= '<button type="button" class="icp icp-dd tw-icon-choose-btn">'
            . '<i class="mdi mdi-palette-outline text-sm"></i>'
            . ' ' . __('Choose Icon')
            . '</button>';
        $output .= '</div>';
        $output .= '<input type="hidden" value="' . $value . '" name="' . $this->name() . '" class="' . $this->field_class() . '" />';

        $output .= $this->field_after();

        return $output;
    }
}
