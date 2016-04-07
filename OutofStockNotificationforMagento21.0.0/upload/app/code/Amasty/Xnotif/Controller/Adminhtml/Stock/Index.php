<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller\Adminhtml\Stock;

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
        $pageResult = $this->resultPageFactory->create();

        $pageResult->getLayout();

        $pageResult->setActiveMenu('Amasty_Xnotif::amxnotif_stock');

        $pageResult->addBreadcrumb(__('Alerts'), __('Stock Alerts'));

        $pageResult->addContent($pageResult->getLayout()->createBlock('Amasty\Xnotif\Block\Adminhtml\Stock'));

        $this->_helper->addMessage();

        $pageResult->getConfig()->getTitle()->prepend(__('Stock Alerts '));

        return $pageResult;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Xnotif::amxnotif_stock');
    }

}