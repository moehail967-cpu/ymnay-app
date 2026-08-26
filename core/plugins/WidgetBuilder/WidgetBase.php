<?php


namespace Plugins\WidgetBuilder;


use App\Models\Language;
use App\Models\Widgets;
use Plugins\WidgetBuilder\Traits\RenderViews;

abstract class WidgetBase
{
    use RenderViews;

    protected $args;
    protected $rand_number;

    public function __construct(array $args=[])
    {
        $defaults = [
            'id' => '',
            'name' => $this->widget_name(),
            'namespace' => static::class,
            'type' =>'',
            'order' =>'',
            'location' =>'',
            'before' => true,
            'after' => true
        ];

        $this->args = array_merge($defaults,$args);
        try {
            $this->rand_number = random_int(999, 99999);
        } catch (\Exception $e) {
        }
    }
    /**
     * enable
     * this method must have to use to show/hide widget in widget area
     * @since 1.0.1
     * */
     public function enable() : bool{
         return true;
     }
    /**
     * admin_render
     * this method must have to implement by all widget to render admin panel widget content
     * @since 1.0.0
     * */
    abstract public function admin_render();

    /**
     * frontend_render
     * this method must have to implement by all widget to render frontend widget content
     * @since 1.0.0
     * */
    abstract public function frontend_render();
    /**
     * widget_title
     * this method must have to implement by all widget to register widget title
     * @since 1.0.0
     * */
    abstract public function widget_title();
    /**
     * widget_name
     * this method must have to implement by all widget to register widget name
     * @since 1.0.0
     * */
    public function widget_name()
    {
        return substr(strrchr(get_called_class(), "\\"), 1);
    }

    /**
     * default_fields
     * this method will return all the default field required by any widget
     * @since 1.0.0
     * */
    public function default_fields()
    {
        $output = '';

        $output .= !empty($this->args['id']) ? '<input type="hidden" name="id" value="' . $this->args['id'] . '">' : '';
        $output .= '<input type="hidden" value="' . $this->args['name'] . '" name="widget_name">';
        $output .= '<input type="hidden" value="' . $this->args['type'] . '" name="widget_type">';
        $output .= '<input type="hidden" value="' . $this->args['location'] . '" name="widget_location">';
        $output .= '<input type="hidden" value="' . $this->args['order'] . '" name="widget_order">';
        $output .= '<input type="hidden" value="' . $this->args['namespace'] . '" name="namespace">';

        return $output;
    }

    /**
     * get_settings
     * this method will return all the settings value saved for widget
     * @since 1.0.0
     * */

    public function get_settings()
    {
        $widget_data = !empty($this->args['id']) ? Widgets::find($this->args['id']) : null;
        $widget_data = !empty($widget_data) ? $this->format_widget_content($widget_data?->widget_content,['class' => false]) : "";
        return json_decode($widget_data,true);
    }

    private function format_widget_content($data)
    {
        if (!$this->check_json($data))
        {
            $unserialized = unserialize($data);
            return json_encode($unserialized);
        }

        return $data;
    }

    private function check_json($data): bool
    {
        json_decode($data);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * widget_column_start / widget_column_end
     * add column markup for frontend rendering
     * @since 1.0.0
     */
    public function widget_column_start()
    {
        if (isset($this->args['column']) && $this->args['column']){
            return '<div class="col-lg-3 col-md-6">';
        }
    }

    public function widget_column_end()
    {
        if (isset($this->args['column']) && $this->args['column']){
            return '</div>';
        }
    }

    /**
     * widget_before / widget_after
     * wrap widget in frontend
     * @since 1.0.0
    */
    public function widget_before($class = null)
    {
        return $this->widget_column_start().'<div class="'.$class.' '.$this->args['location'].'-widget widget">';
    }

    public function widget_after()
    {
        return $this->widget_column_end().'</div>';
    }

    /**
     * admin_form_start / admin_form_end
     * form wrapper for admin panel widget
     * @since 1.0.0
     */
    public function admin_form_start()
    {
        return '<form method="post" action="' . route(route_prefix().'admin.widgets.' . $this->args['type']). '" enctype="multipart/form-data"><input type="hidden" value="' . csrf_token() . '" name="_token">';
    }

    public function admin_form_end()
    {
        return '</form>';
    }

    /**
     * admin_form_submit_button
     * Tailwind-styled save button for widget admin form
     * @since 1.0.0
     */
    public function admin_form_submit_button($text = null){
        $button_text = $text ?? __('Save Changes');
        return '<button class="widget_save_change_button">' . $button_text . '</button>';
    }

    /**
     * admin_language_tab
     * Render language tab navigation (Tailwind-styled, Bootstrap data attrs for JS compat)
     * @since 1.0.0
     */
    public function admin_language_tab(){
        $all_languages = Language::all();
        $output = '<nav class="tw-lang-nav"><div class="flex border-b border-gray-200 gap-0.5 overflow-x-auto" role="tablist">';
        foreach ($all_languages as $key => $lang) {
            $active_class = $key == 0
                ? 'tw-lang-tab-btn px-3 py-2 text-xs font-semibold border-b-2 border-primary text-primary -mb-px whitespace-nowrap cursor-pointer transition-colors'
                : 'tw-lang-tab-btn px-3 py-2 text-xs font-medium border-b-2 border-transparent text-gray-500 hover:text-dark -mb-px whitespace-nowrap cursor-pointer transition-colors';
            $output .= '<a class="' . $active_class . '" data-bs-toggle="tab" href="#nav-home-'. $lang->slug .$this->rand_number. '" role="tab" aria-selected="true">' . $lang->name . '</a>';
        }
        $output .= '</div></nav>';
        return $output;
    }

    /**
     * admin_language_tab_start / admin_language_tab_end
     * @since 1.0.0
     */
    public function admin_language_tab_start(){
        return '<div class="tab-content mt-4">';
    }

    public function admin_language_tab_end(){
        return '</div>';
    }

    /**
     * admin_language_tab_content_start / admin_language_tab_content_end
     * @since 1.0.0
     */
    public function admin_language_tab_content_start($args){
        return '<div class="' . $args['class'] . '" id="'. $args['id'] .$this->rand_number .'" role="tabpanel">';
    }

    public function admin_language_tab_content_end(){
        return '</div>';
    }

    /**
     * admin_form_before / admin_form_after
     * Widget wrapper markup for admin panel (drag handle, expand/remove controls)
     * @since 1.0.0
     */
    public function admin_form_before(){
        $markup = '';
        if ($this->args['before']){
            $markup .= '<li class="ui-state-default widget-handler" data-name="'.$this->widget_name().'">';
        }
        $markup .= '<h4 class="top-part"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$this->widget_title().'</h4>
        <span class="expand"><i class="mdi mdi-chevron-down text-sm"></i></span>
        <span class="remove-widget"><i class="mdi mdi-close text-sm"></i></span>
        <div class="content-part">';
        return $markup;
    }

    public function admin_form_after(){
        $markup = '</div>';
        if ($this->args['after']){
            $markup .= '</li>';
        }
        return $markup;
    }

    /**
     * admin_media_upload_field
     * New Tailwind media uploader field for admin widget forms
     * @since 2.0.0
     */
    public function admin_media_upload_field(string $name, string $label, $value = null, string $hint = ''): string
    {
        $uploaderId = 'tw_upload_' . preg_replace('/\W/', '_', $name) . '_' . $this->rand_number;

        $imgPreview = '';
        if (!empty($value)) {
            $imgTag = render_image_markup_by_attachment_id($value, 'tw-preview-img w-24 h-24 rounded-lg object-cover border border-gray-200', 'thumb', false);
            $imgPreview = '<div class="tw-attachment-preview relative inline-block">' .
                $imgTag .
                '<button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>' .
                '</div>';
        }

        $btnLabel = !empty($value)
            ? '<i class="ti tabler-photo text-sm"></i> ' . __('Change Image')
            : '<i class="ti tabler-photo text-sm"></i> ' . __('Upload Image');

        $hintHtml = !empty($hint)
            ? '<p class="text-xs text-gray-500 mt-1">' . $hint . '</p>'
            : '';

        return '<div class="form-group">' .
            '<label>' . $label . '</label>' .
            '<div class="tw-media-upload-wrapper" id="' . $uploaderId . '">' .
            '<div class="tw-img-wrap mb-2">' . $imgPreview . '</div>' .
            '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value ?? '') . '" class="tw-media-id-input">' .
            '<button type="button" class="tw-media-open-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-primary text-white rounded-lg hover:opacity-90 active:scale-95 transition" data-target="' . $uploaderId . '">' . $btnLabel . '</button>' .
            '</div>' .
            $hintHtml .
            '</div>';
    }

    public function repairSerializeString($value)
    {
        $data = preg_replace_callback(
            '/(?<=^|\{|;)s:(\d+):\"(.*?)\";(?=[asbdiO]\:\d|N;|\}|$)/s',
            function($m){
                return 's:' . strlen($m[2]) . ':"' . $m[2] . '";';
            },
            $value
        );

        return $data;
    }
}
