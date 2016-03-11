<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\Core\Model\Notification;

use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * Notification feed model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Feed extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Config paths
     */
    const XML_PATH_FEED_URL    = 'salesids_core_extensions/admin_notification/feed_url';
    const XML_PATH_USE_HTTPS   = 'salesids_core_extensions/admin_notification/use_https';
    const XML_PATH_FREQUENCY   = 'salesids_core_extensions/admin_notification/frequency';
    const XML_PATH_TYPE_ALLOW  = 'salesids_core_extensions/admin_notification/type_allow';

    /**
     * Module name prefix key
     */
    const MODULE_PREFIX_KEY    = 'SalesIds_';

    /**
     * Feed last check cache key
     */
    const CACHE_KEY_LAST_CHECK = 'salesids_core_admin_notification_lastcheck';

    /**
     * Feed url
     *
     * @var string
     */
    protected $_feedUrl;

    /**
     * Allowed types
     *
     * @var array
     */
    protected $_allowedTypes;

    /**
     * Installed modules
     *
     * @var array
     */
    protected $_installedModules;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_backendConfig;

    /**
     * @var \Magento\AdminNotification\Model\InboxFactory
     */
    protected $_inboxFactory;

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     *
     */
    protected $curlFactory;

    /**
     * Deployment configuration
     *
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $_deploymentConfig;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\ConfigInterface $backendConfig
     * @param InboxFactory $inboxFactory
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_backendConfig    = $backendConfig;
        $this->_inboxFactory     = $inboxFactory;
        $this->curlFactory       = $curlFactory;
        $this->_deploymentConfig = $deploymentConfig;
        $this->productMetadata   = $productMetadata;
        $this->urlBuilder        = $urlBuilder;
        $this->localeResolver    = $localeResolver;
        $this->_moduleList       = $moduleList;
    }

    /**
     * Get locale code
     *
     * @return string
     */
    protected function _getLocaleCode()
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Get notification types allowed
     *
     * @return array
     */
    protected function _getAllowedTypes()
    {
        if (null === $this->_allowedTypes) {
            $this->_allowedTypes = explode(',', $this->_backendConfig->getValue(self::XML_PATH_TYPE_ALLOW));
        }
        return $this->_allowedTypes;
    }

    /**
     * Get modules installed
     *
     * @return array
     */
    protected function _getInstalledModules()
    {
        if (null === $this->_installedModules) {
            $installedModules = array();
            $modules = $this->_moduleList->getNames();
            foreach ($modules as $module) {
                if (strpos($module, self::MODULE_PREFIX_KEY) !== false) {
                    $installedModules[] = $module;
                }
            }
            $this->_installedModules = $installedModules;
        }
        return $this->_installedModules;
    }

    /**
     * Is allowed notification type
     *
     * @param  string $type Notification type
     *
     * @return bool
     */
    protected function _isTypeAllowed($type = '')
    {
        if (null === $this->_allowedTypes) {
            $this->_allowedTypes = $this->_getAllowedTypes();
        }
        if (in_array($type, $this->_allowedTypes)) {
            return true;
        }
        return false;
    }

    /**
     * Is installed module
     *
     * @param  string $module Module
     *
     * @return bool
     */
    protected function _isModuleInstalled($module = '')
    {
        if (null === $this->_installedModules) {
            $this->_installedModules = $this->_getInstalledModules();
        }
        if (in_array($module, $this->_installedModules)) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        if (null === $this->_feedUrl) {
            $httpPath = $this->_backendConfig->isSetFlag(self::XML_PATH_USE_HTTPS) ? 'https://' : 'http://';
            $this->_feedUrl = $httpPath . $this->_backendConfig->getValue(self::XML_PATH_FEED_URL);
        }
        return $this->_feedUrl;
    }

    /**
     * Check feed for modification
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function checkUpdate()
    {
        if ($this->getFrequency() + $this->getLastUpdate() > time()) {
            return $this;
        }

        $feedData = [];

        $feedXml = $this->getFeedData();

        $installDate = strtotime($this->_deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_INSTALL_DATE));

        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                // Exclude notification if its type is not allowed
                $type = (string)$item->type;
                if (!$this->_isTypeAllowed($type)) {
                    continue;
                }
                // Exclude notification if the module defined is not installed
                $module = isset($item->module) ? (string)$item->module : null;
                if ($module && !$this->_isModuleInstalled($module)) {
                    continue;
                }

                $itemPublicationDate = strtotime((string)$item->pubDate);
                if ($installDate <= $itemPublicationDate) {
                    $feedData[] = [
                        'severity'    => (int)$item->severity,
                        'date_added'  => date('Y-m-d H:i:s', $itemPublicationDate),
                        'title'       => (string)$item->title,
                        'description' => (string)$item->description,
                        'url'         => (string)$item->link,
                    ];
                }
            }

            if ($feedData) {
                $this->_inboxFactory->create()->parse(array_reverse($feedData));
            }
        }

        $this->setLastUpdate();
        return $this;
    }

    /**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency()
    {
        return (int) $this->_backendConfig->getValue(self::XML_PATH_FREQUENCY) * 3600;
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->_cacheManager->load(self::CACHE_KEY_LAST_CHECK);
    }

    /**
     * Set last update time (now)
     *
     * @return $this
     */
    public function setLastUpdate()
    {
        $this->_cacheManager->save(time(), self::CACHE_KEY_LAST_CHECK);
        return $this;
    }

    /**
     * Retrieve feed data as XML element
     *
     * @return \SimpleXMLElement
     */
    public function getFeedData()
    {
        $params = array('locale' => $this->_getLocaleCode());

        $curl = $this->curlFactory->create();
        $curl->setConfig(
            [
                'timeout'   => 2,
                'useragent' => $this->productMetadata->getName()
                    . '/' . $this->productMetadata->getVersion()
                    . ' (' . $this->productMetadata->getEdition() . ')',
                'referer'   => $this->urlBuilder->getUrl('*/*/*')
            ]
        );

        $curl->write(\Zend_Http_Client::GET, $this->getFeedUrl(), '1.0', array(), $params);

        $data = $curl->read();
        if ($data === false) {
            return false;
        }

        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        try {
            $xml = new \SimpleXMLElement($data);
        } catch (\Exception $e) {
            return false;
        }
        return $xml;
    }

    /**
     * Retrieve feed as XML element
     *
     * @return \SimpleXMLElement
     */
    public function getFeedXml()
    {
        try {
            $data = $this->getFeedData();
            $xml = new \SimpleXMLElement($data);
        } catch (\Exception $e) {
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>');
        }
        return $xml;
    }
}
