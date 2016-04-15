<?php

namespace Emosys\Custom\Block\Product\Liste;

class Related extends \Magento\Catalog\Block\Product\ProductList\Related {
    protected $_limit;
    /**
     * @return $this
     */
    protected function _prepareData() {
        $product = $this->_coreRegistry->registry('product');
        /* @var $product \Magento\Catalog\Model\Product */

        $this->_itemCollection = $product->getRelatedProductCollection()->addAttributeToSelect(
                        'required_options'
                )->setPositionOrder()->addStoreFilter();

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        if($this->_limit) {
            $this->_itemCollection->setPageSize($this->_limit);
        }
        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }
    
    public function setLimit($limit=null) {
        $this->_limit = $limit;
        return $this;
    }
    
    public function getLimit() {
        return $this->_limit;
    }
}
