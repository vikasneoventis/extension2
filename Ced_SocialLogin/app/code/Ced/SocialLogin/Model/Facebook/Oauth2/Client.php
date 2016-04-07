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
 * SocialLogin 	Facebook/Oauth2/Client Model
 *
 * @category   	Ced
 * @package    	Ced_SocialLogin
 * @author		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */
namespace Ced\SocialLogin\Model\Facebook\Oauth2;

class Client extends \Magento\Framework\DataObject

{

    const REDIRECT_URI_ROUTE = 'cedsociallogin/facebook/connect';

    const XML_PATH_ENABLED = 'cedcommerce/ced_sociallogin_facebook/enabled';

    const XML_PATH_CLIENT_ID = 'cedcommerce/ced_sociallogin_facebook/client_id';

    const XML_PATH_CLIENT_SECRET = 'cedcommerce/ced_sociallogin_facebook/client_secret';

    const OAUTH2_SERVICE_URI = 'https://graph.facebook.com';

    const OAUTH2_AUTH_URI = 'https://graph.facebook.com/oauth/authorize';

    const OAUTH2_TOKEN_URI = 'https://graph.facebook.com/oauth/access_token';

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

    /**

     *

     * @var string

     */

    protected $clientId = null;

    /**

     * @var mixed

     */

    protected $clientSecret = null;

    /**

     *

     * @var string

     */

    protected $redirectUri = null;

    /**

     *

     * @var string

     */

    protected $state = null;

    /**

     *

     * @var array

     */

    protected $scope = array('public_profile','email','user_birthday');

    /**

     *

     * @var \StdClass

     */

    protected $token;

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

     * @throws \Magento\Framework\Exception

     */

    public function setAccessToken(\StdClass $token)

    {

        $this->token = $token;


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

                    'client_id' => $this->getClientId(),

                    'redirect_uri' => $this->getRedirectUri(),

                    'state' => $this->getState(),

                    'scope' => implode(',', $this->getScope())

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

     * @throws \Magento\Framework\Exception

     */

    public function api($endpoint, $method = 'GET', $params = array())
    {

	
	
	 if(empty($this->token)) {
            $this->fetchAccessToken();
        }

        $url = self::OAUTH2_SERVICE_URI.$endpoint;

      $method = strtoupper($method);

        $params = array_merge(array(
            'access_token' => $this->token->access_token
        ), $params);


        $response = $this->_httpRequest($url, $method, $params);

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

                'redirect_uri' => $this->getRedirectUri(),

                'client_id' => $this->getClientId(),

                'client_secret' => $this->getClientSecret(),

                'grant_type' => 'authorization_code'

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

     * @throws \Magento\Framework\Exception

     * @throws \Zend_Http_Client_Exception

     */

    protected function _httpRequest($url, $method = 'GET', $params = array())

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

        //$this->_helperData->log($response->getStatus().' - '. $response->getBody());




        $decodedResponse = json_decode($response->getBody());

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

                    $message = $decodedResponse->error->message;

                } else {

                    $message = __('Unspecified OAuth error occurred.');

                }

                throw new \Magento\Framework\Exception\LocalizedException(__($message));

            } else {

                $message = sprintf(

                    __('HTTP error %d occurred while issuing request.'),

                    $status

                );

                throw new \Magento\Framework\Exception\LocalizedException(__($message));

            }

        }

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

}