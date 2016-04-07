define([
    'jquery',
    'aweMenu'
], function ($) {
    'use strict';

    $.widget("mage.aweMenuLoader", {
        options: {
        },
        _create: function () {
            $('#awemenu').awemenu();
        }
    });

    return $.mage.aweMenuLoader;
});