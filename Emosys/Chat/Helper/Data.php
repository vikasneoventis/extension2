<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Chat
 */
namespace Emosys\Chat\Helper;

use Magento\Framework\App\ActionInterface;

/**
 * Wishlist Data Helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config key 'Enabled'
     */
    const XML_PATH_CHAT_ENABLED = 'emosys_chat/general/enabled';

    /**
     * Config key 'Key'
     */
    const XML_PATH_CHAT_KEY = 'emosys_chat/general/key';

    /**
     * Config key 'Language'
     */
    const XML_PATH_CHAT_LANGUAGE = 'emosys_chat/general/language';

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    )
    {
        $this->_localeResolver = $localeResolver;
        parent::__construct($context);
    }

    /**
     * Get Zopim enabled
     *
     * @return string
     */
    public function getEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CHAT_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Zopim chat key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CHAT_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Zopim chat key
     *
     * @return string
     */
    public function getLanguage()
    {
        $language = $this->scopeConfig->getValue(
            self::XML_PATH_CHAT_LANGUAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($language == 'auto') {
            return null;
        } elseif ($language == 'md') {
            return "\$zopim.livechat.setLanguage('" . substr($this->_localeResolver->getLocale(), 0, 2)."');" . "\n";
        }
        return "\$zopim.livechat.setLanguage('" . $language . "');" . "\n";
    }
}
