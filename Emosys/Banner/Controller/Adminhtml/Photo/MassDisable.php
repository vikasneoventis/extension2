<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Controller\Adminhtml\Photo;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Emosys\Banner\Model\ResourceModel\Photo\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDisable
 */
class MassDisable  extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;


    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        foreach ($collection as $item) {
            $item->setIsActive(false);
            $item->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been disabled.', $collection->getSize()));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}