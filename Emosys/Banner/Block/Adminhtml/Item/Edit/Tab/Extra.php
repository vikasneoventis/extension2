<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Block\Adminhtml\Item\Edit\Tab;

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Cms page edit form main tab
 */
class Extra extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Emosys\Banner\Model\Item */
        $model = $this->_coreRegistry->registry('emosys_banner_item');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Emosys_Banner::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('item_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Extra Config')]);

        $fieldset->addField(
            'extra_option_autoplay',
            'text',
            [
                'label' => __('Auto Play?'),
                'title' => __('Auto Play?'),
                'name' => 'extra_option[autoplay]',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'extra_option_speed',
            'text',
            [
                'label' => __('Duration Speed?'),
                'title' => __('Duration Speed?'),
                'name' => 'extra_option[speed]',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'extra_option_slides_per_view',
            'text',
            [
                'label' => __('Slides per View?'),
                'title' => __('Slides per View?'),
                'name' => 'extra_option[slides_per_view]',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'extra_option_show_thumb',
            'select',
            [
                'label' => __('Show Thumb?'),
                'title' => __('Show Thumb?'),
                'name' => 'extra_option[show_thumb]',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'extra_option_direction',
            'select',
            [
                'label' => __('Direction?'),
                'title' => __('Direction?'),
                'name' => 'extra_option[direction]',
                'options' => $model->getDirections(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'extra_option_effect',
            'select',
            [
                'label' => __('Effect'),
                'title' => __('Effect'),
                'name' => 'extra_option[effect]',
                'options' => $model->getEffects(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'extra_option_keyboard_control',
            'select',
            [
                'label' => __('Keyboard Control?'),
                'title' => __('Keyboard Control?'),
                'name' => 'extra_option[keyboard_control]',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'extra_option_mousewheel_control',
            'select',
            [
                'label' => __('Mousewheel Control?'),
                'title' => __('Mousewheel Control?'),
                'name' => 'extra_option[mousewheel_control]',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'extra_option_pagination_type',
            'select',
            [
                'label' => __('Pagination Type?'),
                'title' => __('Pagination Type?'),
                'name' => 'extra_option[pagination_type]',
                'options' => $model->getPaginationTypes(),
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_emosys_banner_item_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Banner Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Banner Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
}
