<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Controller;

use Magento\Framework\App\RequestInterface;

abstract class AbstractController extends \Magento\Framework\App\Action\Action
{
    const TITLE = '';
    const TYPE = '';

    protected $_title;
    protected $_type;


    protected $_customerUrl;
    protected $_customerSession;
    protected $_storeManager;
    protected $_resultPageFactory;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManger,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->_customerUrl = $customerUrl;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManger;
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_customerSession->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }


}