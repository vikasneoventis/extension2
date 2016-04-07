<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller\Email;

class Price extends AbstractEmail
{

    public function execute()
    {
        $backUrl = $this->getRequest()->getParam(\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED);
        $productId = (int)$this->getRequest()->getParam('product_id');
        $guestEmail = $this->getRequest()->getParam('guest_email_price');
        $parentId = (int)$this->getRequest()->getParam('parent_id');
        $redirect = $this->_redirectFactory->create();


        if (!$backUrl) {
            $redirect->setUrl('/');
            return $redirect;
        }
        $product = $this->_productRepository->getById(
            $productId,
            false,
            $this->_storeManager->getStore()->getWebsiteId()
        );
        if (!$product) {
            $this->messageManager->addError(__('Not enough parameters.'));
            $redirect->setUrl($backUrl);
            return $redirect;
        }

        try {
            $model = $this->_objectManager->get('Magento\ProductAlert\Model\Price')
                ->setCustomerId($this->_customerSession->getId())
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());

            if ($parentId) {
                $model->setParentId($parentId);
            } else {
                $model->setParentId($productId);
            }
            $collection = $this->_objectManager->get('Magento\ProductAlert\Model\Price')
                ->getCollection()
                ->addWebsiteFilter($this->_storeManager->getWebsite()->getId())
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('status', 0)
                ->setCustomerOrder();

            if ($guestEmail) {
                if (!\Zend_Validate::is($guestEmail, 'EmailAddress')) {
                    $this->messageManager->addError(__('Please enter a valid email address.'));
                    $redirect->setUrl($this->_redirect->getRefererUrl());
                    return $redirect;
                }
                $customer = $this->_objectManager->get('Magento\Customer\Model\Customer');
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