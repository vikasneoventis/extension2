<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Block\Adminhtml\Item\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('item_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Banner Information'));
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('Banner Info'),
                'content' => $this->getLayout()->createBlock(
                    'Emosys\Banner\Block\Adminhtml\Item\Edit\Tab\Main'
                )->toHtml()
            ]
        )->addTab(
            'extra_section',
            [
                'label' => __('Extra Config'),
                'content' => $this->getLayout()->createBlock(
                    'Emosys\Banner\Block\Adminhtml\Item\Edit\Tab\Extra'
                )->toHtml()
            ]
        );
    }
}
