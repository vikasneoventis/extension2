<?php

namespace Emosys\Custom\Block\Product\Liste;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Action\Action;

class Compare extends \Magento\Catalog\Block\Product\Compare\ListCompare {
	/**
     * Retrieve Product Compare items collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection
     */
    /*public function getItems()
    {
        if ($this->_items === null) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$this->_items = $objectManager->get('Magento\Catalog\Helper\Product\Compare')->getItemCollection();
			$this->_items->addAttributeToSelect(
                $this->_catalogConfig->getProductAttributes()
            )->loadComparableAttributes()->addMinimalPrice()->addTaxPercents()->setVisibility(
                $this->_catalogProductVisibility->getVisibleInSiteIds()
            );
            $this->_items
            	->addAttributeToSelect('price')
            	->addAttributeToSelect('short_description')
            	->addAttributeToSelect('sku')
            	->load();
        }

        return $this->_items;
    }*/

    /**
     * Render price block
     *
     * @param Product $product
     * @param string|null $idSuffix
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product, $idSuffix = '')
    {
        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        /*if(!$priceRender) {
            $priceRender = $this->getLayout()->createBlock('Magento\Framework\Pricing\Render')
                ->setData('price_render_handle','catalog_product_prices')
                ->setData('use_link_for_as_low_as',true);
        }*/

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'price_id' => 'product-price-' . $product->getId() . $idSuffix,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                ]
            );
        }
        return $price;
    }
}