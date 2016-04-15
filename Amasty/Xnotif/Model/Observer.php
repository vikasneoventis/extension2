<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Model;

class Observer extends \Magento\ProductAlert\Model\Observer
{
    protected $_customerSession;

    protected $_logger;

    protected $_registry;

    protected $_helper;

    protected $_objectManager;

    protected $_colFactorys = [];

    protected $_configurableType;

    protected $_state;

    public function __construct(
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory $priceColFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory $stockColFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\ProductAlert\Model\EmailFactory $emailFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $registry,
        \Amasty\Xnotif\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
    )
    {
        parent::__construct(
            $catalogData,
            $scopeConfig,
            $storeManager,
            $priceColFactory,
            $customerRepository,
            $productRepository,
            $dateFactory,
            $stockColFactory,
            $transportBuilder,
            $emailFactory,
            $inlineTranslation
        );
        $this->_customerSession = $customerSession;
        $this->_logger = $logger;
        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->_objectManager = $objectManager;
        $this->_colFactorys['price'] = $priceColFactory;
        $this->_colFactorys['stock'] = $stockColFactory;
        $this->_configurableType = $configurableType;

    }


    protected function _processStock(\Magento\ProductAlert\Model\Email $email)
    {
        $this->_foreachAlert('stock', $email);
    }

    protected function _foreachAlert($type, $email)
    {

        $email->setType($type);
        foreach ($this->_getWebsites() as $website) {
            /* @var $website \Magento\Store\Model\Website */

            if (!$website->getDefaultGroup()
                || !$website->getDefaultGroup()->getDefaultStore()
            ) {
                continue;
            }

            if (!$this->_scopeConfig->getValue(
                self::XML_PATH_PRICE_ALLOW,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )
            ) {
                continue;
            }
            try {
                $collection = $this->_colFactorys[$type]->create()
                    ->addWebsiteFilter(
                        $website->getId()
                    )
                    ->addFieldToFilter('status', 0)
                    ->setCustomerOrder();
            } catch (\Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                $this->_registry->unregister('amxnotif_data');
                try {
                    $email->clean();
                    $isGuest = (0 == $alert->getCustomerId()) ? 1 : 0;
                    if ($isGuest) {
                        $this->_registry->register('amxnotif_data', ['guest' => 1, 'email' => $alert->getEmail()]);

                        $customer = $this->_objectManager->get(
                            'Magento\Customer\Model\Customer'
                        );
                        $customer->setWebsiteId(
                            $this->_storeManager->getWebsite()->getId()
                        );
                        $customer->loadByEmail($alert->getEmail());

                        if (!$customer->getId()) {
                            $customer = $this->_objectManager->get(
                                'Magento\Customer\Model\Data\Customer'
                            );
                            $customer->setWebsiteId(
                                $this->_storeManager->getWebsite()->getId()
                            );
                            $customer->setEmail($alert->getEmail());
                            $customer->setLastname(
                                $this->_scopeConfig->getValue(
                                    'amxnotif/general/customer_name'
                                )
                            );
                            $customer->setGroupId(0);
                            $customer->setId(0);
                        } else {
                            $customer = $this->customerRepository->getById(
                                $customer->getId()
                            );
                        }
                    } else {
                        $customer = $this->customerRepository->getById(
                            $alert->getCustomerId()
                        );
                    }

                    if (!$customer) {
                        continue;
                    }

                    $email->setCustomerData($customer);

                    $product = $this->productRepository->getById(
                        $alert->getProductId(),
                        false,
                        $website->getDefaultStore()->getId()
                    );

                    if (!$product) {
                        continue;
                    }

                    $product->setCustomerGroupId($customer->getGroupId());

                    if ('stock' == $type) {
                        $minQuantity = $this->_scopeConfig->getValue(
                            'amxnotif/general/min_qty'
                        );
                        if ($minQuantity < 1) {
                            $minQuantity = 1;
                        }

                        $isInStock = false;
                        if ($product->canConfigure() && $product->isInStock()) {

                            $allProducts = $this->_configurableType->getUsedProducts($product);

                            foreach ($allProducts as $simpleProduct) {
                                $stockItem = $this->_objectManager->get(
                                    'Magento\CatalogInventory\Model\Stock\Item'
                                )->load($simpleProduct->getId(), 'product_id');
                                $quantity = $stockItem->getData('qty');
                                $isInStock =
                                    (
                                        $simpleProduct->isSalable()
                                        || $simpleProduct->isSaleable()
                                    )
                                    && $quantity >= $minQuantity;
                                if ($isInStock) {
                                    break;
                                }
                            }
                        } else {
                            $stockItem = $this->_objectManager->get(
                                'Magento\CatalogInventory\Model\Stock\Item'
                            )->load($product->getId(), 'product_id');
                            $quantity = $stockItem->getData('qty');
                            $isInStock =
                                ($product->isSalable())
                                && ($quantity >= $minQuantity);
                        }


                        if ($isInStock) {
                            if ($alert->getParentId()
                                && !$product->canConfigure()
                            ) {
                                $productParent = $this->_objectManager->get(
                                    'Magento\Catalog\Model\Product'
                                )
                                    ->setStoreId(
                                        $website->getDefaultStore()->getId()
                                    )
                                    ->load($alert->getParentId());
                                $email->addStockProduct($productParent);
                            }else{
                                $email->addStockProduct($product);
                            }



                            $alert->setSendDate(
                                $this->_dateFactory->create()->gmtDate()
                            );
                            $alert->setSendCount($alert->getSendCount() + 1);
                            $alert->setStatus(1);
                            $alert->save();
                        }


                    } else {

                        if ($alert->getPrice() > $product->getFinalPrice()) {
                            $productPrice = $product->getFinalPrice();
                            $product->setFinalPrice(
                                $this->_catalogData->getTaxPrice(
                                    $product, $productPrice
                                )
                            );
                            $product->setPrice(
                                $this->_catalogData->getTaxPrice(
                                    $product, $product->getPrice()
                                )
                            );
                            $email->addPriceProduct($product);

                            $alert->setPrice($productPrice);
                            $alert->setLastSendDate(
                                $this->_dateFactory->create()->gmtDate()
                            );
                            $alert->setSendCount($alert->getSendCount() + 1);
                            $alert->setStatus(1);
                            $alert->save();
                        }
                    }

                } catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }

                try {
                    $email->send();

                } catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
                $this->unsubscribe(
                    $product, $customer, $isGuest,
                    $website, $type
                );
            }
        }
        return $this;

    }

    private function unsubscribe($product, $customer, $isGuest, $website,
                                 $type
    )
    {
        try {
            if (!$product
                || (!$isGuest && !$this->_scopeConfig->isSetFlag('amxnotif/' . $type . '/unsubscribeC'))
                || ($isGuest && !$this->_scopeConfig->isSetFlag('amxnotif/' . $type . '/unsubscribeG'))
            ) {
                return;
            }

            if (!$product->getId() || !$product->isVisibleInCatalog()) {
                return;
            }
            $_customerId = (!$isGuest && $customer && $customer->getId())
                ? $customer->getId() : 0;

            $models = $this->_objectManager->create('Magento\ProductAlert\Model\\' . ucfirst($type))
                ->getCollection()
                ->addFieldToFilter('customer_id', $_customerId)
                ->addFieldToFilter('product_id', $product->getId())
                ->addFieldToFilter('website_id', $website->getId());
            if ($isGuest) {
                $models->addFieldToFilter('email', $customer->getEmail());
            }
            $models->load();

            foreach ($models as $model) {
                $model->delete();
            }

            return true;

        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    protected function _processPrice(\Magento\ProductAlert\Model\Email $email)
    {
        $this->_foreachAlert('price', $email);
    }
}