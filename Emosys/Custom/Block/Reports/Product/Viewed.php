<?php
namespace Emosys\Custom\Block\Reports\Product;
use \Magento\Framework\DataObject\IdentityInterface;

class Viewed extends \Magento\Reports\Block\Product\Viewed {

    /**
     * Viewed Product Index type
     *
     * @var string
     */
    protected $_indexType = \Magento\Reports\Model\Product\Index\Factory::TYPE_VIEWED;

    /**
     * Retrieve page size (count)
     *
     * @return int
     */
    public function getPageSize()
    {
        if ($this->hasData('page_size')) {
            return $this->getData('page_size');
        }
        return $this->_scopeConfig->getValue(
            self::XML_PATH_RECENTLY_VIEWED_COUNT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Added predefined ids support
     *
     * @return int
     */
    public function getCount()
    {
        $ids = $this->getProductIds();
        if (!empty($ids)) {
            return count($ids);
        }
        return parent::getCount();
    }

    /**
     * Prepare to html
     * check has viewed products
     *
     * @return string
     */
    protected function _toHtml()
    {
    	if(isset($_GET['dev'])) {
    		echo 1111;
    	}
    	
        $this->setRecentlyViewedProducts($this->getItemsCollection());
        return parent::_toHtml();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getItemsCollection() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        return $identities;
    }

    public function getItemsCollection()
    {
        if ($this->_collection === null) {
            $attributes = $this->_catalogConfig->getProductAttributes();

            $this->_collection = $this->getModel()->getCollection()->addAttributeToSelect($attributes);

            if ($this->getCustomerId()) {
                $this->_collection->setCustomerId($this->getCustomerId());
            }

            $this->_collection->excludeProductIds(
                $this->getModel()->getExcludeProductIds()
            )->addUrlRewrite()->setPageSize(
                $this->getPageSize()
            )->setCurPage(
                1
            );

            /* Price data is added to consider item stock status using price index */
            $this->_collection->addPriceData();
            $ids = $this->getProductIds();
            if(isset($_GET['dev'])) {
            	var_dump('ids: ',$ids);
        	}
            if (empty($ids)) {
                $this->_collection->addIndexFilter();
            } else {
                $this->_collection->addFilterByIds($ids);
            }
            $this->_collection->setAddedAtOrder()->setVisibility($this->_productVisibility->getVisibleInSiteIds());
            if(isset($_GET['dev'])) {
            	var_dump('a: '.count($this->_collection));exit;
        	}
        }

        return $this->_collection;
    }
}