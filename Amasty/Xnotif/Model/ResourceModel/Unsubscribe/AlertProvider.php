<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Model\ResourceModel\Unsubscribe;

class AlertProvider
{
    const STOCK_TYPE = 'stock';

    const PRICE_TYPE = 'price';

    const REMOVE_ALL = 'all';

    protected $_alertFactory = [];

    protected $_request;

    public function __construct(
        \Magento\ProductAlert\Model\PriceFactory $priceFactory,
        \Magento\ProductAlert\Model\StockFactory $stockFactory,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_alertFactory[self::PRICE_TYPE] = $priceFactory;
        $this->_alertFactory[self::STOCK_TYPE] = $stockFactory;
        $this->_request = $request;
    }

    public function getAlertModel($type, $productId, $email)
    {

        $collection = null;

        if (isset($this->_alertFactory[strtolower($type)])) {
            $collection = $this->_alertFactory[strtolower($type)]->create()->getCollection();
        }
        if (empty($collection)) {
            return null;
        }

        if (strcmp($productId, self::REMOVE_ALL) != 0) {
            $collection->addFieldToFilter('parent_id', $productId);
        }
        $collection->addFieldToFilter('email', $email);

        return $collection;
    }
}