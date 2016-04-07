<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;

use Magento\Framework\Api\AttributeValueFactory;

class Product extends \Magento\Catalog\Model\Product
{
    public function getCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get(
            'Amasty\Xnotif\Model\ResourceModel\Product\Collection'
        );
    }

    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\ResourceModel\Product');
    }

}