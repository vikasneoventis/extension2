<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Block\Product\View\Type;


use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Configurable
//    extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{
    protected $_moduleManager;
    protected $_objectManager;
    protected $_helper;
    protected $_customerSession;
    protected $_jsonEncoder;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Xnotif\Helper\Data $_helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    )
    {
        /* parent::__construct(
             $context,
             $arrayUtils,
             $jsonEncoder,
             $helper,
             $catalogProduct,
             $currentCustomer,
             $priceCurrency,
             $configurableAttributeData,
             $data
         );*/
        $this->_moduleManager = $moduleManager;
        $this->_objectManager = $objectManager;
        $this->_helper = $_helper;
        $this->_customerSession = $customerSession;
        $this->_jsonEncoder = $jsonEncoder;
    }

    public function afterGetAllowProducts($subject, $products)
    {
        if (!$subject->hasAllProducts()) {
            $allProducts = $subject->getProduct()->getTypeInstance(true)
                ->getUsedProducts($subject->getProduct());
            foreach ($allProducts as $product) {
                if (!$product->isSaleable()) {
                    $products[] = $product;
                }
            }
            $subject->setAllowProducts($products);
            $subject->setAllProducts(true);
        }
        return $subject->getData('allow_products');
    }

    public function afterToHtml($subject, $html)
    {
        // return $html;
        if ('product.info.options.swatches' == $subject->getNameInLayout()
            && !$this->_moduleManager->isEnabled('Amasty_Stockstatus')
        ) {
            $allProducts = $subject->getProduct()->getTypeInstance(true)
                ->getUsedProducts($subject->getProduct());

            $_attributes = $subject->getProduct()->getTypeInstance(true)
                ->getConfigurableAttributes($subject->getProduct());
            foreach ($allProducts as $product) {

                $key = [];
                foreach ($_attributes as $attribute) {
                    $key[] = $product->getData(
                        $attribute->getData('product_attribute')->getData(
                            'attribute_code'
                        )
                    );
                }
                $stockStatus = '';
                $stockItem = $this->_objectManager->get(
                    'Magento\Cataloginventory\Model\Stock\Item'
                )->setProduct($product)->load($product->getId());
                if (!$product->isInStock()) {
                    $stockStatus = 'Out of Stock';
                }
                if ($key) {
                    $aStockStatus[implode(',', $key)] = [
                        'is_in_stock' => $product->isSaleable(),
                        'custom_status' => $stockStatus,
                        'is_qnt_0' => (int)($product->isInStock()
                            && $stockItem->getData('qty') <= 0),
                        'product_id' => $product->getId(),
                        'stockalert' => $this->_helper->getStockAlert(
                            $product, $this->_customerSession->isLoggedIn()
                        ),
                    ];
                }
            }
            foreach ($aStockStatus as $k => $v) {
                if (!$v['is_in_stock'] && !$v['custom_status']) {
                    $v['custom_status'] = __('Out of Stock');
                    $aStockStatus[$k] = $v;
                }
            }
            $aStockStatus['changeConfigurableStatus'] = true;
            $data = $this->_jsonEncoder->encode($aStockStatus);


            $html
                = '<script type="text/x-magento-init">
                    {
                        ".product-options-wrapper": {
                                    "amnotification": {
                                        "xnotif": ' . $data . '
                                    }
                         }
                    }
                   </script>' . $html;

        }
        return $html;
    }
}