<?php
namespace Emosys\Custom\Controller\Compare;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Catalog\Controller\Product\Compare\Index
{
    /**
     * View blog homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $session = $this->_objectManager->get('Magento\Customer\Model\Session');
        if(!$this->getRequest()->isXmlHttpRequest()) {
            if($url = $session->getData('url_compare_back')) {
                $this->_redirect->redirect($this->_response, $url);
                return;
            }
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
            $this->_redirect->redirect($this->_response, $baseUrl);
            return;
        }

        $beforeUrl = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED);
        $urlCurrent = $this->getRequest()->getParam('url_current');
        $session->setData('url_compare_back',$urlCurrent);
        if ($beforeUrl) {
            $this->_catalogSession->setBeforeCompareUrl(
                $this->urlDecoder->decode($beforeUrl)
            );
        }

        $this->resultPageFactory->create();

        $this->_view->loadLayout();
        //$blockCompare = $this->_view->getLayout()->createBlock('Emosys\Custom\Block\Product\Liste\Compare')->setTemplate('Magento_Catalog::product/compare/list.phtml')->toHtml();
        $blockCompare = $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Product\Compare\ListCompare')->setTemplate('Magento_Catalog::product/compare/list.phtml')->toHtml();
        echo $blockCompare;
        return;
    }

}
