<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Model\ResourceModel\Price\Customer;

class Collection extends \Magento\ProductAlert\Model\ResourceModel\Price\Customer\Collection
{
    protected $_customerModeFlag = false;


    public function join($productId, $websiteId)
    {

        $this->getSelect()->joinRight(
            ['alert' => $this->getTable('product_alert_price')],
            'alert.customer_id=e.entity_id',
            ['add_date', 'last_send_date', 'send_count', 'status',
                'guest_email' => 'email']
        )
            ->reset(\Zend_Db_Select::WHERE)
            ->where('alert.product_id=?', $productId)
            ->group('alert.email')->group('e.email');
        if ($websiteId) {
            $this->getSelect()->where('alert.website_id=?', $websiteId);
        }
        $this->_setIdFieldName('alert_price_id');
        $this->addAttributeToSelect('*');
        $this->setIsCustomerMode(true);

        return $this;
    }

    /**
     * Set customer mode flag value
     *
     * @param bool $value
     *
     * @return Mage_Sales_Model_ResourceModel_Order_Grid_Collection
     */
    public function setIsCustomerMode($value)
    {
        $this->_customerModeFlag = (bool)$value;
        return $this;
    }

    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->_orders[$field] = $direction;

        if ($field == "email") {
            $field = "guest_email";
        }
        $this->getSelect()->order($field . ' ' . $direction);

        return $this;
    }

    public function getSelectCountSql()
    {
        if ($this->getIsCustomerMode()) {
            $this->_renderFilters();

            $unionSelect = clone $this->getSelect();

            $unionSelect->reset(\Zend_Db_Select::COLUMNS);
            $unionSelect->columns('e.entity_id');

            $unionSelect->reset(\Zend_Db_Select::ORDER);
            $unionSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
            $unionSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);

            $countSelect = clone $this->getSelect();
            $countSelect->reset();
            $countSelect->from(['a' => $unionSelect], 'COUNT(*)');
        } else {
            $countSelect = parent::getSelectCountSql();
        }

        return $countSelect;
    }

    /**
     * Get customer mode flag value
     *
     * @return bool
     */
    public function getIsCustomerMode()
    {
        return $this->_customerModeFlag;
    }
}
