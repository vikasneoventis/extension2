<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Block\Product\View\Type;

class Grouped extends \Magento\GroupedProduct\Block\Product\View\Type\Grouped
{
    protected function _toHtml()
    {
        if (strpos($this->getTemplate(), "availability") <= 0) {
            //  $this->setTemplate('Amasty_Xnotif::product/view/type/grouped.phtml');
        }
        return parent::_toHtml();
    }
}