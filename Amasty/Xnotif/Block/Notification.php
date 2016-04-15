<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Block;
class Notification extends \Magento\Framework\View\Element\Template
{
    protected $_objectManager;
    protected $_jsonEncoder;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
        $this->_jsonEncoder = $jsonEncoder;
    }

    public function getJsonConfig()
    {
        return $this->_jsonEncoder->encode(['igor' => 'korol']);
    }
}