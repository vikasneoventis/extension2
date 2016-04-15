<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Item
 */
class Item implements OptionSourceInterface
{
    /**
     * @var \Emosys\Banner\Model\Item
     */
    protected $emosysBannerItem;

    /**
     * Constructor
     *
     * @param \Emosys\Banner\Model\Item $emosysBannerItem
     */
    public function __construct(\Emosys\Banner\Model\Item $emosysBannerItem)
    {
        $this->emosysBannerItem = $emosysBannerItem;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->emosysBannerItem->getResourceCollection();
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
