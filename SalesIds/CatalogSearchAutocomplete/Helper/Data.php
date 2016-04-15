<?php
/**
 * Copyright © 2015 SalesIds. All rights reserved.
 * See SALESIDS_COPYING.txt for license details.
 */

namespace SalesIds\CatalogSearchAutocomplete\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config paths
     *
     * @var string
     */
    const XML_PATH_ENABLED        = 'catalog/search/salesids_ajax_product_search';
    const XML_PATH_DISABLE_OUTPUT = 'advanced/modules_disable_output/SalesIds_CatalogSearchAutocomplete';

    /**
     * Check if enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (!$this->isModuleOutputEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
