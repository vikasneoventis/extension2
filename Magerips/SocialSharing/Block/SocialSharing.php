<?php
namespace Magerips\SocialSharing\Block;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class SocialSharing extends \Magento\Framework\View\Element\Template
{
    public $_coreRegistry;
    public $_product;
    public $_priceHelper;    

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PricingHelper $PricingHelper,
        array $data = array() )
    {
        $this->_priceHelper = $PricingHelper;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
        $this->_product = $this->_coreRegistry->registry('current_product');
    }

    public function enableWhatsapp() {
        return $this->_scopeConfig->getValue('socialsharing/whatsapp/enable_whatsapp', ScopeInterface::SCOPE_STORE);
    }

    public function enableFacebook() {
        return $this->_scopeConfig->getValue('socialsharing/facebook/enable_facebook', ScopeInterface::SCOPE_STORE);
    }

    public function enableTwitter() {
        return $this->_scopeConfig->getValue('socialsharing/twitter/enable_twitter', ScopeInterface::SCOPE_STORE);;
    }

    public function enableGoogleplus() {
        return $this->_scopeConfig->getValue('socialsharing/googleplus/enable_google_plus', ScopeInterface::SCOPE_STORE);;
    }

    public function enablePinit() {
        return $this->_scopeConfig->getValue('socialsharing/pinitsharing/enable_pinit', ScopeInterface::SCOPE_STORE);;
    }

    public function enableGoogleshare() {
        return $this->_scopeConfig->getValue('socialsharing/googleplus/enable_google_share', ScopeInterface::SCOPE_STORE);;
    }

    public function getSharetext() {

        $_product=$this->_product;
                
        $productName         = $_product->getName();
        $productPrice         = strip_tags($this->_priceHelper->currency(round($_product->getPrice(),2)));
        $limit=$this->_scopeConfig->getValue('socialsharing/whatsapp/description_limit', ScopeInterface::SCOPE_STORE);
        $productShortdesc     = substr(strip_tags($_product->getDescription()),0,$limit);
        $productsData = $this->_scopeConfig->getValue('socialsharing/whatsapp/share_desc', ScopeInterface::SCOPE_STORE);
        $productsData = nl2br($productsData);
        $productsData = str_replace(array("[product-title]","[product-price]","[product-description]"),array($productName,$productPrice,$productShortdesc),$productsData);
        $productsData = str_replace(array("<br>","<br/>","<br />"),array("%0a","%0a","%0a"),$productsData);
        
        return $productsData;
    }
    
    public function getButtonSize() {

        $buttonSize = $this->_scopeConfig->getValue('socialsharing/whatsapp/button_size', ScopeInterface::SCOPE_STORE);
        switch($buttonSize){
        case 'small':
            $class="wa_btn_s";
            break;
        case 'medium':
            $class="wa_btn_m";
            break;
        case 'large':
            $class="wa_btn_l";
            break;
        default:
            $class="wa_btn_m";
            break;
        }
        return $class;
    }
    
    public function getWhatsappButton(){
        $buttonText=$this->_scopeConfig->getValue('socialsharing/whatsapp/button_text', ScopeInterface::SCOPE_STORE);
        return '<div class="whatsapp_button social_button"> <a href="whatsapp://send" data-text="'.$this->getSharetext().'" data-href="'.$this->_product->getProductUrl().'" class="wa_btn '.$this->getButtonSize().'" style="display:none;">'.$buttonText.'</a> </div>';
    }
    
    public function getFacebookButton(){
        
     $facebookID=$this->_scopeConfig->getValue('socialsharing/facebook/fb_id', ScopeInterface::SCOPE_STORE);
     $displayLike=$this->_scopeConfig->getValue('socialsharing/facebook/display_onlylike', ScopeInterface::SCOPE_STORE);
     $displayFbCount=$this->_scopeConfig->getValue('socialsharing/facebook/display_facebook_count',ScopeInterface::SCOPE_STORE);
     $facebookID = ( $facebookID != "") ?  $facebookID : '184881481528086';
     $like_button = ($displayLike == 1) ? false : true;
     $count_button = ($displayFbCount == 1) ? 'button_count' : 'button';
    
return '<div class="facebook_button social_button">
 <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=<?php echo $facebookID; ?>";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>

  <div class="fb-like" data-layout="'.$count_button.'" data-width="400" data-show-faces="false"  data-href="'.$this->_product->getProductUrl().'"  data-send="'.$like_button.'"></div></div>';
    }
    
    public function getTwitterButton(){
        return "<div class='twitter_button social_button'>
  <a href='https://twitter.com/share' class='twitter-share-button' data-url='".$this->_product->getProductUrl()."' >Tweet</a>
  <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>";
        }
        
        public function getPinItButton(){
            
            $count_button=$this->_scopeConfig->getValue('socialsharing/pinitsharing/display_pinit_count', ScopeInterface::SCOPE_STORE);
  $count_button = ($count_button == 1) ? 'beside' : 'none';

  return '<div class="pinit_button social_button">
  <a href="//www.pinterest.com/pin/create/button/?url='.urlencode($this->_product->getProductUrl()).'&description='.urlencode($this->_product->getName()).'" data-pin-do="buttonPin" data-pin-color="red" data-pin-config="'.$count_button.'"  data-pin-height="20">pinit</a>
  </div>
  <script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>';
        }
        
        public function getGooglePlusButton(){
            $count_button=$this->_scopeConfig->getValue('socialsharing/googleplus/display_google_count', ScopeInterface::SCOPE_STORE);
      $count_button = ($count_button == 1) ? 'bubble' : 'none';
            return '<div class="google_button social_button">
  <div class="g-plusone" data-size="medium"  data-annotation="'.$count_button.'"></div>
   </div>';
            }
            public function getGoogleShareButton(){
            $count_button=$this->_scopeConfig->getValue('socialsharing/googleplus/display_google_count', ScopeInterface::SCOPE_STORE);
      $count_button = ($count_button == 1) ? 'bubble' : 'none';
            return '<div class="googleshare_button social_button"> 
  <div class="g-plus" data-action="share" data-size="medium"  data-annotation="'.$count_button.'"></div>
  </div>';
            }

}