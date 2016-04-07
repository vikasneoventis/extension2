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
 * SocialLogin 	Twitter/Oauth2/Client Model
 *
 * @category   	Ced
 * @package    	Ced_SocialLogin
 * @author		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */
namespace Ced\SocialLogin\Model\Twitter\Oauth2;

class Client extends \Magento\Framework\DataObject

{

   

    /**

     * @var \Magento\Backend\App\ConfigInterface

     */

    protected $_config;

    /**

     *

     * @var \Magento\Framework\HTTP\ZendClientFactory

     */

    protected $_httpClientFactory;

    /**

     * Url 

     *

     * @var \Magento\Framework\UrlInterface

     */

    protected $_url;

    /**

     *

     * @var \Ced\SocialLogin\Helper\Data

     */

    protected $_helperData;
	protected $_customerSession;
    const REDIRECT_URI_ROUTE = 'cedsociallogin/twitter/connect';

    const REQUEST_TOKEN_URI_ROUTE = 'cedsociallogin/twitter/request';

 

    const OAUTH_URI = 'https://api.twitter.com/oauth';
    const OAUTH2_SERVICE_URI = 'https://api.twitter.com/1.1';    


    const XML_PATH_ENABLED = 'cedcommerce/ced_sociallogin_twitter/enabled';

    const XML_PATH_CLIENT_ID = 'cedcommerce/ced_sociallogin_twitter/client_id';

    const XML_PATH_CLIENT_SECRET = 'cedcommerce/ced_sociallogin_twitter/client_secret';



    protected $clientId = null;

    protected $clientSecret = null;

    protected $redirectUri = null;

    protected $client = null;

    protected $token = null;
	

    /**

     *

     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory

     * @param \Magento\Backend\App\ConfigInterface $config

     * @param \Magento\Framework\UrlInterface $url

     * @param \Ced\SocialLogin\Helper\Data $helperData

     * @param array $data

     */

    public function __construct(

            \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,

            \Magento\Backend\App\ConfigInterface $config,

            \Magento\Framework\UrlInterface $url,

            \Ced\SocialLogin\Helper\Data $helperData,
			
			\Magento\Customer\Model\Session $customerSession,

            // Parent

            array $data = array())

    {

        $this->_httpClientFactory = $httpClientFactory;

        $this->_config = $config;

        $this->_url = $url;

        $this->redirectUri = $this->_url->sessionUrlVar(

            $this->_url->getUrl(self::REDIRECT_URI_ROUTE)

        );
		
		

        $this->_helperData = $helperData;

        $this->clientId = $this->_getClientId();

        $this->clientSecret = $this->_getClientSecret();

        $this->_config = $config;
		$this->_customerSession = $customerSession;
		
		
		 $this->client = new \Zend_Oauth_Consumer(
                array(
                    'callbackUrl' => $this->redirectUri,
                    'siteUrl' => self::OAUTH_URI,
                    'authorizeUrl' => self::OAUTH_URI.'/authenticate',
                    'consumerKey' => $this->clientId,
                    'consumerSecret' => $this->clientSecret
                )
            );

        parent::__construct($data);

    }

    /**

     * @return bool

     */

    public function isEnabled()

    {

        return (bool) $this->_isEnabled();

    }

    /**

     * @return mixed|string

     */

    public function getClientId()

    {

        return $this->clientId;

    }

    /**

     * @return mixed|string

     */

    public function getClientSecret()

    {

        return $this->clientSecret;

    }

    /**

     * @return string

     */

    public function getRedirectUri()

    {

        return $this->redirectUri;

    }

    /**

     * @return array

     */

    public function getScope()

    {

        return $this->scope;

    }

    /**

     * @return string

     */

    public function getState()

    {

        return $this->state;

    }

    /**

     * @param $state

     */

    public function setState($state)

    {

        $this->state = $state;

    }

    /**

     * @param \StdClass $token

     * @throws \Magento\Framework\Exception

     */

    
    public function setAccessToken($token)
    {
        $this->token = unserialize($token);
    }

    /**

     * @return \StdClass

     */

    public function getAccessToken()

    {

      if(empty($this->token)) {
            $this->fetchAccessToken();
        }
        return serialize($this->token);

    }

    /**

     * @return string

     */

    public function createAuthUrl()

    {

        

               return $this->_url->getUrl(self::REQUEST_TOKEN_URI_ROUTE);



    }

    /**

     * @param $endpoint

     * @param string $method

     * @param array $params

     * @return mixed

     * @throws Exception

     * @throws \Magento\Framework\Exception

     */

    public function api($endpoint, $method = 'GET', $params = array())

    {

      if(empty($this->token)) {
            throw new \Magento\Framework\Exception\LocalizedException(
               __('Unable to proceed without an access token.')
            );
        }

        $url = self::OAUTH2_SERVICE_URI.$endpoint; 
        $response = $this->_httpRequest($url, strtoupper($method), $params);

        return $response;

    }

    /**

     * @param null $code

     * @return \StdClass

     * @throws Exception

     * @throws \Magento\Framework\Exception

     */

    protected function fetchAccessToken($code = null)

    {
	
	 if (!($params = $_REQUEST)
            ||
            !($requestToken = $this->_customerSession
                ->getTwitterRequestToken())
            ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Unable to retrieve access code.')
            );
        }
       
	    if(!($token = $this->client->getAccessToken(
                    $params,
                    unserialize($requestToken)
                )
            )
        ) {
           throw new \Magento\Framework\Exception\LocalizedException(
               __('Unable to retrieve access token.')
            );
        }

        $this->_customerSession->unsTwitterRequestToken();

        return $this->token = $token;
		
		
		
    }


    /**

     * @param $url

     * @param string $method

     * @param array $params

     * @return mixed

     * @throws Exception

     * @throws \Magento\Framework\Exception

     * @throws \Zend_Http_Client_Exception

     */
	 
	 protected function _httpRequest($url, $method = 'GET', $params = array())
    {
        $client = $this->token->getHttpClient(
            array(
                'callbackUrl' => $this->redirectUri,
                'siteUrl' => self::OAUTH_URI,
                'consumerKey' => $this->clientId,
                'consumerSecret' => $this->clientSecret
            )
        );

        $client->setUri($url);
        
        switch ($method) {
            case 'GET':
                $client->setMethod(\Zend_Http_Client::GET);
                $client->setParameterGet($params);
                break;
            case 'POST':
                $client->setMethod(\Zend_Http_Client::POST);
                $client->setParameterPost($params);
                break;
            case 'DELETE':
                $client->setMethod(\Zend_Http_Client::DELETE);
                break;
            default:
                throw new Exception(
                    Mage::helper('sociallogin')
                        ->__('Required HTTP method is not supported.')
                );
        }

        $response = $client->request();

      //  Mage::log($response->getStatus().' - '. $response->getBody());

        $decoded_response = json_decode($response->getBody());
        if($response->isError()) {
            $status = $response->getStatus();
            if(($status == 400 || $status == 401 || $status == 429)) {
                if(isset($decoded_response->error->message)) {
                    $message = $decoded_response->error->message;
                } else {
                    $message = Mage::helper('sociallogin')
                        ->__('Unspecified OAuth error occurred.');
                }

                throw new Ced_SocialLogin_TwitterOAuthException($message);
            } else {
                $message = sprintf(
                    Mage::helper('sociallogin')
                        ->__('HTTP error %d occurred while issuing request.'),
                    $status
                );

                throw new Exception($message);
            }
        }

        return $decoded_response;
    }

    protected function _httpRequest11($url, $method = 'GET', $params = array())

    {

        $client = $this->_httpClientFactory->create();

        $client->setUri($url);

        switch ($method) {

            case 'GET':

                $client->setParameterGet($params);

                break;

            case 'POST':

                $client->setParameterPost($params);

                break;

            case 'DELETE':

                $client->setParameterGet($params);

                break;

            default:

                throw new \Magento\Framework\Exception\LocalizedException(

                    __('Required HTTP method is not supported.')

                );

        }

        $response = $client->request($method);

       // $this->_helperData->log($response->getStatus().' - '. $response->getBody());

        $decodedResponse = json_decode($response->getBody());
		echo "uri: ".$url;
		echo "<br> method: ".$method;

echo "paramss";		print_r($params);

        /*

         * Per http://tools.ietf.org/html/draft-ietf-oauth-v2-27#section-5.1

         * Facebook should return data using the "application/json" media type.

         * Facebook violates OAuth2 specification and returns string. If this

         * ever gets fixed, following condition will not be used anymore.

         */

        if(empty($decodedResponse)) {

            $parsed_response = array();

            parse_str($response->getBody(), $parsed_response);

            $decodedResponse = json_decode(json_encode($parsed_response));

        }

        if($response->isError()) { 

            $status = $response->getStatus();

            if(($status == 400 || $status == 401)) {

                if(isset($decodedResponse->error->message)) {
echo "dee1: Unspecified OAuth error occurred.".$decodedResponse->error->message;die;
                    $message = $decodedResponse->error->message;
                } else {
echo "dee2: Unspecified OAuth error occurred.";die;
                    $message = __('Unspecified OAuth error occurred.');
                }
echo "dee4: ".$message;die;
                throw new \Magento\Framework\Exception\LocalizedException($message);

            } else {echo 'HTTP error %d occurred while issuing request.';die;

                $message = sprintf(

                    __('HTTP error %d occurred while issuing request.'),

                    $status

                );
echo "dee3: ".$message;die;
                throw new \Magento\Framework\Exception\LocalizedException($message);

            }

        }
print_r($decodedResponse);die;
        return $decodedResponse;



    }

    /**

     * @return mixed

     */

    protected function _isEnabled()

    {

        return $this->_getStoreConfig(self::XML_PATH_ENABLED);

    }

    /**

     * @return mixed

     */

    protected function _getClientId()

    {

        return $this->_getStoreConfig(self::XML_PATH_CLIENT_ID);

    }

    /**

     * @return mixed

     */

    protected function _getClientSecret()

    {

        return $this->_getStoreConfig(self::XML_PATH_CLIENT_SECRET);

    }

    /**

     * @param $xmlPath

     * @return mixed

     */

    protected function _getStoreConfig($xmlPath)

    {

        return $this->_config->getValue($xmlPath);

    }

	

	

    

  



    public function fetchRequestToken()

    {

        if(!($requestToken = $this->client->getRequestToken())) {

           throw new \Magento\Framework\Exception\LocalizedException(
						__('Unable to retrieve request token.')

            );

        }



        $this->_customerSession

            ->setTwitterRequestToken(serialize($requestToken));



        $this->client->redirect();

    }



   



}

