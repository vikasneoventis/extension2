<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\Core\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

/**
 * Backend system config datetime field renderer
 */
class Notification extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param array $data
     * @return void
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        DateTimeFormatterInterface $dateTimeFormatter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * Retrieve element html
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setValue($this->_cache->load(\SalesIds\Core\Model\Notification\Feed::CACHE_KEY_LAST_CHECK));
        $format = $this->_localeDate->getDateTimeFormat(
            \IntlDateFormatter::MEDIUM
        );

        return $this->dateTimeFormatter->formatObject(
            $this->_localeDate->date(intval($element->getValue())),
            $format
        );
    }
}
