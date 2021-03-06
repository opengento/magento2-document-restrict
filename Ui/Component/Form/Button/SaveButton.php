<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Ui\Component\Form\Button;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

final class SaveButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {

        return [
            'label' => new Phrase('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save'
            ],
            'sort_order' => 100,
        ];
    }
}
