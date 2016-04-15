<?php
/**
 * Catalog layer filter renderer
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Emizentech\Priceslider\Block;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\LayeredNavigation\Block\Navigation\FilterRenderer;

class FilterRendererRewrite extends FilterRenderer
{
    
    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $filter)
    {	
        $this->assign('filterItems', $filter->getItems());
        $this->assign('filter' , $filter);
        $this->_setRenderTemplate($filter);
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }
    
    public function getPriceRange($filter){
    	$Filterprice = array('min' => 0 , 'max'=>0);
    	if($filter->getName() == 'Price' ){
			$priceArr = $filter->getResource()->loadPrices(10000000000);
     		$Filterprice['min'] = reset($priceArr);
     		$Filterprice['max'] = end($priceArr);
    	}
    	return $Filterprice;
    }
    
    public function getFilterUrl($filter){
    		$query = ['price'=> ''];
    	 return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
    protected function _setRenderTemplate($filter) {
        if($filter->getRequestVar() == 'price') {
            $this->setTemplate('Emizentech_Priceslider::filter_price.phtml');
        }
        else {
            $this->setTemplate('Magento_LayeredNavigation::layer/filter.phtml');
        }
        return $this;
    }
}