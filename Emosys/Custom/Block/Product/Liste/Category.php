<?php
namespace Emosys\Custom\Block\Product\Liste;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Action\Action;

class Category extends \Magento\Catalog\Block\Product\AbstractProduct {
	protected $_categoryFactory;
	protected $_category;

	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		array $data = []) {
        $this->_categoryFactory = $categoryFactory;
        $this->setTemplate('product/list/slider.phtml');
        parent::__construct($context, $data);
    }

	public function getCategory() {
		if($this->_category) {
			return $this->_category;
		}
	    $categoryId = $this->getCategoryId();
	    if(!$categoryId) {
	    	return null;
	    }
	    $category = $this->_categoryFactory->create()->load($categoryId);
	    if(!$category->getId()) {
	    	return null;
	    }
	    $this->_category = $category;
	    return $category;
	}

	public function getLoadedProductCollection() {
		if($this->getCategory()) {
			return $this->getCategory()->getProductCollection()->addAttributeToSelect('*');
		}
		return null;
	}
}