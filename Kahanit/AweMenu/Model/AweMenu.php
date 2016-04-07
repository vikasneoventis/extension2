<?php

namespace Kahanit\AweMenu\Model;

class AweMenu extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kahanit_awemenu';

    protected $_cacheTag = 'kahanit_awemenu';

    protected $_eventPrefix = 'kahanit_awemenu';

    /* table fields */
    const ID = 'id';

    const TITLE = 'title';

    const SHOP = 'shop';

    const AUTHOR = 'author';

    const MENU = 'menu';

    const THEME = 'theme';

    const EDIT = 'edit';

    const LIVE = 'live';

    const DELETED = 'deleted';

    const DATE = 'date';

    protected function _construct()
    {
        $this->_init('Kahanit\AweMenu\Model\ResourceModel\AweMenu');
    }

    public function getIdentities()
    {
        return [
            self::CACHE_TAG . '_' . $this->getId()
        ];
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function getShop()
    {
        return $this->getData(self::SHOP);
    }

    public function getAuthor()
    {
        return $this->getData(self::AUTHOR);
    }

    public function getMenu()
    {
        return $this->getData(self::MENU);
    }

    public function getTheme()
    {
        return $this->getData(self::THEME);
    }

    public function getEdit()
    {
        return $this->getData(self::EDIT);
    }

    public function getLive()
    {
        return $this->getData(self::LIVE);
    }

    public function getDeleted()
    {
        return $this->getData(self::DELETED);
    }

    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }
}
