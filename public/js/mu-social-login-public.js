// for google login
window.onLoad = function() {
    gapi.load('auth2', function () {
        gapi.auth2.init();
    });
};

(function(window, document, $, undefined){

	'use strict';

    window.MuSocialLogin = {};

    MuSocialLogin.init = function () {
        window.fbAsyncInit = function() {
            FB.init({
                appId      : msl.fb_app_id,
                cookie     : true,  // enable cookies to allow the server to access
                xfbml      : true,  // parse social plugins on this page
                version    : 'v3.3'
            });

        };

        // Load the SDK asynchronously
        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    };

    // Send data to Server and show result on UI
    MuSocialLogin.sendToServer = function(data, $form_obj, $redirect_to) {
        $form_obj.find('.msl_error').remove();
        console.log(data);
        console.log(msl.ajaxurl);
        $.ajax({
            data: data,
            global: false,
            type: "POST",
            url: msl.ajaxurl,
            success: function (data) {

                if (data && data.success) {
                    console.log('succes:');
                    var loc_url = '';
                    if( data.redirect && data.redirect.length ) {
                        loc_url = data.redirect;
                    } else if ( $redirect_to.length ) {
                        loc_url = $redirect_to;
                    } else {
                        loc_url = msl.site_url;
                    }
                    loc_url = MuSocialLogin.add_query_arg(loc_url, 'mt', data.method);
                    location.href = loc_url;
                } else if (data && data.error) {
                    window.msl_button.removeClass('loading');
                    if (window.msl_button.prop('disabled')===true) {
                        window.msl_button.prop('disabled', false);
                    }
                    alert(data.error);
                    $form_obj.append('<p class="msl_error">' + data.error + '</p>');
                }
            },
            error: function(xhr, exception){
                // alert('錯誤碼('+xhr.status+')，請稍候再試。');
                window.msl_button.removeClass('loading');
                console.log(xhr.responseText);
                $form_obj.append('<p class="msl_error">' + '錯誤碼('+xhr.status+')，請通知客服人員，或稍候再試。' + '</p>');
            }
        });
	};
	
    // Google Login Btn
    $( document ).on( 'click', '.btn_google_login', function( e ) {
        $(this).prop('disabled', true);
		e.preventDefault();
		window.msl_button = $(this);
		window.msl_button.addClass('loading');
		window.msl_button.parents('div.container-fluid').addClass('d-none');
		$('.fa-spinner ').removeClass('d-none');
		gapi.load('auth2', MuSocialLogin.gapi_initSigninV2 );
    });

    MuSocialLogin.add_query_arg = function (uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }else {
            return uri + separator + key + "=" + value;
        }
    }

    MuSocialLogin.gapi_initSigninV2 = function() {
        window.obj_auth2 = gapi.auth2.getAuthInstance();
        window.obj_auth2.isSignedIn.listen(MuSocialLogin.gapi_signinChanged);
        window.obj_auth2.currentUser.listen(MuSocialLogin.gapi_userChanged);
        window.obj_auth2.signIn();
    }

    MuSocialLogin.gapi_userChanged = function(user) {
        var $google_user_id = user.getBasicProfile().getId();
        var $google_token = user.getAuthResponse().id_token,
            $form_obj       = window.msl_button.parents('form') || false,
            $redirect_to    = $form_obj.find('input[name="redirect_to"]').val() || window.msl_button.data('redirect'),
            $data = {
                action: "msl_google_login",
                google_token: $google_token,
                google_user_id: $google_user_id,
                security: window.msl_button.data('google_nonce')
            };

        MuSocialLogin.sendToServer($data, $form_obj, $redirect_to);
        $('.fa-spinner ').addClass('d-none');
        // $('.btn_google_login').prop('disabled', false);
    };
    MuSocialLogin.gapi_signinChanged = function() {
        //console.log('the user must be signed in to print this');
    };

    // FB Login Btn
    $( document ).on( 'click', '.btn_fb_login', function( e ) {
        $(this).prop('disabled', true);
        e.preventDefault();
        window.msl_button    = $(this);
        window.msl_button.addClass('loading');
        window.msl_button.parents('div.container-fluid').addClass('d-none');
        $('.fa-spinner ').removeClass('d-none');
        try {
            FB.login(MuSocialLogin.fb_handleResponse, {
                scope: msl.fb_scopes,
                return_scopes: true,
                auth_type: 'rerequest'
            });
        } catch (err) {
            alert('Facebook Init is not loaded. Check that you are not running any blocking software or that you have tracking protection turned off if you use Firefox');
        }
	});

    MuSocialLogin.fb_handleResponse = function( response ) {
        var $form_obj       = window.msl_button.parents('form') || false,
            $redirect_to    = $form_obj.find('input[name="redirect_to"]').val() || window.msl_button.data('redirect');

        if (response.status == 'connected') {
            var fb_response = response;
            var $data = {
                action: "msl_facebook_login",
                fb_response: fb_response,
                security: window.msl_button.data('fb_nonce')
            };
            MuSocialLogin.sendToServer($data, $form_obj, $redirect_to);
        } else {
            window.msl_button.removeClass('loading');
            window.msl_button.parents('div.container-fluid').removeClass('d-none');
            $('.fa-spinner ').addClass('d-none');
            $('.btn_fb_login').prop('disabled', false);
            if( navigator.userAgent.match('CriOS') )
                window.open('https://www.facebook.com/dialog/oauth?client_id=' + msl.fb_app_id + '&redirect_uri=' + document.location.href + '&scope=email,public_profile', '', null);
        }
    };

    $(document).on( 'ready', MuSocialLogin.init );

})(window, document, jQuery);

