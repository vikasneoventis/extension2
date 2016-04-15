<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller\Email;

abstract class AbstractEmail extends \Magento\Framework\App\Action\Action
{
    protected $_scopeConfig;
    protected $_customerSession;
    protected $_storeManager;
    protected $_productRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_productRepository = $productRepository;
    }

    public function preDispatch()
    {
        parent::preDispatch();
        if ($this->_scopeConfig->isSetFlag('amxnotif/stock/disable_guest')) {
            if (!$this->_customerSession->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
                if (!$this->_customerSession->getBeforeUrl()) {
                    $this->_customerSession->setBeforeUrl($this->_getRefererUrl());
                }
            }
        }
    }

}