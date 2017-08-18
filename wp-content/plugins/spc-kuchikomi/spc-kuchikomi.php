<?php
/**
* Plugin Name: SPC - Kuchikomi
* Author: Dinh Van Huong
* Description: Allow user vote, comment, rating for products
*/
global $post, $wp, $user_login;


/**
 * define
 * @author Dinh Van Huong
 */
define( 'WPHD_KUCHIKOMI_PLUGIN_DIR', 	plugin_dir_path( __FILE__ ));
define( 'WPHD_KUCHIKOMI_PLUGIN_URI', 	plugin_dir_url( __FILE__ ));
define( 'WPHD_KUCHIKOMI_PLUGIN_STYLE', 	plugin_dir_url( __FILE__ ) . 'css/');
define( 'WPHD_KUCHIKOMI_PLUGIN_SCRIPT', plugin_dir_url( __FILE__ ) . 'js/');
define( 'WPHD_KUCHIKOMI_PLUGIN_IMAGES', plugin_dir_url( __FILE__ ) . 'images/');

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


/**
 * disable notification update for special plugin
 *
 * @author Dinh Van Huong
 */
add_filter( 'site_transient_update_plugins', 'wphd_kuchikomi_disable_notification_plugin_updates' );
if ( !function_exists('wphd_kuchikomi_disable_notification_plugin_updates')) {
	function wphd_kuchikomi_disable_notification_plugin_updates( $value ) 
	{
	    if ( isset( $value ) && is_object( $value ) ) {
	        unset( $value->response[ plugin_basename(__FILE__) ] );
	    }

	    return $value;
	}
}


/**
 * check device is smart phone of PC
 *
 * @author Dinh Van Huong
 */
if ( !function_exists ( 'cf_is_mobile' ) ) {
	function cf_is_mobile() 
	{
		$cf_is_mobile = isset($_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER']) ? $_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] : null;
		$cf_is_tablet = isset($_SERVER['HTTP_CLOUD_FRONT_IS_TABLET_VIEWER']) ? $_SERVER['HTTP_CLOUD_FRONT_IS_TABLET_VIEWER'] : null;
		return wp_is_mobile() || $cf_is_mobile === 'true' || $cf_is_tablet === 'true';
	}
}


/**
 * load special page template for questionnaire
 *
 * @author Dinh Van Huong
 */
if (!is_admin()) {
	add_filter( 'page_template', 'wphd_kuchikomi_load_page_template' );
	add_filter( 'single_template', 'wphd_kuchikomi_load_page_template' );
	add_action( 'wp_enqueue_scripts', 'wphd_kuchikomi_plugin_unique_style', 99 );

	// add action for login, logout, validate, login error page ...
	add_action( 'init','wphd_kuchikomi_redirect_login_page' );
	add_action( 'wp_login_failed', 'wphd_kuchikomi_login_failed' );
	add_filter( 'authenticate', 'wphd_kuchikomi_verify_username_password', 1, 3);
	add_action( 'wp_logout','wphd_kuchikomi_logout_page' );
}

/**
 * load page template
 *
 * @param string $page_template
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_kuchikomi_load_page_template' )) {
	function wphd_kuchikomi_load_page_template( $page_template )
	{
	    if ( is_page( 'login' ) ) {
	        $page_template = WPHD_KUCHIKOMI_PLUGIN_DIR . 'accounts/wphd-kuchikomi-login.php';
	    }

	    if ( is_page( 'register' ) ) {
	        $page_template = WPHD_KUCHIKOMI_PLUGIN_DIR . 'accounts/wphd-kuchikomi-register.php';
	    }

	    if ( is_page( 'my-page' ) ) {
	        $page_template = WPHD_KUCHIKOMI_PLUGIN_DIR . 'accounts/wphd-kuchikomi-mypage.php';
	    }
	    
	    return $page_template;
	}
}

/**
 * create a new page
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_kuchikomi_plugin_unique_style' )) {
	function wphd_kuchikomi_plugin_unique_style()
	{
		wp_enqueue_style( 'wphd-kuchikomi-css', WPHD_KUCHIKOMI_PLUGIN_STYLE . 'style-kuchikomi.css' );
	}
}

/**
 * plugin install
 *
 * @author Dinh Van Huong
 */
if (!function_exists('wphd_kuchikomi_plugin_install')) {
	function wphd_kuchikomi_plugin_install() {
	    global $wpdb;

	    wphd_kuchikomi_create_new_page('Login', 'login');
	    wphd_kuchikomi_create_new_page('My page', 'my-page');
	    wphd_kuchikomi_create_new_page('Register', 'register');
	}
}
register_activation_hook( __FILE__ , 'wphd_kuchikomi_plugin_install');

/**
 * create a new page
 *
 * @param string $the_page_title
 * @param string $the_page_name
 * @param string $the_page_template
 *
 * @author Dinh Van Huong
 */
if ( !function_exists ( 'wphd_kuchikomi_create_new_page' ) ) {
	function wphd_kuchikomi_create_new_page($the_page_title = '', $the_page_name = '', $the_page_template = '')
	{
		global $wpdb;
		
		if (empty($the_page_title) || empty($the_page_name)) {
			return 0;
		}

		$the_page = get_page_by_title( $the_page_title );

	    if ( ! $the_page ) {

	        // Create post object
	        $_page = array();
	        $_page['post_title'] 	= $the_page_title;
	        $_page['post_name'] 	= $the_page_name;
	        $_page['post_content'] 	= "";
	        $_page['post_status'] 	= 'publish';
	        $_page['post_type'] 	= 'page';
	        $_page['comment_status'] = 'closed';
	        $_page['ping_status'] 	= 'closed';

	        if ($the_page_template) {
	        	$_page['page_template'] = $the_page_template;
	        }

	        // Insert the post into the database
	        $the_page_id = wp_insert_post( $_page, false );
	    }
	}
}

/**
 * check user login or not
 *
 * @param none
 * @return boolean
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_kuchikomi_check_user_login' )) {
	function wphd_kuchikomi_check_user_login () {
		return is_user_logged_in();
	}
}
add_action('init', 'wphd_kuchikomi_check_user_login');

/**
 * redirect to login page
 *
 * @param none
 * @return void
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_kuchikomi_redirect_login_page' )) {
	function wphd_kuchikomi_redirect_login_page() {
		// $login_page  = home_url( '/login' );
		// $page_viewed = basename($_SERVER['REQUEST_URI']);

		// if( $page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
		// 	wp_redirect($login_page);
		// 	exit;
		// }
	}
}

/**
 * login failed
 *
 * @param none
 * @return void
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_kuchikomi_login_failed' )) {
	function wphd_kuchikomi_login_failed() {
		// $login_page  = home_url( '/login' );
		// wp_redirect( $login_page . '?login=failed' );
		// exit;
	}
}

/**
 * verify username and password of user
 *
 * @param none
 * @return void
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_kuchikomi_verify_username_password' )) {
	function wphd_kuchikomi_verify_username_password( $user, $username, $password ) {
		// $login_page  = home_url( '/login' );
		// if (!empty($_POST)) {
		// 	if( $username == "" || $password == "" ) {
		// 		wp_redirect( $_POST['redirect_to'] );
		// 		exit;
		// 	}
		// }
	}
}

/**
 * logout page
 *
 * @param none
 * @return void
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_kuchikomi_logout_page' )) {
	function wphd_kuchikomi_logout_page() {
		// $login_page  = home_url( '/login' );
		// wp_redirect( $login_page . "?login=false" );
		// exit;
	}
}

/**
 * setting menu
 *
 * @param none
 * @return void
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_kuchikomi_setting_menu' )) {
	function wphd_kuchikomi_setting_menu() {
	    add_options_page('設定', '掲示板、アンケートの設定', 'manage_options', 'spc_setting','wphd_kuchikomi_setting_options');
	}
}
add_action('admin_menu','wphd_kuchikomi_setting_menu');

/**
 * kuchikomi setting options
 *
 * @param none
 * @return void
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_kuchikomi_setting_options' )) {
	function wphd_kuchikomi_setting_options() {
	    if(strtolower($_SERVER['REQUEST_METHOD'])=='post'){
	        if(isset($_POST['spc_options'])) {
	            if(get_option('spc_options')!==false) {
	                update_option('spc_options', $_POST['spc_options']);
	            } else {
	                add_option('spc_options', $_POST['spc_options']);
	            }
	        }
	    }
	    
	    $spc_option = get_option('spc_options');
	    include_once( WPHD_KUCHIKOMI_PLUGIN_DIR . 'accounts/wphd-kuchikomi-setting.php' );
	}
}

add_action('init', 'ajax_login_init');
function ajax_login_init() {
	if (!is_user_logged_in()) {
		$page_viewed = basename($_SERVER['REQUEST_URI']);
		if( $page_viewed != "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
			wp_register_script('wphd-kuchikomi-script', WPHD_KUCHIKOMI_PLUGIN_URI . 'js/wphd-kuchikomi.js', array('jquery') ); 
		    wp_enqueue_script('wphd-kuchikomi-script');

		    wp_localize_script( 'wphd-kuchikomi-script', 'wphd_kuchikomi_object', array( 
		        'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
		        'redirecturl' 		=> home_url('/my-page'),
		        'loadingmessage' 	=> __('Sending user info, please wait...'),
		        'field_required'	=> __('This field is required'),
		        'login_wrong'		=> __('Wrong username or password.'),
		        'login_success'		=> __('Login successful, Welcome back'),
		        'security'			=> wp_create_nonce( 'wphd-kuchikomi-nonce' )
		    ));

		    // Enable the user with no privileges to run ajax_login() in AJAX
		    add_action( 'wp_ajax_nopriv_wphd_ajaxlogin', 'wphd_ajaxlogin' );
		}
	}
}

add_action( 'wp_ajax_nopriv_wphd_ajaxlogin', 'wphd_ajaxlogin' );
function wphd_ajaxlogin() {
    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'wphd-kuchikomi-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] 	= sanitize_text_field($_POST['username']);
    $info['user_password'] 	= sanitize_text_field($_POST['password']);
    $info['remember'] 		= $_POST['remember'];

    $user_signon = wp_signon( $info, false );

    if ( is_wp_error($user_signon) ) {
        echo json_encode(array('loggedin' => false));
    } else {
        echo json_encode(array('loggedin' => true));
    }

    exit;
}

add_action( 'wp_ajax_nopriv_check_existed_email_front', 'wphd_check_existed_email_front', 10 );
add_action( 'wp_ajax_check_existed_email_front', 'wphd_check_existed_email_front', 10 );
if (!function_exists('wphd_check_existed_email_front')) {
	function wphd_check_existed_email_front() {
		$result = (email_exists($_POST['email'])) ? true : false;
		echo json_encode(array('result' => $result));
		exit;
	}
}

add_action( 'wp_ajax_register_account_front', 'wphd_register_account_front', 12 );
add_action( 'wp_ajax_nopriv_register_account_front', 'wphd_register_account_front', 12 );
if (!function_exists('wphd_register_account_front')) {
	function wphd_register_account_front() {

		// echo '<pre>';
		// print_r($_POST);
		// print_r($_FILES);
		// echo '</pre>';
		// exit;

		if (isset( $_POST['submitted'] )) {
			$userdata = array(
				'user_login' => wp_strip_all_tags($_POST['email']),
		        'user_pass'  => wp_strip_all_tags($_POST['password']),
		        'user_email' => wp_strip_all_tags($_POST['email']),
		        'nickname'   => wp_strip_all_tags($_POST['nickname']),
		        'role' 		 => 'subscriber'
		    );

		 	$res_email = email_exists( $userdata['user_email'] );
		    if ( email_exists( $userdata['user_email'] ) == false ) {
		        $user_id = wp_insert_user( $userdata );

		        if ($user_id && !is_array($user_id)) {
		        	update_user_meta($user_id, 'gender', $_POST['gender']);
		        	update_user_meta($user_id, 'birthday', $_POST['birthday']);
		        	update_user_meta($user_id, 'num_of_child', $_POST['num_of_child']);

		        	/* update avatar for user */
		        	$avatar = '';
		        	if (isset($_FILES['picture'])) {
		        		$avatar = update_profile_picture_for_user($user_id, $_FILES);
		        	}
		        	
		        	if ($avatar) {
						update_user_meta($user_id, 'wphd_profile_picture', $avatar);
						// update_option( 'avatar_default', $avatar );
						// update_option( 'avatar_rating', $avatar );
		        	}


		        	echo json_encode(array('result' => 'success'));
		        } else {
		        	echo json_encode(array('result' => 'false'));
		        }
		    } else {
	        	echo json_encode(array('result' => 'false'));
	        }
		}

		exit;
	}
}

if (!function_exists('update_profile_picture_for_user')) {
	function update_profile_picture_for_user($user_id, $files) {
		if (!function_exists('wp_handle_upload')) {
        	require_once(ABSPATH . 'wp-admin/includes/file.php');
       	}

       	$uploadedfile = $_FILES['picture'];
		$upload_overrides = array('test_form' => false);
		$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
		if ($movefile && !isset($movefile['error'])) {
        	return $movefile['url'];
	    }

	    return false;
	}
}

//add_filter( 'avatar_defaults', 'customgravatar' );

// function customgravatar ($avatar_defaults) {
// 	//echo 'aaaaa';exit;
// 	$myavatar = get_home_url('Template_directory') . '/images/mycustomgravatar.jpg';
// 	$avatar_defaults[$myavatar] = "My Custom Logo";
// 	return $avatar_defaults;
// }



// function new_contact_methods( $contactmethods ) {
//     $contactmethods['phone'] = 'Phone Number';
//     return $contactmethods;
// }
// add_filter( 'user_contactmethods', 'new_contact_methods', 10, 1 );

// function new_modify_user_table( $column ) {
//     return $column;
// }
// add_filter( 'manage_users_columns', 'new_modify_user_table' );

// function wphd_custom_user_list_column( $column_name, $user_id ) {
//     switch ($column_name) {
//         case 'username' :
//             $text = '';
//             break;
//         default:
//     }
//     return $val;
// }
// add_filter( 'manage_users_custom_column', 'wphd_custom_user_list_column', 10, 2 );

// function cgc_ub_action_links($actions, $user_object) {
// 	return $actions;
// }
// add_filter('user_row_actions', 'cgc_ub_action_links', 10, 2);