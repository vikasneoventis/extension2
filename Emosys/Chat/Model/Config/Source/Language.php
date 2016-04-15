<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Chat
 */
namespace Emosys\Chat\Model\Config\Source;

class Language implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'auto', 'label' => __('- Auto Detect -')],
            ['value' => 'md', 'label' => __('- Magento Locale Detection -')],
            ['value' => 'ar', 'label' => __("Arabic")],
            ['value' => 'bg', 'label' => __("Bulgarian")],
            ['value' => 'cs', 'label' => __("Czech")],
            ['value' => 'da', 'label' => __("Danish")],
            ['value' => 'de', 'label' => __("German")],
            ['value' => 'en', 'label' => __("English")],
            ['value' => 'es', 'label' => __("Spanish; Castilian")],
            ['value' => 'fa', 'label' => __("Persian")],
            ['value' => 'fo', 'label' => __("Faroese")],
            ['value' => 'fr', 'label' => __("French")],
            ['value' => 'he', 'label' => __("Hebrew")],
            ['value' => 'hr', 'label' => __("Croatian")],
            ['value' => 'id', 'label' => __("Indonesian")],
            ['value' => 'it', 'label' => __("Italian")],
            ['value' => 'ja', 'label' => __("Japanese")],
            ['value' => 'ko', 'label' => __("Korean")],
            ['value' => 'ms', 'label' => __("Malay")],
            ['value' => 'nb', 'label' => __("Norwegian Bokmal")],
            ['value' => 'nl', 'label' => __("Dutch; Flemish")],
            ['value' => 'pl', 'label' => __("Polish")],
            ['value' => 'pt', 'label' => __("Portuguese")],
            ['value' => 'ru', 'label' => __("Russian")],
            ['value' => 'sk', 'label' => __("Slovak")],
            ['value' => 'sl', 'label' => __("Slovenian")],
            ['value' => 'sv', 'label' => __("Swedish")],
            ['value' => 'th', 'label' => __("Thai")],
            ['value' => 'tr', 'label' => __("Turkish")],
            ['value' => 'ur', 'label' => __("Urdu")],
            ['value' => 'vi', 'label' => __("Vietnamese")],
            ['value' => 'zh_CN', 'label' => __("Chinese (China)")]
        ];
    }
}