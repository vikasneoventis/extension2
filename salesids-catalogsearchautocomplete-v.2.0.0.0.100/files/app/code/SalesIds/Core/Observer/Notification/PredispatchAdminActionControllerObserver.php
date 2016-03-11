<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\Core\Observer\Notification;

use Magento\Framework\Event\ObserverInterface;

/**
 * Notification observer
 */
class PredispatchAdminActionControllerObserver implements ObserverInterface
{
    /**
     * @var \SalesIds\Core\Model\Notification\FeedFactory
     */
    protected $_feedFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \SalesIds\Core\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Initialize dependencies
     *
     * @param \SalesIds\Core\Model\Notification\FeedFactory $feedFactory
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \SalesIds\Core\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function __construct(
        \SalesIds\Core\Model\Notification\FeedFactory $feedFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \SalesIds\Core\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_feedFactory = $feedFactory;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_dataHelper = $dataHelper;
        $this->_logger = $logger;
    }

    /**
     * Predispath admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_backendAuthSession->isLoggedIn()) {
            return;
        }
        if (!$this->_dataHelper->isNotificationEnabled()) {
            return;
        }
        try {
            $feedModel = $this->_feedFactory->create();
            /* @var $feedModel \SalesIds\Core\Model\Notification\Feed */
            $feedModel->checkUpdate();
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
    }
}
