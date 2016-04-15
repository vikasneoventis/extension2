<?php
/**
 * @author Emosys team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Model\ResourceModel\Photo;

/**
 * CMS Block Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'photo_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Emosys\Banner\Model\Photo', 'Emosys\Banner\Model\ResourceModel\Photo');
    }

    /**
     * Returns pairs block_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('photo_id', 'title');
    }

    /**
     * Filter by item
     *
     * @param int|array|\Emosys\Banner\Model\Item $item
     * @return void
     */
    public function addItemFilter($item)
    {
        if ($item instanceof \Emosys\Banner\Model\Item) {
            $item = [$item->getId()];
        }

        if (!is_array($item)) {
            $item = [$item];
        }

        $this->addFilter('item_id', ['in' => $item], 'public');
    }
}
