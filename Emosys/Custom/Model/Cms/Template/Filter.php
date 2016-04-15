<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Emosys\Custom\Model\Cms\Template;

/**
 * Cms Template Filter Model
 */
class Filter extends \Magento\Cms\Model\Template\Filter
{
    /**
     * Retrieve store URL directive
     * Support url and direct_url properties
     *
     * @param string[] $construction
     * @return string
     */
    public function storeDirective($construction)
    {
        $params = $this->getParameters($construction[2]);
        if ( isset($params['_store']) ) {
            return $this->_storeManager->getStore($params['_store'])->getUrl($params['url']);
        }
        return parent::storeDirective($construction);
    }
}
