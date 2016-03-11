<?php
/**
 * Created by: IWD Agency "iwdagency.com"
 * Developer: Andrew Chornij "iwd.andrew@gmail.com"
 * Date: 16.11.2015
 */
namespace IWD\NewsletterPopup\Block;

use Magento\Framework\View\Element\Template;

class Subscribe extends Template{

    protected $_helper;

    public function __construct(
        Template\Context $context, array $data,
        \IWD\NewsletterPopup\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', ['_secure' => true]);
    }

    public function getEnable(){
        return $this->_helper->getEnable();
    }
    public function getPopupWidth(){
        return $this->_helper->getPopupWidth();
    }
    public function getPopupTopPosition(){
        return $this->_helper->getPopupTopPosition();
    }
    public function getButtonColor(){
        return $this->_helper->getButtonColor();
    }
    public function getButtonHoverColor(){
        return $this->_helper->getButtonHoverColor();
    }
    public function getButtonTextColor(){
        return $this->_helper->getButtonTextColor();
    }
    public function getButtonHoverTextColor(){
        return $this->_helper->getButtonHoverTextColor();
    }
    public function getPopupParams(){
        return $this->_helper->getJsonConfig();
    }

    public function getSocialIcons(){
        return $this->_helper->getSocialIcons();
    }

    public function getSocialIconsColor(){
        return $this->_helper->getSocialIconsColor();
    }
}
