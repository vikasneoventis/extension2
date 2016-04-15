<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Core
 */
namespace Emosys\Core\Helper;

use Magento\Framework\App\ActionInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;

/**
 * Wishlist Data Helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockState;

    /**
     * @var StockConfigurationInterface
     */
    protected $_stockConfiguration;

    /**
     * @var StockRegistryProviderInterface
     */
    protected $_stockRegistryProvider;

    /**
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        StockRegistryProviderInterface $stockRegistryProvider,
        StockConfigurationInterface $stockConfiguration
    )
    {
        $this->_storeManager = $storeManager;
        $this->_stockState = $stockState;
        $this->_stockConfiguration = $stockConfiguration;
        $this->_stockRegistryProvider = $stockRegistryProvider;
        parent::__construct($context);
    }

    public function getStoreName()
    {
        return $this->getStore()->getName();
    }

    public function getStore($code = null)
    {
        return $this->_storeManager->getStore($code);
    }

    /**
     * Get websites
     *
     * @return \Magento\Store\Model\Website[]
     */
    public function getWebsites($withDefault = false)
    {
        return $this->_storeManager->getWebsites($withDefault);
    }

    /**
     * Get stock quantity
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getStockQty($product = false)
    {
        $scopeId = $this->_stockConfiguration->getDefaultScopeId();
        $stockItem = $this->_stockRegistryProvider->getStockItem($product->getId(), $scopeId);

        if ( $stockItem->getManageStock() ) {
            return $this->_stockState->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
        }
        return false;
    }

    public function getThresholdQty() {
        return $this->scopeConfig->getValue(
            'cataloginventory/options/stock_threshold_qty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
