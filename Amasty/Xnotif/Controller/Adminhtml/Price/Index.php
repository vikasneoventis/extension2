<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller\Adminhtml\Price;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;
    protected $_helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Amasty\Xnotif\Helper\Data $helper
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->getLayout();

        $resultPage->setActiveMenu('Amasty_Xnotig::amxnotif_price');

        $resultPage->addBreadcrumb(__('Alerts'), __('Price Alerts'));

        $resultPage->addContent($resultPage->getLayout()->createBlock('Amasty\Xnotif\Block\Adminhtml\Price'));

        $this->_helper->addMessage();

        $resultPage->getConfig()->getTitle()->prepend(__('Price Alerts'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Xnotif::xnotif_price');
    }
}