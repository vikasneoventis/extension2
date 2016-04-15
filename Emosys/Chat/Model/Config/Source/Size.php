<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Chat
 */
namespace Emosys\Chat\Model\Config\Source;

class Size implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'small', 'label' => __('Simple')],
            ['value' => 'medium', 'label' => __('Advanced')],
            ['value' => 'large', 'label' => __('Large')]
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
            'small' => __('Simple'),
            'medium' => __('Advanced'),
            'large' => __('Large')
        ];
    }
}
