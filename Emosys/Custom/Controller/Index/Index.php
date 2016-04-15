<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */
namespace Emosys\Custom\Controller\Index;

/**
 * Blog home page view
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * View blog homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        echo '<h1>aa</h1>';
        $block = $this->_view->getLayout()
            ->createBlock('Emosys\Custom\Block\Product\Liste\Category')
            ->setCategoryId(33);
        $_productCollection = $block->getLoadedProductCollection();
        if($_productCollection && $_productCollection->count()) {
            foreach ($_productCollection as $_product) {
                print_r($_product->getId());
                echo '<br/>';
            }
        }
        //$this->_view->renderLayout();
    }

}
