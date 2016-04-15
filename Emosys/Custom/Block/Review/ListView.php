<?php 
namespace Emosys\Custom\Block\Review;

class ListView extends \Magento\Review\Block\Product\View\ListView {
    protected function _prepareLayout()
    {
        \Magento\Review\Block\Product\View::_prepareLayout();
        $toolbar = $this->getLayout()->getBlock('product_review_list.toolbar');
        if ($toolbar) {
        	$toolbar->setAvailableLimit([2 => 2]);
            $toolbar->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }
        return $this;
    }
}