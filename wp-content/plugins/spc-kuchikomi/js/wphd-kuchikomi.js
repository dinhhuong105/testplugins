var max_upload_file_size = 1; /* 2MB */
var megabyte_value = 1024000;
jQuery(document).ready(function ($) {
    /* login area */
    // Perform AJAX login on form submit
    $('#hd-login-form .login-form form').on('submit', function(e) {
    	var $this = $(this);
    	var ok = true;

    	/* validation login form */
    	var username = $this.find('#username').val();
    	var password = $this.find('#password').val();
    	var remember = $this.find('#rememberme').val();
    	var security = wphd_kuchikomi_object.security;

    	/* remove error message */
    	$('.hd-login-error').remove();
    	$('#hd-login-form .login-form .login-group .error-message').remove();

    	/* validate username */
    	if (username == '') {
    		$this.find('#username').addClass('error');
    		$this.find('.login-name').append('<span class="error-message">'+ wphd_kuchikomi_object.field_required +'</span>');
    		ok = false;
    	}

    	/* validate password */
    	if (password == '') {
    		$this.find('#password').addClass('error');
    		$this.find('.login-pass').append('<span class="error-message">'+ wphd_kuchikomi_object.field_required +'</span>');
    		ok = false;
    	}

    	/* show error message */
    	if (ok === false) {
    		$('.hd-login').prepend('<div class="hd-login-error">'+ wphd_kuchikomi_object.login_wrong +'</div>');
    		return false;
    	}

    	/* login via ajax */
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: wphd_kuchikomi_object.ajaxurl,
            data: {
                'action': 'wphd_ajaxlogin',
                'username': username,
                'password': password,
                'remember': remember,
                'security': security
            },
            success: function(data) {
                if (data.loggedin == true) {
                    document.location.href = wphd_kuchikomi_object.redirecturl;
                } else {
                	$('.hd-login-error').remove();
                	$('.hd-login').prepend('<div class="hd-login-error">'+ wphd_kuchikomi_object.login_wrong +'</div>');
                }
            }
        });
        e.preventDefault();
    });

    /* remove error message when typing on textbox */
    $('#hd-login-form .login-form #username, #hd-login-form .login-form #password').on('blur', function() {
    	var $this = $(this);
    	$this.removeClass('error');
    	$this.parent().find('.error-message').remove();
    });


    /* register area */
    /* load image by base64 encode*/
    jQuery("#user-register-picture").change(function(){
        readImageURL(this);
    });

    jQuery('#register-blog-form #wp-next').click(function(e) {
        /* validation register form */
        var ok = true;
        var $register_blog_form = jQuery('#register-blog-form');
        var picture = $register_blog_form.find('.avatar-preview #preview-image').attr('src');
        var nickname = $register_blog_form.find('#user-nickname').val();
        var email = $register_blog_form.find('#user-email').val();
        var password = $register_blog_form.find('#user-pass').val();
        var gender = $register_blog_form.find('.gender-group input[name=gender]:checked').val();
        var birthday_y = $register_blog_form.find('.register-birthday select.birthday_y :selected').val();
        var birthday_m = $register_blog_form.find('.register-birthday select.birthday_m :selected').val();
        var birthday_d = $register_blog_form.find('.register-birthday select.birthday_d :selected').val();
        var num_of_child = $register_blog_form.find('.register-num-of-child select :selected').val();

        jQuery('p.error-message').remove();
        $register_blog_form.find('.field-error').removeClass('field-error');

        /* validate nickname */
        if (typeof nickname === 'undefined' || nickname == '') {
            ok = false;
            $register_blog_form.find('.register-nickname #user-nickname').addClass('field-error');
            fieldRequireMessage($register_blog_form.find('.register-nickname'));
        }

        if (nickname != '' && (nickname.length < 6 || nickname.length > 12)) {
            ok = false;
            $register_blog_form.find('.register-nickname #user-nickname').addClass('field-error');
            $register_blog_form.find('.register-nickname').append('<p class="error-message">Nickname lengths from 6 to 12 characters, Japanese characters, numerals</p>');
        }

        /* validate email */
        if (typeof email === 'undefined' || email == '') {
            ok = false;
            $register_blog_form.find('.register-email #user-email').addClass('field-error');
            fieldRequireMessage($register_blog_form.find('.register-email'));   
        }

        var reg_email = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (reg_email.test(email) == false) {
            ok = false;
            $register_blog_form.find('.register-email .error-message').remove();
            $register_blog_form.find('.register-email #user-email').addClass('field-error');
            $register_blog_form.find('.register-email').append('<p class="error-message">Email is invalid</p>');
        }

        if (email != '') {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'check_existed_email_front',
                    email: email,
                },
                cache: false,
                dataType: "json",
                async: false,
                success: function (res) {
                    if (res.result == true) {
                        ok = false;
                        $register_blog_form.find('.register-email #user-email').addClass('field-error');
                        $register_blog_form.find('.register-email').append('<p class="error-message">Email already exists</p>');
                    }
                }
            });
        }

        /* validate password */
        if (typeof password === 'undefined' || password == '') {
            ok = false;
            $register_blog_form.find('.register-password #user-password').addClass('field-error');
            fieldRequireMessage($register_blog_form.find('.register-password'));
        }

        if (password != '' && (password.length < 6 || password.length > 12)) {
            ok = false;
            $register_blog_form.find('.register-pass #user-pass').addClass('field-error');
            $register_blog_form.find('.register-pass').append('<p class="error-message">Password lengths from 6 to 12 characters, Japanese characters, numerals</p>');
        }

        /* validate gender */
        if (typeof gender === 'undefined' || gender == '') {
            ok = false;
            fieldRequireMessage($register_blog_form.find('.register-gender'));
        }

        /* validate birthday */
        if (
            typeof birthday_y === 'undefined' || typeof birthday_m === 'undefined' ||typeof birthday_d === 'undefined' ||
            birthday_y == '' || birthday_m == '' || birthday_d == ''
        ) {
            ok = false;
            fieldRequireMessage($register_blog_form.find('.register-birthday .birthday-group'));   
        }

        var dayInMonth = daysInMonth(birthday_y, birthday_m);
        if (dayInMonth < birthday_d) {
            ok = false;
            $register_blog_form.find('.register-birthday .birthday-group').append('<p class="error-message">Birthday is invalid</p>');
        }

        /* validate num of child */
        if (typeof num_of_child === 'undefined' || num_of_child == '') {
            ok = false;
            fieldRequireMessage($register_blog_form.find('.register-num-of-child'));
        }

        if (ok == false) {
            scrollToTop();
            return false;
        } else {

            /* assign data to confirm page */
            $register_blog_confirm = jQuery('#register-blog-confirm');
            $register_blog_confirm.find('.avatar-preview #preview-image').attr('src', picture);
            $register_blog_confirm.find('.register-nickname .confirm-nickname').text(nickname);
            $register_blog_confirm.find('.register-email .confirm-email').text(email);
            $register_blog_confirm.find('.register-pass .confirm-pass').text(password);
            $register_blog_confirm.find('.register-gender .confirm-gender').text(gender);
            $register_blog_confirm.find('.register-birthday .confirm-birthday').text(birthday_y +'年'+ birthday_m +'月'+ birthday_d +'日');
            $register_blog_confirm.find('.register-num-of-child .confirm-number-of-child').text(num_of_child +'人');

            jQuery('#register-blog-form').hide();
            jQuery('#register-blog-confirm').show();
            jQuery('#register-blog-finish').hide();
        }

        scrollToTop();
        e.preventDefault();
    });

    jQuery("#register-blog-confirm #wp-back").on('click',function(e) {
        e.preventDefault();
        jQuery('#register-blog-form').show();
        jQuery('#register-blog-confirm').hide();
        jQuery('#register-blog-finish').hide();
        scrollToTop();
        return false;
    });

    /* remove error message when typing on textbox */
    $('#register-blog-form input, #register-blog-form textarea, #register-blog-form select').on('blur', function() {
        var $this = $(this);
        $this.removeClass('field-error');
        $this.parent().find('.error-message').remove();
    });

    /* register account via ajax */
    jQuery("#registerform").submit(function(e) {
        e.preventDefault();
        jQuery('.confirm').html('<div align="center"><img class="loading-img" src="/wp-content/plugins/spc-kuchikomi/images/loading.gif"/> 投稿中です。。。</div>');
        scrollToTop();
        var data = new FormData(this);
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            cache: false,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (res) {
                if (res.result == 'success') {
                    jQuery('#register-blog-form').hide();
                    jQuery('#register-blog-confirm').hide();
                    jQuery('#register-blog-finish').show();
                } else {
                    jQuery('#register-blog-finish div h1').html('FAIL!');
                    jQuery('#register-blog-form').hide();
                    jQuery('#register-blog-confirm').hide();
                    jQuery('#register-blog-finish').show();
                }
            },
            error: function(res) {
                jQuery('#register-blog-finish div h1').html('ERROR!');
                jQuery('#register-blog-form').hide();
                jQuery('#register-blog-confirm').hide();
                jQuery('#register-blog-finish').show();
            }
        });
        return false;
    });

});

function readImageURL(input) {
    jQuery('#register-blog-form .register-avatar .avatar-preview').find('.error-message').remove();
    if (input.files && input.files[0]) {

        if (input.files[0].size > (max_upload_file_size*megabyte_value)) {
            jQuery('#register-blog-form .register-avatar .avatar-preview').append('<div class="error-message">※画像のサイズは'+ max_upload_file_size +'MBまで等</div>');
            jQuery(input).val('');
            return false;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            jQuery('#preview-image').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }

    // jQuery('#register-blog-form .register-avatar .avatar-preview').find('.error-message').remove();
}

function fieldRequireMessage($obj) {
    if ($obj == '' || $obj == 'undefined') {
        return;
    }

    $obj.append('<p class="error-message">This field is required</p>');
}

function scrollToTop() {
    jQuery("html, body").stop().animate({scrollTop:0}, 500, 'swing', function(){ });
}

function daysInMonth(year, month) {
    return new Date(year, month, 0).getDate();
}