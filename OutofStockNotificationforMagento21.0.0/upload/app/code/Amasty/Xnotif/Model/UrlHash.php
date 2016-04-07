<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Model;

use Amasty\Xnotif\Model\Unsubscribe;

class UrlHash
{
    const SOLD = 'qprugn1234njd';

    protected $_alertProvider;

    public function __construct(
        ResourceModel\Unsubscribe\AlertProvider $alertProvider
    )
    {
        $this->alertProvider = $alertProvider;
    }

    public function getHash($productId, $email)
    {
        return md5($productId . $email . self::SOLD);
    }

    public function check(\Magento\Framework\App\Request\Http $request)
    {

        $hash = $request->getParam('hash');
        $productId = $request->getParam('product_id');
        $email = $request->getParam('email');

        if (empty($hash) || empty($productId) || empty($email)) {
            return false;
        }

        $real = $this->getHash($productId, $email);
        return $hash == $real;
    }
}