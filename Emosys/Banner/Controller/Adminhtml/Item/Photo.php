<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Emosys\Banner\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;

class Photo extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Emosys_Banner::save');
    }

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Get upsell products grid and serializer block
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('item_id');
        $model = $this->_objectManager->create('Emosys\Banner\Model\Item');

        if ($id) {
            $model->load($id);
        }

        $this->_coreRegistry->register('emosys_banner_item', $model);

        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('emosys_banner.item.edit.tab.photo')
            ->setPhotos($this->getRequest()->getPost('products_upsell', null));
        return $resultLayout;
    }
}
