/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "underscore",
    "mage/template",
    "priceUtils",
    "Magento_ConfigurableProduct/js/configurable",
    "priceBox",
    "jquery/ui",
    "jquery/jquery.parsequery",
    "mage/mage",
    "mage/validation",
    "Magento_Swatches/js/SwatchRenderer"
], function ($, _, mageTemplate, utils, Component) {

    $.widget('mage.amnotification', {
        configurableStatus: null,
        spanElement: null,
        options: {},
        _create: function () {
            this._rewritePrototypeFunction();
            this.spanElement = $('.stock.available span')[0];
            this.settings = $('.swatch-option');
            $(document).ready($.proxy(function () {
               // this.onConfigure();
                var val = jQuery('.super-attribute-select:last').val();
                if (val) {
                    jQuery('.super-attribute-select:last').change();
                }
            }, this));
        },
        _removeStockStatus: function () {
            if ($('#amstockstatus-status').legth) {
                $('#amstockstatus-status').remove();
            }
        },
        /**
         * remove stock alert block
         */
        _hideStockAlert: function () {
            if ($('#amstockstatus-stockalert').length) {
                $('#amstockstatus-stockalert').remove();
            }
        },
        _reloadDefaultContent: function (key) {
            if (this.spanElement) {
                this.spanElement.innerHTML = this.configurableStatus;
            }
            $('.box-tocart').each(function (index, elem) {
                $(elem).show();
            });
        },
        showStockAlert: function (code) {
            var wrapper = $('.product-add-form')[0];
            // var beforeNode = wrapper.children()[0];
            var div = document.createElement('div');
            div.id = 'amstockstatus-stockalert';
            div.innerHTML = code;
            $(div).insertBefore($(wrapper));
            $('#form-validate-stock').mage('validation');

        },

        /*
         * configure statuses at product page
         */
        onConfigure: function ($this,$widget) {
            $proxy = $.proxy($widget._OnClickSuper,$widget);
            $proxy($this,$widget);

            var settings = this.settings;
            this._hideStockAlert();
            this._removeStockStatus();
            if (null == this.configurableStatus && this.spanElement) {
                this.configurableStatus = this.spanElement.innerHTML;
            }
            //get current selected key
            var selectedKey = "";
            for (var i = 0; i < settings.length; i++) {
                if ($(settings[i]).hasClass('selected')) {
                    selectedKey += $(settings[i]).attr('option-id') + ',';
                }
            }
            var trimSelectedKey = selectedKey.substr(0, selectedKey.length - 1);
            var countKeys = selectedKey.split(",").length - 1;

            /*reload main status*/
            if ('undefined' != typeof(this.options.xnotif[trimSelectedKey])) {
                this._reloadContent(trimSelectedKey);
            }
            else {
                this._reloadDefaultContent(trimSelectedKey);
            }

            if(this.options.xnotif[trimSelectedKey]){
                jQuery('#form-validate-price input[name="product_id"]').val(this.options.xnotif[trimSelectedKey]['product_id']);
            }else{
                jQuery('#form-validate-price input[name="product_id"]').val(jQuery('#form-validate-price input[name="parent_id"]').val());
            }

            var a = 1;

            /*add statuses to dropdown*/
            /*for (var i = 0; i < settings.length; i++) {
                for (var x = 0; x < settings[i].options.length; x++) {
                    if (!settings[i].options[x].value) continue;

                    if (countKeys == i + 1) {
                        var keyCheckParts = trimSelectedKey.split(',');
                        keyCheckParts[keyCheckParts.length - 1] = settings[i].options[x].value;
                        var keyCheck = keyCheckParts.join(',');

                    }
                    else {
                        var keyCheck = selectedKey + settings[i].options[x].value;
                    }

                    if ('undefined' != typeof(this.options.xnotif[keyCheck]) && this.options.xnotif[keyCheck]) {
                        var status = this.options.xnotif[keyCheck]['custom_status'];
                        if (status) {
                            status = status.replace(/<(?:.|\n)*?>/gm, ''); // replace html tags
                            if (settings[i].options[x].text.indexOf(status) === -1) {
                                settings[i].options[x].text = settings[i].options[x].text + ' (' + status + ')';
                            }
                        }
                    }
                }
            }*/

        },
        /*
         * reload default stock status after select option
         */
        _reloadContent: function (key) {

            if ('undefined' != typeof(this.options.xnotif.changeConfigurableStatus) && this.options.xnotif.changeConfigurableStatus && this.spanElement) {
                if (this.options.xnotif[key] && this.options.xnotif[key]['custom_status']) {
                    if (this.options.xnotif[key]['custom_status_icon_only'] == 1) {
                        this.spanElement.innerHTML = this.options.xnotif[key]['custom_status_icon'];
                    } else {
                        this.spanElement.innerHTML = this.options.xnotif[key]['custom_status_icon'] + this.options.xnotif[key]['custom_status'];
                    }
                } else {
                    this.spanElement.innerHTML = this.configurableStatus;
                }


            }

            if ('undefined' != typeof(this.options.xnotif[key]) && this.options.xnotif[key] && 0 == this.options.xnotif[key]['is_in_stock']) {
                $('.box-tocart').each(function (index, elem) {
                    $(elem).hide();
                });
                if (this.options.xnotif[key]['stockalert']) {
                    this.showStockAlert(this.options.xnotif[key]['stockalert']);
                }
            } else {
                $('.box-tocart').each(function (index, elem) {
                    $(elem).show();
                });
            }
        },

        /*
         * rewrite methods from /js/varien/configurable.js
         */
        _rewritePrototypeFunction: function () {
            $.custom.SwatchRenderer.prototype._OnClickSuper = $.custom.SwatchRenderer.prototype._OnClick;
            $.custom.SwatchRenderer.prototype._OnClick = $.proxy(this.onConfigure,this);
        }
    });

    //return $.mage.amnotification;


});
