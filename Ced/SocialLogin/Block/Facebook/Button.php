<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_SocialLogin
 * @author 		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SocialLogin 	Facebook\Button block
 *
 * @category   	Ced
 * @package    	Ced_SocialLogin
 * @author		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */
namespace Ced\SocialLogin\Block\Facebook;

class Button extends \Magento\Framework\View\Element\Template

{

    /**

     * @var \Magento\Framework\Registry

     */

    protected $_registry;

    /**

     * Facebook client model

     *

     * @var \Ced\SocialLogin\Model\Facebook\Oauth2\Client

     */

    protected $_clientFacebook;

    /**

     *

     * @var \Magento\Customer\Model\Session

     */

    protected $_customerSession;

    protected $userInfo = null;

    /**

     * @param \Ced\SocialLogin\Model\Facebook\Oauth2\Client $clientFacebook

     * @param \Magento\Framework\Registry $registry

     * @param \Magento\Customer\Model\Session $customerSession

     * @param \Magento\Framework\View\Element\Template\Context $context

     * @param array $data

     */

    public function __construct(

        \Ced\SocialLogin\Model\Facebook\Oauth2\Client $clientFacebook,

        \Magento\Framework\Registry $registry,

        \Magento\Customer\Model\Session $customerSession,

        \Magento\Framework\View\Element\Template\Context $context,

        array $data = array())

    {

        $this->_clientFacebook = $clientFacebook;

        $this->_registry = $registry;

        $this->_customerSession = $customerSession;

        $this->userInfo = $this->_registry->registry('ced_sociallogin_facebook_userdetails');

        parent::__construct($context, $data);

    }

    protected function _construct()

    {

        parent::_construct();

        // CSRF protection

        $this->_customerSession->setFacebookCsrf($csrf = md5(uniqid(rand(), true)));

        $this->_clientFacebook->setState($csrf);

    }

    /**

     * @return string

     */

    public function getButtonText()

    {

        // Get user info for currently logged in user if it already exists

        $userInfo = $this->_registry->registry('ced_sociallogin_userinfo');

        if (is_null($userInfo) || !$userInfo->hasData()) {

            // No user info, see if we have something set through layout

            if (!($text = $this->getData('button_text'))) {

                // "Connect" is fallback used when text isn't set through layout

                $text = __('Connect');

            }

        } else {

            $text = __('Disconnect');

        }

        return $text;

    }

	

	
    /**

     * @return string

     */
    public function getButtonUrl()
    {
        if(empty($this->userInfo)) {
            return $this->_clientFacebook->createAuthUrl();
        } else {

           return $this->getUrl('cedsociallogin/facebook/disconnect');
       }
    }

	
    /**

     * @return string

     */
	 public function getButtonClass()

    {
        if(empty($this->userInfo)) {

               $text = "ced_fb_connect";
        } else {
               $text = "ced_fb_disconnect";
       }

      return $text;
    }

    /**

     * @return array

     */

    public function getScope()

    {

        return $this->_clientFacebook->getScope();

    }

    /**

     * @return mixed|string

     */

    public function getAppId()

    {

        return $this->_clientFacebook->getClientId();

    }

    /**

     * @return string

     */

    public function getState()

    {

        return $this->_clientFacebook->getState();

    }



}