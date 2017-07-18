<?php
/**
* Plugin Name: Threads Management
* Author: Dinh Van Huong
* Description: A custom post type allow management of thread and allow end user post a new thread
*/
global $post, $wp;


/**
 * define
 * @author Dinh Van Huong
 */
define( 'WPHD_THREAD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPHD_THREAD_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'WPHD_THREAD_PLUGIN_STYLE', plugin_dir_url( __FILE__ ) . 'css/');
define( 'WPHD_THREAD_PLUGIN_SCRIPT', plugin_dir_url( __FILE__ ) . 'js/');
define( 'WPHD_THREAD_PLUGIN_IMAGES', plugin_dir_url( __FILE__ ) . 'images/');

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * disable notification update for special plugin
 *
 * @author Dinh Van Huong
 */
add_filter( 'site_transient_update_plugins', 'wphd_thread_disable_notification_plugin_updates' );
function wphd_thread_disable_notification_plugin_updates( $value ) 
{
    if ( isset( $value ) && is_object( $value ) ) {
        unset( $value->response[ plugin_basename(__FILE__) ] );
    }

    return $value;
}


/**
 * add posts columns
 *
 * @param array $columns
 * @return array
 *
 * @author Dinh Van Huong
 */
add_filter( 'manage_posts_columns', 'wphd_add_posts_columns' );
if ( !function_exists( 'wphd_add_posts_columns' )) {
	function wphd_add_posts_columns ($columns)
	{
		if ( in_array(get_post_type(), array('question_post', 'thread_post')) ) {
			$columns['thumbnail'] = 'サムネイル';
		    $columns['slug'] = 'スラッグ';
		    $columns['count'] = '文字数';
		    if ( 'post' == get_current_screen()->post_type ){ // 投稿のみ
		        $columns['top'] = 'TOP';
		    }

		    echo '<style type="text/css">
			.fixed .column-thumbnail {width: 90px;}
			.fixed .column-slug, .fixed .column-count {width: 10%;}
		    .fixed .column-top {width: 5%;}
			</style>';
		}

	    return $columns;
	}
}

/**
 * add posts columns row
 *
 * @param string $column_name
 * @param int $post_id
 *
 * @author Dinh Van Huong
 */
add_action( 'manage_posts_custom_column', 'wphd_add_posts_columns_row', 10, 2 );
if ( !function_exists( 'wphd_add_posts_columns_row' )) {
	function wphd_add_posts_columns_row ($column_name, $post_id)
	{
		if ( in_array(get_post_type(), array('question_post', 'thread_post')) ) {
			if ( 'thumbnail' == $column_name ) {
		        $thumb = get_the_post_thumbnail($post_id, array(80,80), 'thumbnail');
		        echo ( $thumb ) ? $thumb : '－';
		    } elseif ( 'slug' == $column_name ) {
		        $slug = get_post($post_id) -> post_name;
		        echo $slug;
		    } elseif ( 'count' == $column_name ) {
		        $count = mb_strlen(strip_tags(get_post_field('post_content', $post_id)));
		        echo $count;
		    } elseif( 'top' == $column_name ) {
		        $meta_array = get_post_meta($post_id);
		        $top = $meta_array['_myplg_toppage'][0];
		        if( $top === 'on'){
		            echo 'TOP';
		        }
		    }
		}
	}
}


/**
 * Initial for function Thread
 * @author Dinh Van Huong
 */
add_action( 'init', 'wphd_thread_post_type' );
if ( !function_exists( 'wphd_thread_post_type' )) {
	function wphd_thread_post_type() 
	{
		//カスタム投稿タイプ名を指定
		$thread_args = array (
            'labels' => array (
                   'name' 			=> __( 'スレッド投稿' ),
                   'singular_name' 	=> __( 'スレッド投稿' ),
                   'add_new' 		=> '新規追加',
                   'add_new_item' 	=> 'スレッドを新規追加',
                   'edit_item' 		=> 'スレッドを編集する',
                   'new_item' 		=> '新規サイト',
                   'all_items' 		=> 'スレッド一覧',
                   'view_item' 		=> 'スレッドの説明を見る',
                   'search_items' 	=> '検索する',
                   'not_found' 		=> 'スレッドが見つかりませんでした。',
                   'not_found_in_trash' => 'ゴミ箱内にスレッドが見つかりませんでした。'
            ),
            'taxonomies' 	=> array( 'post', "category"),
            'public' 		=> true,
            'has_archive' 	=> true, 	/* アーカイブページを持つ */
            'menu_position' =>5, 		//管理画面のメニュー順位　投稿の下
            'supports' 		=> array( 'title', 'editor', 'thumbnail', 'comments', 'author' ),
	    );

	    register_post_type( 'thread_post', $thread_args );
	    flush_rewrite_rules(false);
	}
}


/**
 * load special page template for questionnaire
 *
 * @author Dinh Van Huong
 */
if (!is_admin()) {
	add_filter('page_template', 'wphd_load_page_template');
	add_filter('single_template', 'wphd_load_page_template');
	add_action('wp_enqueue_scripts', 'wphd_thread_plugin_unique_style', 99 );
}

/**
 * load page template
 *
 * @param string $page_template
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_load_page_template' )) {
	function wphd_load_page_template( $page_template )
	{
		$active_question = is_plugin_active( 'spc-questionnaires/spc-questionnaires.php' );
		$active_thread = is_plugin_active( 'spc-threads/spc-threads.php' );
		$plugin_dir = ( $active_question ) ? SPCV_CUSTOME_PLUGIN_DIR : WPHD_THREAD_PLUGIN_DIR;

	    if ( is_page( 'notice' ) ) {
	        $page_template = $plugin_dir . 'views/page-notice.php';
	    }

	    if (is_single() && get_post_type() == 'question_post') {
	    	$page_template = $plugin_dir . 'views/single-question_post.php';
	    }

	    if ( $active_thread && is_page( 'add-thread' ) ) {
	        $page_template = WPHD_THREAD_PLUGIN_DIR . 'views/add-thread.php';
	    }

	    if (is_single() && get_post_type() == 'thread_post') {
	    	$page_template = WPHD_THREAD_PLUGIN_DIR . 'views/single-thread_post.php';
	    }

	    return $page_template;
	}
}

/**
 * create a new page
 *
 * @author Dinh Van Huong
 */
if ( !function_exists( 'wphd_thread_plugin_unique_style' )) {
	function wphd_thread_plugin_unique_style()
	{
		if (is_page( 'notice' ) || is_page( 'add-thread' ) || (is_single() && get_post_type() == 'thread_post')) {
			wp_enqueue_style( 'font_awesome', WPHD_THREAD_PLUGIN_STYLE . 'font-awesome.min.css' );
			if( cf_is_mobile() && file_exists(WPHD_THREAD_PLUGIN_DIR . 'css/style_sp.css')) {
				wp_enqueue_style( 'style_sp', WPHD_THREAD_PLUGIN_STYLE . 'style_sp.css' );
			} elseif (file_exists(WPHD_THREAD_PLUGIN_DIR . 'css/style_pc.css')) {
				wp_enqueue_style( 'style_pc', WPHD_THREAD_PLUGIN_STYLE . 'style_pc.css' );
			}
		}
	}
}

/**
 * plugin install
 *
 * @author Dinh Van Huong
 */
register_activation_hook( __FILE__ , 'wphd_thread_plugin_install');
function wphd_thread_plugin_install() {
    global $wpdb;

    create_new_page('新規スレッド作成', 'add-thread');
    create_new_page('質問掲示板', 'notice');
    wphd_add_guest_user();
}

/**
 * Create GUEST user
 * @author Dinh Van Huong
 */
if ( !function_exists ( 'wphd_add_guest_user' ) ) {
	function wphd_add_guest_user() 
	{
	    $userData = array(
	        'user_login' 	=> 'guest',
	        'user_pass' 	=> 'password',
	        'user_email' 	=> 'guest@examle.com',
	        'first_name' 	=> 'ゲスト',
	        'last_name' 	=> 'ゲスト',
	        'role' 			=> 'author'
	    );
		
	    $user_id = username_exists( $userData['user_login'] );
	    if ( !$user_id && email_exists($userData['user_email']) == false ) {
	        $user_id = wp_insert_user( $userData );
	    }
	}
}


/**
 * create a new page
 *
 * @param string $the_page_title
 * @param string $the_page_name
 * @param string $the_page_template
 *
 * @author Dinh Van Huong
 */
if ( !function_exists ( 'create_new_page' ) ) {
	function create_new_page($the_page_title = '', $the_page_name = '', $the_page_template = '')
	{
		global $wpdb;
		
		if (empty($the_page_title) || empty($the_page_name)) {
			return 0;
		}

		$the_page = get_page_by_title( $the_page_title );

	    if ( ! $the_page ) {

	        // Create post object
	        $_p = array();
	        $_p['post_title'] = $the_page_title;
	        $_p['post_name'] = $the_page_name;
	        $_p['post_content'] = "";
	        $_p['post_status'] = 'publish';
	        $_p['post_type'] = 'page';
	        $_p['comment_status'] = 'closed';
	        $_p['ping_status'] = 'closed';

	        if ($the_page_template) {
	        	$_p['page_template'] = $the_page_template;
	        }

	        // Insert the post into the database
	        $the_page_id = wp_insert_post( $_p, false );
	    }
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
 * change comments template
 *
 * @author Dinh Van Huong
 */
add_filter( 'comments_template', function ( $template ) {
	if ( is_single() && get_post_type() == 'thread_post' ) {
		include_once( WPHD_THREAD_PLUGIN_DIR . 'views/notice_comments.php' );
	}
});


/**
 * highlight reported comment list
 *
 * @author Dinh Van Huong
 */
add_action( 'admin_head-post.php', 'highlight_reported_comment_list' );
if ( !function_exists ( 'highlight_reported_comment_list' ) ) {
    function highlight_reported_comment_list()
    {
        $screen = get_current_screen();
        $report_comment_ids = array();
        $thread_post_no = get_post_meta($_GET['post'], '_thread_comment_no', true);
        if ( $screen->post_type == 'thread_post' && $_GET['action'] == 'edit' ) {
            if ( function_exists ( 'wprc_get_post_reports' ) ) {
                $results = wprc_get_post_reports($_GET['post']);
                foreach ($results as $result) {
                    if ($result['comment_id'] > 0) {
                        $report_comment_ids[] = $result['comment_id'];
                    }
                }
            }
        }

        ?>
        <script type="text/javascript">
            var report_comment_ids = <?php echo json_encode($report_comment_ids); ?>;
            var thread_post_no = <?php echo json_encode($thread_post_no); ?>;
        </script>
        <?php
    }
}

/**
 * replace duplicate <br> tag
 * @param string $content
 * @return string
 * @author Dinh Van Huong
 */
if ( !function_exists ( 'replace_duplicate_br_tag' ) ) {
    function replace_duplicate_br_tag($content)
    {
        if (empty($content)) {
            return '';
        }

        for ($i = 0; $i < 5; $i++) {
            $content = str_replace('<br><br>', '<br>', $content);
        }

        return $content;
    }
}

/**
 * Sort list comment by like count
 *
 * @param int $a
 * @param int $b
 *
 * @return int
 * @author Dinh Van Huong
 */
if ( !function_exists ('comment_compare_like_count')) {
	function comment_compare_like_count($a, $b)
	{
	    $compared = 0;
	    $a_count = (get_comment_meta( $a->comment_ID, 'cld_like_count', true )) ? get_comment_meta( $a->comment_ID, 'cld_like_count', true ) : 0;
	    $b_count = (get_comment_meta( $b->comment_ID, 'cld_like_count', true )) ? get_comment_meta( $b->comment_ID, 'cld_like_count', true ) : 0;
	    
	    if($a_count != $b_count) {
	        $compared = $a_count > $b_count ? -1 : 1;
	    }

	    return $compared;
	}
}

/**
 * compare comment by status
 *
 * @param int $a
 * @param int $b
 *
 * @return int
 * @author Dinh Van Huong
 */
if ( !function_exists ('comment_compare_old')) {
	function comment_compare_old ($a, $b)
	{
	    $compared = 0;
	    $a_date = $a->comment_date_gmt;
	    $b_date = $b->comment_date_gmt;
	    
	    if($a_date != $b_date) {
	        $compared = $a_date > $b_date ? 1 : -1;
	    }
	    
	    return $compared;
	}
}

/**
 * compare comment by status
 *
 * @param int $a
 * @param int $b
 *
 * @return int
 * @author Dinh Van Huong
 */
if ( !function_exists ('comment_compare_new')) {
	function comment_compare_new ($a, $b)
	{
	    $compared = 0;
	    $a_date = $a->comment_date_gmt;
	    $b_date = $b->comment_date_gmt;
	    
	    if ($a_date != $b_date) {
	        $compared = $a_date > $b_date ? -1 : 1;
	    }

	    return $compared;
	}
}

/**
 * remove thread category on admin menu of thread management
 *
 * @author Dinh Van Huong
 */
add_action( 'admin_menu', 'wphd_thread_remove_thread_category_menu', 999 );
if ( !function_exists ('wphd_thread_remove_thread_category_menu')) {
	function wphd_thread_remove_thread_category_menu()
	{
	    remove_submenu_page( 'edit.php?post_type=thread_post', 'edit-tags.php?taxonomy=category&amp;post_type=thread_post' );
	}
}

/**
 * Add comment id into input hidden for scroll down
 *
 * @author Dinh Van Huong
 */
add_action( 'edit_form_after_title', 'wphd_thread_add_content_before_editor' );
if ( !function_exists ('wphd_thread_add_content_before_editor')) {
	function wphd_thread_add_content_before_editor() 
	{
	    if (isset($_GET['comment_id_scroll'])) {
	        echo "<input class='comment_id_scroll' type='hidden' value=".$_GET['comment_id_scroll'].">";
	    }
	}
}

/**
 * Get user IP for check report
 *
 * @author Dinh Van Huong
 */
if ( !function_exists ('get_user_IP')) {
	function get_user_IP() 
	{
	    $client = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote = $_SERVER['REMOTE_ADDR'];

		if ( filter_var( $client, FILTER_VALIDATE_IP )) {
			$ip = $client;
		} elseif ( filter_var( $forward, FILTER_VALIDATE_IP )) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}

		return $ip;
	}
}

/**
 * Check if IP have reported comment
 *
 * @param int $comment_id
 * @param string $ip | IP address (IPv4 or IPv6)
 * @return boolean
 *
 * @author Dinh Van Huong
 */
if ( !function_exists ('ip_report_comment')) {
	function ip_report_comment($comment_id, $ip)
	{
		global $wpdb;

		$query = $wpdb->prepare( "SELECT COUNT(*) FROM wp_contentreports WHERE comment_id = %d AND reporter_ip = %s;", $comment_id, $ip );
		$count_ip = $wpdb->get_var( $query );

		if ( $count_ip>0 ) {
			return true;
		}

		return false;
	}
}

/**
 * Check if IP have reported thread post
 *
 * @param int $post_id
 * @param string $ip | IP address (IPv4 or IPv6)
 * @return boolean
 *
 * @author Dinh Van Huong
 */
if ( !function_exists ('ip_report_post')) {
	function ip_report_post($post_id, $ip)
	{
		global $wpdb;

		$query = $wpdb->prepare( "SELECT COUNT(*) FROM wp_contentreports WHERE post_id = %d AND comment_id = 0 AND reporter_ip = %s;", $post_id, $ip );
		$count_ip = $wpdb->get_var( $query );

		if ( $count_ip>0 ) {
			return true;
		}

		return false;
	}
}


/**
 * Add notification for thread menu
 *
 * @author Dinh Van Huong
 */
add_action('admin_menu', 'wphd_thread_notification_thread_menu');
if ( !function_exists ('wphd_thread_notification_thread_menu')) {
	function wphd_thread_notification_thread_menu()
	{
		global $menu;
		
		$args = array (
			'post_type' 		=> 'thread_post',
			'post_status' 		=> 'pending',
			'posts_per_page' 	=> 10000,
			// 'cat' 				=> array(-1,-281),
		);

		$pending_post = new WP_Query($args);
		if ($pending_post->post_count>0) {
			$key_thread = false;
			
			foreach ($menu as $key=>$parent_menu) {
				if ($menu[$key][0] == 'スレッド投稿') {
					$key_thread = $key;
					break;
				}
			}

			if ($key_thread) {
				$menu[$key_thread][0] .= "<span class='update-plugins count-1'><span class='update-count'> $pending_post->post_count </span></span>";
			}
		}
	}
}

/**
 * rewrite slug before save for admin site
 *
 * @author Dinh Van Huong
 */
add_action( 'save_post', 'wphd_rewrite_slug_before_save' );
if ( !function_exists ('wphd_thread_notification_thread_menu')) {
    function wphd_rewrite_slug_before_save($post_id)
    {
        if (in_array($_POST['post_type'], array('thread_post', 'question_post'))) {
            
            // check is japanese language or not
            $checked = false;
            if (!empty($_POST['post_name'])) {
                $checked = isJapanese($_POST['post_name']);
            } else if (!empty($_POST['post_title'])) {
                $checked = isJapanese($_POST['post_title']);
            }

            // get slug
            $slug_code = '';
            if ($checked) {
                $slug_code = make_slug_random();
            }

            // unhook this function to prevent infinite looping
            remove_action( 'save_post', 'wphd_rewrite_slug_before_save' );

            // Update the post into the database
            if ($slug_code) {
                wp_update_post( array('ID' => $post_id, 'post_name' => $slug_code) );    
            }

            // re-hook this function
            add_action( 'save_post', 'wphd_rewrite_slug_before_save' );
        }
    }
}

/**
 * make random slug post
 * @return string
 * @author Dinh Van Huong
 */
if ( !function_exists ('make_slug_random')) {
    function make_slug_random()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 12 ; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

/**
 * check is japanese or not
 * @param string $words
 * @return boolean
 * @author Dinh Van Huong
 */
if ( !function_exists ('isJapanese')) {
    function isJapanese($words) 
    {
        return preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $words);
    }
}

/**
 * Submit add Thread post on front
 * 
 * @author Dinh Van Huong
 */
add_action('wp_ajax_nopriv_add_thread_front', 'wphd_add_thread_front');
add_action('wp_ajax_add_thread_front', 'wphd_add_thread_front');
if ( !function_exists ('wphd_add_thread_front')) {
	function wphd_add_thread_front()
	{
	    if (isset( $_POST['submitted'] )) {
	        $user_guest = get_user_by( 'login', 'guest' );	        
	        $content = $_POST['thread_content'];
	        $content_url = preg_replace("/<img[^>]+\>/i", " ", $content);
	        $is_include_url = false;
	        $url_pattern_pre = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
	        $url_pattern_tail = '/[\w\d\.]+\.(com|org|ca|net|uk|jp|site|me|link|blog|email|online|mobi|press|website|cloud)/';
	        
	        if (preg_match($url_pattern_pre, $content_url) || preg_match($url_pattern_tail, $content_url) || strstr($content_url, "<a href=") || strstr($content_url, "www")) {
	            $is_include_url = true;
	        }

	        $post_status = $is_include_url ? 'pending' : 'publish';
	        
	        // Convert URL
	        $content = preg_replace( '~(;|>|\s|^)(https?://.+?)(<|\s|$)~im', '$1<a href="$2" target="_blank">$2</a>$3', $content );
	        $content = preg_replace( '~(;|>|\s|^)(www\..+?)(<|\s|$)~im', '$1<a href="http://$2" target="_blank">$2</a>$3', $content );
	        $content = replace_duplicate_br_tag($content);
	        
	        $post_information = array (
	                'post_title' 	=> wp_strip_all_tags( $_POST['thread_title'] ),
	                'post_name' 	=> wp_strip_all_tags( $_POST['thread_title'] ),
	                'post_content' 	=> $content,
	                'post_type' 	=> 'thread_post',
	                'post_author' 	=> $user_guest->ID,//default guest for author
	                'post_status' 	=> $post_status,
	        );
	        
	        $post_id = wp_insert_post( $post_information );

	        // update post name if it is japanese characters
	        if (isJapanese($_POST['thread_title'])) {
	            $slug_code = make_slug_random();
	            if ($slug_code) {
	                wp_update_post( array('ID' => $post_id, 'post_name' => $slug_code) );    
	            }
	        }
	        
	        // Categories
	        $category_ids = [];
	        if ($_POST['parent_cat'] > 0) {
	            array_push($category_ids, $_POST['parent_cat']);
	        }
	        
	        if ($_POST['child_cat'] > 0) {
	            array_push($category_ids, $_POST['child_cat']);
	        }

	        if ($_POST['grandchild_cat'] > 0) {
	            array_push($category_ids, $_POST['grandchild_cat']);
	        }
	        
	        if ( $post_id ) {
	            // set thumbnail
	            if (isset($_FILES)) {
	                $file = 'thread_thumb';
	            
	                $attach_id = media_handle_upload( $file, $post_id );
	                if ($attach_id) {
	                    update_post_meta($post_id, '_thumbnail_id', $attach_id);
	                }
	            }
	            
	            // set category
	            if (count($category_ids) > 0) {
	                wp_set_post_categories( $post_id, $category_ids );
	            }

	            $return = array('result'=>'success');
	        } else {
	            $return = array('result'=>'fail');
	        }

	        ob_clean();
	        wp_send_json($return);
	    }
	}
}

/**
 * is_link_on_content
 *
 * @param string $content
 * @return boolean
 * @author Dinh Van Huong
 */
if ( !function_exists ('is_link_on_content')) {
	function is_link_on_content($content)
	{
	    if (empty($content)) {
	        return false;
	    }

	    $content_url = preg_replace("/<img[^>]+\>/i", " ", $content);
	    $url_pattern_pre = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
	    $url_pattern_tail = '/[\w\d\.]+\.(com|org|ca|net|uk|jp|site|me|link|blog|email|online|mobi|press|website|cloud)/';
	    if (preg_match($url_pattern_pre, $content_url) || preg_match($url_pattern_tail, $content_url) || strstr($content_url, "<a href=") || strstr($content_url, "www")) {
	        return true;
	    }

	    return false;
	}
}

/**
 * Submit comment on Thread post
 *
 * @param int $post_id
 * @return void
 * @author Dinh Van Huong
 */
if ( !function_exists ('add_comment_on_notice')) {
	function add_comment_on_notice($post_id) 
	{

	    if (isset( $_POST['submitted'] )) {
	        $current_user = $_POST['name'];
	        $comment = $_POST['comment'];
	        $comment = replace_duplicate_br_tag($comment);

	        $time = current_time('mysql');
	        $is_link = is_link_on_content($comment);

	        $data = array(
	            'comment_post_ID' => $post_id,
	            'comment_author' => $current_user,
	            'comment_content' => $comment,
	            'comment_date' => $time,
	            'comment_approved' => ($is_link) ? 0 : 1
	        );

	        $orders = get_post_meta($post_id, '_thread_comment_no', true);
	        $comment_id = wp_insert_comment($data);
	        $orders[$comment_id] = ($orders) ? max($orders) + 1 : 1;
	        update_post_meta($post_id, '_thread_comment_no', $orders);
	        
	        // wp_redirect( get_post_permalink($post_id) );
	        // exit;
	        echo '<script type="text/javascript">window.location.href = "'. get_post_permalink($post_id) .'";</script>';
			exit;
	    }
	}
}

/**
 * Function for call ajax upload image when add thread on front
 *
 * @author Dinh Van Huong
 */
add_action('wp_ajax_upload_image_thread', 'upload_image_thread');
add_action('wp_ajax_nopriv_upload_image_thread', 'upload_image_thread');
if ( !function_exists ('upload_image_thread')) {
	function upload_image_thread() 
	{
	    $file = 'content_image';
	    $attach_id 	= media_handle_upload( $file );
	    $post_image = get_post($attach_id);
	    $image_link = $post_image->guid;
	    $image_title = $post_image->post_title;
	    $return = array(
	        'status' 		=> 'OK',
	        'id'    		=> $attach_id,
	        'image_link' 	=> $image_link,
	        'image_title' 	=> $image_title
	    );

	    wp_send_json($return);
	}
}

/**
 * Function for call ajax change category
 *
 * @author Dinh Van Huong
 */
add_action('wp_ajax_thread_change_category', 'thread_change_category');
add_action('wp_ajax_nopriv_thread_change_category', 'thread_change_category');
if ( !function_exists ('thread_change_category')) {
	function thread_change_category() 
	{
	    $id = $_POST['id'];
	    if ($id > 0) {
	        $child_categories = get_categories( array( 'parent' => $id, 'hide_empty' => false ));
	    } else {
	        $child_categories = array();
	    }

	    wp_send_json($child_categories);
	}
}

/**
 * add setting menu into setting panel
 *
 * @author Dinh Van Huong
 */
add_action('admin_menu','spc_setting_menu');
if ( !function_exists ('spc_setting_menu')) {
	function spc_setting_menu() 
	{
	    add_options_page('設定', 'MUGYUU!の設定', 'manage_options', 'spc_setting','spc_setting_options');
	}
}

if ( !function_exists ('spc_setting_options')) {
	function spc_setting_options() 
	{
	    // save MUGYUU! Options
	    if (strtolower($_SERVER['REQUEST_METHOD'])=='post') {
	        if (isset($_POST['spc_options'])) {
	            if (get_option('spc_options') !== false) {
	                update_option('spc_options', $_POST['spc_options']);
	            } else {
	                add_option('spc_options', $_POST['spc_options']);
	            }
	        }
	    }
	    
	    $spc_option = get_option('spc_options');
	    include_once WPHD_THREAD_PLUGIN_DIR . 'views/spc_setting.php';
	}
}

/**
 * Change status of report when unpublish thread
 *
 * @param string $new_status
 * @param string $old_status
 * @param object $post
 * @return void
 * @author Dinh Van Huong
 */
add_action( 'transition_post_status', 'post_unpublished', 10, 3 );
if ( !function_exists ('post_unpublished')) {
	function post_unpublished( $new_status, $old_status, $post ) 
	{
	    if ($post->post_type == 'thread_post' || $post->post_type == 'question_post') {
	        if ( $old_status == 'publish'  &&  $new_status != 'publish' ) {
	            if ( function_exists ( 'wprc_table_name' ) ){
	                global $wpdb;
	                $table_name = wprc_table_name();
	                $query = "UPDATE $table_name SET status='processed' WHERE post_id = $post->ID";
	                
	                $wpdb->query($query);
	        	}
	    	}
		}
	}
}


/**
 * Theme comment on Thread post
 *
 * @author Dinh Van Huong
 */
if ( !function_exists ('noticetheme_comment')) :
	function noticetheme_comment($comment, $args, $depth) {
	    $GLOBALS['comment'] = $comment; 
	    global $thread_no;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	    <div id="comment-<?php comment_ID(); ?>" class="commentData">
	        <p class="data" data-comment-author="<?php echo $comment->comment_author; ?>">
	            <?php echo ($thread_no && array_key_exists($comment->comment_ID, $thread_no)) ? $thread_no[$comment->comment_ID] . '.' : ''; ?>
	            <?php echo get_comment_date(('Y/m/d')); ?>
	            <?php printf(__('%s'), get_comment_author_link()); ?>
	        </p>
	        <?php if (!ip_report_comment(get_comment_ID(), get_user_IP())) : ?>
	            <div class="report modal">
	                <input id="modal-trigger-<?php comment_ID(); ?>" type="checkbox">
	                <label for="modal-trigger-<?php comment_ID(); ?>">
	                	<?php
	                		if (is_plugin_active( 'report-content/report-content.php' )) {
                                wprc_report_submission_form();
                            }
	                	?>
	                </label>
	                <div class="modal-overlay">
	                    <div class="modal-wrap">
	                        <label for="modal-trigger-<?php comment_ID(); ?>">✖</label>
	                        <h3>このコメントを通報</h3>
	                        <p>このコメントを削除すべき不適切なコメントとして通報しますか？</p>
	                        <div class="btnArea">
	                            <button type="button" class="reportBtn">通報</button>
	                            <button type="button" class="cancelBtn">やめる</button>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        <?php endif; ?>
	    </div>
	    <div class="comment"><?php comment_text(); ?></div>
	</li>

	<?php } /* end function */ ?>
<?php endif;?>
<?php

/**
 * show breadcrumb for layout
 *
 * @author Dinh Van Huong
 */
if ( !function_exists ('breadcrumb')) {
	function breadcrumb()
	{
	    global $post;
	    $str = '';

	    if (!is_home()&&!is_admin()) { /* !is_admin は管理ページ以外という条件分岐 */

	        $str.= '<div id="breadcrumb">';
	        $str.= '<ul class="breadcrumbList">';
	        $str.= '<li><a href="' . home_url('/') .'">トップ</a></li>';

	        /* 投稿のページ */
	        if (is_single() || $post->post_type === "thread_post" || $post->post_type === "question_post") {
	            if ( $post->post_type === "movingimage_post" || $post->post_type === "movie_post") {//動画、レシピ
					
					if ($post->post_type === "movingimage_post") {
					 	$str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'. home_url('/') .'recipe-list"><span>レシピ</span></a></li>';
					 	$cats = get_the_terms($post->ID,"movingimage_cat");
					} elseif ($post->post_type === "movie_post") {
					  	$cats = get_the_terms($post->ID,"movie_cat");
					}
					
					usort( $cats , '_usort_terms_by_ID');
	             
	               foreach($cats as $cat) {
	                    $str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'.get_category_link($cat->term_id). '"><span>'. $cat->name . '</span></a></li>';
	               }

	            } elseif ( $post->post_type === "item_post" ) {

	               	$cats = get_the_terms($post->ID, 'item_cat');
	               	$count_cat = is_array($cats)?count($cats):0;
	               	$new_cats = array();

	               	for ($i = 0; $i < $count_cat; $i++) {
	                    $ancestors = get_ancestors( $cats[$i]->term_id, 'item_cat' );
	                    $count_anc = count($ancestors);
	                    $new_cats[$count_anc] = $cats[$i];  // 先祖の数をキーとした要素
	               }

	               ksort($new_cats);    // キーでソートする
	               $end_term = end($new_cats);
	               $end_term_id = $end_term->term_id;

	               foreach ($new_cats as $cat) {
	                    $str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'.get_category_link($cat->term_id). '"><span>'. $cat->name . '</span></a></li>';
	               }

	             } else {

	               $categories = get_the_category($post->ID);
	               $cat = $categories;
	               $catId = $categories[0]->cat_ID;
	               usort( $cat , '_usort_terms_by_ID');

	            	$ancestors = $cat ;
	            	if ($catId == 1) {
		                foreach ($ancestors as $ancestor) {
		                    $str;
		                }
		                $str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'. get_category_link($cat[0]->term_id). '"><span>'. $cat[0]-> cat_name . '</span></a></li>';
	            	} else {
		                $count = 0;
		                foreach ($ancestors as $ancestor) {
		                    $count += 1;
		                    $str;
		                }

		                if ($count === 4 ) {
		                    $str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'. get_category_link($cat[1]->term_id). '"><span>'. $cat[1]->cat_name . '</span></a></li>';
		                    $str.= '<li class="top"><i class="fa fa-angle-right arrowIcon"></i><span>'. $cat[2]->cat_name .'</span></li>';
		                } elseif($count === 3) {
		                    $str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'. get_category_link($cat[1]->term_id). '"><span>'. $cat[1]->cat_name . '</span></a></li>';
		                    $str.= '<li><i class="fa fa-angle-right arrowIcon"></i><a href="'. get_category_link($cat[2]->term_id). '"><span>'. $cat[2]->cat_name .'</span></a></li>';
		                } elseif($count === 2) {
		                    $str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'. get_category_link($cat[1]->term_id). '"><span>'. $cat[1]->cat_name . '</span></a></li>';
		                } else {
		                    $str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'. get_category_link($cat[0]->term_id). '"><span>'. $cat[0]->cat_name . '</span></a></li>';
		                }
	                }
	            }

	          /* 固定ページ ライター個人ページ*/
	        } elseif ( is_page('author-more') ) { 
	            $author_id = $_GET['uID'];
	            $author = get_userdata($author_id);
	            $str.= '<li class="fixPage"><i class="fa fa-angle-right arrowIcon"></i><span>'. $author->display_name .'</span></li>';
	        
	          /* 固定ページ */
	        } elseif(is_page()) { 
	            if ($post -> post_parent != 0 ) {
	                $ancestors = array_reverse(get_post_ancestors( $post->ID ));
	                foreach ($ancestors as $ancestor) {
	                    $str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'. get_permalink($ancestor).'"><span>'. get_the_title($ancestor) .'</span></a></li>';
	                }
	            }
	            $str.= '<li class="fixPage"><i class="fa fa-angle-right arrowIcon"></i><span>'. $post->post_title .'</span></li>';
	       
	          /* カテゴリページ */
	        } elseif (is_category()) { 
	            $cat = get_queried_object();
	            $parent = get_category($cat->category_parent);
	            $str.='<li class="fixPage"><i class="fa fa-angle-right arrowIcon"></i><span>' . $cat->name . '</span></li>';
	        
	          /* タグページ */
	        } elseif (is_tag()) { 
	            $str.='<li><i class="fa fa-angle-right arrowIcon"></i><span>'. single_tag_title( '' , false ). '</span></li>';
	        
	          /* レシピカテゴリーページ */
	        } elseif (is_tax('movingimage_cat')) { 
				$tax = get_queried_object();
	            $str.='<li><i class="fa fa-angle-right arrowIcon"></i><a href="'. home_url('/') .'recipe-list"><span>レシピ</span></a></li>';
				$breadcrumb = '<li><i class="fa fa-angle-right arrowIcon"></i><a href="'.get_category_link($tax->term_id).
					'"><span>'. $tax->name . '</span></a></li>';
				$tax_list = get_ancestors( $tax->term_id, 'movingimage_cat' );
				foreach( $tax_list as $tax_id ){
					$tax = get_term_by( 'id', $tax_id, 'movingimage_cat');
					$breadcrumb = '<li><i class="fa fa-angle-right arrowIcon"></i><a href="'.get_category_link($tax->term_id).
						'"><span>'. $tax->name . '</span></a></li>'.$breadcrumb;
				}
				$str .= $breadcrumb;

			  /* 動画カテゴリーページ */
			} elseif (is_tax('movie_cat')) {
				$tax = get_queried_object();
				$breadcrumb = '<li><i class="fa fa-angle-right arrowIcon"></i><a href="'.get_category_link($tax->term_id).
					'"><span>'. $tax->name . '</span></a></li>';

				$tax_list = get_ancestors( $tax->term_id, 'movingimage_cat' );
				
				foreach ( $tax_list as $tax_id ) {
					$tax = get_term_by( 'id', $tax_id, 'movingimage_cat');
					$breadcrumb = '<li><i class="fa fa-angle-right arrowIcon"></i><a href="'.get_category_link($tax->term_id).
						'"><span>'. $tax->name . '</span></a></li>'.$breadcrumb;
				}

				$str .= $breadcrumb;

			  /* 商品カテゴリーページ */
			} elseif (is_tax('item_cat')) {
				$tax = get_queried_object();
				$breadcrumb = '<li><i class="fa fa-angle-right arrowIcon"></i><a href="'.get_category_link($tax->term_id).
					'"><span>'. $tax->name . '</span></a></li>';

				$tax_list = get_ancestors( $tax->term_id, 'item_cat' );
				foreach ( $tax_list as $tax_id ) {
					$tax = get_term_by( 'id', $tax_id, 'item_cat');
					$breadcrumb = '<li><i class="fa fa-angle-right arrowIcon"></i><a href="'.get_category_link($tax->term_id).
						'"><span>'. $tax->name . '</span></a></li>'.$breadcrumb;
				}

				$str .= $breadcrumb;

			  /* 時系列アーカイブページ */
			} elseif (is_date()) {
	            if (get_query_var('day') != 0) {
	                $str.='<li itemtype="http://data-vocabulary.org/"><a href="'. get_year_link(get_query_var('year')). '"><span>' . get_query_var('year'). '年</span></a></li>';
	                $str.='<li><a href="'. get_month_link(get_query_var('year'), get_query_var('monthnum')). '"><span>'. get_query_var('monthnum') .'月</span></a></li>';
	                $str.='<li><span>'. get_query_var('day'). '</span>日</li>';
	            } elseif (get_query_var('monthnum') != 0) {
	                $str.='<li><a href="'. get_year_link(get_query_var('year')) .'"><span>'. get_query_var('year') .'年</span.</a></li>';
	                $str.='<li><span>'. get_query_var('monthnum'). '</span>月</li>';
	            } else {
	                $str.='<li><span>'. get_query_var('year') .'年</span></li>';
	            }

	          /* 投稿者ページ */
	        } elseif(is_author()){
	            $str .='<li class="fixPage"><i class="fa fa-angle-right arrowIcon"></i><span>'. get_the_author_meta('display_name', get_query_var('author')).'</span></li>';
	        
	          /* 添付ファイルページ */
	        } elseif (is_attachment()) {
	            if($post -> post_parent != 0 ){
	                $str.= '<li><a href="'. get_permalink($post -> post_parent).'"><span>'. get_the_title($post -> post_parent) .'</span></a></li>';
	            }
	            $str.= '<li><span>' . $post -> post_title . '</span></li>';

	          /* 検索結果ページ */
	        } elseif(is_search()) {
	            $str.='<li class="fixPage"><i class="fa fa-angle-right arrowIcon"></i><span>「'. get_search_query() .'」の検索結果</span></li>';
	        
	          /* 404 Not Found ページ */
	        } elseif(is_404()) {
	            $str.='<li class="fixPage"><i class="fa fa-angle-right arrowIcon"></i><span>お探しのページが見つかりません</span></li>';
	        
	          /* その他のページ */
	        } else {
	            $str.='<li><i class="fa fa-angle-right arrowIcon"></i><span>'. wp_title('', false) .'</span></li>';
	        }

	        $str.='</ul>';
	        $str.='</div>';
	    }
	    echo $str;
	}
}

/**
 * pagination
 *
 * @author Dinh Van Huong
 */
if ( !function_exists ('breadcrumb')) {
	function pagination($pages = '', $range = 2)
	{
	    $showitems = ($range * 2)+1;//表示するページ数（５ページを表示）

	    global $paged;//現在のページ値

	    // if(empty($paged)) $paged = 2;//デフォルトのページ
		if(empty($paged)) { 
			$paged = 1; //デフォルトのページ  サーバー戻ったらこっちに直す
		}

	    if($pages == '') {
	        global $wp_query;
	        $pages = $wp_query->max_num_pages;//全ページ数を取得
	        if(!$pages) { //全ページ数が空の場合は、１とする
	            // $pages = 2;
				$pages = 1; //サーバー戻ったらこっちに直す
	        }
	    }

	    if(1 != $pages)//全ページが１でない場合はページネーションを表示する
	    {
	        echo "<div class=\"pagination\">\n";
	        echo "<ul>\n";
	        //Prev：現在のページ値が１より大きい場合は表示
	        // if($paged > 2) echo "<li class=\"prev\"><a href='".get_pagenum_link($paged - 1)."'><i class=\"fa fa-angle-left\"></i></a></li>\n";
			if($paged > 1) echo "<li class=\"prev\"><a href='".get_pagenum_link($paged - 1)."'><i class=\"fa fa-angle-left\"></i></a></li>\n"; //サーバー戻ったらこっちに直す

			// for ($i=2; $i <= $pages; $i++)
			for ($i=1; $i <= $pages; $i++) {
	            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
	                //三項演算子での条件分岐
	                echo ($paged == $i)? "<li class=\"active\">".$i."</li>\n":"<li><a href='".get_pagenum_link($i)."'>".$i."</a></li>\n";
	            }
	        }
	        //Next：総ページ数より現在のページ値が小さい場合は表示
	        if ($paged < $pages) echo "<li class=\"next\"><a href=\"".get_pagenum_link($paged + 1)."\"><i class=\"fa fa-angle-right\"></i></a></li>\n";
	        echo "</ul>\n";
	        echo "</div>\n";
	    }
	}
}

/*---------------------------------------------------------------*/
/* WordPressの投稿作成画面で必須項目を作る（空欄ならJavaScriptのアラート）
/*---------------------------------------------------------------*/
add_action( 'admin_head-post-new.php', 'mytheme_post_edit_required' ); // 新規投稿画面でフック
add_action( 'admin_head-post.php', 'mytheme_post_edit_required' );     // 投稿編集画面でフック
if ( !function_exists( 'mytheme_post_edit_required' )) {
	function mytheme_post_edit_required() {
	?>
	<script type="text/javascript">
	    jQuery(document).ready(function($){
	        if(
	          $('#post_type').val() == 'thread_post' || 
	          $('#post_type').val() == 'question_post'){ // post_type 判定。例は投稿ページ。固定ページ、カスタム投稿タイプは適宜追加
	            $("#post").submit(function(e){ // 更新あるいは下書き保存を押したとき
	                if('' == $('#title').val()) { // タイトル欄の場合
	                    alert('タイトルを入力してください！');
	                    $('.spinner').hide(); // spinnerアイコンを隠す
	                    $('#publish').removeClass('button-primary-disabled'); // #publishからクラス削除
	                    $('#title').focus(); // 入力欄にフォーカス
	                    return false;
	                }

	                var cate = $("#taxonomy-category input:checked");
	                if(cate.length < 1) { // カテゴリーがチェックされているかどうか。条件を要確認。普通は設定したカテゴリーになるから要らない
	                   alert('カテゴリーを選択してください');
	                   $('.spinner').hide();
	                   $('#publish').removeClass('button-primary-disabled');
	                   $('#taxonomy-category').focus();
	                   return false;
	                }

	                if($("#post_name").val() == '') {
	                    alert('スラッグを入力してください！');
	                    $('.spinner').hide();
	                    $('#publish').removeClass('button-primary-disabled');
	                    $('#post_name').focus();
	                    return false;
	                } else if( $("#post_name").val().indexOf("questionary") != -1) {
	                    alert('「questionary」が含まれていないスラッグを入力してください！');
	                    $('.spinner').hide();
	                    $('#publish').removeClass('button-primary-disabled');
	                    $('#post_name').focus();
	                    return false;
	                }
	                if( $("#set-post-thumbnail img").length < 1 ) { // アイキャッチ画像
	                    alert('アイキャッチ画像を設定してください！');
	                    $('.spinner').hide();
	                    $('#publish').removeClass('button-primary-disabled');
	                    $('#set-post-thumbnail').focus();
	                    return false;
	                }
	            });
	        }
	    });
	</script>
	<?php
	}
}