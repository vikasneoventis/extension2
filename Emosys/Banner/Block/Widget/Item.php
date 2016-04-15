<?php
/**
 * @author Emosys team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Block\Widget;

/**
 * Cms Static Block Widget
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Item extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Storage for used widgets
     *
     * @var array
     */
    protected static $_widgetUsageMap = [];

    /**
     * Block factory
     *
     * @var \Emosys\Banner\Model\ItemFactory
     */
    protected $_itemFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Emosys\Banner\Model\ItemFactory $itemFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_itemFactory = $itemFactory;
    }

    /**
     * Prepare block text and determine whether block output enabled or not
     * Prevent blocks recursion if needed
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $itemId = $this->getData('item_id');
        $itemHash = get_class($this) . $itemId;

        if (isset(self::$_widgetUsageMap[$itemHash])) {
            return $this;
        }
        self::$_widgetUsageMap[$itemHash] = true;

        if ($itemId) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var \Emosys\Banner\Model\Item $item */
            $item = $this->_itemFactory->create();
            $item->setStoreId($storeId)->load($itemId);
            if ($item->isActive()) {
                $this->setItem($item);
            }
        }

        unset(self::$_widgetUsageMap[$itemHash]);
        return $this;
    }
}