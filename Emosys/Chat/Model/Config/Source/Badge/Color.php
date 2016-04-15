<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Chat
 */
namespace Emosys\Chat\Model\Config\Source\Badge;

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
            ['value' => 'badge_color_primary', 'label' => __('Use primary color')],
            ['value' => 'badge_color_customized', 'label' => __('Use custom color')]
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
            'badge_color_primary' => __('Use primary color'),
            'badge_color_customized' => __('Use custom color')
        ];
    }
}
