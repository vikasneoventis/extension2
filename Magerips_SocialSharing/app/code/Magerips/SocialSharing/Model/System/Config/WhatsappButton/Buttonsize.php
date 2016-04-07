<?php
 
namespace Magerips\SocialSharing\Model\System\Config\WhatsappButton;
 
use Magento\Framework\Option\ArrayInterface;
 
class Buttonsize implements ArrayInterface
{
    const SMALL      = 'small';
    const MEDIUM     = 'medium';
    const LARGE	  = 'large';

    public function toOptionArray()
    {
        return [
            self::SMALL => __('Small'),
            self::MEDIUM => __('Medium'),
            self::LARGE => __('Large')
        ];
    }
}