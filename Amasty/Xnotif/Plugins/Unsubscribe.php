<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */

namespace Amasty\Xnotif\Plugins;

class Unsubscribe
{
    protected $_request;

    protected $_urlHash;

    protected $_alertProviderCollection;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Amasty\Xnotif\Model\UrlHash $urlHash,
        \Amasty\Xnotif\Model\ResourceModel\Unsubscribe\AlertProvider $alertProvider
    )
    {
        $this->_request = $request;
        $this->_urlHash = $urlHash;
        $this->_alertProviderCollection = $alertProvider;
    }


    public function afterDispatch($subject, $result)
    {
        //remove alerts for guests
        if (!$this->_urlHash->check($this->_request)) {
            return $result;
        }

        $productId = $this->_request->getParam('product_id');
        $email = $this->_request->getParam('email');
        $type = $this->_request->getParam('type');

        $collection = $this->_alertProviderCollection->getAlertModel($type, $productId, $email);

        foreach ($collection as $alert) {
            $alert->delete();
        }
    }
}