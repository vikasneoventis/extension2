<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Emosys\Banner\Ui\Component\Listing\Column\Item;

use Magento\Framework\Escaper;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\System\Store as SystemStore;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * Item model
     *
     * @var \Emosys\Banner\Model\Item
     */
    protected $_ebItem;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param \Emosys\Banner\Model\Item $ebItem
     */
    public function __construct(\Emosys\Banner\Model\Item $ebItem)
    {
        $this->_ebItem = $ebItem;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_ebItem->getResourceCollection();
        $options = [];
        foreach ($collection as $_item) {
            $options[] = [
                'label' => $_item->getTitle(),
                'value' => $_item->getId(),
            ];
        }
        return $options;
    }
}
