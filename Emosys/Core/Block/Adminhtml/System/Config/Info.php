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

class Info extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<div style="background:url(' ."'http://www.emosys.com/_logo.png'". ') no-repeat scroll 15px center #EAF0EE; border:1px solid #CCCCCC; margin-bottom:10px; padding:10px 5px 5px 210px;">
                <p>Emosys is a dynamic market leading provider of Magento extensions and custom development services. With 10+ Magento certificated developers we ensure providing best-in-class solutions for your eCommerce businesses.</p>
                <p>View more extensions @ <a href="http://www.emosys.com/magento-extensions.html" target="_blank">www.emosys.com/magento-extensions.html</a><br />
                <a href="http://www.emosys.com/contact-us.html" target="_blank">Request a FREE Quote / Contact Us</a><br />
                Skype me @ <a href="skype:emosys.com?chat">emosys.com</a><br />
                E : <a href="mailto:info@emosys.com">info@emosys.com</a><br />
                W : <a href="http://www.emosys.com" target="_blank">www.emosys.com</a></p>
            </div>';

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Decorate field row html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
    }
}
