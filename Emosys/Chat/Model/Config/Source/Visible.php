<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Chat
 */
namespace Emosys\Chat\Model\Config\Source;

class Visible implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'show', 'label' => __('Show')],
            ['value' => 'hide', 'label' => __('Hide')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'small' => __('Show'),
            'hide' => __('Hide')
        ];
    }
}
