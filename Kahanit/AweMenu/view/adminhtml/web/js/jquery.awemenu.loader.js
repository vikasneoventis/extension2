define([
    'jquery',
    'jquery/ui',

    'handlebars',
    'getBootstrap',

    'datatables.net',
    'datatables.netBootstrap',
    'fancytree',

    'colorpickerBootstrap',
    'iconpickerBootstrap',

    'aceEditor',
    'tinymce',

    'googleMaps',

    'aweMenu'
], function ($) {
    'use strict';

    $.widget("mage.aweMenuLoader", {
        options: {
            url: '',
            jsUrl: '',
            langs: '',
            activeLang: '',
            entities: ''
        },
        _create: function () {
            $('#am-builder').awemenu({
                'url': this.options.url,
                'jsUrl': this.options.jsUrl,
                'langs': this.options.langs,
                'activeLang': this.options.activeLang,
                'entities': this.options.entities
            });
        }
    });

    return $.mage.aweMenuLoader;
});