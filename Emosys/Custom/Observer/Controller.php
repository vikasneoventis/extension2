<?php
namespace Emosys\Custom\Observer;

use Magento\Framework\Event\ObserverInterface;


class Controller implements ObserverInterface
{
    /**
     * Https request
     *
     * @var \Zend\Http\Request
     */
    protected $_redirect;
    protected $_storeManager;
    protected $_objectManager;
    protected $_response;

    /**
     * @param Item $item
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
            
    ) {
        
        $this->_objectManager = $context->getObjectManager();
        $this->_redirect = $context->getRedirect();
        $this->_storeManager = $storeManager;
        $this->_response = $context->getResponse();
    }
 
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /*
        $session = $this->_objectManager->get('Magento\Customer\Model\Session');
        if($session->getData('first_visted_site')) {
            return;
        }
        $session->setData('first_visted_site',1);
        */
        if(isset($_SESSION['first_visted_site']) && $_SESSION['first_visted_site']) {
            return $this;
        }
        $_SESSION['first_visted_site'] = 1;
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $this->_redirect->redirect($this->_response, $baseUrl.'landing');
        return $this->_response;
    }
    
}