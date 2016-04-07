<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */

namespace Amasty\Xnotif\Observer;

use Magento\Framework\Event\ObserverInterface;

class saveCustomer implements ObserverInterface
{

    protected $_registry;
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_customerSession = $customerSession;
        $this->_registry = $registry;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_registry->register('customerIsLoggedIn', $this->_customerSession->isLoggedIn());
    }
}