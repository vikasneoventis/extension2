<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller;

abstract class AbstractRemove extends AbstractController
{

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('item');

        $modelName = "Magento\\ProductAlert\\Model\\" . ucfirst(strtolower(static::TYPE));
        $item = $this->_objectManager->get($modelName)->load($id);
        $_customer = $this->_objectManager->get(
            'Magento\Customer\Model\Customer'
        )
            ->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())
            ->load($this->_customerSession->getCustomerId());

        // check if not a guest subscription (cust. id is set) and is matching with logged in customer
        if ($item->getCustomerId() > 0
            && $item->getCustomerId() == $_customer->getId()
        ) {
            try {
                $item->delete();
            } catch (Mage_Core_Exception $e) {
                $this->messageManager->addError(
                    __(
                        'An error occurred while deleting the item from Subscriptions: %s',
                        $e->getMessage()
                    )
                );
            } catch (Exception $e) {
                $this->messageManager->addError(
                    __(
                        'An error occurred while deleting the item from Subscriptions.'
                    )
                );
            }
        }
        $redirect = $this->_redirectFactory->create();
        $redirect->setPath($this->_url->getUrl('*/*'));

        return $redirect;
    }
}
