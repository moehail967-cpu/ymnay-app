<?php


namespace Plugins\PageBuilder\Fields;


use Plugins\PageBuilder\Helpers\Traits\FieldInstanceHelper;
use Plugins\PageBuilder\PageBuilderField;

class Image extends PageBuilderField
{
    use FieldInstanceHelper;

    /**
     * render field markup
     * */
    public function render()
    {
        $output = '';
        $output .= $this->field_before();
        $output .= $this->label();

        $uploaderId  = 'tw_img_' . preg_replace('/\W/', '_', $this->name()) . '_' . substr(md5(uniqid()), 0, 6);
        $value       = $this->value();
        $imgPreview  = '';

        if (!empty($value)) {
            $img = get_attachment_image_by_id($value, 'medium', false);
            if (!empty($img)) {
                $imgPreview = '<div class="tw-attachment-preview relative inline-block">'
                    . '<img class="tw-preview-img w-24 h-24 rounded-lg object-cover border border-gray-200" src="' . $img['img_url'] . '" />'
                    . '<button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>'
                    . '</div>';
            }
        }

        $btnLabel = !empty($value)
            ? '<i class="ti tabler-photo text-sm"></i> ' . __('Change Image')
            : '<i class="ti tabler-photo text-sm"></i> ' . __('Upload Image');

        $output .= '<div class="tw-media-upload-wrapper" id="' . $uploaderId . '">';
        $output .= '<div class="tw-img-wrap mb-2">' . $imgPreview . '</div>';
        $output .= '<input type="hidden" value="' . $value . '" name="' . $this->name() . '" class="tw-media-id-input ' . $this->field_class() . '" />';
        $output .= '<button type="button" class="tw-media-open-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-primary text-white rounded-lg hover:opacity-90 active:scale-95 transition" data-target="' . $uploaderId . '">' . $btnLabel . '</button>';
        $output .= '</div>';

        if (isset($this->args['dimensions'])) {
            $output .= '<p class="text-xs text-gray-500 mt-1">' . __('Recommended size:') . ' ' . $this->args['dimensions'] . '</p>';
        }

        $output .= $this->field_after();

        return $output;
    }
}
