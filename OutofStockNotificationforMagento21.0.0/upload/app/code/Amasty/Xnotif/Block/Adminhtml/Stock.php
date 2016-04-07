<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Block\Adminhtml;
class Stock extends// \Magento\Framework\View\Element\AbstractBlock
    \Magento\Backend\Block\Widget\Grid\Container
{
    protected $_objectManager;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = [])
    {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_controller = 'adminhtml_stock';
        $this->_blockGroup = 'Amasty_Xnotif';
        $this->removeButton('add');
    }
}