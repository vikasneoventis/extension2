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
 * SocialLogin 	Data Helper
 *
 * @category   	Ced
 * @package    	Ced_SocialLogin
 * @author		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */
namespace Ced\SocialLogin\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper

{

    /**

     * @var \Magento\Framework\App\State

     */

    protected $_appState;

    /**

     * @param \Magento\Framework\App\Helper\Context $context

     * @param \Magento\Framework\App\State $appState

     */

    public function __construct(

        \Magento\Framework\App\Helper\Context $context,

        \Magento\Framework\App\State $appState)

    {

        $this->_appState = $appState;

        parent::__construct($context);

    }

    public function log1($message, $level = \Zend_Log::DEBUG, $loggerKey = \Magento\Framework\Logger::LOGGER_SYSTEM)

    {

        if($this->_appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER) {

            $this->_logger->log($message, $level, $loggerKey);

        }

    }

}