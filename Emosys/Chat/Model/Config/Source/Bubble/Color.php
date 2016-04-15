<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Chat
 */
namespace Emosys\Chat\Model\Config\Source\Bubble;

class Color implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'bubble_color_primary', 'label' => __('Use primary color')],
            ['value' => 'bubble_color_customized', 'label' => __('Use custom color')]
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
            'bubble_color_primary' => __('Use primary color'),
            'bubble_color_customized' => __('Use custom color')
        ];
    }
}
