<?php
if(!session_id()){
    session_start();
}

// 
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once WPHD_KUCHIKOMI_PLUGIN_DIR . 'vendor/twitteroauth/config.php';
require_once WPHD_KUCHIKOMI_PLUGIN_DIR . 'vendor/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

if(isset($_REQUEST['oauth_token']) && isset($_SESSION['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']){
    // echo 'bbbbb';exit;
    unset($_SESSION['oauth_token']);
    unset($_SESSION['oauth_token_secret']);
}

// unset($_SESSION['userdata']);
// unset($_SESSION['request_vars']);
// session_destroy();

// echo '<pre>';
// print_r($_REQUEST);
// echo '</pre>';

// If user already verified 
if (isset($_SESSION['status']) && $_SESSION['status'] == 'verified' && !empty($_SESSION['request_vars'])) {
 // echo 'session';
 // echo '<pre>';
 // print_r($_SESSION);
 // echo '</pre>';
 // exit;


 //Retrive variables from session
    $username         = $_SESSION['request_vars']['screen_name'];
    $twitterId        = $_SESSION['request_vars']['user_id'];
    $oauthToken       = $_SESSION['request_vars']['oauth_token'];
    $oauthTokenSecret = $_SESSION['request_vars']['oauth_token_secret'];
    #$profilePicture   = $_SESSION['userData']['picture'];

 $twClient = new TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
 //$access_token = $twClient->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

 // $userInfo = $twClient->get('account/verify_credentials', array('include_entities' => false, 'include_email' => false));
 $userInfo = $twClient->get('account/verify_credentials');
    // Initialize User class
    // $user = new User();
    echo 'verified session';
    echo '<pre>';
    print_r($userInfo);
    echo '</pre>';
    exit;

} elseif (isset($_REQUEST['oauth_token']) && isset($_SESSION['oauth_token']) && $_SESSION['oauth_token'] == $_REQUEST['oauth_token']) {
 // Call Twitter API
    $twClient = new TwitterOAuth($consumerKey, $consumerSecret, $_SESSION['oauth_token'] , $_SESSION['oauth_token_secret']);

    // Get OAuth token
    #$access_token = $twClient->getAccessToken($_REQUEST['oauth_verifier']);
    $access_token = $twClient->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

    // $access_token = '';

 // echo 'access_token';
 // echo '<pre>';
 // print_r($twClient);
 // echo '</pre>';
 // exit;

    // If returns success
    if ($twClient->getLastHttpCode() == 200) {
        $_SESSION['status'] = 'verified';
        $_SESSION['request_vars'] = $access_token;

        // Get user profile data from twitter
        $userInfo = $twClient->get('account/verify_credentials');
        // Initialize User class
        // $user = new User();
        echo 'verified';
        echo '<pre>';
        print_r($userInfo);
        echo '</pre>';
        exit;

        // Insert or update user data to the database
        $name = explode(" ",$userInfo->name);
        $fname = isset($name[0]) ? $name[0] : '';
        $lname = isset($name[1]) ? $name[1] : '';
        $profileLink = 'https://twitter.com/' . $userInfo->screen_name;
        $twUserData = array(
            'oauth_provider'=> 'twitter',
            'oauth_uid'     => $userInfo->id,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'email'         => '',
            'gender'        => '',
            'locale'        => $userInfo->lang,
            'picture'       => $userInfo->profile_image_url,
            'link'          => $profileLink,
            'username'      => $userInfo->screen_name
        );

        echo '<pre>';
        print_r($twUserData);
        echo '</pre>';
        exit;
        
        $userData = $user->checkUser($twUserData);
        
        // Storing user data into session
        $_SESSION['userData'] = $userData;
        
        // Remove oauth token and secret from session
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);
        
        // Redirect the user back to the same page
        wp_redirect(home_url('/my-page'));exit;

    } else {
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
        echo '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }

} else {
    // Fresh authentication
    $twClient = new TwitterOAuth($consumerKey, $consumerSecret);
    $request_token = $twClient->oauth('oauth/request_token', array('oauth_callback' => $redirectURL));

    // echo '<pre>';
    // print_r($twClient);
    // print_r($request_token);
    // echo '</pre>';
    // exit;
    
    // Received token info from twitter
    $_SESSION['oauth_token']        = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret']  = $request_token['oauth_token_secret'];

    // If authentication returns success
    if ($twClient->getLastHttpCode() == '200') {
        // Get twitter oauth url
        $url = $twClient->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

        wp_redirect($url);exit;
        
        // Display twitter login button
     $output = '<a href="'. $url .'">Login on twitter</a>';
    } else {
        $output = '<h3 style="color:red">Error connecting to twitter! try again later!</h3>';
    }

    //exit;

}





    // require_once(WPHD_KUCHIKOMI_PLUGIN_DIR . 'vendor/twittersimpleoauth/twitteroauth/twitteroauth.php');
    // require_once(WPHD_KUCHIKOMI_PLUGIN_DIR . 'vendor/twittersimpleoauth/config.php');


    // /* If the oauth_token is old redirect to the connect page. */
    // if (!isset($_SESSION['oauth_token']) || isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
    //     //login_redirect();

    //     /* Build TwitterOAuth object with client credentials. */
    //     $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
         
    //     /* Get temporary credentials. */
    //     $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

    //     // echo '<pre>';
    //     // print_r($request_token);
    //     // echo '</pre>';
    //     // exit;

    //     /* Save temporary credentials to session. */
    //     $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
    //     $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    //     // echo '<pre>';
    //     // print_r($_SESSION);
    //     // echo '</pre>';
    //     // exit;

    // }

    // // echo '<pre>';
    // // print_r($_SESSION);
    // // echo '</pre>';
    // // exit;

    // $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    // // echo '<pre>';
    // // print_r($connection);
    // // echo '</pre>';
    // // exit;

    // // echo $_REQUEST['oauth_verifier'];exit;

    // $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

    // echo '<pre>';
    // print_r($access_token);
    // echo '</pre>';
    // exit;

    // $_SESSION['access_token'] = $access_token;

    // echo '<pre>';
    // print_r($_SESSION);
    // echo '</pre>';
    // exit;

    // /* Remove no longer needed request tokens */
    // unset($_SESSION['oauth_token']);
    // unset($_SESSION['oauth_token_secret']);

    // /* If HTTP response is 200 continue otherwise send to connect page to retry */
    // if (200 == $connection->http_code) {
    //     /* The user has been verified and the access tokens can be saved for future use */
    //     $_SESSION['status'] = 'verified';
        
    //     if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    //         login_redirect();
    //     }

    //     /* Get user access tokens out of the session. */
    //     $access_token = $_SESSION['access_token'];

    //     /* Create a TwitterOauth object with consumer/user tokens. */
    //     $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

    //     /* If method is set change API call made. Test is called by default. */
    //     $data = $connection->get('account/verify_credentials');

    //     echo '<pre>';
    //     print_r($data);
    //     echo '</pre>';
    //     exit;


    // } else {
    //     login_redirect();
    // }


    // if (!function_exists('login_redirect')) {
    //     function login_redirect() {
    //         session_start();
    //         session_destroy();

    //         wp_redirect(home_url('/login'));
    //     }
    // }