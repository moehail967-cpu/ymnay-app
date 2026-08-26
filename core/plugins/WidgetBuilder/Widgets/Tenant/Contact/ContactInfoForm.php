<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant\Contact;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ContactInfoForm extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_contact_info_form'; }
    protected function getWidgetName(): string { return 'Contact Info & Form'; }
    protected function getWidgetIcon(): string|array { return 'las la-envelope-open-text'; }
    protected function getWidgetDescription(): string { return __('Contact info cards (icon/label/value) + embedded custom form side by side'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['contact', 'form', 'info', 'address', 'map']; }

    public function getGeneralFields(): array
    {
        $forms = [];
        try {
            if (class_exists(\App\Models\FormBuilder::class)) {
                $forms = \App\Models\FormBuilder::orderBy('id')->pluck('title', 'id')->toArray();
            }
        } catch (\Throwable $e) {}

        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title',       FieldManager::TEXT()->setLabel('Section Title')->setDefault('Get In Touch'))
            ->registerField('description', FieldManager::TEXTAREA()->setLabel('Description')->setDefault(''))
            ->registerField('info_items',  FieldManager::REPEATER()
                ->setLabel('Contact Info Items')
                ->setItemLabel('Item')
                ->setAddButtonText('Add Item')
                ->setMin(0)->setMax(6)
                ->setFields([
                    'icon'  => FieldManager::TEXT()->setLabel('Icon Class (e.g. las la-phone)')->setDefault('las la-phone'),
                    'label' => FieldManager::TEXT()->setLabel('Label')->setDefault('Phone'),
                    'value' => FieldManager::TEXT()->setLabel('Value')->setDefault('+1 234 567 890'),
                ])
                ->setDefault([
                    ['icon' => 'las la-phone',    'label' => 'Phone',   'value' => '+1 (234) 567-890'],
                    ['icon' => 'las la-envelope', 'label' => 'Email',   'value' => 'hello@example.com'],
                    ['icon' => 'las la-map-pin',  'label' => 'Address', 'value' => '123 Main St, New York, USA'],
                ])
            )
            ->registerField('form_id', FieldManager::SELECT()
                ->setLabel('Custom Form')
                ->setOptions($forms)
                ->setDefault(array_key_first($forms) ?? '')
            )
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Contact Info & Form — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $form_id      = (int) ($content['form_id'] ?? 0);
        $contact_form = null;
        try {
            if (class_exists(\App\Models\FormBuilder::class)) {
                if ($form_id > 0) {
                    $contact_form = \App\Models\FormBuilder::find($form_id);
                }
                // Fall back to first available form when none explicitly saved (default not yet persisted)
                if (!$contact_form) {
                    $contact_form = \App\Models\FormBuilder::orderBy('id')->first();
                }
            }
        } catch (\Throwable $e) {}

        return view('widgetbuilder::tenant.contact.contact-info-form', [
            'title'          => $content['title']       ?? 'Get In Touch',
            'description'    => $content['description'] ?? '',
            'info_items'     => !empty($content['info_items']) ? $content['info_items'] : [
                ['icon' => 'las la-phone',    'label' => 'Phone',   'value' => '+1 (234) 567-890'],
                ['icon' => 'las la-envelope', 'label' => 'Email',   'value' => 'hello@example.com'],
                ['icon' => 'las la-map-pin',  'label' => 'Address', 'value' => '123 Main St, New York, USA'],
            ],
            'contact_form'   => $contact_form,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
