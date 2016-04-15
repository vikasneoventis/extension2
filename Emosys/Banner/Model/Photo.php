<?php
/**
 * @author Emosys team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Model;

use Emosys\Banner\Api\Data\PhotoInterface;

/**
 * CMS block model
 *
 * @method \Magento\Cms\Model\ResourceModel\Block _getResource()
 * @method \Magento\Cms\Model\ResourceModel\Block getResource()
 */
class Photo extends \Magento\Framework\Model\AbstractModel implements PhotoInterface
{
    /**
     * @var \Emosys\Banner\Model\Photo\Media\Config
     */
    protected $_mediaConfig;

    /**
     * CMS block cache tag
     */
    const CACHE_TAG = 'emosys_banner_photo';

    /**
     * @var string
     */
    protected $_cacheTag = 'emosys_banner_photo';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'emosys_banner_photo';

    /**#@+
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Emosys\Banner\Model\Photo\Media\Config $mediaConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_mediaConfig = $mediaConfig;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Emosys\Banner\Model\ResourceModel\Photo');
    }

    /**
     * Retrieve block id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::PHOTO_ID);
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return BlockInterface
     */
    public function setId($id)
    {
        return $this->setData(self::PHOTO_ID, $id);
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getSrc()
    {
        return $this->_mediaConfig->getBaseMediaUrl() . $this->getData('file');
    }
}