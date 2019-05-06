if (typeof textSelect === 'undefined') {
    var textSelect = ' --- Please Select --- ';
}

if (typeof textNone === 'undefined') {
    var textNone = ' --- None --- ';
}

if (typeof textWait === 'undefined') {
    var textWait = 'Please Wait';
}

// replaced all ".on(clickEvent, " with ".clickOrTouch("
$.fn.clickOrTouch = function(selector, handler) {
    this.on("touchend", selector, function(e) {
        handler.call(this, e);
        e.stopPropagation();
        e.preventDefault();
    });
    this.on("click", selector, function(e) {
        handler.call(this, e);
    });
    return this;
};

// String replace
String.prototype.formatUnicorn = String.prototype.formatUnicorn || function() {
    "use strict";
    var str = this.toString();
    if (arguments.length) {
        var t = typeof arguments[0];
        var key;
        var args = ("string" === t || "number" === t)
            ? Array.prototype.slice.call(arguments)
            : arguments[0];

        for (key in args) {
            str = str.replace(new RegExp("\\{" + key + "\\}", "gi"), args[key]);
        }
    }

    return str;
};

// Mobile Detect
var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

var colorboxDefault = {
    rel: $(this).attr('rel'),
    opacity: 0.4,
    transition: "none",
    innerWidth: "100%",
    maxWidth: "100%",
    innerHeight: "80%",
    current: "{current} of {total}",
    previous: "<i class='fa fa-chevron-left'></i>",
    next: "<i class='fa fa-chevron-right'></i>",
    close: "<i class='fa fa-times'></i>",
    onOpen: function() {
        $("#colorbox").css("opacity", 0);
    },
    onComplete: function() {
        $(this).resize();
        $("#colorbox").animate({
            "opacity": 1
        }, 300, 'swing');
    }
};

document.documentElement.className = document.documentElement.className.replace("no-js", "js");

window.setTimeout("fadeNotification();", 2000);

function fadeNotification() {
    $('.notification').fadeOut(300, function() {
        $(this).remove();
    });
}

function scrollToSection(sectionId, offset) {
    $('html, body').animate({
        scrollTop: $(sectionId).offset().top - offset
    }, 300);
}

// Action Buttons
function addToCart(product_id, quantity) {
    if (!product_id) {
        return false;
    }

    var data = 'product_id=' + product_id;

    if (typeof(quantity) !== 'undefined') {
        data += '&quantity=' + quantity;
    }

    $.ajax({
        url: 'cart-add',
        type: 'post',
        data: data,
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, .information, .error').remove();

            if (json['redirect']) {
                location = json['redirect'];
            }

            if (json['success']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['success'] + '</p></div></div>');
            }

            if (json['error']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + json['error'] + '</p></div></div>');
            }

            $('#notification .widget').fadeIn('slow');

            setTimeout(function() {
                $('#notification .bg').fadeOut('slow');

                if (json['success']) {
                    $('#cart-total').html(json['total']);
                    $('#cart .heading').click();
                    scrollToSection('#cart', 0);
                }
            }, 2000);
        }
    });
}

function addToWishList(product_id) {
    $.ajax({
        url: 'savelist-add',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, .information').remove();

            if (json['success']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['success'] + '</p></div></div>');
            }

            $('#notification .widget').fadeIn('slow');

            setTimeout(function() {
                $('#notification .bg').fadeOut('slow');

                if (json['success']) {
                    $('.wishlist-total').html(json['total']);
                }
            }, 2000);
        }
    });
}

function addToCompare(product_id) {
    $.ajax({
        url: 'compare-add',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, .information').remove();

            if (json['error']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + json['error'] + '</p></div></div>');
            }

            if (json['success']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['success'] + '</p></div></div>');
            }

            $('#notification .widget').fadeIn('slow');

            setTimeout(function() {
                $('#notification .bg').fadeOut('slow');

                if (json['redirect']) {
                    location = json['redirect'];
                }

                if (json['success']) {
                    $('.compare-total').html(json['total']);
                    $('.product-compare').addClass('active');

                    if ($('#listing-' + product_id).length) {
                        var html = '<a onclick=\"removeFromCompare(\'' + product_id + '\');\" rel=\"tooltip\" data-placement=\"top\" data-original-title=\"' + json['compare'] + '\"><i class="fa fa-copy"></i></a>';

                        $('#listing-' + product_id + ' footer a:last-child').replaceWith(html);
                        $('#listing-' + product_id).addClass('grid-item-compare');
                    }

                    if ($('#compare').length) {
                        var html = '<a id=\"compare\" onclick=\"removeFromCompare(\'' + product_id + '\');\" class=\"links button_compare\" rel=\"tooltip\" data-placement=\"top\" data-original-title=\"' + json['compare'] + '\"><i class="fa fa-copy"></i>' + json['compare'] + '</a>';

                        $('#compare').replaceWith(html);
                    }
                }
            }, 2000);
        }
    });
}

function removeFromCompare(product_id) {
    $.ajax({
        url: 'compare-remove',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, .information').remove();

            if (json['error']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + json['error'] + '</p></div></div>');
            }

            if (json['success']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['success'] + '</p></div></div>');
            }

            $('#notification .widget').fadeIn('slow');

            setTimeout(function() {
                $('#notification .bg').fadeOut('slow');

                if (json['redirect']) {
                    location = json['redirect'];
                }

                if (json['success']) {
                    $('.compare-total').html(json['total']);

                    if (json['total'].indexOf('(0)') !== -1) {
                        $('.product-compare').removeClass('active');
                    }

                    if ($('#listing-' + product_id).length) {
                        var html = '<a onclick=\"addToCompare(\'' + product_id + '\');\" rel=\"tooltip\" data-placement=\"top\" data-original-title=\"' + json['compare'] + '\"><i class="fa fa-copy"></i></a>';

                        $('#listing-' + product_id + ' footer a:last-child').replaceWith(html);
                        $('#listing-' + product_id).removeClass('grid-item-compare');
                    }

                    if ($('#compare').length) {
                        var html = '<a id=\"compare\" onclick=\"addToCompare(\'' + product_id + '\');\" class=\"links button_compare\" rel=\"tooltip\" data-placement=\"top\" data-original-title=\"' + json['compare'] + '\"><i class="fa fa-copy"></i>' + json['compare'] + '</a>';

                        $('#compare').replaceWith(html);
                    }
                }
            }, 2000);
        }
    });
}

function flagListing(product_id, text_confirm) {
    if (!confirm(text_confirm)) {
        return false;
    }

    $.ajax({
        url: 'flag-listing',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        beforeSend: function() {
            $('#flaglisting').hide().after('<a class="links button-wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i> ' + textWait + '</a>')
        },
        success: function(json) {
            $('.success, .warning, .attention, .information').remove();
            $('#flaglisting').show();
            $('.links.button-wait').hide()

            if (json['success']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['success'] + '</p></div></div>');
                $('.widget-success').fadeIn('slow');
            }

            if (json['error']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + json['error'] + '</p></div></div>');
                $('.widget-warning').fadeIn('slow');
            }
        }
    });
}

// reCaptchas
var reCaptchaSettings = {
    'sitekey': '6Lcy2GEUAAAAAEi3Y7KvnbXA2epWHXowYukhv5-s',
    'theme': 'light'
};

function onloadReCaptcha() {
    $('.recaptcha-box').each(function(i) {
        var container_id = $(this).prop('id');
        var captcha_widget_id;

        if (!$(this).is(':empty')) {
            captcha_widget_id = $(this).next('input[name=\'captcha_widget_id\']').val();
            grecaptcha.reset(captcha_widget_id);
            // console.log('Reset reCaptcha Widget ' + captcha_widget_id + ' in ' + container_id);
        } else {
            captcha_widget_id = grecaptcha.render(container_id, reCaptchaSettings);
            $(this).after('<input type="hidden" name="captcha_widget_id" value="' + captcha_widget_id + '" />');
            // console.log('Loaded reCaptcha Widget ' + captcha_widget_id + ' in ' + container_id);
        }
    });

    //  non-jQuery approach (but not supported by IE)
    // var captchaWidgetIds = [];
    // var captchaContainers = document.getElementsByClassName("recaptcha-box");
    //
    // for (i = 0; i < captchaContainers.length; i++) {
    // 	var container_id = captchaContainers.item(i).id;
    // 	captchaWidgetIds[i] = grecaptcha.render(container_id, reCaptchaSettings);
    // }
};

// if ($(window).width() > 698) {
    // Floating Menu and To-Top
    setTimeout(function() {
        $(function() {
            var previousScroll = 0;
            var top = $('.container-header').offset().top;
            var bottomFooterHeight = $('.container-footer-bottom').height();

            $(window).on('scroll', function(e) {
                var scrollTop = $(this).scrollTop();
                var scrollBottom = $(document).height() - $(window).height() - $(window).scrollTop();

                if (scrollTop >= top + 60 && scrollTop <= previousScroll) {
                    $('.container-header').addClass('floating-menu');
                } else {
                    $('.container-header').removeClass('floating-menu');
                }

                previousScroll = scrollTop;

                if (scrollTop > top + 60 && scrollBottom >= bottomFooterHeight) {
                    $('#top').addClass('show-to-top');
                } else {
                    $('#top').removeClass('show-to-top');
                }
            });
        });
    }, 1000);
// }

// To Top
$('#top').clickOrTouch(null, function(e) {
    e.preventDefault();
    $('html, body').animate({
        scrollTop: 0
    }, 300);
});

// Logout Popup
$('.container-header-top #hboxaccount a[href$="logout"]').clickOrTouch(null, logoutUser);

if (!isMobile.any() && $(window).width() > 446) {
    // Register Popup
    $('.container-header-top .header .mini-info a[href$="join"], .content-page a[href$="join"]').clickOrTouch(null, loadPopupRegisterForm);
    // Login Popup
    $('.container-header-top .header #hboxaccount a[href$="login"], .content-page a[href$="login"]').clickOrTouch(null, loadPopupLoginForm);
}

// Contact Popup
$('.content-page a[href*="contact_id"]').clickOrTouch(null, loadPopupContactForm);

function logoutUser(e) {
    e.preventDefault();
    e.stopPropagation();

    var url = $(this).attr('href') + '?popup=true';

    $.ajax({
        url: url,
        dataType: 'json',
        success: function(json) {
            if (json['status']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['message'] + '</p></div></div>');
                $('.widget-success').fadeIn(600);
            } else {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + json['message'] + '</p></div></div>');
                $('.widget-warning').fadeIn(600);
            }

            setTimeout(function() {
                if (json['redirect']) {
                    location = json['redirect'];
                } else {
                    location.reload();
                }
            }, 900);
        },
        error: handleError
    });
}

function loadPopupLoginForm(e) {
    e.preventDefault();

    // if (!$('#login-popup').length) {
    //     return false;
    // }

    if (!$('#popup-login-form').length) {
        $.ajax({
            url: 'popup-login',
            dataType: 'json',
            complete: popupLoginFormLoaded,
            success: function(json) {
                if (json['redirect']) {
                    location = json['redirect'];
                }

                $('#login-popup').html(json['html']);

                setTimeout(function() {
                    $("#login-popup [rel=tooltip]").tooltip().off("focusin focusout");
                }, 1000);
            },
            error: handleError
        });
    } else {
        popupLoginFormLoaded();
    }
}

function popupLoginFormLoaded() {
    var $form = $('#popup-login-form');

    if (!$form.length) {
        return false;
    }

    onloadReCaptcha();

    // clear previous event handlers, in case user clicks button more than once on a page
    $form.off();

    $form.on('submit', function(e) {
        e.preventDefault();

        var url = $(this).attr('action');
        var data = $(this).serialize();

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                $form.find('input[type="submit"]').prop('disabled', true);
                $form.find('.button-login').hide();
                $form.find('.button-login').after('<a class="button button-wait button_alt bigger icon fullwidth"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i> ' + textWait + '</a>');
                $form.find('.warning').remove();
            },
            complete: function() {
                $form.find('input[type="submit"]').prop('disabled', false);
                $form.find('.button-login').show();
                $form.find('.button-wait').remove();
                $.colorbox.resize();
            },
            success: function(json) {
                if (json['status']) {
                    // $form.find('.content').prepend('<div class="success"><p>' + json['message'] + '</p><span class="icon"><i class="fa fa-check"></i></span></div>');
                    $("#colorbox, #cboxOverlay").animate({
                        "opacity": 0
                    }, 300, 'swing');
                    $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['message'] + '</p></div></div>');
                    $('.widget-success').fadeIn(600);

                    setTimeout(function() {
                        $.colorbox.close();

                        if (json['redirect']) {
                            location = json['redirect'];
                        } else {
                            location.reload();
                        }
                    }, 900);
                } else {
                    $form.find('.popup-notification').html('<div class="warning"><p class="error"><span><i class="fa fa-exclamation-triangle"></i> ' + json['message'] + '</span></p></div>');
                }

                if (json['captcha_widget_id'].length) {
                    grecaptcha.reset(json['captcha_widget_id']);
                }

                $('input[name=\'csrf_token\']').val(json['csrf_token']);
            },
            error: handleError
        })
    });

    $.colorbox({
        inline: true,
        href: '#login-popup',
        innerWidth: '450px',
        maxWidth: '100%',
        opacity: 0.4,
        transition: 'none',
        scrolling: true,
        close: "<i class='fa fa-times'></i>",
        onLoad: function() {
            // $('#cboxClose').remove();
        },
        onOpen: function() {
            $("#colorbox").css("opacity", 0);
        },
        onComplete: function() {
            $("#colorbox").animate({
                "opacity": 1
            }, 300, 'swing');

            $.getScript('catalog/view/root/javascript/login.js').fail(handleError);
        },
        onCleanup: function() {
            $form.find('.warning').remove();
        }
    });
}

function loadPopupRegisterForm(e) {
    e.preventDefault();

    var $registerPopup = $('#register-popup');
    var $registerPopupForm = $('#popup-register-form');

    // if (!$registerPopup.length) {
    //     return false;
    // }

    if (!$registerPopupForm.length) {
        $.ajax({
            url: 'popup-register',
            dataType: 'json',
            complete: popupRegisterFormLoaded,
            success: function(json) {
                if (json['redirect']) {
                    location = json['redirect'];
                }

                $('#register-popup').html(json['html']);

                setTimeout(function() {
                    $("#register-popup [rel=tooltip]").tooltip().off("focusin focusout");
                    $("#register-popup [rel=popover]").popover();
                }, 1000);
            },
            error: handleError
        });
    } else {
        popupRegisterFormLoaded();
    }
}

function popupRegisterFormLoaded() {
    var $form = $('#popup-register-form');

    if (!$form.length) {
        return false;
    }

    onloadReCaptcha();

    // clear previous event handlers, in case user clicks button more than once on a page
    $form.off();

    $form.on('submit', function(e) {
        e.preventDefault();

        var url = $(this).attr('action');
        var data = $(this).serialize();

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                $form.find('input[type="submit"]').prop('disabled', true);
                $form.find('.button-register').hide();
                $form.find('.button-register').after('<a class="button button-wait button_alt icon fullwidth"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i> ' + textWait + '</a>');
                $form.find('.warning').remove();
            },
            complete: function() {
                $form.find('input[type="submit"]').prop('disabled', false);
                $form.find('.button-register').show();
                $form.find('.button-wait').remove();
                $.colorbox.resize();
            },
            success: function(json) {
                if (json['status']) {
                    $("#colorbox, #cboxOverlay").animate({
                        "opacity": 0
                    }, 300, 'swing');
                    $('#notification').html('<div class="bg"><span class="close close-refresh"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['message'] + '</p></div></div>');
                    $('.widget-success').fadeIn(600);

                    $('#notification').clickOrTouch('.bg,.close-refresh', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        setTimeout(function() {
                            $.colorbox.close();

                            if (json['redirect']) {
                                location = json['redirect'];
                            } else {
                                location.reload();
                            }
                        }, 600);
                    });
                } else if (json['error']) {
                    var errors = json['error'];
                    var error_msgs = '';

                    var arr = $.map(errors, function(msg, id) {
                        error_msgs += '<span><i class="fa fa-exclamation-triangle"></i> ' + msg + '</span>';
                    });

                    $form.find('.popup-notification').html('<div class="warning"><p class="error">' + error_msgs + '</p></div>');
                }

                if (json['captcha_widget_id'].length) {
                    grecaptcha.reset(json['captcha_widget_id']);
                }

                $('input[name=\'csrf_token\']').val(json['csrf_token']);
            },
            error: handleError
        })
    });

    $.colorbox({
        inline: true,
        href: '#register-popup',
        top: '10%',
        innerWidth: '450px',
        maxWidth: '100%',
        opacity: 0.4,
        transition: 'none',
        scrolling: true,
        close: "<i class='fa fa-times'></i>",
        onLoad: function() {
            // $('#cboxClose').remove();
        },
        onOpen: function() {
            $("#colorbox").css("opacity", 0);
        },
        onComplete: function() {
            $("#colorbox").animate({
                "opacity": 1
            }, 300, 'swing');

            $.getScript('catalog/view/root/javascript/login.js').fail(handleError);
        },
        onCleanup: function() {
            $form.find('.warning').remove();
        }
    });
}

function loadPopupContactForm(e) {
    e.preventDefault();

    if (!$('#contact-popup').length) {
        return false;
    }

    if (!$('#popup-contact-form').length) {
        var data = $(this).attr('href').split('?')[1]; // query strim params

        loadPopupContactFormAjax(data);
    } else {
        popupContactFormLoaded();
    }
}

function messageProfile(customer_id, listing_id) {
    if (!$('#contact-popup').length) {
        return false;
    }

    var data = 'contact_id=' + customer_id + '&listing_id=' + listing_id;

    loadPopupContactFormAjax(data);
}

function loadPopupContactFormAjax(data) {
    if (!data.length) {
        return false;
    }

    $.ajax({
        url: 'popup-contact',
        data: data,
        beforeSend: function() {
            $('#contact-popup').html();
        },
        complete: popupContactFormLoaded,
        success: function(html) {
            $('#contact-popup').html(html);

            setTimeout(function() {
                $("#contact-popup [rel=tooltip]").tooltip().off("focusin focusout");
            }, 1000);
        },
        error: handleError
    });
}

function popupContactFormLoaded() {
    var $form = $('#popup-contact-form');

    if (!$form.length) {
        return false;
    }

    onloadReCaptcha();

    // clear previous event handlers, in case user clicks button more than once on a page
    $form.off();

    $form.on('submit', function(e) {
        e.preventDefault();

        var url = $(this).attr('action');
        var data = $(this).serialize();

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                $form.find('input[type="submit"]').prop('disabled', true);
                $form.find('.button-contact').hide();
                $form.find('.button-contact').after('<a class="button button-wait button_alt icon fullwidth"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i> ' + textWait + '</a>');
                $form.find('.warning').remove();
            },
            complete: function() {
                $form.find('input[type="submit"]').prop('disabled', false);
                $form.find('.button-contact').show();
                $form.find('.button-wait').remove();
                $.colorbox.resize();
            },
            success: function(json) {
                if (json['status']) {
                    //$form.find('.information').before('<div class="success"><p>' + json['message'] + '</p><span class="icon"><i class="fa fa-check"></i></span></div>');
                    $("#colorbox, #cboxOverlay").animate({
                        "opacity": 0
                    }, 300, 'swing');
                    $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['message'] + '</p></div></div>');
                    $('.widget-success').fadeIn(600);

                    setTimeout(function() {
                        $.colorbox.close();
                        $('#notification > .bg').fadeOut(600, function() {
                            $(this).remove();
                        });
                        // location.reload();
                    }, 3600);
                } else if (json['error']) {
                    var errors = json['error'];
                    var error_msgs = '';

                    var arr = $.map(errors, function(msg, id) {
                        error_msgs += '<span><i class="fa fa-exclamation-triangle"></i> ' + msg + '</span>';
                    });

                    $form.find('.information').after('<div class="warning"><p class="error">' + error_msgs + '</p></div>');
                }

                if (json['captcha_widget_id'].length) {
                    grecaptcha.reset(json['captcha_widget_id']);
                }

                $('input[name=\'csrf_token\']').val(json['csrf_token']);
            },
            error: handleError
        })
    });

    // open loaded form in colorbox
    $.colorbox({
        inline: true,
        href: '#contact-popup',
        top: '10%',
        innerWidth: '560px',
        maxWidth: '100%',
        opacity: 0.4,
        transition: 'none',
        scrolling: true,
        close: "<i class='fa fa-times'></i>",
        onLoad: function() {
            // $('#cboxClose').remove();
        },
        onOpen: function() {
            $("#colorbox").css("opacity", 0);
        },
        onComplete: function() {
            $("#colorbox").animate({
                "opacity": 1
            }, 300, 'swing');
        },
        onCleanup: function() {
            $form.find('.warning').remove();
        }
    });
}

// Mobile Menu
$('.panelbody').clickOrTouch('#closedbody', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $('body').toggleClass('movebody');
});

$('.responsive-menu-inner').clickOrTouch('#mobile-menu', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $('body').toggleClass('movebody');
});

// Colorbox
$('body').clickOrTouch('.colorbox', function() {
    $('.colorbox').colorbox(colorboxDefault);
});

// Tooltip && Popover
if (!isMobile.any() && $(window).width() > 698) {
    $("[rel=tooltip]").tooltip().off("focusin focusout");
} else {
    $("#form [rel=tooltip]").tooltip().off("focusin focusout");
}

$("[rel=popover]").popover();

// Header Search
function redirectToSearch(searchTerm) {
    var url = $('base').attr('href') + 'search';

    if (searchTerm) {
        url += '?s=' + encodeURIComponent(searchTerm);
    }

    location = url;
}

function toggleSearchHeader(e) {
    e.preventDefault();
    $('#search,#menu,#responsive-menu').toggleClass('hidden');
    $('#search input[type="text"]').focus();
    $('html, body').animate({
        scrollTop: 0
    }, 300);
}

$('#page-header').clickOrTouch('#search-link,.menu-nav-search', toggleSearchHeader);
$('.header .middle-header #search').clickOrTouch('.button-search-close i', toggleSearchHeader);

$('.header').clickOrTouch('.button-search', function(e) {
    var search = $(this).siblings('input[name^=\'search\']').val();
    redirectToSearch(search);
});

$('.header').on('keydown', 'input[name=\'search\']', function(e) {
    if (e.keyCode == 13) {
        var search = $(this).val();
        redirectToSearch(search);
    }
});

// Header Menu
$('#menu .menuchildren').each(function(index, element) {
    var menu = $('#menu').offset();
    var dropdown = $(this).parent().offset();

    i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

    if (i > 0) {
        $(this).css('margin-left', '-' + (i + 0) + 'px');
    }
});

// Ajax Cart
$('#cart').clickOrTouch('.heading', function() {
    $('#cart').load('cart-module #cart > *');
});

// Cart Remove
$('#cart').clickOrTouch('.cart-remove', function(e) {
    e.preventDefault();

    var cart_key = $(this).attr('data-cart-key');
    var path_name = String(document.location.pathname);

    if (path_name.indexOf('cart') !== -1 || path_name.indexOf('checkout') !== -1) {
        location = 'cart?remove=' + cart_key;
    } else {
        $('#cart').load('cart-module?remove=' + cart_key + ' #cart > *');
    }
});

// Top Header
function hboxCloser(e) {
    var $thisElement = $(e.target);

    // if a link is clicked, follow it
    if ($thisElement.attr('href') !== undefined) {
        location = $thisElement.attr('href');
    }

    // if the click is not inside the drop-down/sub-menu, hide it
    if (!$thisElement.closest('.hbox').length) {
        $('body').off('click touchstart');
        $('.hbox.has-menu, #cart').removeClass('active');
    }
}

function hboxOpener(e) {
    var $thisElement = $(e.target);

    // close all other separate instances, unless drilling down into multi-level menu (i.e. multiple .hbox parents)
    if ($(this).parents('.hbox').length <= 1) {
        $('.hbox.has-menu, #cart').removeClass('active');
    }

    // for multi-level menus with sub-menu structure li.has-menu > a.heading + ul.list-children (e.g. main menu, category module)
    if ($thisElement.attr('href') !== undefined) {
        $thisElement.clickOrTouch(null, function(e) {
            location = $thisElement.attr('href');
        });
    }

    // adding .active class makes nested ul.list-children visible
    var $thisHBox = $(this).parent('.hbox');
    $thisHBox.addClass('active');

    // add event listener to check for click-off (i.e. outside target)
    $('body').clickOrTouch(null, hboxCloser);
}

$('#page-header .hbox.has-menu, #cart').clickOrTouch('.heading', hboxOpener);

if (!isMobile.any() && $(window).width() > 979) {
    $('#sidebar .hbox.has-menu').clickOrTouch('.heading', hboxOpener);
}

// Currency
$('#currency').clickOrTouch('a', function(e) {
    e.preventDefault();
    var currency_code = $(this).attr('data-code');
    $('input[name=\'currency_code\']').val(currency_code);
    $(this).closest('form').trigger('submit');
});

// Tabs
$('#tabs a').tabs();

// Language
$('#language').clickOrTouch('a', function(e) {
    e.preventDefault();
    var language_code = $(this).attr('data-code');
    $('input[name=\'language_code\']').val(language_code);
    $(this).closest('form').trigger('submit');
});

// Notifications
// $('.success,.warning,.attention,.information,.error,.notification,#notification').clickOrTouch('.close', function() {
$(document).clickOrTouch('.close', function(e) {
    e.preventDefault();

    $(this).parent().fadeOut(300, function() {
        $(this).remove();
    });
});

// All Form Submissions
$(document).clickOrTouch('#form-submit', function(e) {
    e.preventDefault();
    $('#form').submit();
})

$('form .button-submit').clickOrTouch(null, function(e) {
    e.preventDefault();
    $(this).closest('form').trigger('submit');
})

$('form').on('keydown', 'input', function(e) {
    if (e.keyCode == 13) {
        $(this).closest('form').submit();
    }
});

$('form').on('submit', function() {
    $(this).find('input[type="submit"]').prop('disabled', true);
    $(this).find('.button-submit > i').attr('class', 'fa fa-spin fa-circle-o-notch icon-spin');
    $(this).find('.button-submit').removeClass('button-submit').addClass('button-wait icon');
});

// Select All
$('#check-select-all').clickOrTouch(null, function(e) {
    $('input[name*=\'selected\']').attr('checked', this.checked);
});

// Sticky
$('.sticky-info').each(function() {
    $thisSticky = $(this);

    $thisSticky.clickOrTouch('.sticky-icon', function() {
        $thisSticky.addClass('sticky-open');
        $thisSticky.siblings('.sticky-info').removeClass('sticky-open');
    });

    $thisSticky.clickOrTouch('.sticky-closed', function() {
        $(this).addClass('sticky-closed-gost');
        $thisSticky.removeClass('sticky-open');
    });

    $thisSticky.on('mouseover', '.sticky-icon', function() {
        $thisSticky.find('.sticky-closed').removeClass('sticky-closed-gost');
        $thisSticky.siblings('.sticky-info').removeClass('sticky-open');
    });
});

function handleError(xhr, ajaxOptions, thrownError) {
    var msg = "Sorry, but there was an error: ";
    $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + msg + xhr.status + " " + xhr.statusText + '</p></div></div>');
    $('.widget-warning').fadeIn('slow');
    //console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
}
