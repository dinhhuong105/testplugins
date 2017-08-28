<?php
/**
 * @file
 * User has successfully authenticated with Twitter. Access tokens saved to session and DB.
 */

/* Load required lib files. */
session_start();
require_once(WPHD_KUCHIKOMI_PLUGIN_DIR . 'vendor/twittersimpleoauth/twitteroauth/twitteroauth.php');
require_once(WPHD_KUCHIKOMI_PLUGIN_DIR . 'vendor/twittersimpleoauth/config.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    echo 'empty'; exit;
    //header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* If method is set change API call made. Test is called by default. */
$data = $connection->get('account/verify_credentials');

/* Fetch and create the content from data which can be displayed */
$content = '<p>You are logged into twitter now.</p>';
$content = $content . '<p>Your name is <b>' . $data->name . '</b><br/>';
$content = $content . 'Your twitter id is <b>' . $data->screen_name . '</b></p>';

echo $content;