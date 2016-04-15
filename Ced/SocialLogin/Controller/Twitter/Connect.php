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
 * SocialLogin 	Twitter controller
 *
 * @category   	Ced
 * @package    	Ced_SocialLogin
 * @author		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */
      
namespace Ced\SocialLogin\Controller\Twitter;
use Magento\Framework\App\Action\NotFoundException;
 
class Connect extends \Magento\Framework\App\Action\Action
 
{
	/**
     * @var \Ced\SocialLogin\Helper\Twitter
     */
    protected $_helperTwitter;
	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
	/**
     * @var \Ced\SocialLogin\Model\Twitter\Oauth2\Client
     */
	protected $_client;
	protected $_accountRedirect;
    /**

     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Ced\SocialLogin\Model\Twitter\Oauth2\Client $client
     * @param \Ced\SocialLogin\Helper\Twitter $helperTwitter
	 * @param Magento\Customer\Model\Account\Redirect $accountRedirect

     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Ced\SocialLogin\Model\Twitter\Oauth2\Client $client,
        \Ced\SocialLogin\Helper\Twitter $helperTwitter,
		\Magento\Customer\Model\Account\Redirect $accountRedirect
    ){
		$this->_customerSession = $customerSession;
		$this->_client = $client;
		$this->_accountRedirect = $accountRedirect;
		$this->_helperTwitter = $helperTwitter;
		parent::__construct($context);
    }

 

    /**

     * Dispatch request

     *

     * @param RequestInterface $request

     * @return \Magento\Framework\App\ResponseInterface

     * @throws \Magento\Framework\App\Action\NotFoundException

     */

    public function execute()

    {
        try {
            $this->_connectCallback();
			}
       catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        }  catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __("Some error during Twitter login.")
                );
            }
		return $this->_sendResponse();
    }
	
	
	/**

     * connect to twitter account

     */

    protected function _connectCallback() {
	
	 if (!($params = $this->getRequest()->getParams())
            ||
            !($requestToken = unserialize($this->_customerSession
                ->getTwitterRequestToken()))
            ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                    __('Twitter Connect process aborted')
            );
            return;
        }

        $this->referer = $this->_customerSession->getTwitterRedirect();
        
        if(isset($params['denied'])) {
          $this->messageManager
                    ->addNotice(
                        $this->__('Twitter Connect process aborted.')
                    );
            
            return;
        }       

	
	
	
	
	
	
	
        

		 


            $client = $this->_client;
			
			$token = $client->getAccessToken();
			 $userInfo = (object) array_merge(
                (array) ($userInfo = $client->api('/account/verify_credentials.json', 'GET', array('skip_status' => true))),
                array('email' => sprintf('%s@twitter-user.com', strtolower($userInfo->screen_name)))
        	);

            $customersByTwitterId = $this->_helperTwitter
                ->getCustomersByTwitterId($userInfo->id);

				if($this->_customerSession->isLoggedIn()) {
                /* Logged in user*/
                if($customersByTwitterId->count()){
                    /* Twitter account already connected to other account - deny*/
                   $this->messageManager
                        ->addNotice(
                            __('Your Twitter account is already connected to one of our store accounts.')
                        );
                    return;
                }


                /* Connect from account dashboard - attach*/
                $customer = $this->_customerSession->getCustomer();
                $this->_helperTwitter->connectByTwitterId(
                    $customer,
                    $userInfo->id,
                    $token
                );
               $this->messageManager->addSuccess(
                    __('Your Twitter account is now connected to your new user accout at our store. You can login next time by the Twitter SocialLogin button or Store user account. Account confirmation mail has been sent to your email.')
                );
                return;
            }



            if($customersByTwitterId->count()) {
                /* Existing connected user - login*/
                $customer = $customersByTwitterId->getFirstItem();
                $this->_helperTwitter->loginByCustomer($customer);
               	$this->messageManager->addSuccess(
                        __('You have successfully logged in using your Twitter account.')
                    );
                return;
            }



            $customersByEmail = $this->_helperTwitter
                ->getCustomersByEmail($userInfo->email);


            if($customersByEmail->count()) {                
                /* Email account already exists - attach, login*/
                $customer = $customersByEmail->getFirstItem();
                $this->_helperTwitter->connectByTwitterId(
                    $customer->getId(),
                    $userInfo->id,
                    $token
                );


                $this->messageManager->addSuccess(
                    __('We find you already have an account at our store. Your Twitter account is now connected to your store account. Account confirmation mail has been sent to your email.')
                );



                return;

            }



            /* New connection - create, attach, login*/

            if(empty($userInfo->name)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Sorry, could not retrieve your Twitter name. Please try again.')
                );

            }




            $this->_helperTwitter->connectByCreatingAccount(

                $userInfo->email,

                $userInfo->name,

                $userInfo->id,

                $token

            );

            $this->messageManager->addSuccess(

                __('Your Twitter account is now connected to your new user accout at our store. You can login next time by the Twitter SocialLogin button or Store user account. Account confirmation mail has been sent to your email.')

            );
			
 $this->messageManager->addSuccess(
sprintf(__('Since Twitter doesn\'t support third-party access to your email address, we were unable to send you your store accout credentials. To be able to login using store account credentials you will need to update your email address and password using our <a href="%s">Edit Account Information</a>.'), $this->urlModel->getUrl(customer/account/edit)))
		;

    }

	/**

     * success login redirect to the customer account

     */
	protected function _sendResponse()
    {
		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath('customer/account');
		return $resultRedirect;

    }
	

}

?>