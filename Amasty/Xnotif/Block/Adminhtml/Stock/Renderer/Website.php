<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Block\Adminhtml\Stock\Renderer;

use \Magento\Framework\DataObject;

class  Website extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    protected $_objectManger;

    protected $_websiteFactory;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $data);
        $this->_objectManger = $objectManager;
        $this->_websiteFactory = $websiteFactory;
    }

    public function render(DataObject $row)
    {
        $website = $row->getWebsiteId();
        $websites = $this->_websiteFactory->create()->getCollection()->toOptionArray();
        $sites = explode(',', $website);
        $webSitesLabels = [];
        foreach ($websites as $v) {
            if (array_search($v['value'], $sites) !== false) {
                $webSitesLabels[] = $v['label'];
            }
        }
        $website = implode(", ", array_unique($webSitesLabels));
        return $website;
    }
}
