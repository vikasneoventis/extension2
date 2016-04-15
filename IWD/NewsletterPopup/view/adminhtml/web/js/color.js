/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'uiClass',
    'underscore',
    'jquery/colorpicker/js/colorpicker'
], function ($, Class, _) {
    'use strict';

    return Class.extend({

        initialize: function () {
            this.initColorPicker('iwd_newsletterpopup_design_button_color');
            this.initColorPicker('iwd_newsletterpopup_design_button_hover_color');
            this.initColorPicker('iwd_newsletterpopup_design_button_text_color');
            this.initColorPicker('iwd_newsletterpopup_design_button_hover_text_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_facebook_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_facebook_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_facebook_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_facebook_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_twitter_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_twitter_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_twitter_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_twitter_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_linkedIn_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_linkedin_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_linkedin_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_linkedin_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_google_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_google_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_google_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_google_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_youtube_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_youtube_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_youtube_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_youtube_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_flickr_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_flickr_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_flickr_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_flickr_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_vimeo_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_vimeo_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_vimeo_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_vimeo_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_pinterest_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_pinterest_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_pinterest_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_pinterest_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_instagram_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_instagram_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_instagram_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_instagram_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_foursquare_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_foursquare_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_foursquare_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_foursquare_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_tumblr_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_tumblr_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_tumblr_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_tumblr_icon_hover_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_rss_icon_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_rss_icon_hover_background');
            this.initColorPicker('iwd_newsletterpopup_social_icons_rss_icon_color');
            this.initColorPicker('iwd_newsletterpopup_social_icons_rss_icon_hover_color');
            return this;
        },

        initColorPicker: function (elementId) {
            var element = document.getElementById(elementId);

            $('#'+elementId).ColorPicker({
                onSubmit: function(hsb, hex, rgb, el) {
                    $(el).val(hex);
                    $(el).ColorPickerHide();
                },
                onBeforeShow: function () {
                    $(this).ColorPickerSetColor(this.value);
                }

            })
                .bind('keyup', function(){
                    $(this).ColorPickerSetColor(this.value);
                });


        },

    });
});
