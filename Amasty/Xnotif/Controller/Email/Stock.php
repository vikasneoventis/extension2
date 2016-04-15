<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller\Email;

class Stock extends AbstractEmail
{

    public function execute()
    {
        $backUrl = $this->getRequest()->getParam(\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED);
        $productId = (int)$this->getRequest()->getParam('product_id');
        $guestEmail = $this->getRequest()->getParam('guest_email');
        $parentId = (int)$this->getRequest()->getParam('parent_id');
        $redirect = $this->resultRedirectFactory->create();
        if (!$backUrl) {
            $redirect->setUrl('/');
            return $redirect;
        }

        if (!$product = $this->_objectManager->get(
            'Magento\Catalog\Model\Product')->load($productId)
        ) {

            $this->messageManager->addError(__('Not enough parameters.'));
            $this->_redirectUrl($backUrl);
            return;
        }

        try {
            $model = $this->_objectManager->get('Magento\ProductAlert\Model\Stock')
                ->setProductId($product->getId())
                ->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());

            if ($parentId) {
                $model->setParentId($parentId);
            } else {
                $model->setParentId($productId);
            }
            $collection = $this->_objectManager->get('Magento\ProductAlert\Model\Stock')
                ->getCollection()
                ->addWebsiteFilter($this->_storeManager->getWebsite()->getId())
                ->addFieldToFilter('product_id', $productId)
                ->addStatusFilter(0)
                ->setCustomerOrder();

            if ($guestEmail) {
                if (!\Zend_Validate::is($guestEmail, 'EmailAddress')) {
                    $this->messageManager->addError(__('Please enter a valid email address.'));
                    $redirect->setUrl($this->_redirect->getRefererUrl());
                    return $redirect;
                }
                $customer = $this->_objectManager->get(
                    'Magento\Customer\Model\Customer');
                $customer->setWebsiteId($this->_storeManager->getWebsite()->getId());
                $customer->loadByEmail($guestEmail);

                if (!$customer->getId()) {
                    $model->setEmail($guestEmail);
                    $collection->addFieldToFilter('email', $guestEmail);
                } else {
                    $model->setCustomerId($customer->getId());
                    $collection->addFieldToFilter('customer_id', $customer->getId());
                }
            } else {
                $model->setCustomerId($this->_customerSession->getId());
                $collection->addFieldToFilter('customer_id', $this->_customerSession->getId());
            }


            if ($collection->getSize() > 0) {
                $this->messageManager->addSuccess(__('Thank you! You are already subscribed to this product.'));
            } else {
                $model->save();
                $this->messageManager->addSuccess(__('Alert subscription has been saved.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }

        $redirect->setUrl($this->_redirect->getRefererUrl());
        return $redirect;
    }


}