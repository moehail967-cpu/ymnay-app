<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Contact;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ContactTwo extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'contact_two';
    }

    protected function getWidgetName(): string
    {
        return 'Contact Two';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-paper-plane';
    }

    protected function getWidgetDescription(): string
    {
        return 'Simple contact form with configurable fields, direct email delivery, and contact info cards';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['contact', 'form', 'email', 'simple'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('form_fields', 'Form Fields')
            ->registerField('full_name_label', FieldManager::TEXT()
                ->setLabel('Full Name Label')
                ->setDefault('Full Name')
            )
            ->registerField('email_label', FieldManager::TEXT()
                ->setLabel('Email Label')
                ->setDefault('Email Address')
            )
            ->registerField('phone_label', FieldManager::TEXT()
                ->setLabel('Phone Label')
                ->setDefault('Phone Number')
            )
            ->registerField('subject_label', FieldManager::TEXT()
                ->setLabel('Subject Label')
                ->setDefault('Subject')
            )
            ->registerField('message_label', FieldManager::TEXT()
                ->setLabel('Message Label')
                ->setDefault('Message')
            )
            ->registerField('submit_text', FieldManager::TEXT()
                ->setLabel('Submit Button Text')
                ->setDefault('Send Message')
            )
            ->endGroup();

        $control->addGroup('notification', 'Notification')
            ->registerField('recipient_email', FieldManager::EMAIL()
                ->setLabel('Recipient Email')
                ->setDescription('Email address that will receive contact form submissions')
                ->setDefault('')
            )
            ->registerField('success_message', FieldManager::TEXT()
                ->setLabel('Success Message')
                ->setDefault('Thanks for your message! We will get back to you soon.')
            )
            ->endGroup();

        $control->addGroup('info_cards', 'Contact Info Cards')
            ->registerField('cards', FieldManager::REPEATER()
                ->setLabel('Info Cards')
                ->setItemLabel('Card')
                ->setAddButtonText('Add Card')
                ->setMin(0)
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

        $fieldsGroup = $general['form_fields'] ?? [];
        $fullNameLabel  = $fieldsGroup['full_name_label'] ?? 'Full Name';
        $emailLabel     = $fieldsGroup['email_label']     ?? 'Email Address';
        $phoneLabel     = $fieldsGroup['phone_label']     ?? 'Phone Number';
        $subjectLabel   = $fieldsGroup['subject_label']   ?? 'Subject';
        $messageLabel   = $fieldsGroup['message_label']   ?? 'Message';
        $submitText     = $fieldsGroup['submit_text']     ?? 'Send Message';

        $notifGroup      = $general['notification'] ?? [];
        $recipientEmail  = $notifGroup['recipient_email']  ?? '';
        $successMessage  = $notifGroup['success_message']  ?? 'Thanks for your message! We will get back to you soon.';

        $infoGroup  = $general['info_cards'] ?? [];
        $cardItems  = $infoGroup['cards']    ?? [];

        $cards = [];
        foreach ($cardItems as $item) {
            $infoRaw   = $item['info_lines'] ?? '';
            $infoLines = array_filter(array_map('trim', explode("\n", $infoRaw)));
            $cards[] = [
                'icon'       => $this->resolveImageUrl($item['icon'] ?? ''),
                'title'      => $item['title']      ?? '',
                'info_lines' => $infoLines,
            ];
        }

        return view('widgetbuilder::landlord.contact.contact_two', [
            'fullNameLabel'  => $fullNameLabel,
            'emailLabel'     => $emailLabel,
            'phoneLabel'     => $phoneLabel,
            'subjectLabel'   => $subjectLabel,
            'messageLabel'   => $messageLabel,
            'submitText'     => $submitText,
            'recipientEmail' => $recipientEmail,
            'successMessage' => $successMessage,
            'cards'          => $cards,
        ])->render();
    }
}
