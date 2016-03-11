<?php
/**
 * Created by: IWD Agency "iwdagency.com"
 * Developer: Andrew Chornij "iwd.andrew@gmail.com"
 * Date: 14.12.2015
 */
namespace IWD\NewsletterPopup\Block\Adminhtml\System\Config;

class Documentation extends \Magento\Config\Block\System\Config\Form\Field{

    protected $user_guide_url = "https://iwdagency.com/help/m2-newsletter-pop-up/newsletter-2-settings";

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return sprintf("<span style='display: block; margin-bottom: -8px;'><a href='%s' target='_blank'>%s</a></span>", $this->user_guide_url, __("User Guide"));
    }
}