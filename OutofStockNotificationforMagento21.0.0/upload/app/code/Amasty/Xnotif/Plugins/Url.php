<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Plugins;

class Url
{
    protected $data;

    protected $_registry;

    protected $_productId;

    protected $_urlHash;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Amasty\Xnotif\Model\UrlHash $urlHash
    )
    {
        $this->_registry = $registry;
        $this->_urlHash = $urlHash;
        $this->data = $registry->registry('amxnotif_data');
    }

    private function getType($subject)
    {
        $type = null;
        if ($subject instanceof \Magento\ProductAlert\Block\Email\Price) {
            $type = 'price';
        }
        if ($subject instanceof \Magento\ProductAlert\Block\Email\Stock) {
            $type = 'stock';
        }
        return $type;
    }

    public function beforeGetProductUnsubscribeUrl($subject, $productId)
    {
        $this->_productId = $productId;
    }

    public function afterGetProductUnsubscribeUrl($subject, $url)
    {
        if ($this->data['guest'] && $this->data['email']) {
            if ($type = $this->getType($subject)) {
                $hash = $this->_urlHash->getHash(
                    $this->_productId, $this->data['email']
                );
                $url .= "?product_id={$this->_productId}&email={$this->data['email']}&hash={$hash}&type={$type}";
            }
        }
        return $url;
    }

    public function afterGetUnsubscribeUrl($subject, $url)
    {
        if ($this->data['guest'] && $this->data['email']) {
            if ($type = $this->getType($subject)) {
                $hash = $this->_urlHash->getHash(
                    \Amasty\Xnotif\Model\ResourceModel\Unsubscribe\AlertProvider::REMOVE_ALL,
                    $this->data['email']
                );
                $url .= "?product_id="
                    . \Amasty\Xnotif\Model\ResourceModel\Unsubscribe\AlertProvider::REMOVE_ALL
                    . "&email={$this->data['email']}&hash={$hash}&type={$type}";
            }
        }
        return $url;
    }
}