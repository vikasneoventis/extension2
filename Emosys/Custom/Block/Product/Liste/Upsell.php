<?php

namespace Emosys\Custom\Block\Product\Liste;

class Upsell extends \Magento\Catalog\Block\Product\ProductList\Upsell {
    protected $_limit;
    /**
     * @return $this
     */
    protected function _prepareData() {
        $product = $this->_coreRegistry->registry('product');
        /* @var $product \Magento\Catalog\Model\Product */
        $this->_itemCollection = $product->getUpSellProductCollection()->setPositionOrder()->addStoreFilter();
        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        if($this->_limit) {
            $this->_itemCollection->setPageSize($this->_limit);
        }
        $this->_itemCollection->load();

        /**
         * Updating collection with desired items
         */
        $this->_eventManager->dispatch(
                'catalog_product_upsell', ['product' => $product, 'collection' => $this->_itemCollection, 'limit' => null]
        );

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
