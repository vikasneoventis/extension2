/**
 * Easily manage navigation with Kahanit Awe Menu
 *
 * Kahanit Awe Menu by Kahanit(http://www.kahanit.com) is licensed under a
 * Creative Creative Commons Attribution-NoDerivatives 4.0 International License.
 * Based on a work at http://www.kahanit.com.
 * Permissions beyond the scope of this license may be available at http://www.kahanit.com.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/4.0/.
 *
 * @author    Amit Sidhpura <amit@kahanit.com>
 * @copyright 2015 Kahanit
 * @license   http://creativecommons.org/licenses/by-nd/4.0/
 */

(function ($) {
    var menu = {},
        defaults = {};

    menu.settings = {};

    $.fn.awemenu = function (options) {
        menu = this;
        menu.settings = $.extend({}, defaults, options);

        /* load google maps script if tag available */
        if (menu.find('.am-googlemap-container').length > 0) {
            if (typeof google === 'undefined') {
                $.getScript('//maps.googleapis.com/maps/api/js?v=3.exp').done(function (script, textStatus) {
                    initGoogleMaps();
                });
            }
            else
                initGoogleMaps();
        }

        var amTabs = menu.find('.am-tabs'),
            amTab = menu.find('.am-tab'),
            amTabHome = menu.find('.am-tab-home');

        amTab.each(function () {
            if ($(this).find('> .am-dropdown').length == 0)
                $(this).addClass('am-tab-no-dropdown');
        });

        menu.find('.am-tab-dropdown-parent-center').each(function () {
            var tabwidth = $('> a', this).outerWidth(),
                dropdownwidth = $('> .am-dropdown', this).outerWidth();

            $('> .am-dropdown', this).css('left', ((tabwidth - dropdownwidth) / 2) + 'px');
        });

        amTabHome.parent().addClass('am-tabs-hidetabs');
        amTabHome.click(function () {
            if ($(this).parent().hasClass('am-tabs-hidetabs'))
                $(this).parent().removeClass('am-tabs-hidetabs');
            else
                $(this).parent().addClass('am-tabs-hidetabs');
        });

        /* tabs root event handler */
        var amTabsRoot = $('.am-tabs-root'),
            amTimeOut;

        amTabsRoot.find('> .am-sortable > .am-tab').hover(function () {
            var tabToHover = $(this);

            amTimeOut = setTimeout(function () {
                tabToHover.addClass('am-tab-hover');
                menu.trigger('tabactive', [tabToHover]);
            }, 175);
        }, function () {
            var tabToHover = $(this);

            clearTimeout(amTimeOut);

            amTimeOut = setTimeout(function () {
                tabToHover.removeClass('am-tab-hover');
            }, 75);
        });

        /* tabs horizontal and vertical event handler */
        var amTabsHoriVert = $('.am-tabs-horizontal, .am-tabs-vertical');

        amTabsHoriVert.find('> .am-sortable > .am-tab').hover(function (e) {
            $(this).siblings().removeClass('am-tab-hover');
            $(this).addClass('am-tab-hover');
            menu.trigger('tabactive', [$(this)]);
        });

        /* tabs accordion event handler */
        $('.am-tabs-accordion').find('> .am-sortable > .am-tab').click(function (e) {
            e.stopPropagation();

            $(this).siblings().removeClass('am-tab-hover');

            if (!$(this).hasClass('am-tab-hover')) {
                $(this).addClass('am-tab-hover');
                menu.trigger('tabactive', [$(this)]);
            }
            else
                $(this).removeClass('am-tab-hover');
        });

        /* tabs event handler */
        amTab.click(function (e) {
            e.stopPropagation();

            $(this).siblings().removeClass('am-tab-active').attr('data-content', '+');

            if (!$(this).hasClass('am-tab-active')) {
                $(this).addClass('am-tab-active');
                $(this).attr('data-content', '-');
                menu.trigger('tabactive', [$(this)]);
            }
            else {
                $(this).removeClass('am-tab-active');
                $(this).attr('data-content', '+');
            }
        });

        /* make first tab active */
        amTabsHoriVert.find('> .am-sortable > .am-tab:first-child').addClass('am-tab-hover');

        /* setting the height of tabs */
        var amDropDown = $('.am-dropdown');

        amDropDown.css('display', 'block');

        amTabsHoriVert.each(function () {
            var mbHeight = 0,
                ddHeight = 0,
                tabsBorder = parseInt($(this).outerHeight()) - parseInt($(this).height());

            $(this).find('> .am-sortable > .am-tab > .am-dropdown').each(function () {
                var tmpHeight = parseInt($(this).outerHeight());

                if (tmpHeight > ddHeight)
                    ddHeight = tmpHeight;
            });

            if ($(this).hasClass('am-tabs-horizontal')) {
                mbHeight = $(this).find('> .am-sortable').first().outerHeight();

                $(this).css('min-height', mbHeight + ddHeight + tabsBorder);
                $(this).find('> .am-sortable > .am-tab > .am-dropdown').css('min-height', ddHeight);
            }
            else {
                mbHeight = $(this).find('> .am-sortable').first().outerHeight();

                if (mbHeight > ddHeight) {
                    $(this).css('min-height', mbHeight + tabsBorder);
                    $(this).find('> .am-sortable > .am-tab > .am-dropdown').css('min-height', mbHeight);
                }
                else {
                    $(this).css('min-height', ddHeight + tabsBorder);
                    $(this).find('> .am-sortable > .am-tab > .am-dropdown').css('min-height', ddHeight);
                }
            }
        });

        amDropDown.css('display', '');

        /* google map */
        menu.on('tabactive', function (e, tab) {
            // resize google maps
            if (typeof google !== 'undefined')
                $.each(tab.find('.am-googlemap-container'), function () {
                    var gMap = $(this).data('gMap'),
                        gMarker = $(this).data('gMarker');

                    google.maps.event.trigger(gMap, 'resize');
                    gMap.setCenter(gMarker.getPosition());
                });
        });
    };

    var initGoogleMaps = function () {
        menu.find('.am-googlemap-container').each(function () {
            var dataLat = $(this).attr('data-lat'),
                dataLng = $(this).attr('data-lng'),
                dataZoom = $(this).attr('data-zoom');

            if ($.isNumeric(dataLat)
                && $.isNumeric(dataLng)
                && $.isNumeric(dataZoom)) {
                var gLatLng = new google.maps.LatLng(dataLat, dataLng),
                    gMap = new google.maps.Map($(this)[0], {
                        zoom: parseInt(dataZoom),
                        center: gLatLng
                    }),
                    gMarker = new google.maps.Marker({
                        position: gLatLng,
                        map: gMap,
                        title: "Location"
                    });

                $(this).data("gMap", gMap);
                $(this).data("gMarker", gMarker);
            }
        });
    };
})(jQuery);