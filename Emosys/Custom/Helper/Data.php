<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Chat
 */
namespace Emosys\Custom\Helper;

use Magento\Framework\App\ActionInterface;

/**
 * Wishlist Data Helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function getProductWeight($productWeight) {
        if(is_object($productWeight)) {
            $productWeight = $productWeight->getData('weight');
        }
        if(!$productWeight) {
            return null;
        }
        $productWeight = floatval($productWeight);
        if($productWeight >=1) {
            $productWeight = $productWeight. ' ' .'kilograms';
        }
        else {
            $productWeight = ($productWeight * 1000). ' ' .'grams';
        }
        return $productWeight;
        ;
    }
}
