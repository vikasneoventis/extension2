<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Emosys\Custom\Block\Template;

use Magento\Developer\Helper\Data as DevHelper;
use Magento\Developer\Model\TemplateEngine\Decorator\DebugHintsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\TemplateEngineFactory;
use Magento\Framework\View\TemplateEngineInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
/**
 * Overide sitemap
 */
class DebugHints extends \Magento\Developer\Model\TemplateEngine\Plugin\DebugHints {
    
    public function afterCreate(
        TemplateEngineFactory $subject,
        TemplateEngineInterface $invocationResult
    ) {
        if(isset($_GET['hinte']) && $_GET['hinte']) {
            if(isset($_GET['blocke']) && $_GET['blocke']) {
                $blockName = 1;
            }
            else {
                $blockName = 0;
            }
            return $this->debugHintsFactory->create([
                'subject' => $invocationResult,
                'showBlockHints' => $blockName,
            ]);
        }
        return parent::afterCreate($subject, $invocationResult);
    }
}
