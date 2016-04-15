<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Controller\Adminhtml\Photo;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Emosys_Banner::save');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Emosys_Banner::item')
            ->addBreadcrumb(__('Photo'), __('Photo'))
            ->addBreadcrumb(__('Manage Photos'), __('Manage Photos'));
        return $resultPage;
    }

    /**
     * Edit Blog post
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('photo_id');
        $model = $this->_objectManager->create('Emosys\Banner\Model\Photo');

        $itemId = $this->getRequest()->getParam('item_id');
        $item = $this->_objectManager->create('Emosys\Banner\Model\Item');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This photo no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        if ($itemId) {
            $item->load($itemId);
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('emosys_banner_photo', $model);
        $this->_coreRegistry->register('emosys_banner_item', $item);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Banner') : __('New Photo'),
            $id ? __('Edit Banner') : __('New Photo')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Photos'));
        if ( $model->getId() ) {
            $resultPage->getConfig()->getTitle()
                ->prepend( $model->getTitle() );
        } elseif ( $item->getId() ) {
            $resultPage->getConfig()->getTitle()
                ->prepend( __("New Photo for '%1'", $item->getTitle()) );
        } else {
            $resultPage->getConfig()->getTitle()
                ->prepend( __('New Photo') );
        }

        return $resultPage;
    }
}