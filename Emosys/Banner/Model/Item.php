<?php
/**
 * @author Emosys team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Banner
 */
namespace Emosys\Banner\Model;

use Emosys\Banner\Api\Data\ItemInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * CMS block model
 *
 * @method \Magento\Cms\Model\ResourceModel\Block _getResource()
 * @method \Magento\Cms\Model\ResourceModel\Block getResource()
 */
class Item extends \Magento\Framework\Model\AbstractModel implements ItemInterface, IdentityInterface
{
    /**
     * CMS block cache tag
     */
    const CACHE_TAG = 'emosys_banner_item';

    /**
     * @var string
     */
    protected $_cacheTag = 'emosys_banner_item';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'emosys_banner_item';

    /**#@+
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * @var \Emosys\Banner\Model\PhotoFactory
     */
    protected $_photoFactory;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

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
        \Emosys\Banner\Model\PhotoFactory $photoFactory,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_photoFactory = $photoFactory;
        $this->_resultJsonFactory = $resultJsonFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Emosys\Banner\Model\ResourceModel\Item');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
    }

    /**
     * Retrieve block id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ITEM_ID);
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
        return $this->setData(self::ITEM_ID, $id);
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
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
     * Retrieve array of up sell products
     *
     * @return array
     */
    public function getPhotos()
    {
        if (!$this->hasPhotos()) {
            $photos = [];
            foreach ($this->getPhotoCollection() as $_photo) {
                $photos[] = $_photo;
            }
            $this->setPhotos($photos);
        }
        return $this->getData('photos');
    }

    /**
     * Retrieve collection up sell product
     *
     * @return \Emosys\Banner\Model\ResourceModel\Photo\Collection
     */
    public function getPhotoCollection()
    {
        $collection = $this->_photoFactory->create()->getCollection();
        $collection->addItemFilter($this);
        return $collection;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptionArray()
    {
        $collection = $this->getResourceCollection();
        $options = [];
        foreach ($collection as $_item) {
            $options[$_item->getId()] = $_item->getTitle();
        }
        return $options;
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getEffects()
    {
        return [
            'slide' => __('Slide'),
            'fade' => __('Fade'),
            'cube' => __('Cube'),
            'coverflow' => __('Coverflow'),
            'flip' => __('flip')
        ];
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getDirections()
    {
        return [
            'horizontal' => __('Horizontal'),
            'vertical' => __('Vertical')
        ];
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getPaginationTypes()
    {
        return [
            'bullets' => __('Bullets'),
            'fraction' => __('Fraction'),
            'progress' => __('Progress'),
            'custom' => __('Custom')
        ];
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getExtraOption($name = null)
    {
        if ( !$this->hasExtraOptions() ) {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            //$resultJson = $this->_resultJsonFactory->create();
            //$this->setExtraOptions( $resultJson->getData( $this->getData('extra_option') ) );
            $this->setExtraOptions(unserialize($this->getData('extra_option')));
        }

        /* Check option */
        $extraOptions = $this->getData('extra_options');
        if ( isset($extraOptions[$name]) ) {
            return $extraOptions[$name];
        }
        return $extraOptions;
    }
}