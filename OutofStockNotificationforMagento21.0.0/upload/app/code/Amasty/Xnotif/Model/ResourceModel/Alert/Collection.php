<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Model\ResourceModel\Alert;

class Collection
    extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function getSelectCountSql()
    {
        $select = clone $this->getSelect();
        $select->reset(\Zend_Db_Select::ORDER);
        return $this->getConnection()->select()->from($select, 'COUNT(*)');

    }

    protected function _initSelect()
    {
        //   parent::_initSelect();

        $this->addAttributeToSelect('name');
        $select = $this->getSelect();
        $productTable = Mage::getSingleton('core/resource')->getTableName(
            'catalog/product_entity'
        );
        $select->joinInner(
            ['s' => $productTable],
            'e.product_id = s.entity_id',
            ['cnt' => 'count(e.product_id)',
                'last_d' => 'MAX(add_date)',
                'first_d' => 'MIN(add_date)',
                'product_id',
                'website_id'])
            ->where('send_count=0')
            ->group(['e.website_id', 'e.product_id']);
        return $this;
    }
}
  