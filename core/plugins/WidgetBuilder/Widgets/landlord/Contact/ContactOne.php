<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Contact;

use App\Models\FormBuilder;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ContactOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'contact_one';
    }

    protected function getWidgetName(): string
    {
        return 'Contact One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-envelope';
    }

    protected function getWidgetDescription(): string
    {
        return 'Contact page with form and contact info cards';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['contact', 'form', 'email', 'phone'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $forms = FormBuilder::all(['id', 'title'])
            ->mapWithKeys(fn($f) => [(string) $f->id => "$f->title"])
            ->toArray();

        $control->addGroup('form', 'Contact Form')
            ->registerField('form_id', FieldManager::SELECT()
                ->setLabel('Select Form')
                ->setDescription('Choose a form from Form Builder (Admin › Form Builder)')
                ->setOptions($forms ?: ['' => 'No forms found — create one first'])
                ->setSearchable(true)
            )
            ->endGroup();

        $control->addGroup('info_cards', 'Contact Info Cards')
            ->registerField('cards', FieldManager::REPEATER()
                ->setLabel('Info Cards')
                ->setItemLabel('Card')
                ->setAddButtonText('Add Card')
                ->setMin(1)
                ->setMax(4)
                ->setFields([
                    'icon' => FieldManager::IMAGE()
                        ->setLabel('Icon'),
                    'title' => FieldManager::TEXT()
                        ->setLabel('Title')
                        ->setDefault('Phone Number'),
                    'info_lines' => FieldManager::TEXTAREA()
                        ->setLabel('Info Lines (one per line)')
                        ->setDefault('+556 455 84846'),
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('section_bg', FieldManager::COLOR()
                ->setLabel('Section Background')
                ->setDefault('#f9fafb')
            )
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(56)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(56)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->endGroup();

        return $control->getFields();
    }

    private function resolveImageUrl($value): string
    {
        if (is_array($value)) {
            return $value['url'] ?? '';
        }
        return is_string($value) ? $value : '';
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];

        // Form settings
        $formGroup = $general['form'] ?? [];
        $formId = (int) ($formGroup['form_id'] ?? 0);

        // Info cards
        $infoGroup = $general['info_cards'] ?? [];
        $cardItems = $infoGroup['cards'] ?? [];

        $cards = [];
        foreach ($cardItems as $item) {
            $infoRaw = $item['info_lines'] ?? '';
            $infoLines = array_filter(array_map('trim', explode("\n", $infoRaw)));

            $cards[] = [
                'icon' => $this->resolveImageUrl($item['icon'] ?? ''),
                'title' => $item['title'] ?? '',
                'info_lines' => $infoLines,
            ];
        }

        return view('widgetbuilder::landlord.contact.contact', [
            'formId' => $formId,
            'cards'  => $cards,
        ])->render();
    }
}
