<?php
/**
 * Created by: IWD Agency "iwdagency.com"
 * Developer: Andrew Chornij "iwd.andrew@gmail.com"
 * Date: 13.11.2015
 */

namespace IWD\NewsletterPopup\Controller\Ajax;

use \Magento\Newsletter\Controller\Subscriber;
use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Controller\ResultFactory;

class Subscribe extends \Magento\Newsletter\Controller\Subscriber\NewAction
{

    /**
     * @var CustomerAccountManagement
     */
    protected $customerAccountManagement;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     * @param CustomerAccountManagement $customerAccountManagement
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement
    )
    {
        $this->customerAccountManagement = $customerAccountManagement;
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl,
            $customerAccountManagement
        );
    }


    public function execute()
    {
        $response = new \Magento\Framework\DataObject();

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string)$this->getRequest()->getPost('email');

            $responseData = array();
            $responseData['error'] = false;

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

               $status = $this->_subscriberFactory->create()->subscribe($email);
                if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                    $responseData['message'] = __('The confirmation request has been sent.');
                } else {
                    $responseData['message'] = __('Thank you for your subscription.');
                    $responseData['error'] = false;
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $responseData['message'] =  __('There was a problem with the subscription: %1', $e->getMessage());
                $responseData['error'] = true;
            } catch (\Exception $e) {
                $responseData['message'] =  __('Something went wrong with the subscription: %1', $e->getMessage());
                $responseData['error'] = true;
            }
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
//        $this->getResponse()->setHeader('Content-type','application/json', true);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }
    public function _toHtml()
    {
        if($this->_helper->getEnable()){
            return parent::_toHtml();
        }
        return '';
    }
}