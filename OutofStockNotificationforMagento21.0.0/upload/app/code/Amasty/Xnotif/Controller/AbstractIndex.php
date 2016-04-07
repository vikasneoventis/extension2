<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller;

abstract class AbstractIndex extends AbstractController
{
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->initLayout();
        $navigationBlock = $resultPage->getLayout()->getBlock(
            'customer_account_navigation'
        );
        if ($navigationBlock) {
            $navigationBlock->setActive('xnotif/' . static::TYPE . '/index');
        }
        $resultPage->getConfig()->getTitle()->prepend(__(static::TITLE));
        return $resultPage;
    }
}