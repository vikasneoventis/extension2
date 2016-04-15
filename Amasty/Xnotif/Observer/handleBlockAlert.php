<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */

namespace Amasty\Xnotif\Observer;

use Magento\Framework\Event\ObserverInterface;

class handleBlockAlert implements ObserverInterface
{
    protected $_scopeConfig;
    protected $_registry;
    protected $_helper;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Amasty\Xnotif\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $event->getLayout();

        $name = $event->getElementName();

        /** @var \Magento\Framework\View\Element\AbstractBlock $block */
        $block = $layout->getBlock($name);
        $transport = $event->getTransport();

        $html = $transport->getData('output');

        $pos = strpos($html, 'alert stock');

        if ($block instanceof \Magento\Productalert\Block\Product\View && $pos
            && !$this->_scopeConfig->isSetFlag('amxnotif/stock/disable_guest')
        ) {
            if (!$this->_registry->registry('customerIsLoggedIn')) {
                $res = preg_match('/product_id\\\\\\/([0-9]+)\\\\\\//', $html, $result);
                if ($result) {
                    $result = [];
                    $product = $this->_registry->registry('current_product');
                    if (!$product->isSaleable()) {
                        $blockHtml = $this->_helper->getStockAlert(
                            $product, $this->_registry->registry('customerIsLoggedIn'), 1
                        );
                        $html = $blockHtml;
                        $transport->setData('output', $html);
                    }
                }
            }
        }

        $pos = strpos($html, 'alert price');
        if ($block instanceof \Magento\Productalert\Block\Product\View && $pos
            && !$this->_scopeConfig->isSetFlag('amxnotif/price/disable_guest')
        ) {
            preg_match('/product_id\\\\\\/([0-9]+)\\\\\\//', $html, $result);
            if ($result && !$this->_registry->registry('customerIsLoggedIn')) {
                $result = [];
                $product = $this->_registry->registry('current_product');
                $blockHtml = $this->_helper->getPriceAlert(
                    $product, $this->_registry->registry('customerIsLoggedIn')
                );
                $html = $blockHtml;
                $transport->setData('output', $html);
            }

        }
    }
}