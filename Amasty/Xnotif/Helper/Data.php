<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_registry;
    protected $_resultPageFactory;
    protected $_objectManager;
    protected $_messageManager;


    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_objectManager = $objectManager;
        $this->_messageManager = $messageManager;
    }

    public function getStockAlert($product, $isLogged, $simple = 0)
    {

        if (!$product->getId() || !$this->_registry->registry('current_product')) // this is the extension's setting.
        {
            return '';
        }
        $tempCurrentProduct = $this->_registry->registry('current_product');

        $this->_registry->unregister('par_product_id');
        $this->_registry->unregister('product');
        $this->_registry->unregister('current_product');

        $this->_registry->register(
            'par_product_id',
            $this->_registry->registry('main_product')
                ? $this->_registry->registry('main_product')->getId()
                : $tempCurrentProduct->getId()
        );
        $this->_registry->register('current_product', $product);
        $this->_registry->register('product', $product);
        $pageResult = $this->_resultPageFactory->create();
        $alertBlock = $pageResult->getLayout()->createBlock('Magento\ProductAlert\Block\Product\View', 'productalert.stock.' . $product->getId());
        if ($alertBlock && !$product->getData('amxnotif_hide_alert')) {
            if (!$isLogged && !$this->scopeConfig->isSetFlag('amxnotif/stock/disable_guest')) {
                if ($simple) {
                    $alertBlock->setTemplate('Amasty_Xnotif::product/view_email_simple.phtml');
                } else {
                    $alertBlock->setTemplate('Amasty_Xnotif::product/view_email.phtml');
                }
            } else {
                $alertBlock->setTemplate('Magento_ProductAlert::product/view.phtml');
                $alertBlock->prepareStockAlertData();
                $alertBlock->setHtmlClass('alert stock link-stock-alert');
                $alertBlock->setSignupLabel(__('Sign up to get notified when this configuration is back in stock'));
            }

            $html = $alertBlock->toHtml();
            $this->_registry->unregister('product');
            $this->_registry->unregister('current_product');
            $this->_registry->register('current_product', $tempCurrentProduct);
            $this->_registry->register('product', $tempCurrentProduct);

            return $html;
        }

        $this->_registry->unregister('product');
        $this->_registry->unregister('current_product');
        $this->_registry->register('current_product', $tempCurrentProduct);
        $this->_registry->register('product', $tempCurrentProduct);

        return '';
    }

    public function getPriceAlert($product, $isLogged)
    {
        if (!$product->getId() || !$this->_registry->registry('current_product')) // this is the extension's setting.
        {
            return '';
        }
        $tempCurrentProduct = $this->_registry->registry('current_product');

        $this->_registry->unregister('par_product_id');
        $this->_registry->unregister('product');
        $this->_registry->unregister('current_product');

        $this->_registry->register('par_product_id', $tempCurrentProduct->getId());
        $this->_registry->register('current_product', $product);
        $this->_registry->register('product', $product);
        $pageResult = $this->_resultPageFactory->create();
        $alertBlock = $pageResult->getLayout()->createBlock('Magento\ProductAlert\Block\Product\View', 'productalert.price' . $product->getId());
        $html = '';

        if ($alertBlock && !$isLogged && !$this->scopeConfig->getValue('amxnotif/price/disable_guest')) {
            $alertBlock->setTemplate('Amasty_Xnotif::product/price/view_email_simple.phtml');
            $html = $alertBlock->toHtml();
        }

        $this->_registry->unregister('product');
        $this->_registry->unregister('current_product');
        $this->_registry->register('current_product', $tempCurrentProduct);
        $this->_registry->register('product', $tempCurrentProduct);

        return $html;
    }

    public function getProduct()
    {
        return $this->_registry->registry('product');
    }

    public function getSignupUrl($type)
    {

        return $this->_getUrl('xnotif/email/' . $type, [
            'product_id' => $this->getProduct()->getId(),
            'parent_id' => $this->_registry->registry('par_product_id'),
            \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        ]);
    }

    public function getEmailUrl($type)
    {

        return $this->_getUrl('xnotif/email/' . $type);
    }

    public function addMessage()
    {
        $scheduleCollection = $this->_objectManager->get('Magento\Cron\Model\Schedule')->getCollection()
            ->addFieldToFilter('job_code', ['eq' => 'catalog_product_alert']);

        $scheduleCollection->getSelect()->order("schedule_id desc");
        $scheduleCollection->getSelect()->limit(1);
        if ($scheduleCollection->getSize() == 0) {
            $message = '<div style="font-size: 13px;">'
                . __('No cron job "catalog_product_alert" found. Please check your cron configuration: <a href="https://support.amasty.com/index.php?/Knowledgebase/Article/View/79/25/i-cant-send-notifications">Read more</a>')
                . '</div>';
            $this->_messageManager->addNotice($message);
        }
    }
}