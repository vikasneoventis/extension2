<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Block\Adminhtml\Stock;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_resource;
    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );

        $this->_resource = $resource;
        $this->_objectManager = $objectManager;
    }


    public function _construct()
    {
        parent::_construct();
        $this->setId('stockGrid');
        $this->setDefaultSort('cnt');
    }

    public function addColors($value, $row, $column)
    {
        switch ($value) {
            case 0:
                $color = "green";
                break;
            case 1:
                $color = "lightcoral";
                break;
            case 2:
                $color = "indianred";
                break;
            case 3:
                $color = "brown";
                break;
            case 4:
                $color = "firebrick";
                break;
            case 4:
                $color = "darkred";
                break;
            default:
                $color = "red";
        }

        return '<div style="width: 50px; margin: 0 auto; border-radius: 3px;text-align: center; background-color: '
        . $color . '">' .
        $value .
        '</div>';
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            'catalog/product/edit', ['id' => $row->getProductId()]
        );
    }

    protected function _prepareCollection()
    {

        $stockAlertTable = $this->_resource->getTableName(
            'product_alert_stock'
        );

        $collection = $this->_objectManager->get('Amasty\Xnotif\Model\Product')
            ->getCollection();
        $collection->addAttributeToSelect('name')
            ->addAttributeToFilter(
                'status',
                ['eq' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED]
            );

        $select = $collection->getSelect();

        $select->joinRight(
            ['s' => $stockAlertTable], 's.product_id = e.entity_id',
            ['total_cnt' => 'count(s.product_id)',
                'cnt' => 'COUNT( NULLIF(`s`.`status`, 1) )',
                'last_d' => 'MAX(add_date)', 'first_d' => 'MIN(add_date)',
                'product_id']
        )
            ->group(['s.product_id']);

        $select->columns(
            ['website_id' => new \Zend_Db_Expr(
                "SUBSTRING( GROUP_CONCAT( `s`.`website_id` ) , 1, 100 )"
            )]
        );

        $columnId = $this->getParam(
            $this->getVarNameSort(), $this->_defaultSort
        );
        $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
        if ($this->getColumn($columnId) && $this->getColumn($columnId)->getIndex()) {
            $dir = strtolower($dir) == 'desc' ? 'desc' : 'asc';
            $this->getColumn($columnId)->setDir($dir);
            $select->order($columnId . ' ' . $dir);
        }
        $collection->setIsCustomerMode(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'website',
                [
                    'header' => __('Websites'),
                    'width' => '100px',
                    'sortable' => false,
                    'index' => 'website_id',
                    'renderer' => 'Amasty\Xnotif\Block\Adminhtml\Stock\Renderer\Website',
                    'filter' => false,
                ]
            );
        }

        $this->addColumn(
            'name', [
                'header' => __('Name'),
                'index' => 'name',
            ]
        );

        $this->addColumn(
            'sku', [
                'header' => __('SKU'),
                'index' => 'sku',
            ]
        );

        $this->addColumn(
            'first_d', [
                'header' => __('First Subscription'),
                'index' => 'first_d',
                'type' => 'datetime',
                'width' => '150px',
                'gmtoffset' => true,
                'default' => ' ---- ',
                'filter' => false,
            ]
        );
        $this->addColumn(
            'last_d', [
                'header' => __('Last Subscription'),
                'index' => 'last_d',
                'type' => 'datetime',
                'width' => '150px',
                'gmtoffset' => true,
                'default' => ' ---- ',
                'filter' => false,
            ]
        );

        $this->addColumn(
            'total_cnt', [
                'header' => __('Total Number of Subscriptions'),
                'index' => 'total_cnt',
                'filter' => false,
                'align' => 'center',
                'width' => '150px'
            ]
        );

        $this->addColumn(
            'cnt', [
                'header' => __('Customers Awaiting Notification'),
                'index' => 'cnt',
                'filter' => false,
                'frame_callback' => [$this, 'addColors'],
                'width' => '150px'
            ]
        );

        return parent::_prepareColumns();
    }
}