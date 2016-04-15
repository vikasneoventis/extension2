/**
 * Created by: IWD Agency "iwdagency.com"
 * Developer: Andrew Chornij "iwd.andrew@gmail.com"
 * Date: 16.11.2015
 */
define([
        'jquery',
        'Magento_Ui/js/modal/modal',
        'jquery/ui',
        'jquery/validate'
    ],
    function ($, modal) {
        'use strict';
        $.widget('mage.iwdNewsletterPopup', {
            popup: null,
            init: function () {
                var self = this;
                $('.iwd-newsletterpopup-main-wrapper').hide();
                if (typeof(iwdNewspopupConfig) != "undefined") {
                    var config = $.parseJSON(iwdNewspopupConfig);
                } else {
                    return;
                }
                this.enableFooterLink();
                this.sendForm();
                this.parsePopupResponse();
                this.initModal();
                setTimeout(function () {
                    self.showModal();
                }, config.loadDelay * 1000);
                this.showPopupWithFooter();
                this.showPopupInCenter();
            },

            enableFooterLink: function () {
                if (typeof(iwdNewspopupConfig) != "undefined") {
                    var config = $.parseJSON(iwdNewspopupConfig);
                } else {
                    console.log("IWD NewsletterPopup Error! Sorry ;(");
                    return false;
                }

                if (config.enableExtension == '1' && config.enableLinkInFrontend == '1') {
                    var pathForInsert = config.cssPathForLinks;
                    var linkName = config.footerLinkText;
                    $("<a  class='iwd-newsletterpopup-footer-link'>" + linkName + "</a>").appendTo($(pathForInsert));
                }
            },

            initModal: function () {
                var self = this;
                var modalContent = $("#iwd-newsletterpopup-wrapper");
                this.popup = modalContent.modal({
                    autoOpen: false,
                    type: 'popup',
                    modalClass: 'iwd-newsletter-wrapper',
                    title: '',
                    buttons: [{
                        class: "iwd-hidden-button-for-popup",
                        text: 'Start Shopping',
                        click: function () {
                            this.closeModal();

                        }
                    }],
                    clickableOverlay: true,
                    innerScroll: true
                });
            },

            reopenModal: function () {
                $("#iwd-newsletterpopup-wrapper").modal('openModal');
            },

            closeModal: function() {
                $("#iwd-newsletterpopup-wrapper").modal('closeModal');
            },

            showModal: function () {
                if (this.readCookie('IWD_NewsletterPopup')) {
                    $('.iwd-newsletterpopup-main-wrapper').addClass('iwd-newsletter-hidden');
                } else {
                    $("#iwd-newsletterpopup-wrapper").modal('openModal');
                    this.createCookie('IWD_NewsletterPopup', 1);
                }
            },

            sendForm: function () {
                $('.iwd-newsletter-success_title').hide();
                $('.iwd-newsletter-error_title').hide();
                $('.iwd-newsletter-footer-response').hide();
                $('.iwd-newsletter-send-button').on('click', function (e) {
                    e.preventDefault();
                    var email = $('#iwd-newsletterpopup-email-field').val();

                    var validator = $(".iwd-newseletter-form").validate();
                    var status = validator.form();
                    if (!status) {
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: $(this).attr('data-post'),
                        data: {email: email},
                        showLoader: true
                    }).done(function (response) {
                        if (typeof(response.message != "undefined")) {
                            $('.iwd-newsletterpopup-main-title').hide();
                            $('.iwd-newsletter-content-wrapper').hide();
                            $('.iwd-newsletter-main-footer').hide();
                            $('.iwd-newsletter-footer-response').show();
                            if (response.error) {
                                $('.iwd-newsletter-error_title').show();
                            } else {
                                $('.iwd-newsletter-success_title').show();
                            }
                            $('.iwd-newsletter-content-response').append(response.message);
                        }
                    });


                });
            },

            parsePopupResponse: function () {
                var self = this;
                $('.iwd-newsletter-go-back').on('click', function () {
                    $('.iwd-newsletterpopup-main-title').show();
                    $('.iwd-newsletter-content-wrapper').show();
                    $('.iwd-newsletter-main-footer').show();
                    $('.iwd-newsletter-footer-response').hide();
                    $('.iwd-newsletter-success_title').hide();
                    $('.iwd-newsletter-content-response').hide();
                    $('.iwd-newsletter-error_title').hide();
                });
                $('.iwd-newsletter-start-shopping').on('click', function () {
                    self.closeModal();
                    $('.iwd-newsletterpopup-main-title').show();
                    $('.iwd-newsletter-content-wrapper').show();
                    $('.iwd-newsletter-main-footer').show();
                    $('.iwd-newsletter-footer-response').hide();
                    $('.iwd-newsletter-success_title').hide();
                    $('.iwd-newsletter-content-response').hide();
                    $('.iwd-newsletter-error_title').hide();
                    //$('.iwd-newsletter-wrapper').hide();
                    //$('.modals-wrapper').hide();

                });
            },

            createCookie: function (name, value, days) {
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    var expires = "; expires=" + date.toGMTString();
                }
                else var expires = "";
                document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
            },

            readCookie: function (name) {
                var nameEQ = escape(name) + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length, c.length));
                }
                return null;
            },

            showPopupWithFooter: function () {
                var self = this;
                $('.iwd-newsletterpopup-footer-link').click(function (element) {
                    element.preventDefault();
                    self.reopenModal();
                    $('.iwd-newsletterpopup-main-title').show();
                    $('.iwd-newsletter-content-wrapper').show();
                    $('.iwd-newsletter-main-footer').show();
                    $('.iwd-newsletter-footer-response').hide();
                    $('.iwd-newsletter-success_title').hide();
                    $('.iwd-newsletter-content-response').hide();
                    $('.iwd-newsletter-error_title').hide();
                });

            },

            showPopupInCenter: function () {
                if (typeof(iwdNewspopupConfig) != "undefined") {
                    var config = $.parseJSON(iwdNewspopupConfig);
                    var position = config.topPosition;
                } else {
                    console.log("IWD NewsletterPopup Error! Sorry ;(");
                    return false;
                }

                if (position != 'undefined' && position.toLowerCase() === 'auto') {
                    var modalBlock = $('.iwd-newsletter-wrapper').children('.modal-inner-wrap');
                    var heightPopup = modalBlock.outerHeight();
                    var halfMargin = (heightPopup / 2) * -1;
                    modalBlock.css({"margin-top": halfMargin, "margin-bottom": "0", "top": "50%"});
                }else{
                    console.log("IWD NewsletterPopup Error! Sorry ;(");
                    return false;
                }
            }

        });

        return $.mage.iwdNewsletterPopup;

    });