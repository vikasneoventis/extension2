<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\Core\Model\Config\Source\Notification;

/**
 * Notification type source
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'update',
                'label' => __('Extension Updates')
            ],
            [
                'value' => 'marketing',
                'label' => __('Marketing Information')
            ],
            [
                'value' => 'info',
                'label' => __('Other Information')
            ]
        ];
    }
}
