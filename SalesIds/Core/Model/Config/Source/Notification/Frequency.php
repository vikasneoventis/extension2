<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\Core\Model\Config\Source\Notification;

/**
 * Notification update frequency source
 */
class Frequency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            1  => __('1 Hour'),
            2  => __('2 Hours'),
            6  => __('6 Hours'),
            12 => __('12 Hours'),
            24 => __('24 Hours')
        ];
    }
}
