<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Chat
 */
namespace Emosys\Chat\Model\Config\Source;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'image_right', 'label' => __('Image Right')],
            ['value' => 'image_left', 'label' => __('Image Left')],
            ['value' => 'image_only', 'label' => __('Image Only')],
            ['value' => 'text_only', 'label' => __('Text Only')]
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
            'image_right' => __('Image Right'),
            'image_left' => __('Image Left'),
            'image_only' => __('Image Only'),
            'text_only' => __('Text Only'),
        ];
    }
}
