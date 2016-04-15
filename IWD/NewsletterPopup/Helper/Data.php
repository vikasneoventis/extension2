<?php
/**
 * Created by: IWD Agency "iwdagency.com"
 * Developer: Andrew Chornij "iwd.andrew@gmail.com"
 * Date: 26.11.2015
 */

namespace IWD\NewsletterPopup\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ENABLE = 'iwd_newsletterpopup/general/enable_in_frontend';
    const POPUP_WIDTH = 'iwd_newsletterpopup/general/popup_with';
    const POPUP_TOP_POSITION = 'iwd_newsletterpopup/general/popup_top_position';
    const ENABLE_LINK_IN_FRONTEND = 'iwd_newsletterpopup/general/enable_link_in_footer';
    const FOOTER_LINK_TEXT = 'iwd_newsletterpopup/general/footer_link_text';
    const CSS_PATH_FOR_LINKS = 'iwd_newsletterpopup/general/css_path_for_links';
    const POPUP_TIMING = 'iwd_newsletterpopup/general/popup_timing';
    const BUTTON_COLOR = 'iwd_newsletterpopup/design/button_color';
    const BUTTON_HOVER_COLOR = 'iwd_newsletterpopup/design/button_hover_color';
    const BUTTON_TEXT_COLOR = 'iwd_newsletterpopup/design/button_text_color';
    const BUTTON_TEXT_HOVER_COLOR = 'iwd_newsletterpopup/design/button_hover_text_color';
    const MULTIPLE_GUEST_SUBSCRIPTION = 'iwd_newsletterpopup/general/multiple_guest_subscription';

    const XML_PATH_ICONSTYLE = 'iwd_newsletterpopup/social_icons/standard_icon_colors';

    const XML_PATH_FACEBOOK_BG = 	   'iwd_newsletterpopup/social_icons/facebook_icon_background';
    const XML_PATH_FACEBOOK_ICON  =    'iwd_newsletterpopup/social_icons/facebook_icon_color';
    const XML_PATH_FACEBOOK_BG_HOVER = 'iwd_newsletterpopup/social_icons/facebook_icon_hover_background';
    const XML_PATH_FACEBOOK_ICON_HOVER='iwd_newsletterpopup/social_icons/facebook_icon_hover_color';

    const XML_PATH_TWITTER_BG = 	   'iwd_newsletterpopup/social_icons/twitter_icon_background';
    const XML_PATH_TWITTER_ICON  =     'iwd_newsletterpopup/social_icons/twitter_icon_color';
    const XML_PATH_TWITTER_BG_HOVER =  'iwd_newsletterpopup/social_icons/twitter_icon_hover_background';
    const XML_PATH_TWITTER_ICON_HOVER= 'iwd_newsletterpopup/social_icons/twitter_icon_hover_color';

    const XML_PATH_LINKEDIN_BG = 	   'iwd_newsletterpopup/social_icons/linkedIn_icon_background';
    const XML_PATH_LINKEDIN_ICON  =    'iwd_newsletterpopup/social_icons/linkedin_icon_color';
    const XML_PATH_LINKEDIN_BG_HOVER = 'iwd_newsletterpopup/social_icons/linkedin_icon_hover_background';
    const XML_PATH_LINKEDIN_ICON_HOVER='iwd_newsletterpopup/social_icons/linkedin_icon_hover_color';

    const XML_PATH_GOOGLE_BG = 	       'iwd_newsletterpopup/social_icons/google_icon_background';
    const XML_PATH_GOOGLE_ICON  =      'iwd_newsletterpopup/social_icons/google_icon_color';
    const XML_PATH_GOOGLE_BG_HOVER =   'iwd_newsletterpopup/social_icons/google_icon_hover_background';
    const XML_PATH_GOOGLE_ICON_HOVER=  'iwd_newsletterpopup/social_icons/google_icon_hover_color';

    const XML_PATH_YOUTUBE_BG = 	   'iwd_newsletterpopup/social_icons/youtube_icon_background';
    const XML_PATH_YOUTUBE_ICON  =     'iwd_newsletterpopup/social_icons/youtube_icon_color';
    const XML_PATH_YOUTUBE_BG_HOVER =  'iwd_newsletterpopup/social_icons/youtube_icon_hover_background';
    const XML_PATH_YOUTUBE_ICON_HOVER= 'iwd_newsletterpopup/social_icons/youtube_icon_hover_color';

    const XML_PATH_FLICKR_BG = 	       'iwd_newsletterpopup/social_icons/flickr_icon_background';
    const XML_PATH_FLICKR_ICON  =      'iwd_newsletterpopup/social_icons/flickr_icon_color';
    const XML_PATH_FLICKR_BG_HOVER =   'iwd_newsletterpopup/social_icons/flickr_icon_hover_background';
    const XML_PATH_FLICKR_ICON_HOVER=  'iwd_newsletterpopup/social_icons/flickr_icon_hover_color';

    const XML_PATH_VIMEO_BG = 	       'iwd_newsletterpopup/social_icons/vimeo_icon_background';
    const XML_PATH_VIMEO_ICON  =       'iwd_newsletterpopup/social_icons/vimeo_icon_color';
    const XML_PATH_VIMEO_BG_HOVER =    'iwd_newsletterpopup/social_icons/vimeo_icon_hover_background';
    const XML_PATH_VIMEO_ICON_HOVER=   'iwd_newsletterpopup/social_icons/vimeo_icon_hover_color';

    const XML_PATH_PINTEREST_BG = 	    'iwd_newsletterpopup/social_icons/pinterest_icon_background';
    const XML_PATH_PINTEREST_ICON  =    'iwd_newsletterpopup/social_icons/pinterest_icon_color';
    const XML_PATH_PINTEREST_BG_HOVER = 'iwd_newsletterpopup/social_icons/pinterest_icon_hover_background';
    const XML_PATH_PINTEREST_ICON_HOVER='iwd_newsletterpopup/social_icons/pinterest_icon_hover_color';

    const XML_PATH_INSTAGRAM_BG = 	    'iwd_newsletterpopup/social_icons/instagram_icon_background';
    const XML_PATH_INSTAGRAM_ICON  =    'iwd_newsletterpopup/social_icons/instagram_icon_color';
    const XML_PATH_INSTAGRAM_BG_HOVER = 'iwd_newsletterpopup/social_icons/instagram_icon_hover_background';
    const XML_PATH_INSTAGRAM_ICON_HOVER='iwd_newsletterpopup/social_icons/instagram_icon_hover_color';

    const XML_PATH_FORSQUARE_BG = 	    'iwd_newsletterpopup/social_icons/foursquare_icon_background';
    const XML_PATH_FORSQUARE_ICON  =    'iwd_newsletterpopup/social_icons/foursquare_icon_color';
    const XML_PATH_FORSQUARE_BG_HOVER = 'iwd_newsletterpopup/social_icons/foursquare_icon_hover_background';
    const XML_PATH_FORSQUARE_ICON_HOVER='iwd_newsletterpopup/social_icons/foursquare_icon_hover_color';

    const XML_PATH_TUMBLR_BG = 	        'iwd_newsletterpopup/social_icons/tumblr_icon_background';
    const XML_PATH_TUMBLR_ICON  =       'iwd_newsletterpopup/social_icons/tumblr_icon_color';
    const XML_PATH_TUMBLR_BG_HOVER =    'iwd_newsletterpopup/social_icons/tumblr_icon_hover_background';
    const XML_PATH_TUMBLR_ICON_HOVER=   'iwd_newsletterpopup/social_icons/tumblr_icon_hover_color';

    const XML_PATH_RSS_BG = 	    	'iwd_newsletterpopup/social_icons/rss_icon_background';
    const XML_PATH_RSS_ICON  =     		'iwd_newsletterpopup/social_icons/rss_icon_color';
    const XML_PATH_RSS_BG_HOVER =  		'iwd_newsletterpopup/social_icons/rss_icon_hover_background';
    const XML_PATH_RSS_ICON_HOVER= 		'iwd_newsletterpopup/social_icons/rss_icon_hover_color';

    public function getEnable(){
        return $this->scopeConfig->getValue(self::ENABLE);
    }
    public function getPopupWidth(){
        return $this->scopeConfig->getValue(self::POPUP_WIDTH);
    }
    public function getPopupTopPosition(){
        return $this->scopeConfig->getValue(self::POPUP_TOP_POSITION);
    }
    public function getEnableLinnkInFrontend(){
        return $this->scopeConfig->getValue(self::ENABLE_LINK_IN_FRONTEND);
    }
    public function getFooterLinkText(){
        return $this->scopeConfig->getValue(self::FOOTER_LINK_TEXT);
    }
    public function getCssPathForLinks(){
        return $this->scopeConfig->getValue(self::CSS_PATH_FOR_LINKS);
    }

    public function getButtonColor(){
        return $this->scopeConfig->getValue(self::BUTTON_COLOR);
    }
    public function getButtonHoverColor(){
        return $this->scopeConfig->getValue(self::BUTTON_HOVER_COLOR);
    }
    public function getButtonTextColor(){
        return $this->scopeConfig->getValue(self::BUTTON_TEXT_COLOR);
    }
    public function getButtonHoverTextColor(){
        return $this->scopeConfig->getValue(self::BUTTON_TEXT_HOVER_COLOR);
    }
    public function getMultipleGuestSubscription(){
        return $this->scopeConfig->getValue(self::MULTIPLE_GUEST_SUBSCRIPTION);
    }

    public function getJsonConfig(){
        $config = array();
        $config['enableExtension'] = $this->scopeConfig->getValue(self::ENABLE);
        $config['topPosition'] = $this->scopeConfig->getValue(self::POPUP_TOP_POSITION);
        $config['loadDelay'] = $this->scopeConfig->getValue(self::POPUP_TIMING);
        $config['enableLinkInFrontend'] = $this->scopeConfig->getValue(self::ENABLE_LINK_IN_FRONTEND);
        $config['footerLinkText'] = $this->scopeConfig->getValue(self::FOOTER_LINK_TEXT);
        $config['cssPathForLinks'] = $this->scopeConfig->getValue(self::CSS_PATH_FOR_LINKS);

        return json_encode($config);
    }

    public function getSocialIcons()
    {
        if($this->scopeConfig->getValue(self::XML_PATH_ICONSTYLE)) {
            $class = 'social_links standard';
            $id = 'standard_color_for_icons';
        }
        else {
            $class = 'social_links';
            $id = 'color_for_icons';
        }
        $html='<div class="'.$class.'" id="'.$id.'">';

        $map = array(
            'facebook' => array(
                'enabled' => 'enable_facebook_icon',
                'link' => 'facebook_link',
                'icon_bg' => 'facebook_icon_background',
                'icon' => 'facebook_icon_color',
                'icon_hover' => 'facebook_icon_hover_color',
                'icon_bg_hover' => 'facebook_icon_hover_color',
                'class' => 'facebook',
                'fa' => 'fa-facebook'
            ),
            'twitter' => array(
                'enabled' => 'enable_twitter_icon',
                'link' => 'twitter_link',
                'icon_bg' => 'twitter_icon_background',
                'icon' => 'twitter_icon_color',
                'icon_hover' => 'twitter_icon_hover_background',
                'icon_bg_hover' => 'twitter_icon_hover_color',
                'class' => 'twitter',
                'fa' => 'fa-twitter'
            ),
            'linkedin' => array(
                'enabled' => 'enable_linkedin_icon',
                'link' => 'linkedin_link',
                'icon_bg' => 'linkedIn_icon_background',
                'icon' => 'linkedin_icon_color',
                'icon_hover' => 'linkedin_icon_hover_background',
                'icon_bg_hover' => 'linkedin_icon_hover_color',
                'class' => 'linkedin',
                'fa' => 'fa-linkedin'
            ),
            'google' => array(
                'enabled' => 'enable_google_icon',
                'link' => 'google_link',
                'icon_bg' => 'google_icon_background',
                'icon' => 'google_icon_color',
                'icon_hover' => 'google_icon_hover_background',
                'icon_bg_hover' => 'google_icon_hover_color',
                'class' => 'google',
                'fa' => 'fa-google'
            ),
            'youtube' => array(
                'enabled' => 'enable_youtube_icon',
                'link' => 'youtube_link',
                'icon_bg' => 'youtube_icon_background',
                'icon' => 'youtube_icon_color',
                'icon_hover' => 'youtube_icon_hover_background',
                'icon_bg_hover' => 'youtube_icon_hover_color',
                'class' => 'youtube',
                'fa' => 'fa-youtube'
            ),
            'flickr' => array(
                'enabled' => 'enable_flickr_icon',
                'link' => 'flickr_link',
                'icon_bg' => 'flickr_icon_background',
                'icon' => 'flickr_icon_color',
                'icon_hover' => 'flickr_icon_hover_background',
                'icon_bg_hover' => 'flickr_icon_hover_color',
                'class' => 'flickr',
                'fa' => 'fa-flickr'
            ),
            'vimeo' => array(
                'enabled' => 'enable_vimeo_icon',
                'link' => 'vimeo_link',
                'icon_bg' => 'vimeo_icon_background',
                'icon' => 'vimeo_icon_color',
                'icon_hover' => 'vimeo_icon_hover_background',
                'icon_bg_hover' => 'vimeo_icon_hover_color',
                'class' => 'vimeo',
                'fa' => 'fa-vimeo'
            ),
            'pinterest' => array(
                'enabled' => 'enable_pinterest_icon',
                'link' => 'pinterest_link',
                'icon_bg' => 'pinterest_icon_background',
                'icon' => 'pinterest_icon_color',
                'icon_hover' => 'pinterest_icon_hover_background',
                'icon_bg_hover' => 'pinterest_icon_hover_color',
                'class' => 'pinterest',
                'fa' => 'fa-pinterest'
            ),
            'instagram' => array(
                'enabled' => 'enable_instagram_icon',
                'link' => 'instagram_link',
                'icon_bg' => 'instagram_icon_background',
                'icon' => 'instagram_icon_color',
                'icon_hover' => 'instagram_icon_hover_background',
                'icon_bg_hover' => 'instagram_icon_hover_color',
                'class' => 'instagram',
                'fa' => 'fa-instagram'
            ),
            'foursquare' => array(
                'enabled' => 'enable_foursquare_icon',
                'link' => 'foursquare_link',
                'icon_bg' => 'foursquare_icon_background',
                'icon' => 'foursquare_icon_color',
                'icon_hover' => 'foursquare_icon_hover_background',
                'icon_bg_hover' => 'foursquare_icon_hover_color',
                'class' => 'foursquare',
                'fa' => 'fa-foursquare'
            ),
            'tumblr' => array(
                'enabled' => 'enable_tumblr_icon',
                'link' => 'tumblr_link',
                'icon_bg' => 'tumblr_icon_background',
                'icon' => 'tumblr_icon_color',
                'icon_hover' => 'tumblr_icon_hover_background',
                'icon_bg_hover' => 'tumblr_icon_hover_color',
                'class' => 'tumblr',
                'fa' => 'fa-tumblr'
            ),
            'rss' => array(
                'enabled' => 'enable_rss_icon',
                'link' => 'rss_link',
                'icon_bg' => 'rss_icon_background',
                'icon' => 'rss_icon_color',
                'icon_hover' => 'rss_icon_hover_background',
                'icon_bg_hover' => 'rss_icon_hover_color',
                'class' => 'rss',
                'fa' => 'fa-rss'
            ),

        );

        foreach ($map as $social) {

            if ($this->scopeConfig->getValue('iwd_newsletterpopup/social_icons/' . $social['enabled'])) {
                //html
                $link = $this->scopeConfig->getValue('iwd_newsletterpopup/social_icons/' . $social['link']);
                if ($link) {
                    $socialNetwork = '<a href="' . $link . '" class="'. $social['class'] .'" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa '. $social['fa'] .' fa-stack-20">&nbsp;</span> </span></a>';
                    $html .= $socialNetwork;
                }
                //css
            }
        }

        $html.='</div>';
        return $html;
    }

    public function getSocialIconsColor(){
        $colors = array();
        $colors['facebook_bg'] = $this->scopeConfig->getValue(self::XML_PATH_FACEBOOK_BG);
        $colors['facebook_icon'] = $this->scopeConfig->getValue(self::XML_PATH_FACEBOOK_ICON);
        $colors['facebook_bg_hover'] = $this->scopeConfig->getValue(self::XML_PATH_FACEBOOK_BG_HOVER);
        $colors['facebook_icon_hover'] = $this->scopeConfig->getValue(self::XML_PATH_FACEBOOK_ICON_HOVER);

        $colors['twitter_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_TWITTER_BG);
        $colors['twitter_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_TWITTER_ICON);
        $colors['twitter_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_TWITTER_BG_HOVER);
        $colors['twitter_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_TWITTER_ICON_HOVER);

        $colors['linkedIn_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_LINKEDIN_BG);
        $colors['linkedin_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_LINKEDIN_ICON);
        $colors['linkedin_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_LINKEDIN_BG_HOVER);
        $colors['linkedin_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_LINKEDIN_ICON_HOVER);

        $colors['google_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_GOOGLE_BG);
        $colors['google_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_GOOGLE_ICON);
        $colors['google_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_GOOGLE_BG_HOVER);
        $colors['google_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_GOOGLE_ICON_HOVER);

        $colors['youtube_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_YOUTUBE_BG);
        $colors['youtube_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_YOUTUBE_ICON);
        $colors['youtube_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_YOUTUBE_BG_HOVER);
        $colors['youtube_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_YOUTUBE_ICON_HOVER);

        $colors['flickr_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_FLICKR_BG);
        $colors['flickr_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_FLICKR_ICON);
        $colors['flickr_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_FLICKR_BG_HOVER);
        $colors['flickr_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_FLICKR_ICON_HOVER);

        $colors['vimeo_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_VIMEO_BG);
        $colors['vimeo_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_VIMEO_ICON);
        $colors['vimeo_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_VIMEO_BG_HOVER);
        $colors['vimeo_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_VIMEO_ICON_HOVER);

        $colors['pinterest_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_PINTEREST_BG);
        $colors['pinterest_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_PINTEREST_ICON);
        $colors['pinterest_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_PINTEREST_BG_HOVER);
        $colors['pinterest_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_PINTEREST_ICON_HOVER);

        $colors['instagram_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_INSTAGRAM_BG);
        $colors['instagram_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_INSTAGRAM_ICON);
        $colors['instagram_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_INSTAGRAM_BG_HOVER);
        $colors['instagram_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_INSTAGRAM_ICON_HOVER);

        $colors['foursquare_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_FORSQUARE_BG);
        $colors['foursquare_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_FORSQUARE_ICON);
        $colors['foursquare_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_FORSQUARE_BG_HOVER);
        $colors['foursquare_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_FORSQUARE_ICON_HOVER);

        $colors['tumblr_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_TUMBLR_BG);
        $colors['tumblr_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_TUMBLR_ICON);
        $colors['tumblr_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_TUMBLR_BG_HOVER);
        $colors['tumblr_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_TUMBLR_ICON_HOVER);

        $colors['rss_icon_background'] = $this->scopeConfig->getValue(self::XML_PATH_RSS_BG);
        $colors['rss_icon_color'] = $this->scopeConfig->getValue(self::XML_PATH_RSS_ICON);
        $colors['rss_icon_hover_background'] = $this->scopeConfig->getValue(self::XML_PATH_RSS_BG_HOVER);
        $colors['rss_icon_hover_color'] = $this->scopeConfig->getValue(self::XML_PATH_RSS_ICON_HOVER);

        return json_encode($colors);
    }
}