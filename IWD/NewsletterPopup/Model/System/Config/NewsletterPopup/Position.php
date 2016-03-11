<?php
/**
 * Created by: IWD Agency "iwdagency.com"
 * Developer: Andrew Chornij "iwd.andrew@gmail.com"
 * Date: 25.11.2015
 */


namespace IWD\NewsletterPopup\Model\System\Config\NewsletterPopup;

use Magento\Framework\Option\ArrayInterface;

class Position implements ArrayInterface
{
    const LEFT      = 1;
    const RIGHT     = 2;
    const DISABLED  = 0;



    /**
     * Get positions of lastest news block
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::LEFT => __('Left'),
            self::RIGHT => __('Right'),
            self::DISABLED => __('Disabled')
        ];
    }
}