if (typeof fbAppID === 'undefined') {
    var fbAppID = '';
}

if (typeof textErrorFBLogin === 'undefined') {
    var textErrorFBLogin = 'Error: Please try again later or login with password';
}

// Facebook Login
window.fbAsyncInit = function() {
    FB.init({
        appId: fbAppID,
        cookie: true, // enable cookies to allow the server to access
        status: true, // the session
        xfbml: false, // parse social plugins on this page
        // oauth: true,
        version: 'v3.2'
    });

    FB.AppEvents.logPageView();

    function statusChangeCallback(response) {
        var button = document.getElementById('fb-auth');

        if (typeof button === 'undefined') {
            // console.log('fb-auth button not found')
            return;
        }

        var userFieldsRequested = {
            fields: 'id,email,first_name,last_name'
        };

        // response.status:
        // connected - user is logged into Facebook and has authenticated the app
        // not_authorized - user is logged into Facebook, but has not authenticted the app
        // unknown - user is not logged into Facebook, so don't know if they've logged into the app
        //          or FB.logout() was called before and therefore, it cannot connect to Facebook

        if (response.status !== 'connected') {
            button.onclick = function(e) {
                e.preventDefault();

                if (this.disabled) return false;

                changeButtonIcon({
                    buttonElement: button,
                    iconClass: 'fa fa-spin fa-circle-o-notch',
                    buttonDisabled: true
                });

                FB.login(function(response) {
                    if (response.authResponse) {
                        FB.api('/me', userFieldsRequested, function(userInfo) {
                            var userInfoComplete = Object.assign({social: 'facebook'}, userInfo, response.authResponse);
                            logInToAccount(userInfoComplete);
                        });
                    } else {
                        // console.log('User cancelled login or did not fully authorize.');
                        changeButtonIcon({
                            buttonElement: button,
                            iconClass: 'fa fa-facebook-square',
                            buttonDisabled: false
                        });
                    }
                }, {
                    scope: 'email,public_profile'
                });
            }
        } else {
            // console.log('user is logged into FB and has authenticated the app')
            button.onclick = function(e) {
                e.preventDefault();

                if (this.disabled) return false;

                changeButtonIcon({
                    buttonElement: button,
                    iconClass: 'fa fa-spin fa-circle-o-notch',
                    buttonDisabled: true
                });

                FB.api('/me', userFieldsRequested, function(userInfo) {
                    var userInfoComplete = Object.assign({social: 'facebook'}, userInfo, response.authResponse);
                    logInToAccount(userInfoComplete);
                });
            }
        }
    }

    // Now that we've initialized the JavaScript SDK, we call FB.getLoginStatus to trigger a call to Facebook
    // to get the login status and call your callback function with the results.
    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });
    // FB.Event.subscribe('auth.authResponseChange', function(response) {
    //     statusChangeCallback(response);
    // });
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// (function() {
//     var e = document.createElement('script');
//     e.async = true;
//     e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
//     document.getElementById('fb-root').appendChild(e);
// }());

function changeButtonIcon(obj) {
    var buttonIcon = obj.buttonElement.children[0];

    obj.buttonElement.disabled = obj.buttonDisabled;

    if (obj.buttonDisabled === true) {
        obj.buttonElement.setAttribute('disabled', true);
    } else {
        obj.buttonElement.removeAttribute('disabled');
    }

    if (buttonIcon !== undefined) {
        buttonIcon.className = obj.iconClass;
    }
}

function logInToAccount(data) {
    // authResponse is included if the status is connected and is made up of the following:
    // accessToken - contains an access token for the person using the app.
    // expiresIn - indicates the UNIX time when the token expires and needs to be renewed.
    // signedRequest - a signed parameter that contains information about the person using the app.
    // userID - the ID of the person using the app.

    var csrf_token = document.getElementById('csrf-token').value;
    var post_data = Object.assign({csrf_token: csrf_token}, data);

    $.ajax({
        url: 'login-social',
        type: 'post',
        data: post_data,
        dataType: 'json',
        beforeSend: function() {
            $('#fb-auth').prop('disabled', true);
            $('.widget .warning').remove();
        },
        complete: function() {
            $('#fb-auth').prop('disabled', false);
            $.colorbox.resize();
        },
        success: function(json) {
            if (json['success']) {
                $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['message'] + '</p></div></div>');
                $('.widget-success').fadeIn(300);

                setTimeout(function() {
                    if (json['redirect']) {
                        location = json['redirect'];
                    } else {
                        location.reload();
                    }
                }, 1200);
            }

            if (json['error']) {
                if ($('.popup-form').length) {
                    $('.popup-form').find('.popup-notification').html('<div class="warning"><p>' + json['error'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
                } else {
                    $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + json['error'] + '</p></div></div>');
                    $('.widget-warning').fadeIn(300);
                }
            }
        },
        error: handleError
    });
}
