<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\CatalogSearchAutocomplete\Model\Override\CatalogSearch\Autocomplete;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Category\Attribute\Source\Layout;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\Search\Model\QueryFactory;
use SalesIds\CatalogSearchAutocomplete\Helper\Data as DataHelper;

class DataProvider extends \Magento\CatalogSearch\Model\Autocomplete\DataProvider
{
    /**
     * Price currency
     *
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * Data helper
     *
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    protected $_layerResolver;

    /**
     * Layout
     *
     * @var Layout
     */
    protected $_layout;

    /**
     * Catalog Product collection
     *
     * @var Collection
     */
    protected $_productCollection;

    /**
     * Image helper
     *
     * @var Image
     */
    protected $_imageHelper;

    /**
     * Retrieve loaded product collection
     *
     * @return Collection
     */
    protected function _getProductCollection()
    {
        if (null === $this->_productCollection) {
            $this->_productCollection = $this->_layout
                ->getBlock('search_result_list')
                ->getLoadedProductCollection();
        }

        return $this->_productCollection;
    }

    /**
     * Get product price
     *
     * @param Product $product
     * @return string
     */
    protected function _getProductPrice($product)
    {
        return $this->_priceCurrency->format(
            $product->getFinalPrice($product),
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $product->getStore()
        );
    }

    /**
     * Get product reviews
     *
     * @param Product $product
     * @return string
     */
    protected function _getProductReviews($product)
    {
        return $this->_layout->createBlock('Magento\Review\Block\View')
            ->getReviewsSummaryHtml($product, 'short');
    }

    /**
     * Product image url getter
     *
     * @param Product $product
     * @return string
     */
    protected function _getImageUrl($product)
    {
        return $this->_imageHelper->init($product, 'product_page_image_small')->getUrl();
    }

    protected function _getWeight($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $eHelper = $objectManager->get('Emosys\Custom\Helper\Data');
        if ($productWeight = $eHelper->getProductWeight($product)) {
            return $productWeight;
        }
        return false;
    }

    /**
     * @param Context $context
     * @param QueryFactory $queryFactory
     * @param ItemFactory $itemFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param DataHelper $dataHelper
     * @param LayoutInterface $layout
     * @param Resolver $layerResolver
     * @return void
     */
    public function __construct(
        Context $context,
        QueryFactory $queryFactory,
        ItemFactory $itemFactory,
        PriceCurrencyInterface $priceCurrency,
        DataHelper $dataHelper,
        Resolver $layerResolver
    ) {
        $this->_imageHelper = $context->getImageHelper();
        $this->_priceCurrency = $priceCurrency;
        $this->_dataHelper = $dataHelper;
        $this->_layout = $context->getLayout();
        $this->_layerResolver = $layerResolver;
        $this->_layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);

        parent::__construct($queryFactory, $itemFactory);
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        if (!$this->_dataHelper->isEnabled()) {
            return parent::getItems();
        }
        $collection = $this->_getProductCollection();

        $results = [];
        foreach ($collection as $product) {
            /** @var \Magento\Catalog\Model\Product $product */
            $results[$product->getId()] = [
                'id'      => $product->getId(),
                'name'    => $product->getName(),
                'price'   => $this->_getProductPrice($product),
                'reviews' => $this->_getProductReviews($product),
                'image'   => $this->_getImageUrl($product),
                'url'     => $product->getProductUrl(),
                'weight'    => $this->_getWeight($product),
            ];
        }
        return $results;
    }
}
