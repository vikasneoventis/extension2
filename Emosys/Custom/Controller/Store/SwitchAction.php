<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Emosys\Custom\Controller\Store;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreIsInactiveException;
use Magento\Store\Model\StoreResolver;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Switch current store view.
 */
class SwitchAction extends \Magento\Store\Controller\Store\SwitchAction
{
    /**
     * @var StoreCookieManagerInterface
     */
    protected $storeCookieManager;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Initialize dependencies.
     *
     * @param ActionContext $context
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param HttpContext $httpContext
     * @param StoreRepositoryInterface $storeRepository
     * @param StoreManagerInterface $storeManager
     */

    /**
     * @return void
     */
    public function execute()
    {
        $storeCode = $this->_request->getParam(
            StoreResolver::PARAM_NAME,
            $this->storeCookieManager->getStoreCodeFromCookie()
        );

        try {
            $store = $this->storeRepository->getActiveStoreByCode($storeCode);
        } catch (StoreIsInactiveException $e) {
            $error = __('Requested store is inactive');
        } catch (NoSuchEntityException $e) {
            $error = __('Requested store is not found');
        }

        if (isset($error)) {
            $this->messageManager->addError($error);
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
            return;
        }
        
        $defaultStoreView = $this->storeManager->getDefaultStoreView();
        if ($defaultStoreView->getId() == $store->getId()) {
            $this->storeCookieManager->deleteStoreCookie($store);
        } else {
            $this->httpContext->setValue(Store::ENTITY, $store->getCode(), $defaultStoreView->getCode());
            $this->storeCookieManager->setStoreCookie($store);
        }
        $_redirectUrl = $this->_redirect->getRedirectUrl();
        $_baseUrl = $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
        $_paramLink = str_replace($_baseUrl, '', $_redirectUrl);
        $_paramLink = trim($_paramLink);
        $_paramLink = trim($_paramLink,'/');
        if($_paramLink == 'landing') {
            return $this->getResponse()->setRedirect($_baseUrl);
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }
}
