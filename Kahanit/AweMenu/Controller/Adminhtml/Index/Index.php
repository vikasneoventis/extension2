<?php

namespace Kahanit\AweMenu\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Kahanit_AweMenu::kahanit_awemenuindex');
        $resultPage->getConfig()->getTitle()->prepend(__('Awe Menu'));

        return $resultPage;
    }
}
