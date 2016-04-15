<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Block\Adminhtml\Item;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize blog post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'item_id';
        $this->_blockGroup = 'Emosys_Banner';
        $this->_controller = 'adminhtml_item';

        parent::_construct();

        if ($this->_isAllowedAction('Emosys_Banner::save')) {
            $this->buttonList->update('save', 'label', __('Save Banner'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
            if ( $this->_coreRegistry->registry('emosys_banner_item')->getId() ) {
                $this->addButton(
                    'upload',
                    [
                        'label' => __('Upload Photos'),
                        'onclick' => 'setLocation(\'' . $this->getUploadUrl() . '\')',
                        'class' => 'upload'
                    ],
                    -1
                );
            }
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Emosys_Banner::item_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Banner'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * @return string
     */
    public function getUploadUrl()
    {
        return $this->getUrl('*/photo/new', [$this->_objectId => $this->getRequest()->getParam($this->_objectId)]);
    }

    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('emosys_banner_item')->getId()) {
            return __("Edit Banner '%1'", $this->escapeHtml($this->_coreRegistry->registry('emosys_banner_item')->getTitle()));
        } else {
            return __('New Banner');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('emosys_banner/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}