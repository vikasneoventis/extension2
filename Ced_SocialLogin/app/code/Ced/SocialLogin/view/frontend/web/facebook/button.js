window.fbAsyncInit = function() {
    FB.init({
        appId      : ced_sociallogin.appid,
        xfbml      : true,
        version    : 'v2.1'
    });
};

function ced_sociallogin_signin_callback(response) {
    if (response.authResponse) {
        // User accepted

        // Successful login, do ajax request
        jQuery.ajax({
            type: 'POST',
            url:  ced_sociallogin.ajaxurl,
            data: {
                // CSRF protection
                state:  ced_sociallogin.state,

                // Short lived token
                access_token: response.authResponse.accessToken,

                // When this token expires
                expires_in: response.authResponse.expiresIn
            },
            dataType: 'json',
            success: function(data) {
                window.location.replace(data.redirect);
            }
        });
    } else {
        // User canceled - This currently doesn't work due to Facebook API issue
        console.log('User cancelled login or did not fully authorize.');
    }
}