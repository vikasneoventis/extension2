<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller\Adminhtml\Stock;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    )
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $alertId = (int)$this->getRequest()->getParam('alert_stock_id');

        if (!$alertId) {
            $this->messageManager->addError(
                __(
                    'An error occurred while deleting the item from Subscriptions.'
                )
            );
        } else {
            $alert = $this->_objectManager->get(
                'Magento\ProductAlert\Model\Stock'
            )->load($alertId);
            if ($alert && $alert->getId()) {
                try {
                    $alert->delete();
                    $this->messageManager->addSuccess(
                        __('The item has been deleted from Subscriptions.')
                    );
                } catch (Exception $e) {
                    $this->messageManager->addError(
                        __(
                            'An error occurred while deleting the item from Subscriptions.'
                        )
                    );
                }
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;

    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Amasty_Xnotif::xnotif_stock'
        );
    }
}