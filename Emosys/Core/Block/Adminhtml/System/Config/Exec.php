<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml VAT ID validation block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Emosys\Core\Block\Adminhtml\System\Config;

class Exec extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Command Field Name
     *
     * @var string
     */
    protected $_commandField = 'emosys_core_advanced_command';

    /**
     * Argument Field Name
     *
     * @var string
     */
    protected $_argsField = 'emosys_core_advanced_args';

    /**
     * Validate VAT Button Label
     *
     * @var string
     */
    protected $_execButtonLabel = 'Exec CLI Command';

    /**
     * Set Merchant Country Field Name
     *
     * @param string $countryField
     * @return \Magento\Customer\Block\Adminhtml\System\Config\Validatevat
     */
    public function setCommandField($name)
    {
        $this->_commandField = $name;
        return $this;
    }

    /**
     * Get Merchant Country Field Name
     *
     * @return string
     */
    public function getCommandField()
    {
        return $this->_commandField;
    }

    /**
     * Set Merchant Country Field Name
     *
     * @param string $countryField
     * @return \Magento\Customer\Block\Adminhtml\System\Config\Validatevat
     */
    public function setArgsField($name)
    {
        $this->_argsField = $name;
        return $this;
    }

    /**
     * Get Merchant Country Field Name
     *
     * @return string
     */
    public function getArgsField()
    {
        return $this->_argsField;
    }

    /**
     * Set Validate VAT Button Label
     *
     * @param string $vatButtonLabel
     * @return \Magento\Customer\Block\Adminhtml\System\Config\Validatevat
     */
    public function setExecButtonLabel($execButtonLabel)
    {
        $this->_execButtonLabel = $execButtonLabel;
        return $this;
    }

    /**
     * Set template to itself
     *
     * @return \Magento\Customer\Block\Adminhtml\System\Config\Validatevat
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/exec.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_execButtonLabel;
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('emosys_core/system_config/exec'),
            ]
        );

        return $this->_toHtml();
    }
}
