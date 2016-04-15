<?php 
namespace Emosys\Custom\Block\Customer\Widget;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\Api\ArrayObjectSearch;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Dob extends \Magento\Customer\Block\Widget\Dob {

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magento_Customer::widget/dob.phtml');
    }

    /**
     * Returns format which will be applied for DOB in javascript
     *
     * @return string
     */
    public function getDateFormat()
    {
        return 'dd/mm/yy';
        return $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
    }

    public function getFieldHtml()
    {
        $inputDob = '<input type="hidden" name="dob" value="'.$this->getValue().'" class="dob-hidden">';
        $this->dateElement->setData([
            'name' => $this->getHtmlId().'_ui',
            'id' => $this->getHtmlId(),
            'class' => $this->getHtmlClass(),
            'value' => $this->getValue(true),
            'date_format' => $this->getDateFormat(),
            'image' => $this->getViewFileUrl('Magento_Theme::calendar.png'),
        ]);
        $inputPicker = $this->dateElement->getHtml();
        return $inputDob . $inputPicker;
    }

    public function getValue($custom = false) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
            $dob = $customerSession->getCustomer()->getData('dob');
            if(!$dob) {
                return '';
            }
            $date = \DateTime::createFromFormat('Y-m-d', $dob);
            if($custom) {
                return $date->format('d/m/Y');
            }
            else {
                return $date->format('m/d/Y');
            }
        }
        return '';
    }
}