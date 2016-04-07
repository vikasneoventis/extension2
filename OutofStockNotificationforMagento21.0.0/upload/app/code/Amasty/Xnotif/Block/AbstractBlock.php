<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Customer\Model\Session;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\ResourceConnection;

class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    public $_title;
    public $_type;
    protected $_resource;
    public $_objectManager;
    protected $_customerSession;
    public $_scopeConfig;

    public function __construct(
        Template\Context $context,
        ResourceConnection $resource,
        ObjectManagerInterface $objectManager,
        Session $customerSession,
        array $data = []
    )
    {
        $this->_resource = $resource;
        $this->_objectManager = $objectManager;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        $this->setTemplate('subscr.phtml');
        $this->_loadCollection();
    }


    private function _loadCollection()
    {

        $tableName = 'product_alert_' . $this->_type;
        $alertTable = $this->_resource->getTableName($tableName);
        //$this->_resource->getTableName($tableName);
        $collection = $this->_objectManager->get(
            'Magento\Catalog\Model\Product'
        )->getCollection();
        $collection->addAttributeToSelect('name');

        $select = $collection->getSelect();
        $entityIdName = 'alert_' . $this->_type . '_id';
        $select->joinInner(
            ['s' => $alertTable], 's.product_id = e.entity_id',
            ['add_date', $entityIdName, 'parent_id']
        )
            ->where('s.status=0')
            ->where(
                'customer_id=? OR email=?',
                $this->_customerSession->getCustomerId(),
                $this->_customerSession->getCustomer()->getEmail()
            )
            ->group(['s.product_id']);

        $this->setSubscriptions($collection);
    }

    public function getRemoveUrl($order)
    {
        $entityIdName = 'alert_' . $this->_type . '_id';

        $id = $order->getData($entityIdName);

        return $this->getUrl(
            'xnotif/' . $this->_type . '/remove',
            ['item' => $id]
        );
    }

    public function getProductUrl($_order)
    {
        $product = $this->getProduct($_order->getEntityId());
        $url = $product->getUrlModel()->getUrl($product);
        if (isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS']
            && $_SERVER['HTTPS'] != ""
        ) {
            $url = str_replace('http:', 'https:', $url);
        }
        return $url;
    }

    public function getSupperAttributesByChildId($id)
    {

        $parentIds = $this->_objectManager->get('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($id);
        $attributes = [];
        if (!empty($parentIds)) {
            foreach ($parentIds as $parentId) {
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($parentId);
                $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
                break;
            }
        }
        return $attributes;
    }

    public function getUrlHash($id)
    {
        $attributes = $this->getSupperAttributesByChildId($id);
        $url = '';
        if (!empty($attributes)) {
            $hash = '';
            foreach ($attributes as $_attribute) {
                $attributeCode = $_attribute->getData('product_attribute')->getData('attribute_code');
                $value = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($id)->getData($attributeCode);
                $hash .= '&' . $_attribute->getData("attribute_id") . "=" . $value;
            }
            $url .= '#' . substr($hash, 1);//remove first &
        }
        return $url;
    }

    public function getUrlProduct($product)
    {
        $parentIds = $this->_objectManager->get('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($product->getId());
        if (!empty($parentIds) && isset($parentIds[0])) {
            $parent = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($parentIds[0]);
            $baseUrl = $parent->getUrlModel()->getUrl($parent);
        } else {
            $baseUrl = $product->getUrlModel()->getUrl($product);
        }
        $hash = $this->getUrlHash($product->getId());
        $url = $baseUrl . $hash;
        return $url;
    }

    public function getProduct($id)
    {
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($id);
        return $product;
    }

}
 