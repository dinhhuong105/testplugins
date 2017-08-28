<?php
    if (!session_id()) {
        session_start();
    }

    get_header();
?>
<div class="main-wrap hd-contents">
	<div class="hd-login">
	    <section class="hd-login-form">

<?php if ( is_user_logged_in() ) : ?>
            <script type="text/javascript">window.location.href="<?php echo home_url('/my-page'); ?>"</script>
<?php echo exit; ?>
<?php else: ?>
            
            <div id="hd-login-form">
                <div class="login-form">
                    <form id="loginform" method="post" action="">
                        <p class="login-group login-name">
                            <label for="username" class="required">Username/Email</label>
                            <input type="text" name="username" id="username" class="input" value="" size="20">
                        </p>
                        <p class="login-group login-pass">
                            <label for="password" class="required">Password</label>
                            <input type="password" name="password" id="password" class="input" value="" size="20">
                        </p>
                        
                        <p class="login-group login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" checked="checked"> Remember Me</label></p>
                        <p class="login-group login-submit">
                            <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Log In">
                            <input type="hidden" name="redirect_to" value="<?php echo home_url('/my-page'); ?>">
                            <?php wp_nonce_field( 'wphd-kuchikomi-nonce', 'security' ); ?>
                        </p>
                    </form>
                </div>
                <div class="clear"></div>
            </div>


            <div class="hd-social-login">


                <div class="hd-facebook-login">
<?php
                        // include_once WPHD_KUCHIKOMI_PLUGIN_DIR . 'vender/Facebook/autoload.php';
                        // $fb = new Facebook\Facebook([
                        //     'app_id' => '145393062678572',
                        //     'app_secret' => '1cfb52a928c74921d8d634def992ae04',
                        //     'default_graph_version' => 'v2.9',
                        // ]);

                        // $helper = $fb->getRedirectLoginHelper();
                        // $permissions = ['email', 'user_friends']; // optional
                        // $loginUrl = $helper->getLoginUrl('http://local.test-plugins-again-01.com/facebook-login/');

                        // echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
?>

                    <!-- <fb:login-button scope="public_profile, email" onlogin="checkLoginState();" data-auto-logout-link="true" data-size="large"> -->
                    <div class="fb-login-button" data-max-rows="1" data-size="large" data-show-faces="false" data-auto-logout-link="true" data-scope="email,user_hometown,user_birthday,user_education_history,user_website,user_work_history" onlogin="checkLoginState();"></div>

                    


                    <div class="twitter-login">
<?php
                            
                            $_SESSION['request_vars'] = '';

                            


                            // echo '<pre>';
                            // print_r($_SESSION);
                            // echo '</pre>';
                            // exit;

                            // if(isset($_REQUEST['oauth_token']) && isset($_SESSION['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']){
                            //     // echo 'bbbbb';exit;
                            //     unset($_SESSION['oauth_token']);
                            //     unset($_SESSION['oauth_token_secret']);
                            // }

                            // if (isset($_SESSION['status']) && $_SESSION['status'] == 'verified' && !empty($_SESSION['request_vars'])) {
                            //  // echo 'session';
                            //  // echo '<pre>';
                            //  // print_r($_SESSION);
                            //  // echo '</pre>';
                            //  // exit;


                            //  //Retrive variables from session
                            //     $username         = $_SESSION['request_vars']['screen_name'];
                            //     $twitterId        = $_SESSION['request_vars']['user_id'];
                            //     $oauthToken       = $_SESSION['request_vars']['oauth_token'];
                            //     $oauthTokenSecret = $_SESSION['request_vars']['oauth_token_secret'];
                            //     #$profilePicture   = $_SESSION['userData']['picture'];

                            //  $twClient = new TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
                            //  //$access_token = $twClient->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

                            //  $userInfo = $twClient->get('account/verify_credentials', array('include_entities' => 'true', 'include_email' => 'true'));
                            //     // $userInfo = $twClient->get('account/verify_credentials');
                            //     // Initialize User class
                            //     // $user = new User();
                            //     echo 'verified session';
                            //     echo '<pre>';
                            //     print_r($userInfo);
                            //     echo '</pre>';
                            //     exit;

                            // } elseif (isset($_REQUEST['oauth_token']) && isset($_SESSION['oauth_token']) && $_SESSION['oauth_token'] == $_REQUEST['oauth_token']) {
                            //  // Call Twitter API

                            //     // echo '<pre>';
                            //     // print_r($_SESSION);
                            //     // echo '</pre>';
                            //     // exit;

                            //     // echo $consumerKey;exit;

                            //     $twClient = new TwitterOAuth($consumerKey, $consumerSecret, $_SESSION['oauth_token'] , $_SESSION['oauth_token_secret']);

                            //     // Get OAuth token
                            //     #$access_token = $twClient->getAccessToken($_REQUEST['oauth_verifier']);
                            //     $access_token = $twClient->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);


                            //     // echo '<pre>';
                            //     // print_r($access_token);
                            //     // echo '</pre>';
                            //     // exit;

                            //     if ($twClient->getLastHttpCode() == 200) {
                            //         $_SESSION['status'] = 'verified';
                            //         $_SESSION['request_vars'] = $access_token;


                            //         $oauthToken       = $_SESSION['request_vars']['oauth_token'];
                            //         $oauthTokenSecret = $_SESSION['request_vars']['oauth_token_secret'];
                            //         $twClient = new TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
                            //         $userInfo = $twClient->get('account/verify_credentials', array('include_entities' => 'true', 'include_email' => 'true'));

                            //         echo 'access_token';
                            //         echo '<pre>';
                            //         print_r($userInfo);
                            //         echo '</pre>';
                            //         exit;

                            //     }

                            //     // If returns success
                            //     if ($twClient->getLastHttpCode() == 200) {
                            //         $_SESSION['status'] = 'verified';
                            //         $_SESSION['request_vars'] = $access_token;

                            //         #echo 'aaaaaaaaaaa';exit;
                            //         // wp_redirect(home_url(add_query_arg(array(),$wp->request)));exit;
                            //         // header("Location: " . home_url(add_query_arg(array(),$wp->request)));exit;

                            //         // Get user profile data from twitter
                            //         $userInfo = $twClient->get('account/verify_credentials', ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
                            //         // Initialize User class
                            //         // $user = new User();
                            //         echo 'verified';
                            //         echo '<pre>';
                            //         print_r($userInfo);
                            //         echo '</pre>';
                            //         exit;

                            //         // Insert or update user data to the database
                            //         $name = explode(" ",$userInfo->name);
                            //         $fname = isset($name[0]) ? $name[0] : '';
                            //         $lname = isset($name[1]) ? $name[1] : '';
                            //         $profileLink = 'https://twitter.com/' . $userInfo->screen_name;
                            //         $twUserData = array(
                            //             'oauth_provider'=> 'twitter',
                            //             'oauth_uid'     => $userInfo->id,
                            //             'first_name'    => $fname,
                            //             'last_name'     => $lname,
                            //             'email'         => '',
                            //             'gender'        => '',
                            //             'locale'        => $userInfo->lang,
                            //             'picture'       => $userInfo->profile_image_url,
                            //             'link'          => $profileLink,
                            //             'username'      => $userInfo->screen_name
                            //         );

                            //         echo '<pre>';
                            //         print_r($twUserData);
                            //         echo '</pre>';
                            //         exit;
                                    
                            //         $userData = $user->checkUser($twUserData);
                                    
                            //         // Storing user data into session
                            //         $_SESSION['userData'] = $userData;
                                    
                            //         // Remove oauth token and secret from session
                            //         unset($_SESSION['oauth_token']);
                            //         unset($_SESSION['oauth_token_secret']);
                                    
                            //         // Redirect the user back to the same page
                            //         wp_redirect(home_url('/my-page'));exit;

                            //     } else {
                            //         $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
                            //         echo '<h3 style="color:red">Some problem occurred, please try again.</h3>';
                            //     }

                            // } else {
                            //     // // Fresh authentication
                            //     // $twClient = new TwitterOAuth($consumerKey, $consumerSecret);
                            //     // $request_token = $twClient->oauth('oauth/request_token', array('oauth_callback' => $redirectURL));

                            //     // // echo '<pre>';
                            //     // // print_r($twClient);
                            //     // // print_r($request_token);
                            //     // // echo '</pre>';
                            //     // // exit;
                                
                            //     // // Received token info from twitter
                            //     // $_SESSION['oauth_token']        = $request_token['oauth_token'];
                            //     // $_SESSION['oauth_token_secret']  = $request_token['oauth_token_secret'];

                            //     // // If authentication returns success
                            //     // if ($twClient->getLastHttpCode() == '200') {
                            //     //     // Get twitter oauth url
                            //     //     $url = $twClient->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

                            //     //     wp_redirect($url);exit;
                                    
                            //     //     // Display twitter login button
                            //     //  $output = '<a href="'. $url .'">Login on twitter</a>';
                            //     // } else {
                            //     //     $output = '<h3 style="color:red">Error connecting to twitter! try again later!</h3>';
                            //     // }

                            //     // //exit;

                            //     $request_token = $wphd_twitter_connection->oauth('oauth/request_token', array('oauth_callback' => $redirectURL));
                            //     $_SESSION['oauth_token'] = $request_token['oauth_token'];
                            //     $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
                            //     if ($wphd_twitter_connection->getLastHttpCode() == '200') {
                            //         $url = $wphd_twitter_connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
                            //         echo '<a href="'. $url .'">Login on twitter</a>';
                            //     }

                            // }




                            require_once(WPHD_KUCHIKOMI_PLUGIN_DIR . 'includes/wphd-kuchikomi-twitter-login.php');
?>
                        <div class="clear"></div>
                    </div>


                    <div class="clear"></div>
                </div>

                <div class="clear"></div>
            </div>

<?php endif; ?> 
		</section>
	</div>
	<div class="clear"></div>
</div>





<div id="fb-root"></div>
<!-- <script>


// Additional JS functions here


window.fbAsyncInit = function() {


FB.init({

      appId      : '145393062678572', // App ID
      // channelUrl : '//myurl/channel.html', // Channel File
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });


FB.getLoginStatus(function(response) {
//alert("Response");
//alert("getloginstatus");
    if (response.status === 'connected') {
        // connected
        alert('connected');
    } else if (response.status === 'not_authorized') {
        // not_authorized
        alert('not_authorized');
        login();
    } else {
        // not_logged_in
        alert('not_logged_in');
        login();
    }
});

};


  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
     //alert("Lodaed SDK");
   }(document));

function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
        alert("Good to see you, " + response.name + ".");
    });
}

function login() {
    FB.login(function(response) {
        if (response.authResponse) {
            // connected
        } else {
            // cancelled
        }
    });
}





</script> -->


<script>
    // This is called with the results from from FB.getLoginStatus().
     function statusChangeCallback(response) {

        console.log(response);

        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            getProfileAPI();
        } else if (response.status === 'not_authorized') {
        } else {
            console.log('else');
            console.log(response);
        }
    }

    // This function is called when someone finishes with the Login
    // Button.  See the onlogin handler attached to it in the sample
    // code below.
    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    window.fbAsyncInit = function() {
        FB.init({
            appId: '145393062678572',
            cookie: true, // enable cookies to allow the server to access 
            xfbml: true, // parse social plugins on this page
            version: 'v2.9' // use version 2.2
        });

        // Now that we've initialized the JavaScript SDK, we call 
        // FB.getLoginStatus().  This function gets the state of the
        // person visiting this page and can return one of three states to
        // the callback you provide.  They can be:
        //
        // 1. Logged into your app ('connected')
        // 2. Logged into Facebook, but not your app ('not_authorized')
        // 3. Not logged into Facebook and can't tell if they are logged into
        //    your app or not.
        //
        // These three cases are handled in the callback function.

        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    };

    // Load the SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    } (document, 'script', 'facebook-jssdk'));

    // Here we run a very simple test of the Graph API after login is
    // successful.  See statusChangeCallback() for when this call is made.
    function getProfileAPI() {
        FB.api('/me', { locale: 'en_US', fields: 'name,email,birthday,gender,picture.width(150).height(150)' }, function(response) {
            
            // // check existed email.
            // if (response.email === 'undefine') {
            //     console.log('Email missing');
            // }

            console.log('aaaaaaaaaaa');
            if (!checkExistsEmail(response.email)) {
                // user existed
            } else {
                // user not existed
            }
        });
    }

    function checkExistsEmail(email) {
        console.log(email);
        jQuery.ajax({
            type: 'POST',
            cache: false,
            dataType: "json",
            async: false,
            url: wphd_kuchikomi_object.ajaxurl,
            data: {
                'action': 'check_existed_email_front',
                'email': email
            },
            success: function(data) {
                console.log(data);
            }
        });
    }
</script>



<script type="text/javascript">
    // (function(d, s, id) {
    //     var js, fjs = d.getElementsByTagName(s)[0];
    //     if (d.getElementById(id)) return;
    //     js = d.createElement(s); js.id = id;
    //     js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9&appId=145393062678572";
    //     fjs.parentNode.insertBefore(js, fjs);
    // } (document, 'script', 'facebook-jssdk'));

    // window.fbAsyncInit = function() {
    //     FB.init({
    //         appId      : '145393062678572', // Set YOUR APP ID
    //         status     : true, // check login status
    //         cookie     : true, // enable cookies to allow the server to access the session
    //         xfbml      : true,  // parse XFBML
    //         version    : 'v2.9',
    //         oauth      : true
    //     });

    //     FB.Event.subscribe('auth.authResponseChange', function(response) {
    //         if (response.status === 'connected') {
    //             //success
    //             FB.api('/me', { locale: 'en_US', fields: 'name,email,birthday,gender,picture.width(150).height(150)' },
    //                 function(response) {
    //                     console.log(response);
    //                 }
    //             );

    //             // loginViaAjax({email: response.email, gender: response.gender, name: response.name, id: response.id, birthday: response.birthday});
    //             // window.location.href="<?php #echo home_url('/my-page'); ?>";
    //         } else if (response.status === 'not_authorized') {
    //             console.log("Failed to Connect");
    //         } else {
    //             console.log("Logged Out");
    //         }
    //     });
    // };


    function loginViaAjax(data) {
        /* login via ajax */
        jQuery.ajax({
            type: 'POST',
            cache: false,
            dataType: "json",
            async: false,
            url: wphd_kuchikomi_object.ajaxurl,
            data: {
                'action': 'wphd_ajaxlogin',
                'username': 'huong.dinh.2@spc-vn.com',
                'password': '123456'
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
    }
</script>
<?php get_footer(); ?>