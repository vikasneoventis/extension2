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
 * SocialLogin 	Resource/Setup Model
 *
 * @category   	Ced
 * @package    	Ced_SocialLogin
 * @author		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */

namespace Ced\SocialLogin\Model\Resource;

class Setup extends \Magento\Eav\Model\Entity\Setup

{

    protected $_customerAttributes = array();

    /**

     *

     * @param array $customerAttributes

     * @return \Ced\SocialLogin\Model\Resource\Setup

     */

    public function setCustomerAttributes(array $customerAttributes)

    {

        $this->_customerAttributes = $customerAttributes;

        return $this;

    }

   /**

     * Add our custom attributes

     *

     * @return \Ced\SocialLogin\Model\Resource\Setup

     */

    public function installCustomerAttributes()

    {

        foreach ($this->_customerAttributes as $code => $attr) {

            $this->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $code, $attr);

        }

        return $this;

    }

    /**

     * Remove custom attributes

     *

     * @return \Ced\SocialLogin\Model\Resource\Setup

     */

    public function removeCustomerAttributes()

    {

        foreach ($this->_customerAttributes as $code => $attr) {

            $this->removeAttribute('customer', $code);

        }

        return $this;

    }

}