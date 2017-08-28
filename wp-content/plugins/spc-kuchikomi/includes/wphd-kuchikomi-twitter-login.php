<?php
require_once WPHD_KUCHIKOMI_PLUGIN_DIR . 'vendor/twitteroauth/config.php';
require_once WPHD_KUCHIKOMI_PLUGIN_DIR . 'vendor/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

$wphd_twitter_connection = new TwitterOAuth($consumerKey, $consumerSecret);


if(isset($_REQUEST['oauth_token']) && isset($_SESSION['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']){
    unset($_SESSION['oauth_token']);
    unset($_SESSION['oauth_token_secret']);
}

if (isset($_SESSION['status']) && $_SESSION['status'] == 'verified' && !empty($_SESSION['request_vars'])) {

    //Retrive variables from session
    $username         = $_SESSION['request_vars']['screen_name'];
    $twitterId        = $_SESSION['request_vars']['user_id'];
    $oauthToken       = $_SESSION['request_vars']['oauth_token'];
    $oauthTokenSecret = $_SESSION['request_vars']['oauth_token_secret'];

    $twClient = new TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $userInfo = $twClient->get('account/verify_credentials', array('include_entities' => 'true', 'include_email' => 'true'));

    echo 'verified session';
    echo '<pre>';
    print_r($userInfo);
    echo '</pre>';
    exit;

} elseif (isset($_REQUEST['oauth_token']) && isset($_SESSION['oauth_token']) && $_SESSION['oauth_token'] == $_REQUEST['oauth_token']) {
    
    // Call Twitter API
    $twClient = new TwitterOAuth($consumerKey, $consumerSecret, $_SESSION['oauth_token'] , $_SESSION['oauth_token_secret']);

    // Get OAuth token
    $access_token = $twClient->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

    if ($twClient->getLastHttpCode() == 200) {
        $_SESSION['status'] = 'verified';
        $_SESSION['request_vars'] = $access_token;

        $oauthToken       = $_SESSION['request_vars']['oauth_token'];
        $oauthTokenSecret = $_SESSION['request_vars']['oauth_token_secret'];
        $twClient = new TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
        $userInfo = $twClient->get('account/verify_credentials', array('include_entities' => 'true', 'include_email' => 'true', "oauth_verifier" => $_REQUEST['oauth_verifier']));

        // echo '<pre>';
        // print_r($userInfo);
        // echo '</pre>';
        // exit;

        if (!isset($userInfo->email) || empty($userInfo->email)) {
            echo 'Login fail. Email is required'; exit;
        }

        $login_info = array(
            'user_login'    => wp_strip_all_tags($userInfo->email),
            'user_password' => '123456',
            'remember'      =>  true
        );

        if (email_exists( $userInfo->email ) == false) {
            // register a new account.
            $userdata = array(
                'user_login' => wp_strip_all_tags($userInfo->email),
                'user_pass'  => WPHD_PASSWORD_DEFAULT,
                'user_email' => wp_strip_all_tags($userInfo->email),
                'nickname'   => wp_strip_all_tags($userInfo->screen_name),
                'display_name'   => wp_strip_all_tags($userInfo->screen_name),
                'role'       => 'subscriber'
            );

            $user_id = wp_insert_user( $userdata );
            if ($user_id && !is_array($user_id)) {
                // update_user_meta($user_id, 'gender', $_POST['gender']);
                // update_user_meta($user_id, 'birthday', $_POST['birthday']);
                // update_user_meta($user_id, 'num_of_child', $_POST['num_of_child']);

                /* update avatar for user */
                // $avatar = '';
                // if (isset($userInfo['picture'])) {
                //     $avatar = update_profile_picture_for_user($user_id, $_FILES);
                // }
                
                if ($userInfo->profile_image_url) {
                    update_user_meta($user_id, 'wphd_profile_picture', $userInfo->profile_image_url);
                    // update_option( 'avatar_default', $avatar );
                    // update_option( 'avatar_rating', $avatar );
                }
            }

            // login by email.
            login_via_social_network($login_info);

        } else {
            // login by email.
            login_via_social_network($login_info);
        }

        // $userdata = array(
        //     'user_login' => wp_strip_all_tags($_POST['email']),
        //     'user_pass'  => wp_strip_all_tags($_POST['password']),
        //     'user_email' => wp_strip_all_tags($_POST['email']),
        //     'nickname'   => wp_strip_all_tags($_POST['nickname']),
        //     'role'       => 'subscriber'
        // );

        // $res_email = email_exists( $userdata['user_email'] );
        // if ( email_exists( $userdata['user_email'] ) == false ) {
        //     $user_id = wp_insert_user( $userdata );

        //     if ($user_id && !is_array($user_id)) {
        //         update_user_meta($user_id, 'gender', $_POST['gender']);
        //         update_user_meta($user_id, 'birthday', $_POST['birthday']);
        //         update_user_meta($user_id, 'num_of_child', $_POST['num_of_child']);

        //         /* update avatar for user */
        //         $avatar = '';
        //         if (isset($_FILES['picture'])) {
        //             $avatar = update_profile_picture_for_user($user_id, $_FILES);
        //         }
                
        //         if ($avatar) {
        //             update_user_meta($user_id, 'wphd_profile_picture', $avatar);
        //             // update_option( 'avatar_default', $avatar );
        //             // update_option( 'avatar_rating', $avatar );
        //         }


        //         echo json_encode(array('result' => 'success'));
        //     } else {
        //         echo json_encode(array('result' => 'false'));
        //     }
        // } else {
        //     echo json_encode(array('result' => 'false'));
        // }


        echo 'access_token';
        echo '<pre>';
        print_r($userInfo);
        echo '</pre>';
        exit;

        // Storing user data into session
        $_SESSION['userData'] = $userData;

        // Remove oauth token and secret from session
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);

        echo '<script type="text/javascript">window.location.href = "'. home_url('/my-page') .'";</script>';
        exit;

    } else {
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
        echo '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }

} else {

    $request_token = $wphd_twitter_connection->oauth('oauth/request_token', array('oauth_callback' => $redirectURL));
    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
    
    if ($wphd_twitter_connection->getLastHttpCode() == '200') {
        $url = $wphd_twitter_connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
        echo '<a href="'. $url .'">Login on twitter</a>';
    }
}