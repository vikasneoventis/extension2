<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Block\Adminhtml\Price;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_resource;
    protected $_objectManager;
    protected $_helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Xnotif\Helper\Data $helper,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );
        $this->_objectManager = $objectManager;
        $this->_resource = $resource;
        $this->_helper = $helper;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('priceGrid');
        $this->setDefaultSort('cnt');
    }

    protected function _prepareCollection()
    {
        $stockAlertTable = $this->_resource->getTableName('product_alert_price');

        $collection = $this->_objectManager->get('Amasty\Xnotif\Model\Product')->getCollection();
        $collection->addAttributeToSelect('name');

        $select = $collection->getSelect();
        $select->joinRight(['s' => $stockAlertTable], 's.product_id = e.entity_id', ['cnt' => 'count(s.product_id)', 'last_d' => 'MAX(add_date)', 'first_d' => 'MIN(add_date)', 'product_id'])
            ->where('status=0')
            ->group(['s.product_id']);

        $select->columns(['website_id' => new \Zend_Db_Expr("SUBSTRING( GROUP_CONCAT( `s`.`website_id` ) , 1, 100 )")]);

        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
        if ($this->getColumn($columnId) && $this->getColumn($columnId)->getIndex()) {
            $dir = strtolower($dir) == 'desc' ? 'desc' : 'asc';
            $this->getColumn($columnId)->setDir($dir);
            $select->order($columnId . ' ' . $dir);
        }
        $collection->setIsCustomerMode(TRUE);
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        $hlp = $this->_helper;
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('website',
                [
                    'header' => __('Websites'),
                    'width' => '100px',
                    'sortable' => false,
                    'index' => 'website_id',
                    'renderer' => 'Amasty\Xnotif\Block\Adminhtml\Stock\Renderer\Website',
                    'filter' => false,
                ]);
        }

        $this->addColumn('name', [
            'header' => __('Name'),
            'index' => 'name',
        ]);

        $this->addColumn('sku', [
            'header' => __('SKU'),
            'index' => 'sku',
        ]);

        $this->addColumn('cnt', [
            'header' => __('Count'),
            'index' => 'cnt',
            'filter' => false,
        ]);

        $this->addColumn('first_d', [
            'header' => __('First Subscription'),
            'index' => 'first_d',
            'type' => 'datetime',
            'width' => '150px',
            'gmtoffset' => true,
            'default' => ' ---- ',
            'filter' => false,
        ]);
        $this->addColumn('last_d', [
            'header' => __('Last Subscription'),
            'index' => 'last_d',
            'type' => 'datetime',
            'width' => '150px',
            'gmtoffset' => true,
            'default' => ' ---- ',
            'filter' => false,
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {

        return $this->getUrl('catalog/product/edit', ['id' => $row->getProductId()]);
    }
}