<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Block;

class Stock extends \Amasty\Xnotif\Block\AbstractBlock
{
    public function _construct()
    {
        $this->_type = "stock";
        parent::_construct();
    }
}
 