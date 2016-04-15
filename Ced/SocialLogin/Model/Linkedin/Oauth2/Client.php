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
 * SocialLogin 	Linkedin/Oauth2/Client Model
 *
 * @category   	Ced
 * @package    	Ced_SocialLogin
 * @author		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */






namespace Ced\SocialLogin\Model\Linkedin\Oauth2;

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

   

    const REDIRECT_URI_ROUTE = 'cedsociallogin/linkedin/connect';

    const XML_PATH_ENABLED = 'cedcommerce/ced_sociallogin_linkedin/enabled';

    const XML_PATH_CLIENT_ID = 'cedcommerce/ced_sociallogin_linkedin/client_id';

    const XML_PATH_CLIENT_SECRET = 'cedcommerce/ced_sociallogin_linkedin/client_secret';

    const OAUTH2_REVOKE_URI = 'https://accounts.google.com/o/oauth2/invalidateToken';

    const OAUTH2_TOKEN_URI = 'https://www.linkedin.com/uas/oauth2/accessToken';

    const OAUTH2_AUTH_URI = 'https://www.linkedin.com/uas/oauth2/authorization';

    const OAUTH2_SERVICE_URI = 'https://api.linkedin.com/v1';







    protected $isEnabled = null;



    protected $clientId = null;



    protected $clientSecret = null;



    protected $redirectUri = null;



    protected $state = '';



    protected $scope = array('r_basicprofile', 'r_emailaddress');

 



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

     * @throws \Magento\Framework\Exception\LocalizedException

     */

    public function setAccessToken($token)

    {

        //$this->token = $token;

        $this->token = json_decode($token);



        $this->extendAccessToken();

    }

    /**

     * @return \StdClass

     */

    public function getAccessToken()

    {
		if(empty($this->token)) {
            $this->fetchAccessToken();
        }

        return json_encode($this->token);

    }

    /**

     * @return string

     */

    public function createAuthUrl()

    {

        $url =

        self::OAUTH2_AUTH_URI.'?'.

            http_build_query(

                array(



                    'response_type' => 'code',



                    'redirect_uri' => $this->redirectUri,



                    'client_id' => $this->clientId,



                    'scope' => implode(" ", $this->scope),



                    'state' => $this->state,



                    )

            );

        return $url;







    }

    /**

     * @param $endpoint

     * @param string $method

     * @param array $params

     * @return mixed

     * @throws Exception

     * @throws \Magento\Framework\Exception\LocalizedException

     */

    public function api($endpoint, $method = 'GET', $params = array())

    {
       
	 if(empty($this->token)) {
            $this->fetchAccessToken();
        }
        $url = self::OAUTH2_SERVICE_URI.$endpoint;

      $method = strtoupper($method);

        $params = array_merge(array(
            'oauth2_access_token' => $this->token->access_token
        ), $params);



        $response = $this->_httpRequest($url, $method, $params);

        return $response;

    }

    /**

     * @param null $code

     * @return \StdClass

     * @throws Exception

     * @throws \Magento\Framework\Exception\LocalizedException

     */

    protected function fetchAccessToken($code = null)

    {

        if(empty($_REQUEST['code'])) {

            throw new \Magento\Framework\Exception\LocalizedException(

                __('Unable to retrieve access code.')

            );

        }
	
        $response = $this->_httpRequest(

            self::OAUTH2_TOKEN_URI,

            'POST',

           array(



                'code' => $_REQUEST['code'],



                'redirect_uri' => $this->redirectUri,



                'client_id' => $this->clientId,



                'client_secret' => $this->clientSecret,



                'grant_type' => 'authorization_code'



            )

        );
        $this->token = $response;

        //$this->setAccessToken($response);
        return $this->getAccessToken();

    }

    /**

     * @return mixed

     * @throws Exception

     * @throws \Magento\Framework\Exception\LocalizedException

     */

    public function extendAccessToken()

    {

        $accessToken = $this->getAccessToken();

        if(!$accessToken) {

        throw new \Magento\Framework\Exception\LocalizedException(

                __('Unable to retrieve access token.')

            );

        }

        // Expires over two hours means long lived token

        if($accessToken->expires > 7200) {

            // Long lived token, no need to extend

            return $this->getAccessToken();

        }

        $response = $this->_httpRequest(

            self::OAUTH2_TOKEN_URI,

            'GET',

            array(

                'client_id' => $this->_getClientId(),

                'client_secret' => $this->getClientSecret(),

                'fb_exchange_token' => $this->getAccessToken()->access_token,

                'grant_type' => 'fb_exchange_token'

            )

        );

        $this->setAccessToken($response);

        return $this->getAccessToken();

    }

    /**

     * @param $url

     * @param string $method

     * @param array $params

     * @return mixed

     * @throws Exception

     * @throws \Magento\Framework\Exception\LocalizedException

     * @throws \Zend_Http_Client_Exception

     */

    protected function _httpRequest($url, $method = 'GET', $params = array())

    {

        $client = new \Zend_Http_Client($url, array('timeout' => 60));


        switch ($method) {

            case 'GET':

                $client->setParameterGet($params);

                break;

            case 'POST':

                $client->setParameterPost($params);

                break;

            case 'DELETE':

                break;

            default:

                throw new \Magento\Framework\Exception\LocalizedException(
                        __('Required HTTP method is not supported.')
                );

        }

;
        $response = $client->request($method);



       // Mage::log($response->getStatus().' - '. $response->getBody());
        $decoded_response = json_decode($response->getBody());


        if($response->isError()) {

            $status = $response->getStatus();
            if(($status == 400 || $status == 401)) {

                if(isset($decoded_response->error->message)) {

                    $message = $decoded_response->error->message;

                } else {

                    $message = __('Unspecified OAuth error occurred.');

                }



                throw new \Magento\Framework\Exception\LocalizedException($message);

            } else {

                $message = sprintf(
                       __('HTTP error %d occurred while issuing request.'),
                    $status

                );



                throw new \Magento\Framework\Exception\LocalizedException($message);

            }

        }



        return $decoded_response;

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

	



















    public function revokeToken()



    {



        if(empty($this->token)) {



            throw new Exception(



                Mage::helper('sociallogin')



                    ->__('No access token available.')



            );



        }







        if(empty($this->token->refresh_token)) {



            throw new Exception(



                Mage::helper('sociallogin')



                    ->__('No refresh token, nothing to revoke.')



            );



        }







        $this->_httpRequest(



            self::OAUTH2_REVOKE_URI,



            'POST',



           array(



               'token' => $this->token->refresh_token



           )



        );



    }







    protected function refreshAccessToken()



    {



        if(empty($this->token->refresh_token)) {



            throw new Exception(



                Mage::helper('sociallogin')



                    ->__('No refresh token, unable to refresh access token.')



            );



        }







        $response = $this->_httpRequest(



            self::OAUTH2_TOKEN_URI,



            'POST',



            array(



                'client_id' => $this->clientId,



                'client_secret' => $this->clientSecret,



                'refresh_token' => $this->token->refresh_token,



                'grant_type' => 'refresh_token'



            )



        );







        $this->token->access_token = $response->access_token;



        $this->token->expires_in = $response->expires_in;



        $this->token->created = time();



    }







    protected function isAccessTokenExpired() {



        // If the token is set to expire in the next 30 seconds.



        $expired = ($this->token->created + ($this->token->expires_in - 30)) < time();







        return $expired;



    }









}



