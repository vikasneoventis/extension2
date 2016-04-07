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
    var menu = {};
    menu.settings = {};

    var defaults = {
            'url': '',					// Ajax requests URL
            'jsUrl': '',				// Ajax requests URL
            'langs': [],				// All languages
            'activeLang': '',			// Active language
            'entities': []				// Tables and trees entites
        },
        gMapCounter = 1,
        Handlebars;

    $.fn.awemenu = function (options) {
        Handlebars = require('handlebars');

        Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
            switch (operator) {
                case '==':
                    return (v1 == v2) ? options.fn(this) : options.inverse(this);
                case '!=':
                    return (v1 != v2) ? options.fn(this) : options.inverse(this);
                case '===':
                    return (v1 === v2) ? options.fn(this) : options.inverse(this);
                case '!==':
                    return (v1 !== v2) ? options.fn(this) : options.inverse(this);
                case '&&':
                    return (v1 && v2) ? options.fn(this) : options.inverse(this);
                case '||':
                    return (v1 || v2) ? options.fn(this) : options.inverse(this);
                case 'lt':
                    return (v1 < v2) ? options.fn(this) : options.inverse(this);
                case 'lt=':
                    return (v1 <= v2) ? options.fn(this) : options.inverse(this);
                case 'gt':
                    return (v1 > v2) ? options.fn(this) : options.inverse(this);
                case 'gt=':
                    return (v1 >= v2) ? options.fn(this) : options.inverse(this);
                default:
                    return eval('' + v1 + operator + v2) ? options.fn(this) : options.inverse(this);
            }
        });

        Handlebars.registerHelper('config', function (config) {
            return JSON.stringify(config).replace(/"/g, '&#34;');
        });

        Handlebars.registerHelper('getLangData', function (data, lang_id) {
            if (typeof data === 'object')
                return data['lang_id_' + lang_id];
            else
                return data;
        });

        var oldMouseStart = $.ui.sortable.prototype._mouseStart;
        $.ui.sortable.prototype._mouseStart = function (event, overrideHandle, noActivation) {
            this._trigger('beforeStart', event, this._uiHash());
            oldMouseStart.apply(this, [event, overrideHandle, noActivation]);
        };

        menu = this;
        menu.settings = $.extend({}, defaults, options);

        // setup items, rows and columns dialogs
        setupDialogs();

        // setup editor
        ajaxRequest({
            app: 'admin/menu/geteditormenu'
        }).done(function (json) {
            setupMenu(json);
        });

        // setup revisions
        setupRevisions();

        // langs logic
        amHideOtherLanguages(menu.settings.activeLang);
        $('.am-translatable-field .dropdown-menu a').click(amHideOtherLanguages);

        // dropdown align image radio button login
        $('.image-checkboxes .image-checkbox').click(function () {
            $(this).siblings().removeClass('checked');
            $(this).addClass('checked');
        });

        //attach html load event
        menu.on('update', onMenuUpdate);
        menu.on('tabactive', onTabActive);
    };

    /*
     main three methods
     */

    var setupDialogs = function () {
        // tab/item configuration dialog
        var itemConfig = $('#am-item-configuration'),
            source = itemConfig.html(),
            template = Handlebars.compile(source),
            context = {
                'entities': menu.settings.entities,
                'langs': menu.settings.langs
            },
            html = template(context);

        itemConfig.html(html);

        var itemConfigModal = $('#am-item-configuration-modal');

        itemConfigModal.on('show.bs.modal', onItemConfigModalShow);
        itemConfigModal.on('shown.bs.modal', onItemConfigModalShown);
        itemConfigModal.on('hidden.bs.modal', resetItemConfigModalData);

        $('#am-select-item').change(selectItemChange);
        $('#googlemap-address').change(function () {
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode({'address': $(this).val()}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var latlng = results[0].geometry.location;

                    $('#googlemap-latitude').val(latlng.lat());
                    $('#googlemap-longitude').val(latlng.lng());
                }
                else
                    alert('No result found');
            });
        });
        $('#am-update-item').click(onItemConfigModalUpdate);

        // row configuration dialog
        var rowConfigModal = $('#am-row-configuration-modal');

        rowConfigModal.on('show.bs.modal', onRowConfigModalShow);
        rowConfigModal.on('shown.bs.modal', onRowConfigModalShown);
        rowConfigModal.on('hidden.bs.modal', resetRowConfigModalData);

        $('#am-update-row').click(editRow);

        // column configuration dialog
        var columnConfigModal = $('#am-column-configuration-modal');

        columnConfigModal.on('show.bs.modal', onColumnConfigModalShow);
        columnConfigModal.on('shown.bs.modal', onColumnConfigModalShown);
        columnConfigModal.on('hidden.bs.modal', resetColumnConfigModalData);

        $('#am-update-column').click(editColumn);
    };

    var setupMenu = function (json) {
        // set data
        setEditorData(json);

        // setup themes
        var amThemes = $('#am-themes');

        amThemes.find('.am-theme').each(function () {
            var amTheme = $(this),
                amThemeConfig = JSON.parse(amTheme.attr('data-config')),
                amThemeStyle = 'background: ' + amThemeConfig['mb-topclr'] + ';\
								background: -moz-linear-gradient(top, #' + amThemeConfig['mb-topclr'] + ' 0%, #' + amThemeConfig['mb-btmclr'] + ' 100%);\
								background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #' + amThemeConfig['mb-topclr'] + '), color-stop(100%, #' + amThemeConfig['mb-btmclr'] + '));\
								background: -webkit-linear-gradient(top, #' + amThemeConfig['mb-topclr'] + ' 0%, #' + amThemeConfig['mb-btmclr'] + ' 100%);\
								background: -o-linear-gradient(top, #' + amThemeConfig['mb-topclr'] + ' 0%, #' + amThemeConfig['mb-btmclr'] + ' 100%);\
								background: -ms-linear-gradient(top, #' + amThemeConfig['mb-topclr'] + ' 0%, #' + amThemeConfig['mb-btmclr'] + ' 100%);\
								background: linear-gradient(to bottom, #' + amThemeConfig['mb-topclr'] + ' 0%, #' + amThemeConfig['mb-btmclr'] + ' 100%);\
								filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#' + amThemeConfig['mb-topclr'] + ', endColorstr=#' + amThemeConfig['mb-btmclr'] + ', GradientType=0);\
								border: 1px solid #' + amThemeConfig['mb-bdrclr'] + ';';

            amTheme.attr('style', amThemeStyle);
        });
        amThemes.find('.am-theme').click(function () {
            var amThemeConfig = JSON.parse($(this).attr('data-config'));

            $.each(amThemeConfig, function (key, value) {
                $('#am-editor-theme-' + key).val('#' + value).trigger('keyup');
            });

            $('#am-editor-theme-mb-topclr').trigger('change');
        });

        // setup css editor
        var editor = ace.edit('am-css-editor');

        ace.config.set("workerPath", menu.settings.jsUrl);
        ace.config.set("modePath", menu.settings.jsUrl);
        ace.config.set("themePath", menu.settings.jsUrl);

        editor.getSession().setMode('ace/mode/css');
        editor.setTheme('ace/theme/clouds');
        editor.setOption('minLines', 21);
        editor.setOption('maxLines', 21);
        editor.$blockScrolling = Infinity;
        editor.on('change', onCustomCSSChange);

        // setup color picker
        var amColorPicker = $('.am-colorpicker');

        amColorPicker.colorpicker({
            container: '#awemenu'
        });

        amColorPicker.on('change', 'input', onColorChange);
        amColorPicker.colorpicker().on('hidePicker', onColorChange);

        // setup icon picket
        $('.am-iconpicker').iconpicker();

        // setup tinymce
        /*tinySetup({
         editor_selector: "am-htmleditor"
         });*/

        // buttons event handlers
        $('#am-editor-view').click(function () {
            var amTabsRoot = $('#am-editor').find('.am-tabs-root');

            $(this).toggleClass('active');

            if ($(this).hasClass('active'))
                amTabsRoot.addClass('am-tabs-live-view').removeClass('am-tabs-editor-view');
            else
                amTabsRoot.addClass('am-tabs-editor-view').removeClass('am-tabs-live-view');

            menu.trigger('update');
        }).trigger('click');
        $('#am-editor-save').click(function () {
            saveMenu(0);
        });
        $('#am-editor-live').click(function () {
            ajaxRequest({
                app: 'admin/menu/liverevision/id/' + $('#am-editor-revision-hidden').val()
            }).done(function () {
                $('#am-revisions-table').trigger('reload');
            });
        });
        $('#am-editor-save-live').click(function () {
            saveMenu(1);
        });
    };

    var onColorChange = function () {
        var mbtopclr = $('#am-editor-theme-mb-topclr').val().replace('#', ''),
            mbbtmclr = $('#am-editor-theme-mb-btmclr').val().replace('#', ''),
            mbtxtclr = $('#am-editor-theme-mb-txtclr').val().replace('#', ''),

            mbhvrtopclr = $('#am-editor-theme-mb-hvr-topclr').val().replace('#', ''),
            mbhvrbtmclr = $('#am-editor-theme-mb-hvr-btmclr').val().replace('#', ''),
            mbhvrtxtclr = $('#am-editor-theme-mb-hvr-txtclr').val().replace('#', ''),

            mbbdrclr = $('#am-editor-theme-mb-bdrclr').val().replace('#', ''),

            ddtopclr = $('#am-editor-theme-dd-topclr').val().replace('#', ''),
            ddbtmclr = $('#am-editor-theme-dd-btmclr').val().replace('#', ''),
            ddtxtclr = $('#am-editor-theme-dd-txtclr').val().replace('#', ''),
            ddttlclr = $('#am-editor-theme-dd-ttlclr').val().replace('#', ''),

            ddbdrclr = $('#am-editor-theme-dd-bdrclr').val().replace('#', '');

        var link = menu.settings.url + '?refresh=' + (new Date().getTime())
                + '&app=admin/menu/getthemecssfromless'
                + '/mb-topclr/' + mbtopclr + '/mb-btmclr/' + mbbtmclr + '/mb-txtclr/' + mbtxtclr
                + '/mb-hvr-topclr/' + mbhvrtopclr + '/mb-hvr-btmclr/' + mbhvrbtmclr + '/mb-hvr-txtclr/' + mbhvrtxtclr
                + '/mb-bdrclr/' + mbbdrclr
                + '/dd-topclr/' + ddtopclr + '/dd-btmclr/' + ddbtmclr + '/dd-txtclr/' + ddtxtclr + '/dd-ttlclr/' + ddttlclr
                + '/dd-bdrclr/' + ddbdrclr,
            amMenuThemeLink = $('#am-menu-theme-link');

        if (amMenuThemeLink.length == 0)
            $('head').append('<link id="am-menu-theme-link" rel="stylesheet" href="' + link + '" />');
        else
            amMenuThemeLink.attr('href', link);
    };

    var onCustomCSSChange = function () {
        $('#am-custom-css').remove();
        $('head').append('<style id="am-custom-css" type="text/css">' + ace.edit('am-css-editor').getValue() + '</style>');
    };

    var setupRevisions = function () {
        var amRevisionsTable = $('#am-revisions-table');

        amRevisionsTable.dataTable({
            'processing': true,
            'serverSide': true,
            'bPaginate': false,
            'autoWidth': false,
            'ajax': {
                'url': menu.settings.url,
                'data': function (d) {
                    d.app = 'admin/menu/getrevisions';
                }
            },
            'columns': [
                {'data': 'id', 'width': '72px', 'bSortable': false},
                {'data': 'title', 'bSortable': false},
                {'data': 'date', 'width': '250px', 'bSortable': false},
                {'data': 'id', 'width': '95px', 'bSortable': false}
            ],
            'order': [[0, 'desc']],
            'columnDefs': [{
                render: function (data, type, row) {
                    var actions = '';

                    actions += '<a href="#" onclick="return false;" class="am-revision-edit" data-id="' + row.id + '">&nbsp;</a> ';
                    actions += '<a href="#" onclick="return false;" class="am-revision-live" data-id="' + row.id + '">&nbsp;</a> ';
                    actions += '<a href="#" onclick="return false;" class="am-revision-delete" data-id="' + row.id + '">&nbsp;</a>';

                    return actions;
                },
                targets: 3
            }],
            'rowCallback': function (row, data) {
                if (data.live === '1')
                    $(row).addClass('am-is-live');

                if (data.edit === '1')
                    $(row).addClass('am-in-editor');
            }
        }).on('draw.dt', function () {
        });

        amRevisionsTable.on('reload', function () {
            amRevisionsTable.api().ajax.reload();
        });

        amRevisionsTable.on('click', '.am-revision-edit', function () {
            if (!$(this).closest('tr').hasClass('am-in-editor'))
                ajaxRequest({
                    app: 'admin/menu/editrevision/id/' + $(this).data('id')
                }).done(function (json) {
                    setEditorData(json);
                    $('#am-revisions-table').trigger('reload');
                });
            else
                alert('This revsion is already in editor.');
        });

        amRevisionsTable.on('click', '.am-revision-live', function () {
            if (!$(this).closest('tr').hasClass('am-is-live'))
                ajaxRequest({
                    app: 'admin/menu/liverevision/id/' + $(this).data('id')
                }).done(function () {
                    $('#am-revisions-table').trigger('reload');
                });
            else
                alert('Revision live already.');
        });

        amRevisionsTable.on('click', '.am-revision-delete', function () {
            if (confirm('Are you sure?'))
                if (!$(this).closest('tr').hasClass('am-in-editor') && !$(this).closest('tr').hasClass('am-is-live'))
                    ajaxRequest({
                        app: 'admin/menu/deleterevision/id/' + $(this).data('id')
                    }).done(function () {
                        $('#am-revisions-table').trigger('reload');
                    });
                else
                    alert('Cannot delete live or editor revision.')
        });
    };

    /*
     common methods
     */

    var getHTMLFromJSON = function (json) {
        var sourcetplhtml = $('#am-elements').html().replace(/&gt;/g, '>'),
            sourcetplcmpld = Handlebars.compile(sourcetplhtml);

        Handlebars.registerPartial('sourcetpl', sourcetplhtml);

        try {
            var jsonprsd = JSON.parse(json);
        }
        catch (e) {
            jsonprsd = json;
        }

        var $sourcetplcmpld = $('<div>' + sourcetplcmpld({
                'items': jsonprsd
            }) + '</div>');

        // setup tooltip
        $sourcetplcmpld.find('[data-toggle="tooltip"]').tooltip();

        // attach tabs events
        $sourcetplcmpld.find('.am-tab-home').parent().addClass('am-tabs-hidetabs');
        $sourcetplcmpld.find('.am-tab-home').click(function (e) {
            e.stopPropagation();

            if ($(this).parent().hasClass('am-tabs-hidetabs'))
                $(this).parent().removeClass('am-tabs-hidetabs');
            else
                $(this).parent().addClass('am-tabs-hidetabs');
        });
        $sourcetplcmpld.find('.am-tab').click(function (e) {
            e.stopPropagation();

            var amTabs = $(this).closest('.am-tabs'),
                amTab = $(this),
                tabactive = false;

            if (amTabs.hasClass('am-tabs-root') || amTabs.hasClass('am-tabs-accordion')) {
                amTab.siblings().removeClass('am-tab-hover');

                if (!amTab.hasClass('am-tab-hover')) {
                    amTab.addClass('am-tab-hover');
                    tabactive = true;
                }
                else
                    amTab.removeClass('am-tab-hover');
            }
            else if (!amTab.hasClass('am-tab-hover')) {
                amTab.siblings().removeClass('am-tab-hover');
                amTab.addClass('am-tab-hover');
                tabactive = true;
            }

            $(this).siblings().removeClass('am-tab-active');

            if (!$(this).hasClass('am-tab-active')) {
                $(this).addClass('am-tab-active');
                $(this).attr('data-content', '-');
                tabactive = true;
            }
            else {
                $(this).removeClass('am-tab-active');
                $(this).attr('data-content', '+');
            }

            if (tabactive)
                menu.trigger('tabactive', [$(this).closest('.am-tab')]);
        });
        $sourcetplcmpld.find('.am-tab > .am-dropdown').click(function (e) {
            e.stopPropagation();
        });
        $sourcetplcmpld.find('.am-tabs:not(.am-tabs-root,.am-tabs-accordion) > .am-sortable > .am-tab:first-child').addClass('am-tab-hover');
        $sourcetplcmpld.find('.am-button-add-tab-before').click(onAddTabBefore);
        $sourcetplcmpld.find('.am-button-add-tab-after').click(onAddTabAfter);
        $sourcetplcmpld.find('.am-button-edit-tab').click(onEditTab);
        $sourcetplcmpld.find('.am-button-delete-tab').click(deleteTab);

        // attach rows events
        $sourcetplcmpld.find('.am-button-add-row-before').click(addRowBefore);
        $sourcetplcmpld.find('.am-button-add-row-after').click(addRowAfter);
        $sourcetplcmpld.find('.am-button-edit-row').click(onEditRow);
        $sourcetplcmpld.find('.am-button-delete-row').click(deleteRow);

        // attach columns events
        $sourcetplcmpld.find('.am-button-add-column-before').click(addColumnBefore);
        $sourcetplcmpld.find('.am-button-add-column-after').click(addColumnAfter);
        $sourcetplcmpld.find('.am-button-edit-column').click(onEditColumn);
        $sourcetplcmpld.find('.am-button-delete-column').click(deleteColumn);

        // attach items events
        $sourcetplcmpld.find('.am-button-add-item-before').click(onAddItemBefore);
        $sourcetplcmpld.find('.am-button-add-item-after').click(onAddItemAfter);
        $sourcetplcmpld.find('.am-button-edit-item').click(onEditItem);
        $sourcetplcmpld.find('.am-button-delete-item').click(deleteItem);

        // make sortable
        $sourcetplcmpld.find('.am-sortable-tabs').sortable({
            axis: 'x',
            handle: '> a > .am-button-move',
            beforeStart: function (event, ui) {
                if (ui.item.hasClass('am-tab'))
                    ui.item.find('> .am-dropdown').width(ui.item.find('> .am-dropdown').width());
            },
            sort: function (event, ui) {
                if (ui.item.hasClass('am-tab')) {
                    var pos = parseInt(ui.item.css('left')),
                        pos_abs = Math.abs(parseInt(ui.item.css('left'))),
                        sign = '-';

                    if (pos > 0)
                        sign = '-';
                    else
                        sign = '';

                    ui.item.find('> .am-dropdown').css('left', sign + pos_abs + 'px');
                }

            },
            stop: function (event, ui) {
                if (ui.item.hasClass('am-tab'))
                    ui.item.find('> .am-dropdown').removeAttr('style');

            }
        }).disableSelection();
        $sourcetplcmpld.find('.am-sortable-rows').sortable({
            axis: 'y',
            handle: '> .am-button-move',
            beforeStart: function (event, ui) {
                ui.item.parent().width(ui.item.parent().width());
                ui.item.parent().height(ui.item.parent().height());
            },
            stop: function (event, ui) {
                ui.item.parent().removeAttr('style');
            }
        }).disableSelection();
        $sourcetplcmpld.find('.am-sortable-columns').sortable({
            handle: '.am-button-move',
            connectWith: '> .am-sortable-columns',
            beforeStart: function (event, ui) {
                ui.item.data('amDataParent', ui.item.parent());

                ui.item.parent().width(ui.item.parent().width());
                ui.item.parent().height(ui.item.parent().height());
            },
            stop: function (event, ui) {
                ui.item.data('amDataParent').removeAttr('style');
                ui.item.removeData('amDataParent');
            }
        }).disableSelection();
        $sourcetplcmpld.find('.am-sortable-items').sortable({
            handle: '> .am-button-move',
            connectWith: '.am-sortable-items',
            beforeStart: function (event, ui) {
                ui.item.data('amDataParent', ui.item.parent());

                ui.item.parent().width(ui.item.parent().width());
                ui.item.parent().height(ui.item.parent().height());
            },
            stop: function (event, ui) {
                ui.item.data('amDataParent').removeAttr('style');
                ui.item.removeData('amDataParent');
            }
        }).disableSelection();

        // tabs styles
        $sourcetplcmpld.find('.am-tabs').each(function () {
            var tabsConfig = JSON.parse($(this).attr('data-config'));

            $(this).css({
                'width': tabsConfig.width,
                'height': tabsConfig.height
            });

            $(this).addClass(tabsConfig.class);
        });

        // tab styles
        $sourcetplcmpld.find('[data-xtype="tab"]').each(function () {
            var tabConfig = JSON.parse($(this).attr('data-config'));

            $(this).find('> a').css({
                'color': tabConfig.color,
                'background-color': tabConfig['background-color'],
                'font-size': tabConfig['font-size'],
                'font-weight': ((tabConfig.bold) ? 'bold' : ''),
                'font-style': ((tabConfig.italic) ? 'italic' : ''),
                'text-decoration': ((tabConfig.underline) ? 'underline' : '')
            });

            if (tabConfig['custom-text']['lang_id_' + menu.settings.activeLang])
                $(this).find('> a > .am-tab-text').text(tabConfig['custom-text']['lang_id_' + menu.settings.activeLang]);

            if (tabConfig.icon != '' && tabConfig.icon != '-')
                $(this).find('> a').prepend('<i class="fa ' + tabConfig.icon + '"></i>');

            if (tabConfig['badge-text']['lang_id_' + menu.settings.activeLang])
                $('<span class="am-badge">' + tabConfig['badge-text']['lang_id_' + menu.settings.activeLang] + '</span>')
                    .appendTo($(this).find('> a')).css({
                    'color': tabConfig['badge-color'],
                    'background-color': tabConfig['badge-background-color']
                });

            $(this).find('> .am-dropdown').css({
                'width': tabConfig.dropdown.width,
                'height': tabConfig.dropdown.height,
                'background-color': tabConfig.dropdown['background-color'],
                'background-image': ((tabConfig.dropdown['background-image'] !== '') ? 'url("' + tabConfig.dropdown['background-image'] + '")' : ''),
                'background-repeat': tabConfig.dropdown['background-repeat'],
                'background-position': tabConfig.dropdown['background-position']
            }).addClass(tabConfig.dropdown.class);

            $(this).addClass(tabConfig.class);
            $(this).addClass('am-tab-dropdown-' + tabConfig.dropdown.align);
        });

        // row styles
        $sourcetplcmpld.find('.am-row').each(function () {
            var rowConfig = JSON.parse($(this).attr('data-config'));

            $(this).css({
                'height': rowConfig.height,
                'background-color': rowConfig['background-color'],
                'background-image': ((rowConfig['background-image'] !== '') ? 'url("' + rowConfig['background-image'] + '")' : ''),
                'background-repeat': rowConfig['background-repeat'],
                'background-position': rowConfig['background-position']
            });

            $(this).addClass(rowConfig.class);
        });

        // column styles
        $sourcetplcmpld.find('.am-column').each(function () {
            var columnConfig = JSON.parse($(this).attr('data-config'));

            $(this).css({
                'width': columnConfig.width,
                'height': columnConfig.height,
                'background-color': columnConfig['background-color'],
                'background-image': ((columnConfig['background-image'] !== '') ? 'url("' + columnConfig['background-image'] + '")' : ''),
                'background-repeat': columnConfig['background-repeat'],
                'background-position': columnConfig['background-position']
            });

            $(this).addClass(columnConfig.class);
        });

        // link styles
        $sourcetplcmpld.find('.am-link').each(function () {
            var linkConfig = JSON.parse($(this).attr('data-config'));

            $(this).find('> a').css({
                'color': linkConfig.color,
                'background-color': linkConfig['background-color'],
                'font-size': linkConfig['font-size'],
                'font-weight': ((linkConfig.bold) ? 'bold' : ''),
                'font-style': ((linkConfig.italic) ? 'italic' : ''),
                'text-decoration': ((linkConfig.underline) ? 'underline' : '')
            });

            if (linkConfig['custom-text']['lang_id_' + menu.settings.activeLang])
                $(this).find('> a > .am-link-text').text(linkConfig['custom-text']['lang_id_' + menu.settings.activeLang]);

            if (linkConfig.icon != '' && linkConfig.icon != '-')
                $(this).find('> a').prepend('<i class="fa ' + linkConfig.icon + '"></i>');

            if (linkConfig['badge-text']['lang_id_' + menu.settings.activeLang])
                $('<span class="am-badge">' + linkConfig['badge-text']['lang_id_' + menu.settings.activeLang] + '</span>')
                    .appendTo($(this).find('> a')).css({
                    'color': linkConfig['badge-color'],
                    'background-color': linkConfig['badge-background-color']
                });

            $(this).addClass(linkConfig.class);
        });

        // html styles
        $sourcetplcmpld.find('.am-html').each(function () {
            var htmlConfig = JSON.parse($(this).attr('data-config'));

            $(this).css({
                'width': htmlConfig.width,
                'height': htmlConfig.height
            });

            $(this).addClass(htmlConfig.class);
        });

        // youtube styles
        $sourcetplcmpld.find('.am-youtube').each(function () {
            var youtubeConfig = JSON.parse($(this).attr('data-config'));

            $(this).addClass(youtubeConfig.class);

            $(this).css({
                'width': youtubeConfig.width,
                'height': youtubeConfig.height
            });

            $(this).find('iframe').attr({
                'src': '//www.youtube' + ((youtubeConfig['enable-privacy-enh-mode']) ? '-nocookie' : '') + '.com/embed/' + youtubeConfig.video +
                '?showinfo=' + youtubeConfig['show-title-and-actions'] + '&rel=' + youtubeConfig['show-suggested'] +
                '&controls=' + youtubeConfig['show-controls'] + '&autoplay=' + youtubeConfig.autoplay
            });

            $(this).addClass(youtubeConfig.class);
        });

        // vimeo styles
        $sourcetplcmpld.find('.am-vimeo').each(function () {
            var vimeoConfig = JSON.parse($(this).attr('data-config'));

            $(this).addClass(vimeoConfig.class);

            $(this).css({
                'width': vimeoConfig.width,
                'height': vimeoConfig.height
            });

            $(this).find('iframe').attr({
                'src': '//player.vimeo.com/video/' + vimeoConfig.video +
                '?autoplay=' + vimeoConfig.autoplay + '&loop=' + vimeoConfig.loop + '&color=' + vimeoConfig.color +
                '&portrait=' + vimeoConfig['show-portrait'] + '&title=' + vimeoConfig['show-title'] +
                '&byline=' + vimeoConfig['show-byline'] + '&badge=' + vimeoConfig['show-badge']
            });

            $(this).addClass(vimeoConfig.class);
        });

        // google map styles
        $sourcetplcmpld.find('.am-googlemap').each(function () {
            var gMapConfig = JSON.parse($(this).attr('data-config')),
                gMapId = 'am-googlemap-' + gMapCounter;

            $(this).find('> .am-item-pad > .am-googlemap-container').attr({
                'id': gMapId,
                'data-counter': gMapCounter
            });

            $(this).css({
                'width': gMapConfig.width,
                'height': gMapConfig.height
            });

            gMapCounter++;
        });
        $sourcetplcmpld.find('.am-googlemap-container').mousedown(function (event) {
            event.stopPropagation();
        });

        if ($('#am-editor-view').hasClass('active'))
            $sourcetplcmpld.find('.am-tabs-root').addClass('am-tabs-live-view').removeClass('am-tabs-editor-view');
        else
            $sourcetplcmpld.find('.am-tabs-root').addClass('am-tabs-editor-view').removeClass('am-tabs-live-view');

        return $sourcetplcmpld.find('>:first-child');
    };

    var getJSONFromHTML = function ($item) {
        var jsonGetJSONFromHTML = [],
            $firstchild = $item.find('[data-xtype]:first'),
            $childitems = $firstchild.parent().find('> [data-xtype]');

        $.each($childitems, function (index) {
            jsonGetJSONFromHTML[index] = {};
            jsonGetJSONFromHTML[index]['xtype'] = $(this).attr('data-xtype');
            jsonGetJSONFromHTML[index]['config'] = JSON.parse($(this).attr('data-config'));
            jsonGetJSONFromHTML[index]['items'] = getJSONFromHTML($(this));
        });

        return jsonGetJSONFromHTML;
    };

    var prepareItemJson = function (amData) {
        var itemSelected = amData.formdata['select-item'],
            json = [],
            jsonItem = {
                'xtype': itemSelected.xtype,
                'config': {},
                'items': []
            },
            tempJsonItem = {};

        switch (itemSelected.xtype) {
            case 'tabs':
                jsonItem.config = $.extend({}, amData.formdata['tabs-' + itemSelected.layout]);
                jsonItem.config.layout = itemSelected.layout;
                json.push(jsonItem);

                break;
            case 'tab':
            case 'link':
                jsonItem.config = $.extend({}, amData.formdata.link);

                if (jsonItem.xtype == 'tab')
                    jsonItem.config.dropdown = $.extend({}, amData.formdata.dropdown);

                if (itemSelected.format == 'table' || itemSelected.format == 'tree')
                    $.each(amData.formdata['link-' + itemSelected.format], function (index, item) {
                        tempJsonItem = $.extend({}, jsonItem);
                        tempJsonItem.config = $.extend({}, tempJsonItem.config, item);
                        tempJsonItem.config.format = itemSelected.format;

                        if (itemSelected.xtype == 'tab' && amData.tab)
                            tempJsonItem.items = getJSONFromHTML(amData.tab);

                        json.push(tempJsonItem);
                    });
                else {
                    jsonItem.config = $.extend({}, jsonItem.config, amData.formdata['link-form']);
                    jsonItem.config.entity = 'custom';
                    jsonItem.config.format = 'form';

                    if (itemSelected.xtype == 'tab' && amData.tab)
                        jsonItem.items = getJSONFromHTML(amData.tab);

                    json.push(jsonItem);
                }

                break;
            case 'html':
            case 'youtube':
            case 'vimeo':
            case 'googlemap':
                $.extend(jsonItem.config, amData.formdata[itemSelected.xtype]);
                json = [jsonItem];

                break;
        }

        return json;
    };

    var setEditorData = function (json) {
        try {
            json = JSON.parse(json);
        }
        catch (e) {
            console.log('already json');
        }

        $('#am-editor').html(getHTMLFromJSON(json.menu));
        $('#am-editor-revision-hidden').val(json.id);
        $('#am-editor-title-text').val(json.title);
        menu.trigger('update');

        // set editor colorpickers
        $('#am-editor-theme-mb-topclr').val('#' + json.theme['mb-topclr']).trigger('keyup');
        $('#am-editor-theme-mb-btmclr').val('#' + json.theme['mb-btmclr']).trigger('keyup');
        $('#am-editor-theme-mb-txtclr').val('#' + json.theme['mb-txtclr']).trigger('keyup');

        $('#am-editor-theme-mb-hvr-topclr').val('#' + json.theme['mb-hvr-topclr']).trigger('keyup');
        $('#am-editor-theme-mb-hvr-btmclr').val('#' + json.theme['mb-hvr-btmclr']).trigger('keyup');
        $('#am-editor-theme-mb-hvr-txtclr').val('#' + json.theme['mb-hvr-txtclr']).trigger('keyup');

        $('#am-editor-theme-mb-bdrclr').val('#' + json.theme['mb-bdrclr']).trigger('keyup');

        $('#am-editor-theme-dd-topclr').val('#' + json.theme['dd-topclr']).trigger('keyup');
        $('#am-editor-theme-dd-btmclr').val('#' + json.theme['dd-btmclr']).trigger('keyup');
        $('#am-editor-theme-dd-txtclr').val('#' + json.theme['dd-txtclr']).trigger('keyup');
        $('#am-editor-theme-dd-ttlclr').val('#' + json.theme['dd-ttlclr']).trigger('keyup');

        $('#am-editor-theme-dd-bdrclr').val('#' + json.theme['dd-bdrclr']).trigger('keyup');

        ace.edit('am-css-editor').setValue(json.theme.css, 1);
        onCustomCSSChange();
    };

    var saveMenu = function (live) {
        var menuData = {
            'title': $('#am-editor-title-text').val(),
            'menu': getJSONFromHTML($('#am-editor')),
            'theme': {
                'mb-topclr': $('#am-editor-theme-mb-topclr').val().replace('#', ''),
                'mb-btmclr': $('#am-editor-theme-mb-btmclr').val().replace('#', ''),
                'mb-txtclr': $('#am-editor-theme-mb-txtclr').val().replace('#', ''),

                'mb-hvr-topclr': $('#am-editor-theme-mb-hvr-topclr').val().replace('#', ''),
                'mb-hvr-btmclr': $('#am-editor-theme-mb-hvr-btmclr').val().replace('#', ''),
                'mb-hvr-txtclr': $('#am-editor-theme-mb-hvr-txtclr').val().replace('#', ''),

                'mb-bdrclr': $('#am-editor-theme-mb-bdrclr').val().replace('#', ''),

                'dd-topclr': $('#am-editor-theme-dd-topclr').val().replace('#', ''),
                'dd-btmclr': $('#am-editor-theme-dd-btmclr').val().replace('#', ''),
                'dd-txtclr': $('#am-editor-theme-dd-txtclr').val().replace('#', ''),
                'dd-ttlclr': $('#am-editor-theme-dd-ttlclr').val().replace('#', ''),

                'dd-bdrclr': $('#am-editor-theme-dd-bdrclr').val().replace('#', ''),

                'css': ace.edit('am-css-editor').getValue()
            },
            'edit': 1,
            'live': live
        };

        ajaxRequest({
            app: 'admin/menu/savemenu',
            menu: encodeURIComponent(JSON.stringify(menuData))
        }).done(function (json) {
            setEditorData(json);
            $('#am-revisions-table').trigger('reload');
        });
    };

    var onMenuUpdate = function () {
        var amTabs = $('.am-tabs'),
            amTab = $('.am-tab'),
            amDropDown = $('.am-dropdown');

        amDropDown.css({
            'display': 'block',
            'min-height': ''
        });

        $('.am-tabs-horizontal, .am-tabs-vertical').each(function () {
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

        amTabs.find('.am-tab-dropdown-parent-center').each(function () {
            var tabwidth = $('> a', this).outerWidth(),
                dropdownwidth = $('> .am-dropdown', this).outerWidth();

            $('> .am-dropdown', this).css('left', ((tabwidth - dropdownwidth) / 2) + 'px');
        });

        amTabs.not('.am-tabs-root').each(function () {
            if ($(this).find('> .am-sortable > .am-tab-hover').length == 0)
                $(this).find('> .am-sortable > .am-tab').filter(':first-child').trigger('click');
        });

        amDropDown.css('display', '');

        /* initialize google maps */
        $('.am-googlemap-container').each(function () {
            var dataGMap = $(this).data('gMap'),
                dataLat = $(this).attr('data-lat'),
                dataLng = $(this).attr('data-lng'),
                dataZoom = $(this).attr('data-zoom');

            if (typeof dataGMap === 'undefined'
                && $.isNumeric(dataLat)
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

    var onTabActive = function (event, tab) {
        // resize google maps
        $.each(tab.find('.am-googlemap-container'), function () {
            var gMap = $(this).data('gMap'),
                gMarker = $(this).data('gMarker');

            google.maps.event.trigger(gMap, 'resize');
            gMap.setCenter(gMarker.getPosition());
        });
    };

    /*
     item config dialog functions
     */

    var selectItemChange = function () {
        // show hide containers
        var configContainerId = '',
            itemSelectedOption = $(this).find('option:selected'),
            itemSelectedXtype = itemSelectedOption.attr('data-xtype'),
            itemSelectedEntity = itemSelectedOption.attr('data-entity'),
            itemSelectedFormat = itemSelectedOption.attr('data-format'),
            itemSelectedLayout = itemSelectedOption.attr('data-layout'),
            linkConfig = $('#link-configuration'),
            dropdownConfig = $('#dropdown-configuration');

        $('.item-configuration').hide();
        linkConfig.hide();
        dropdownConfig.hide();

        if (itemSelectedXtype == 'link' || itemSelectedXtype == 'tab') {
            configContainerId = 'link-' + itemSelectedFormat + '-configuration';
            linkConfig.show();

            if (itemSelectedXtype == 'tab')
                dropdownConfig.show();
        }
        else if (itemSelectedXtype == 'tabs')
            configContainerId = itemSelectedXtype + '-' + itemSelectedLayout + '-configuration';
        else
            configContainerId = itemSelectedXtype + '-configuration';

        $('#' + configContainerId).show();

        // Load tree or table on change
        var linkTableTable = $('#link-table-table'),
            linkTreeTable = $('#link-tree-table'),
            initdLinkTableTable = linkTableTable.data('initdLinkTableTable'),
            initdLinkTreeTable = linkTreeTable.data('initdLinkTreeTable'),
            linkTableTableEntity = linkTableTable.data('linkTableTableEntity'),
            linkTreeTableEntity = linkTreeTable.data('linkTreeTableEntity');

        if (itemSelectedFormat == 'table') {
            if (typeof initdLinkTableTable == 'undefined') {
                initLinkTableTable();
                linkTableTable.data('initdLinkTableTable', true);
            }
            else if ((linkTableTableEntity != itemSelectedEntity)
                || typeof linkTableTable.data('amDataRowSelected') != 'undefined')
                $('#link-table-reload').trigger('click');

            linkTableTable.data('linkTableTableEntity', itemSelectedEntity);
        }
        else if (itemSelectedFormat == 'tree') {
            if (typeof initdLinkTreeTable == 'undefined') {
                initLinkTreeTable();
                linkTreeTable.data('initdLinkTreeTable', true);
            }
            else if ((linkTreeTableEntity != itemSelectedEntity)
                || typeof linkTreeTable.data('amDataRowSelected') != 'undefined')
                $('#link-tree-reload').trigger('click');

            linkTreeTable.data('linkTreeTableEntity', itemSelectedEntity);
        }

        // for overlay issue
        $(window).trigger('resize');
    };

    var getItemConfigModalData = function () {
        var itemSelectedOption = $('#am-select-item').find('option:selected'),
            linkTableTable = $('#link-table-table'),
            linkTreeTable = $('#link-tree-table'),
            formData;

        // set textarea from tinymce
        //makeTextareaAsTinymce();

        formData = {
            'select-item': {
                'xtype': itemSelectedOption.attr('data-xtype'),
                'entity': itemSelectedOption.attr('data-entity'),
                'format': itemSelectedOption.attr('data-format'),
                'layout': itemSelectedOption.attr('data-layout')
            },
            'link-table': [],
            'link-tree': [],
            'link-form': {
                'text': {},
                'url': {}
            },
            'tabs-horizontal': {
                'width': $('#tabs-horizontal-width').val(),
                'height': $('#tabs-horizontal-height').val(),
                'class': $('#tabs-horizontal-class').val()
            },
            'tabs-vertical': {
                'width': $('#tabs-vertical-width').val(),
                'height': $('#tabs-vertical-height').val(),
                'class': $('#tabs-vertical-class').val()
            },
            'tabs-accordion': {
                'width': $('#tabs-accordion-width').val(),
                'height': $('#tabs-accordion-height').val(),
                'class': $('#tabs-accordion-class').val()
            },
            'html': {
                'content': {},
                'width': $('#html-width').val(),
                'height': $('#html-height').val(),
                'class': $('#html-class').val()
            },
            'youtube': {
                'video': $('#youtube-video').val(),
                'width': $('#youtube-width').val(),
                'height': $('#youtube-height').val(),
                'class': $('#youtube-class').val(),
                'autoplay': $('#youtube-autoplay').is(':checked') & 1,
                'show-suggested': $('#youtube-show-suggested').is(':checked') & 1,
                'show-controls': $('#youtube-show-controls').is(':checked') & 1,
                'show-title-and-actions': $('#youtube-show-title-and-actions').is(':checked') & 1,
                'enable-privacy-enh-mode': $('#youtube-enable-privacy-enh-mode').is(':checked') & 1
            },
            'vimeo': {
                'video': $('#vimeo-video').val(),
                'width': $('#vimeo-width').val(),
                'height': $('#vimeo-height').val(),
                'class': $('#vimeo-class').val(),
                'color': $('#vimeo-color').val(),
                'autoplay': $('#vimeo-autoplay').is(':checked'),
                'loop': $('#vimeo-loop').is(':checked'),
                'show-portrait': $('#vimeo-show-portrait').is(':checked'),
                'show-title': $('#vimeo-show-title').is(':checked'),
                'show-byline': $('#vimeo-show-byline').is(':checked'),
                'show-badge': $('#vimeo-show-badge').is(':checked')
            },
            'googlemap': {
                'address': $('#googlemap-address').val(),
                'latitude': $('#googlemap-latitude').val(),
                'longitude': $('#googlemap-longitude').val(),
                'zoom': $('#googlemap-zoom').val(),
                'width': $('#googlemap-width').val(),
                'height': $('#googlemap-height').val(),
                'class': $('#googlemap-class').val()
            },
            'link': {
                'custom-text': {},
                'icon': $('#link-icon').val(),
                'target': $('#link-target').val(),
                'color': $('#link-color').val(),
                'class': $('#link-class').val(),
                'background-color': $('#link-background-color').val(),
                'font-size': $('#link-font-size').val(),
                'bold': $('#link-bold').is(':checked'),
                'italic': $('#link-italic').is(':checked'),
                'underline': $('#link-underline').is(':checked'),
                'badge-text': {},
                'badge-color': $('#link-badge-color').val(),
                'badge-background-color': $('#link-badge-background-color').val()
            },
            'dropdown': {
                'align': $('.dropdown-align.checked').data('dropdown-align'),
                'width': $('#dropdown-width').val(),
                'height': $('#dropdown-height').val(),
                'class': $('#dropdown-class').val(),
                'background-color': $('#dropdown-background-color').val(),
                'background-image': $('#dropdown-background-image').val(),
                'background-repeat': $('#dropdown-background-repeat').val(),
                'background-position': $('#dropdown-background-position').val()
            }
        };

        var langContent = '',
            activeLangContent = '',
            langItems = [
                {'key': 'link-form', 'name': 'text'},
                {'key': 'link-form', 'name': 'url'},
                {'key': 'link', 'name': 'custom-text'},
                {'key': 'link', 'name': 'badge-text'},
                {'key': 'html', 'name': 'content'}
            ];

        $.each(langItems, function (index, langItem) {
            activeLangContent = $.trim($('*[name="' + langItem.key + '-' + langItem.name + '[' + menu.settings.activeLang + ']"]').val());

            $.each(menu.settings.langs, function (index, lang) {
                langContent = $.trim($('*[name="' + langItem.key + '-' + langItem.name + '[' + lang.id + ']"]').val()).replace(/(&quot;)|(&#34;)/g, '"');

                if (langContent != '')
                    formData[langItem.key][langItem.name]['lang_id_' + lang.id] = langContent;
                else
                    formData[langItem.key][langItem.name]['lang_id_' + lang.id] = activeLangContent;
            });
        });

        if (typeof linkTableTable.data('initdLinkTableTable') != 'undefined')
            linkTableTable.find('tbody tr').each(function () {
                if ($(this).hasClass('selected'))
                    formData['link-table'].push($(this).data('amData'));
            });

        if (typeof linkTreeTable.data('initdLinkTreeTable') != 'undefined')
            linkTreeTable.fancytree('getTree').visit(function (node) {
                if (node.isSelected())
                    formData['link-tree'].push(node.data);
            });

        return formData;
    };

    var setItemConfigModalData = function (amData) {
        amData.config = JSON.parse(amData.config);

        switch (amData.xtype) {
            case 'tabs':
                $('#tabs-' + amData.config.layout + '-width').val(amData.config.width);
                $('#tabs-' + amData.config.layout + '-height').val(amData.config.height);
                $('#tabs-' + amData.config.layout + '-class').val(amData.config.class);

                break;
            case 'tab':
            case 'link':
                if (amData.config.format == 'table') {
                    $('#link-table-table').data('amDataRowSelected', amData.config.entity_item_id);
                    $('#link-table-search').val('id:' + amData.config.entity_item_id);
                }
                else if (amData.config.format == 'tree') {
                    $('#link-tree-table').data('amDataRowSelected', amData.config.entity_item_id);
                    $('#link-tree-search').val('id:' + amData.config.entity_item_id);
                }
                else
                    $.each(menu.settings.langs, function (index, lang) {
                        $('*[name="link-form-text[' + lang.id + ']"]').val(amData.config.text['lang_id_' + lang.id]);
                        $('*[name="link-form-url[' + lang.id + ']"]').val(amData.config.url['lang_id_' + lang.id]);
                    });

                $.each(menu.settings.langs, function (index, lang) {
                    $('*[name="link-custom-text[' + lang.id + ']"]').val(amData.config['custom-text']['lang_id_' + lang.id]);
                    $('*[name="link-badge-text[' + lang.id + ']"]').val(amData.config['badge-text']['lang_id_' + lang.id]);
                });
                $('#link-icon').val(amData.config.icon).trigger('keyup');
                $('#link-target').val(amData.config.target);
                $('#link-color').val(amData.config.color);
                $('#link-class').val(amData.config.class);
                $('#link-background-color').val(amData.config['background-color']);
                $('#link-font-size').val(amData.config['font-size']);
                $('#link-bold').prop('checked', amData.config.bold);
                $('#link-italic').prop('checked', amData.config.italic);
                $('#link-underline').prop('checked', amData.config.underline);
                $('#link-badge-color').val(amData.config['badge-color']);
                $('#link-badge-background-color').val(amData.config['badge-background-color']);

                if (typeof amData.config.dropdown != 'undefined') {
                    $('.dropdown-align[data-dropdown-align="' + amData.config.dropdown.align + '"]').trigger('click');
                    $('#dropdown-width').val(amData.config.dropdown.width);
                    $('#dropdown-height').val(amData.config.dropdown.height);
                    $('#dropdown-class').val(amData.config.dropdown.class);
                    $('#dropdown-background-color').val(amData.config.dropdown['background-color']);
                    $('#dropdown-background-image').val(amData.config.dropdown['background-image']);
                    $('#dropdown-background-repeat').val(amData.config.dropdown['background-repeat']);
                    $('#dropdown-background-position').val(amData.config.dropdown['background-position']);
                }

                break;
            case 'html':
                $.each(menu.settings.langs, function (index, lang) {
                    $('*[name="html-content[' + lang.id + ']"]').val(amData.config.content['lang_id_' + lang.id]);
                });
                $('#html-width').val(amData.config.width);
                $('#html-height').val(amData.config.height);
                $('#html-class').val(amData.config.class);

                break;
            case 'youtube':
                $('#youtube-video').val(amData.config.video);
                $('#youtube-width').val(amData.config.width);
                $('#youtube-height').val(amData.config.height);
                $('#youtube-class').val(amData.config.class);
                $('#youtube-autoplay').prop('checked', amData.config.autoplay);
                $('#youtube-show-suggested').prop('checked', amData.config['show-suggested']);
                $('#youtube-show-controls').prop('checked', amData.config['show-controls']);
                $('#youtube-show-title-and-actions').prop('checked', amData.config['show-title-and-actions']);
                $('#youtube-enable-privacy-enh-mode').prop('checked', amData.config['enable-privacy-enh-mode']);

                break;
            case 'vimeo':
                $('#vimeo-video').val(amData.config.video);
                $('#vimeo-width').val(amData.config.width);
                $('#vimeo-height').val(amData.config.height);
                $('#vimeo-class').val(amData.config.class);
                $('#vimeo-color').val(amData.config.color);
                $('#vimeo-autoplay').prop('checked', amData.config.autoplay);
                $('#vimeo-loop').prop('checked', amData.config.loop);
                $('#vimeo-show-portrait').prop('checked', amData.config['show-portrait']);
                $('#vimeo-show-title').prop('checked', amData.config['show-title']);
                $('#vimeo-show-byline').prop('checked', amData.config['show-byline']);
                $('#vimeo-show-badge').prop('checked', amData.config['show-badge']);

                break;
            case 'googlemap':
                $('#googlemap-address').val(amData.config.address);
                $('#googlemap-latitude').val(amData.config.latitude);
                $('#googlemap-longitude').val(amData.config.longitude);
                $('#googlemap-zoom').val(amData.config.zoom);
                $('#googlemap-width').val(amData.config.width);
                $('#googlemap-height').val(amData.config.height);
                $('#googlemap-class').val(amData.config.class);

                break;
        }

        // set tinymce from textarea
        //makeTinymceAsTextarea();
    };

    var resetItemConfigModalData = function () {
        var layouts = ['vertical', 'horizontal', 'accordion'];

        $.each(function (index, layout) {
            $('#tabs-' + layout + '-width').val('');
            $('#tabs-' + layout + '-height').val('');
            $('#tabs-' + layout + '-class').val('');
        });

        var linkTableTable = $('#link-table-table'),
            linkTableSearch = $('#link-table-search'),
            linkTreeTable = $('#link-tree-table'),
            linkTreeSearch = $('#link-tree-search'),
            initdLinkTableTable = linkTableTable.data('initdLinkTableTable'),
            initdLinkTreeTable = linkTreeTable.data('initdLinkTreeTable');

        if (typeof initdLinkTableTable != 'undefined') {
            linkTableTable.removeData('amDataRowSelected');

            if (linkTableSearch.val() != '') {
                linkTableSearch.val('');
                $('#link-table-reload').trigger('click');
            }
            else
                $('#link-table-deselect').trigger('click');
        }

        if (typeof initdLinkTreeTable != 'undefined') {
            linkTreeTable.removeData('amDataRowSelected');

            if (linkTreeSearch.val() != '') {
                linkTreeSearch.val('');
                $('#link-tree-reload').trigger('click');
            }
            else
                $('#link-tree-deselect').trigger('click');
        }

        $.each(menu.settings.langs, function (index, lang) {
            $('*[name="link-form-text[' + lang.id + ']"]').val('');
            $('*[name="link-form-url[' + lang.id + ']"]').val('');

            $('*[name="link-custom-text[' + lang.id + ']"]').val('');
            $('*[name="link-badge-text[' + lang.id + ']"]').val('');

            $('*[name="html-content[' + lang.id + ']"]').val('');
        });

        $('#link-icon').val('-').trigger('keyup');
        $('#link-target').val('');
        $('#link-color').val('');
        $('#link-class').val('');
        $('#link-background-color').val('');
        $('#link-font-size').val('');
        $('#link-bold').prop('checked', false);
        $('#link-italic').prop('checked', false);
        $('#link-underline').prop('checked', false);
        $('#link-badge-color').val('');
        $('#link-badge-background-color').val('');

        $('.dropdown-align[data-dropdown-align="full-width"]').trigger('click');
        $('#dropdown-width').val('');
        $('#dropdown-height').val('');
        $('#dropdown-class').val('');
        $('#dropdown-background-color').val('');
        $('#dropdown-background-image').val('');
        $('#dropdown-background-repeat').val('');
        $('#dropdown-background-position').val('');

        $('#html-width').val('');
        $('#html-height').val('');
        $('#html-class').val('');

        $('#youtube-video').val('');
        $('#youtube-width').val('');
        $('#youtube-height').val('');
        $('#youtube-class').val('');
        $('#youtube-autoplay').prop('checked', false);
        $('#youtube-show-suggested').prop('checked', true);
        $('#youtube-show-controls').prop('checked', true);
        $('#youtube-show-title-and-actions').prop('checked', false);
        $('#youtube-enable-privacy-enh-mode').prop('checked', false);

        $('#vimeo-video').val('');
        $('#vimeo-width').val('');
        $('#vimeo-height').val('');
        $('#vimeo-class').val('');
        $('#vimeo-color').val('');
        $('#vimeo-autoplay').prop('checked', false);
        $('#vimeo-loop').prop('checked', false);
        $('#vimeo-show-portrait').prop('checked', false);
        $('#vimeo-show-title').prop('checked', false);
        $('#vimeo-show-byline').prop('checked', false);
        $('#vimeo-show-badge').prop('checked', false);

        $('#googlemap-address').val('');
        $('#googlemap-latitude').val('');
        $('#googlemap-longitude').val('');
        $('#googlemap-zoom').val(15);
        $('#googlemap-width').val('');
        $('#googlemap-height').val('');
        $('#googlemap-class').val('');

        // reset tinymce
        //resetTinymce();
    };

    var onItemConfigModalShow = function () {
        // Logic to show hide select options
        var selectItem = $('#am-select-item'),
            amData = $(this).data('amData'),
            xtype = '',
            config = '';

        selectItem.find('option').show();
        selectItem.find('option:first-child').prop('selected', true);

        if (amData.for == 'tab') {
            selectItem.find('option[data-xtype="link"]').attr('data-xtype', 'tab');
            selectItem.find('option').not(':first-child,[data-xtype="tab"]').hide();
        }
        else
            selectItem.find('option[data-xtype="tab"]').attr('data-xtype', 'link');

        if (amData.action == 'edit') {
            xtype = amData[amData.for].attr('data-xtype');
            config = JSON.parse(amData[amData.for].attr('data-config'));

            if (xtype == 'tab' || xtype == 'link')
                selectItem.find('option[data-entity="' + config.entity + '"]').prop('selected', true);
            else if (xtype == 'tabs')
                selectItem.find('option[data-layout="' + config.layout + '"]').prop('selected', true);
            else
                selectItem.find('option[data-xtype="' + xtype + '"]').prop('selected', true);
        }

        selectItem.trigger('change');
    };

    var onItemConfigModalShown = function () {
        $('#am-select-item').focus();

        // resize tinymce
        //resizeTinymce();
    };

    var initLinkTableTable = function () {
        var linkTableTable = $('#link-table-table'),
            linkTableReload = $('#link-table-reload'),
            linkTableSearch = $('#link-table-search'),
            itemSelect = $('#am-select-item'),
            itemSelectedOption = itemSelect.find('option:selected');

        linkTableTable.dataTable({
            'processing': true,
            'serverSide': true,
            'iDisplayLength': 20,
            'autoWidth': false,
            'oSearch': {
                'sSearch': linkTableSearch.val()
            },
            'ajax': {
                'url': menu.settings.url,
                'data': function (d) {
                    var entity = '';

                    itemSelectedOption = itemSelect.find('option:selected');

                    if (typeof itemSelectedOption.data('entity') != 'undefined'
                        && itemSelectedOption.data('format') == 'table')
                        entity = itemSelectedOption.data('entity');
                    else {
                        entity = itemSelect.find('option[data-format="table"]').first().data('entity');
                        linkTableTable.data('linkTableTableEntity', entity);
                    }

                    d.app = 'admin/menu/getentity/entity/' + entity + '/type/table';
                    d.search = linkTableSearch.val();
                }
            },
            'columns': [
                {'data': 'entity_item_id', 'width': '72px'},
                {'data': 'text'}
            ],
            'order': [[0, 'asc']],
            'pagingType': 'full_numbers',
            'bLengthChange': false,
            'columnDefs': [{
                render: function (data) {
                    if (typeof data === 'object')
                        return data['lang_id_' + menu.settings.activeLang];
                    else
                        return data;
                },
                targets: 1
            }],
            'rowCallback': function (row, data, displayIndex) {
                $(row).data('amData', data);

                if ($(this).data('amDataRowSelected') == data.entity_item_id)
                    $(row).addClass('selected');
            }
        }).on('draw.dt', function () {
            // for overlay issue
            $(window).trigger('resize');
        });

        linkTableTable.find('tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');
        });

        linkTableReload.click(function () {
            linkTableTable.api().ajax.reload();
        });

        $('#link-table-select').click(function () {
            linkTableTable.find('tbody tr').addClass('selected');
        });

        $('#link-table-deselect').click(function () {
            linkTableTable.find('tbody tr').removeClass('selected');
        });

        linkTableSearch.keyup(function (e) {
            if (e.keyCode == 13)
                linkTableReload.trigger('click');
        });

        $('#link-table-length').keyup(function (e) {
            if (e.keyCode == 13) {
                var newLength = parseInt($(this).val());

                newLength = isNaN(newLength) ? 25 : newLength;

                linkTableTable.fnSettings()._iDisplayLength = newLength;
                linkTableTable.fnDraw();
            }
        });
    };

    var initLinkTreeTable = function () {
        var linkTreeTable = $('#link-tree-table'),
            linkTreeReload = $('#link-tree-reload'),
            linkTreeSearch = $('#link-tree-search'),
            itemSelect = $('#am-select-item'),
            itemSelectedOption = itemSelect.find('option:selected');

        linkTreeTable.fancytree({
            extensions: ['table', 'glyph'],
            checkbox: true,
            table: {
                indentation: 20,      // indent 20px per node level
                nodeColumnIdx: 2,     // render the node title into the 2nd column
                checkboxColumnIdx: 0  // render the checkboxes into the 1st column
            },
            glyph: {
                map: {
                    doc: 'glyphicon glyphicon-file',
                    docOpen: 'glyphicon glyphicon-file',
                    checkbox: 'glyphicon glyphicon-unchecked',
                    checkboxSelected: 'glyphicon glyphicon-check',
                    checkboxUnknown: 'glyphicon glyphicon-share',
                    error: 'glyphicon glyphicon-warning-sign',
                    expanderClosed: 'glyphicon glyphicon-plus-sign',
                    expanderLazy: 'glyphicon glyphicon-plus-sign',
                    expanderOpen: 'glyphicon glyphicon-minus-sign',
                    folder: 'glyphicon glyphicon-folder-close',
                    folderOpen: 'glyphicon glyphicon-folder-open',
                    loading: 'glyphicon glyphicon-refresh'
                }
            },
            source: $.ajax({
                url: menu.settings.url,
                dataType: 'json',
                data: {
                    'app': 'admin/menu/getentity/entity/' + itemSelectedOption.data('entity') + '/type/tree',
                    'search': linkTreeSearch.val()
                }
            }),
            lazyLoad: function (event, data) {
                itemSelectedOption = $('#am-select-item').find('option:selected');

                data.result = $.ajax({
                    url: menu.settings.url,
                    dataType: 'json',
                    data: {
                        'app': 'admin/menu/getentity/entity/' + itemSelectedOption.data('entity') + '/type/tree',
                        'parent_id': data.node.key
                    }
                });
            },
            loadChildren: function (event, data) {
                if (data.node.isRoot() && data.node.countChildren() === 0)
                    linkTreeTable.find('tbody').html('<tr class="noresultfound">' +
                        '<td colspan="4">No data available in table</td>' +
                        '</tr>');
                else
                    linkTreeTable.find('tbody .noresultfound').remove();
            },
            renderColumns: function (event, data) {
                var node = data.node,
                    $tdList = $(node.tr).find('>td'),
                    url = '';

                if (linkTreeTable.data('amDataRowSelected') == node.data.entity_item_id)
                    node.setSelected(true);

                $tdList.eq(1).text(node.key);

                if (typeof node.data.text === 'object')
                    url = '<a class="link-tree-table-link" href="' + node.data.text['lang_id_' + menu.settings.activeLang] + '" target="_blank">&nbsp;</a>';
                else
                    url = '<a class="link-tree-table-link" href="' + node.data.text + '" target="_blank">&nbsp;</a>';

                $tdList.eq(3).html(url);
            },
            expand: function (event, data) {
                // for overlay issue
                $(window).trigger('resize');
            },
            init: function (event, data) {
                // for overlay issue
                $(window).trigger('resize');
            }
        });

        linkTreeReload.click(function () {
            var entity = '';

            itemSelectedOption = itemSelect.find('option:selected');

            if (typeof itemSelectedOption.data('entity') != 'undefined'
                && itemSelectedOption.data('format') == 'tree')
                entity = itemSelectedOption.data('entity');
            else {
                entity = itemSelect.find('option[data-format="tree"]').first().data('entity');
                linkTreeTable.data('linkTreeTableEntity', entity);
            }

            linkTreeTable.fancytree('getTree').reload($.ajax({
                url: menu.settings.url,
                dataType: 'json',
                data: {
                    'app': 'admin/menu/getentity/entity/' + entity + '/type/tree',
                    'search': linkTreeSearch.val()
                }
            }));
        });

        $('#link-tree-select').click(function () {
            linkTreeTable.fancytree('getTree').visit(function (node) {
                node.setSelected(true);
            });
        });

        $('#link-tree-deselect').click(function () {
            linkTreeTable.fancytree('getTree').visit(function (node) {
                node.setSelected(false);
            });
        });

        $('#link-tree-expand').click(function () {
            linkTreeTable.fancytree('getTree').visit(function (node) {
                node.setExpanded(true);
            });
        });

        $('#link-tree-collapse').click(function () {
            linkTreeTable.fancytree('getTree').visit(function (node) {
                node.setExpanded(false);
            });
        });

        linkTreeSearch.keyup(function (e) {
            if (e.keyCode == 13)
                linkTreeReload.trigger('click');
        });
    };

    var onItemConfigModalUpdate = function () {
        var itemConfigModal = $('#am-item-configuration-modal'),
            amData = itemConfigModal.data('amData');

        amData.formdata = getItemConfigModalData();

        switch (amData.method) {
            case 'addTabBefore':
                addTabBefore(amData);
                break;
            case 'addTabAfter':
                addTabAfter(amData);
                break;
            case 'editTab':
                editTab(amData);
                break;
            case 'addItemBefore':
                addItemBefore(amData);
                break;
            case 'addItemAfter':
                addItemAfter(amData);
                break;
            case 'editItem':
                editItem(amData);
                break;
        }

        itemConfigModal.modal('hide');
    };

    /*
     row config dialog functions
     */

    var getRowConfigModalData = function () {
        return {
            'height': $('#row-height').val(),
            'class': $('#row-class').val(),
            'background-color': $('#row-background-color').val(),
            'background-image': $('#row-background-image').val(),
            'background-repeat': $('#row-background-repeat').val(),
            'background-position': $('#row-background-position').val()
        };
    };

    var setRowConfigModalData = function (amData) {
        amData = JSON.parse(amData);

        $('#row-height').val(amData.height);
        $('#row-class').val(amData.class);
        $('#row-background-color').val(amData['background-color']);
        $('#row-background-image').val(amData['background-image']);
        $('#row-background-repeat').val(amData['background-repeat']);
        $('#row-background-position').val(amData['background-position']);
    };

    var resetRowConfigModalData = function () {
        $('#row-height').val('');
        $('#row-class').val('');
        $('#row-background-color').val('');
        $('#row-background-image').val('');
        $('#row-background-repeat').val('');
        $('#row-background-position').val('');
    };

    var onRowConfigModalShow = function () {
    };

    var onRowConfigModalShown = function () {
        $(this).find('input[name="row-height"]').focus();
    };

    /*
     column config dialog functions
     */

    var getColumnConfigModalData = function () {
        return {
            'width': $('#column-width').val(),
            'height': $('#column-height').val(),
            'class': $('#column-class').val(),
            'background-color': $('#column-background-color').val(),
            'background-image': $('#column-background-image').val(),
            'background-repeat': $('#column-background-repeat').val(),
            'background-position': $('#column-background-position').val()
        };
    };

    var setColumnConfigModalData = function (amData) {
        amData = JSON.parse(amData);

        $('#column-width').val(amData.width);
        $('#column-height').val(amData.height);
        $('#column-class').val(amData.class);
        $('#column-background-color').val(amData['background-color']);
        $('#column-background-image').val(amData['background-image']);
        $('#column-background-repeat').val(amData['background-repeat']);
        $('#column-background-position').val(amData['background-position']);
    };

    var resetColumnConfigModalData = function () {
        $('#column-width').val('');
        $('#column-height').val('');
        $('#column-class').val('');
        $('#column-background-color').val('');
        $('#column-background-image').val('');
        $('#column-background-repeat').val('');
        $('#column-background-position').val('');
    };

    var onColumnConfigModalShow = function () {
    };

    var onColumnConfigModalShown = function () {
        $(this).find('input[name="column-width"]').focus();
    };

    /*
     ajax request
     */

    var ajaxRequest = function (data) {
        return $.ajax({
            type: 'POST',
            url: menu.settings.url,
            data: data,
            beforeSend: function () {
                $('.loading').show();
            },
            complete: function () {
                $('.loading').hide();
            }
        })
    };

    var amHideOtherLanguages = function (langId) {
        if (langId.target)
            langId = $(this).data('id');

        $('.am-translatable-field').removeClass('am-translatable-field-active');
        $('.am-translatable-field[data-langid="' + langId + '"]').addClass('am-translatable-field-active');

        // resize tinymce
        //resizeTinymce();
    };

    var makeTextareaAsTinymce = function () {
        $('.am-htmleditor').each(function () {
            $(this).val(tinyMCE.get($(this).attr('id')).getContent());
        });
    };

    var makeTinymceAsTextarea = function () {
        $('.am-htmleditor').each(function () {
            tinyMCE.get($(this).attr('id')).setContent($(this).val());
        });
    };

    var resetTinymce = function () {
        $('.am-htmleditor').each(function () {
            tinyMCE.get($(this).attr('id')).setContent('');
        });
    };

    var resizeTinymce = function () {
        $('.am-htmleditor').each(function () {
            var tinymce_editor_iframe = $(tinyMCE.get($(this).attr('id')).editorContainer).find('iframe'),
                tinymce_editor_iframe_height = $(tinymce_editor_iframe).contents().find('html').height();

            $(tinymce_editor_iframe).height(tinymce_editor_iframe_height).css('height', tinymce_editor_iframe_height + 'px');
        });
    };

    /*
     tab event handlers
     */

    var onAddTabBefore = function (e) {
        e.stopPropagation();

        var itemConfigModal = $('#am-item-configuration-modal');

        itemConfigModal.data('amData', {
            'for': 'tab',
            'action': 'add',
            'method': 'addTabBefore',
            'tab': $(this).closest('.am-tab')
        });
        itemConfigModal.modal('show');
    };

    var addTabBefore = function (amData) {
        if (amData.formdata['select-item'].xtype != 'tab')
            return;

        var preparedItemJson = prepareItemJson(amData);

        $.each(preparedItemJson, function (index, item) {
            amData.tab.before(getHTMLFromJSON([item]));
            amData.tab.prev().effect("highlight", {color: "#C6FFC6"}, 500);
        });

        menu.trigger('update');
    };

    var onAddTabAfter = function (e) {
        e.stopPropagation();

        var itemConfigModal = $('#am-item-configuration-modal');

        itemConfigModal.data('amData', {
            'for': 'tab',
            'action': 'add',
            'method': 'addTabAfter',
            'btn': $(this)
        });
        itemConfigModal.modal('show');
    };

    var addTabAfter = function (amData) {
        if (amData.formdata['select-item'].xtype != 'tab')
            return;

        var preparedItemJson = prepareItemJson(amData);

        $.each(preparedItemJson, function (index, item) {
            amData.btn.prev().append(getHTMLFromJSON([item]));
            amData.btn.prev().children().last().effect("highlight", {color: "#C6FFC6"}, 500);
        });

        menu.trigger('update');
    };

    var onEditTab = function (e) {
        e.stopPropagation();

        var itemConfigModal = $('#am-item-configuration-modal');

        setItemConfigModalData({
            'xtype': 'tab',
            'config': $(this).closest('.am-tab').attr('data-config')
        });
        itemConfigModal.data('amData', {
            'for': 'tab',
            'action': 'edit',
            'method': 'editTab',
            'tab': $(this).closest('.am-tab')
        });
        itemConfigModal.modal('show');
    };

    var editTab = function (amData) {
        if (amData.formdata['select-item'].xtype != 'tab')
            return;

        var preparedItemJson = prepareItemJson(amData),
            tempDiv = $('<div/>');

        $.each(preparedItemJson, function (index, item) {
            tempDiv.append(getHTMLFromJSON([item]));
        });

        amData.tab.replaceWith(tempDiv.children());
        menu.trigger('update');
    };

    var deleteTab = function (e) {
        e.stopPropagation();

        if (confirm('Are you sure?')) {
            var $tab = $(this).closest('.am-tab');

            if ($tab.hasClass('am-tab-hover'))
                if ($tab.prev().length != 0)
                    $tab.prev().trigger('click');
                else if ($tab.next().length != 0)
                    $tab.next().trigger('click');

            $tab.effect('highlight', {color: '#FFA8A8'}, 500, function () {
                $(this).remove();
            });
            menu.trigger('update');
        }
    };

    /*
     row event handlers
     */

    var addRowBefore = function (e) {
        e.stopPropagation();

        var $row = $(this).closest('.am-row'),
            json = [{
                'xtype': 'row',
                'config': {
                    'height': '',
                    'class': '',
                    'background-color': '',
                    'background-image': '',
                    'background-repeat': '',
                    'background-position': ''
                },
                'items': []
            }];

        $row.before(getHTMLFromJSON(json));
        menu.trigger('update');
        $row.prev().effect("highlight", {color: "#C6FFC6"}, 500);
    };

    var addRowAfter = function (e) {
        e.stopPropagation();

        var json = [{
            'xtype': 'row',
            'config': {
                'height': '',
                'class': '',
                'background-color': '',
                'background-image': '',
                'background-repeat': '',
                'background-position': ''
            },
            'items': []
        }];

        $(this).prev().append(getHTMLFromJSON(json));
        menu.trigger('update');
        $(this).prev().children().last().effect("highlight", {color: "#C6FFC6"}, 500);
    };

    var onEditRow = function (e) {
        e.stopPropagation();

        var rowConfigModal = $('#am-row-configuration-modal');

        setRowConfigModalData($(this).closest('.am-row').attr('data-config'));
        rowConfigModal.data('amData', {
            'row': $(this).closest('.am-row')
        });
        rowConfigModal.modal('show');
    };

    var editRow = function () {
        var rowConfigModal = $('#am-row-configuration-modal'),
            row = rowConfigModal.data('amData').row,
            config = getRowConfigModalData(),
            json = [];

        json.push({
            'xtype': 'row',
            'config': config,
            'items': getJSONFromHTML(row)
        });
        row.replaceWith(getHTMLFromJSON(json).css('width', config.width).css('height', config.height));
        menu.trigger('update');
        rowConfigModal.modal('hide');
    };

    var deleteRow = function (e) {
        e.stopPropagation();

        if (confirm('Are you sure?')) {
            $(this).closest('.am-row').effect('highlight', {color: '#FFA8A8'}, 500, function () {
                $(this).remove();
            });
            menu.trigger('update');
        }
    };

    /*
     column event handlers
     */

    var addColumnBefore = function (e) {
        e.stopPropagation();

        var $column = $(this).closest('.am-column'),
            json = [{
                'xtype': 'column',
                'config': {
                    'width': '',
                    'height': '',
                    'class': '',
                    'background-color': '',
                    'background-image': '',
                    'background-repeat': '',
                    'background-position': ''
                },
                'items': []
            }];

        $column.before(getHTMLFromJSON(json));
        menu.trigger('update');
        $column.prev().effect("highlight", {color: "#C6FFC6"}, 500);
    };

    var addColumnAfter = function (e) {
        e.stopPropagation();

        var json = [{
            'xtype': 'column',
            'config': {
                'width': '',
                'height': '',
                'class': '',
                'background-color': '',
                'background-image': '',
                'background-repeat': '',
                'background-position': ''
            },
            'items': []
        }];

        $(this).prev().append(getHTMLFromJSON(json));
        menu.trigger('update');
        $(this).prev().children().last().effect("highlight", {color: "#C6FFC6"}, 500);
    };

    var onEditColumn = function () {
        var columnConfigModal = $('#am-column-configuration-modal');

        setColumnConfigModalData($(this).closest('.am-column').attr('data-config'));
        columnConfigModal.data('amData', {
            'column': $(this).closest('.am-column')
        });
        columnConfigModal.modal('show');
    };

    var editColumn = function (e) {
        e.stopPropagation();

        var columnConfigModal = $('#am-column-configuration-modal'),
            column = columnConfigModal.data('amData').column,
            config = getColumnConfigModalData(),
            json = [];

        json.push({
            'xtype': 'column',
            'config': config,
            'items': getJSONFromHTML(column)
        });
        column.replaceWith(getHTMLFromJSON(json).css('width', config.width).css('height', config.height));
        menu.trigger('update');
        columnConfigModal.modal('hide');
    };

    var deleteColumn = function (e) {
        e.stopPropagation();

        if (confirm('Are you sure?')) {
            $(this).closest('.am-column').effect('highlight', {color: '#FFA8A8'}, 500, function () {
                $(this).remove();
            });
            menu.trigger('update');
        }
    };

    /*
     item event handlers
     */

    var onAddItemBefore = function (e) {
        e.stopPropagation();

        var itemConfigModal = $('#am-item-configuration-modal');

        itemConfigModal.data('amData', {
            'for': 'item',
            'action': 'add',
            'method': 'addItemBefore',
            'item': $(this).closest('.am-item')
        });
        itemConfigModal.modal('show');
    };

    var addItemBefore = function (amData) {
        var preparedItemJson = prepareItemJson(amData);

        $.each(preparedItemJson, function (index, item) {
            amData.item.before(getHTMLFromJSON([item]));
            amData.item.prev().effect("highlight", {color: "#C6FFC6"}, 500);
        });

        menu.trigger('update');
    };

    var onAddItemAfter = function (e) {
        e.stopPropagation();

        var itemConfigModal = $('#am-item-configuration-modal');

        itemConfigModal.data('amData', {
            'for': 'item',
            'action': 'add',
            'method': 'addItemAfter',
            'btn': $(this)
        });
        itemConfigModal.modal('show');
    };

    var addItemAfter = function (amData) {
        var preparedItemJson = prepareItemJson(amData);

        $.each(preparedItemJson, function (index, item) {
            amData.btn.prev().append(getHTMLFromJSON([item]));
            amData.btn.prev().children().last().effect("highlight", {color: "#C6FFC6"}, 500);
        });

        menu.trigger('update');
    };

    var onEditItem = function (e) {
        e.stopPropagation();

        var itemConfigModal = $('#am-item-configuration-modal');

        setItemConfigModalData({
            'xtype': $(this).closest('.am-item').attr('data-xtype'),
            'config': $(this).closest('.am-item').attr('data-config')
        });
        itemConfigModal.data('amData', {
            'for': 'item',
            'action': 'edit',
            'method': 'editItem',
            'item': $(this).closest('.am-item')
        });
        itemConfigModal.modal('show');
    };

    var editItem = function (amData) {
        var preparedItemJson = prepareItemJson(amData),
            tempDiv = $('<div/>');

        $.each(preparedItemJson, function (index, item) {
            tempDiv.append(getHTMLFromJSON([item]));
        });

        amData.item.replaceWith(tempDiv.children());
        menu.trigger('update');
    };

    var deleteItem = function (e) {
        e.stopPropagation();

        if (confirm('Are you sure?')) {
            $(this).closest('.am-item').effect('highlight', {color: '#FFA8A8'}, 500, function () {
                $(this).remove();
            });
            menu.trigger('update');
        }
    };
})(jQuery);