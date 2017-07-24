<?php
/**
* Plugin Name: Questionnaire Management
* Author: Dinh Van Huong
* Description: Create questionnaire follow post
*/
global $post, $wp;


/**
 * define
 * @author Dinh Van Huong
 */
define( 'SPCV_CUSTOME_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPCV_CUSTOME_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'SPCV_CUSTOME_PLUGIN_STYLE', plugin_dir_url( __FILE__ ) . 'css/');
define( 'SPCV_CUSTOME_PLUGIN_SCRIPT', plugin_dir_url( __FILE__ ) . 'js/');
define( 'SPCV_CUSTOME_PLUGIN_IMAGES', plugin_dir_url( __FILE__ ) . 'images/');

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * disable notification update for special plugin
 *
 * @author Dinh Van Huong
 */
add_filter('transient_update_plugins','wphd_questionnaire_disable_notification_plugin_updates');
add_filter( 'site_transient_update_plugins', 'wphd_questionnaire_disable_notification_plugin_updates');
function wphd_questionnaire_disable_notification_plugin_updates( $value ) 
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

		        $thumbnail_id = get_post_thumbnail_id();
		        $image[0] = '';
                if (!isset($thumbnail_id->errors)) {
                   $image = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' ); 
                }

                if ($image[0]) {
                	echo '<img width="80" height="80" src="'. $image[0] .'" class="attachment-80x80 size-80x80 wp-post-image" alt="" thumbnail="" srcset="'. $image[0] .' 100w, '. $image[0] .' 150w" sizes="(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px">';
                } else {
                	echo '－';
                }

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
 * load special page template for questionnaire
 *
 * @author Dinh Van Huong
 */
if (!is_admin()) {
	add_filter('page_template', 'wphd_load_page_template');
	add_filter('single_template', 'wphd_load_page_template');
	add_action('wp_enqueue_scripts', 'wphd_plugin_unique_style', 99 );
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
		$active_question 	= is_plugin_active( 'spc-questionnaires/spc-questionnaires.php' );
		$active_thread 		= is_plugin_active( 'spc-threads/spc-threads.php' );
		$spc_option 		= get_option('spc_options');

	    if ( (isset($spc_option['notice_slug']) && !empty($spc_option['notice_slug']) && is_page($spc_option['notice_slug'])) || is_page( 'notice' ) ) {
	        $page_template = SPCV_CUSTOME_PLUGIN_DIR . 'views/page-notice.php';
	    }

	    if (is_single() && get_post_type() == 'question_post') {
	    	$page_template = SPCV_CUSTOME_PLUGIN_DIR . 'views/single-question_post.php';
	    }

	    if ( $active_thread && ((isset($spc_option['add_thread_slug']) && !empty($spc_option['add_thread_slug']) && is_page($spc_option['add_thread_slug'])) || is_page( 'add-thread' ))) {
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
if ( !function_exists( 'wphd_plugin_unique_style' )) {
	function wphd_plugin_unique_style()
	{
		$spc_option = get_option('spc_options');
		if (
			((isset($spc_option['notice_slug']) && !empty($spc_option['notice_slug']) && is_page($spc_option['notice_slug'])) || is_page( 'notice' )) || 
			(is_single() && get_post_type() == 'question_post')) 
		{
			wp_enqueue_style( 'font_awesome', SPCV_CUSTOME_PLUGIN_STYLE . 'font-awesome.min.css' );
			if( cf_is_mobile() && file_exists(SPCV_CUSTOME_PLUGIN_DIR . 'css/style_sp.css')) {
				wp_enqueue_style( 'style_sp', SPCV_CUSTOME_PLUGIN_STYLE . 'style_sp.css' );
			} elseif (file_exists(SPCV_CUSTOME_PLUGIN_DIR . 'css/style_pc.css')) {
				wp_enqueue_style( 'style_pc', SPCV_CUSTOME_PLUGIN_STYLE . 'style_pc.css' );
			}
		}
	}
}

/**
 * plugin install
 *
 * @author Dinh Van Huong
 */
register_activation_hook( __FILE__ , 'my_plugin_install');
if ( !function_exists( 'my_plugin_install' )) {
	function my_plugin_install() {
	    global $wpdb;

	    // your customize
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
if ( !function_exists( 'create_new_page' )) {
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
 * change comments template
 *
 * @author Dinh Van Huong
 */
add_filter( 'comments_template', function ( $template ) {
    if ( is_single() && get_post_type() == 'question_post' ) {
		include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/question_comments.php' );
	}
});

if ( !function_exists( 'spc_questionaire' )) {
	function spc_questionaire() 
	{

		/*
		 * Biến $label để chứa các text liên quan đến tên hiển thị của Post Type trong Admin
		 */
		$label = array(
			'name' => 'アンケート管理', //Tên post type dạng số nhiều
			'singular_name' => 'アンケート管理', //Tên post type dạng số ít
			'add_new' => '新規追加',
			'add_new_item' => '新規アンケートの作成',
			 'edit_item' => 'アンケートを編集する',
			 'new_item' => '新規サイト',
			 'all_items' => 'アンケート一覧',
			 'view_item' => 'アンケートの説明を見る',
			 'search_items' => '検索する',
			 'not_found' => 'アンケートが見つかりませんでした。',
			 'not_found_in_trash' => 'ゴミ箱内にアンケートが見つかりませんでした。'
		);

		/*
		 * Biến $args là những tham số quan trọng trong Post Type
		 */
		$args = array(
			'labels' => $label, //Gọi các label trong biến $label ở trên
			'description' => 'Create questionaire follow post', //Mô tả của post type
			'supports' => array(
					'title',
					'editor',
					'author',
					'thumbnail',
					'revisions'
			), //Các tính năng được hỗ trợ trong post type
			'taxonomies' => array( 'category' ), //Các taxonomy được phép sử dụng để phân loại nội dung , 'post_tag' 
			'hierarchical' => false, //Cho phép phân cấp, nếu là false thì post type này giống như Post, true thì giống như Page
			'public' => true, //Kích hoạt post type
			'show_ui' => true, //Hiển thị khung quản trị như Post/Page
			'show_in_menu' => true, //Hiển thị trên Admin Menu (tay trái)
			'show_in_nav_menus' => true, //Hiển thị trong Appearance -> Menus
			'show_in_admin_bar' => true, //Hiển thị trên thanh Admin bar màu đen.
			'menu_position' => 10, //Thứ tự vị trí hiển thị trong menu (tay trái)
			'menu_icon' => 'dashicons-testimonial', //Đường dẫn tới icon sẽ hiển thị
			'can_export' => true, //Có thể export nội dung bằng Tools -> Export
			'has_archive' => true, //Cho phép lưu trữ (month, date, year)
			'exclude_from_search' => false, //Loại bỏ khỏi kết quả tìm kiếm
			'publicly_queryable' => true, //Hiển thị các tham số trong query, phải đặt true
			'capability_type' => 'post'
		);

		register_post_type('question_post', $args); //Tạo post type với slug tên là questionaire và các tham số trong biến $args ở trên
	}
}
add_action( 'init', 'spc_questionaire' );

/**
* Change label menu, submenu
*/
if ( !function_exists( 'change_post_menu_label' )) {
	function change_post_menu_label() 
	{
		global $submenu;

		// echo "<pre>";print_r($submenu);echo "</pre>"; //Print menus and find out the index of your custom post type menu from it.
		$submenu['edit.php?post_type=question_post'][5][0] = 'アンケート一覧'; // Replace the 27 with your custom post type menu index from displayed above $menu array 
		$submenu['edit.php?post_type=question_post'][10][0] = 'アンケート作成'; 
	}
}
add_action( 'admin_menu', 'change_post_menu_label' );

/**
* Do not show category submenu in Questionnaires menu
*/
add_action( 'admin_menu', 'remove_wp_menu', 999 );
if ( !function_exists( 'remove_wp_menu' )) {
	function remove_wp_menu(){
		remove_submenu_page( 'edit.php?post_type=question_post', 'edit-tags.php?taxonomy=category&amp;post_type=question_post' );
		remove_submenu_page( 'edit.php?post_type=question_post','review' );
	}
}
/**
 Khai báo meta box
**/
if ( !function_exists( 'questionaire_meta_box' )) {
	function questionaire_meta_box()
	{
	 	add_meta_box( 'thong-tin', '新規アンケートの作成', 'questionaire_attr', 'question_post' );
	}
}
add_action( 'add_meta_boxes', 'questionaire_meta_box' );

/**
* 
*/

if ( !function_exists( 'questionaire_attr' )) {
	function questionaire_attr( $post )
	{
	 	include_once('templates/questionaire-attr.php');
	}
}

/**
 Lưu dữ liệu meta box khi nhập vào
 @param post_id là ID của post hiện tại
**/
if(isset($_POST)){
	function questionaire_attr_save( $post_id )
	{

		//Description form questionnaire
		if (isset($_POST['ques_description'])) {
			$ques_description = $_POST['ques_description'];
			update_post_meta( $post_id, '_question_description', $ques_description );
		}
		
		
		//List require profile of user comment
		if (isset($_POST['pro_require'])) {
			$pro_require =   $_POST['pro_require']  ;
			update_post_meta( $post_id, '_question_profile_require', $pro_require );
		}

		// type of question
		if (isset($_POST['question'])) {
			$question =   $_POST['question'];
			update_post_meta( $post_id, '_question_type', $question );
		}

		// limit of answer
		if (isset($_POST['limited_answer'])) {
			$limited_answer =   $_POST['limited_answer']  ;
			update_post_meta( $post_id, '_limited_answer', $limited_answer );
		}

		// sort question
		if (isset($_POST['_sort_question'])) {
			$_sort_question =   $_POST['_sort_question'];
			update_post_meta( $post_id, '_sort_question', $_sort_question );
		}
	}

	add_action( 'save_post', 'questionaire_attr_save' );
}

/**
* 
*/
if ( !function_exists( 'answer_attr' )) {
	function answer_attr( $post )
	{
		include_once('templates/answer-attr.php');
	}
}

/**
* update status comment
*/
if ( !function_exists( 'update_status_comment' )) {
	function update_status_comment()
	{
		$commentarr = array();
		$commentarr['comment_ID'] = $_POST['comment_ID'];
		$commentarr['comment_approved'] = $_POST['status']?0:1;
		$result = wp_update_comment( $commentarr );
		if($_POST['status'] == '1'){
				update_report_status_comment($_POST['comment_ID']);
		}
		wp_send_json(['success'=>$result]);
	}
}
add_action('wp_ajax_update_comment', 'update_status_comment');

/**
 * Change report status when unapprove comment
 * @author Hung Nguyen
 */
if ( !function_exists( 'update_report_status_comment' )) {
	function update_report_status_comment($comment_id){
		if ( function_exists ( 'wprc_table_name' ) ){
			global $wpdb;
			$table_name = wprc_table_name();
			$query = "UPDATE $table_name SET status='processed' WHERE comment_id = $comment_id";
			$wpdb->query($query);
		}
	}
}


/**
* export to file
*/
add_action( 'admin_post_exportcsv', 'csv_file' );
if ( !function_exists( 'csv_file' )) {
	function csv_file() {
		include_once('templates/csv.php'); 
	}
}


if ( !function_exists( 'report_link' )) {
	function report_link($actions, $page_object){
		if($page_object->post_type == 'question_post'){
			$actions['report_page'] = '<a href="'.admin_url( 'edit.php?post_type=question_post&page=review&post=' . $page_object->ID ).'">Report</a>';
			unset($actions['inline hide-if-no-js']);
		}
		return $actions;
	}
}
add_filter('post_row_actions', 'report_link', 10, 2);

add_action( 'admin_post_review', 'report_question' );
if ( !function_exists( 'report_question' )) {
	function report_question(){
		include_once('templates/exportcsv.php'); 
	}
}

add_action('admin_menu', 'test_plugin_setup_menu');
if ( !function_exists( 'test_plugin_setup_menu' )) {
	function test_plugin_setup_menu(){
		add_submenu_page( 'edit.php?post_type=question_post','アンケート詳細', 'アンケート詳細', 'manage_options', 'review', 'test_init' );
	}
}

if ( !function_exists( 'test_init' )) {
	function test_init(){
		include_once('templates/exportcsv.php'); 
		include_once('templates/answer-attr.php'); 
	}
}

/**
* update limited comment
*/
if ( !function_exists( 'update_status_limited_comment' )) {
	function update_status_limited_comment(){
		if(isset($_POST['post_ID'])){
			$post_id = $_POST['post_ID'];
			$status = $_POST['status'];
			$result = update_post_meta( $post_id,'_unpublish_answer', $status );
			wp_send_json(['success'=>$result,'status'=>$status]);
		}
	}
}
add_action('wp_ajax_limited_comment', 'update_status_limited_comment');

/**
* update status post
*/
if ( !function_exists( 'update_status_post' )) {
	function update_status_post(){
		if(isset($_POST['post_ID'])){
			$post_id = $_POST['post_ID'];
			$status = $_POST['status']=='publish'?'private':'publish';
			$close = $_POST['status']=='publish'?'open':'close';
			global $wpdb;
			$query = "UPDATE ".$wpdb->prefix."posts SET post_status='".$status."',comment_status='".$close."', ping_status='".$close."', post_modified= '".date("Y-m-d H:i:s")
	."'  WHERE ID = '".$post_id."'";
			$result = $wpdb->query($query);
			if($_POST['status']=='publish'){
					update_report_status_post($_POST['post_ID']);
			}
			wp_send_json(['success'=>$result,'status'=>$status]);
		}
	}
}
add_action('wp_ajax_post_status', 'update_status_post');

/**
 * Change status of report when unpublish questionnaire
 * @author Hung Nguyen
 */
if ( !function_exists( 'update_report_status_post' )) {
	function update_report_status_post( $post_id ) {
		if ( function_exists ( 'wprc_table_name' ) ){
			global $wpdb;
			$table_name = wprc_table_name();
			$query = "UPDATE $table_name SET status='processed' WHERE post_id = $post_id";

			$wpdb->query($query);
		}
	}
}


/**
 * get list comment id by post id
 *
 * @param int $post_id
 * @return array
 * @author Dinh Van Huong
 */
if ( !function_exists( 'get_list_report_comment_id_by_post_id' )) {
	function get_list_report_comment_id_by_post_id($post_id)
	{
		if ( function_exists ( 'wprc_get_post_reports' ) ) {
			$res = array();
			$results = wprc_get_post_reports($post_id);
			foreach ($results as $result) {
				$res[$result['comment_id']] = $result['comment_id'];
			}

			return $res;

		} else {
			return array();
		}
	}
}



/* custommize */

/*------------------------------------------
//ユーザーエージェント判定
------------------------------------------*/
if ( !function_exists( 'cf_is_mobile' )) {
	function cf_is_mobile() 
	{
		$cf_is_mobile = isset($_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER']) ? $_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] : null;
		$cf_is_tablet = isset($_SERVER['HTTP_CLOUD_FRONT_IS_TABLET_VIEWER']) ? $_SERVER['HTTP_CLOUD_FRONT_IS_TABLET_VIEWER'] : null;
		return wp_is_mobile() || $cf_is_mobile === 'true' || $cf_is_tablet === 'true';
	}
}

/*-------------------------------------------*/
/* パンくずリスト
/*-------------------------------------------*/
if ( !function_exists( 'breadcrumb' )) {
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

/*-------------------------------------------*/
/* ページネーション
/*-------------------------------------------*/
if ( !function_exists( 'pagination' )) {
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

/** Add menu */
add_action('admin_menu','spc_setting_menu');
if ( !function_exists( 'spc_setting_menu' )) {
	function spc_setting_menu() {
	    add_options_page('設定', 'MUGYUU!の設定', 'manage_options', 'spc_setting','spc_setting_options');
	}
}

if ( !function_exists( 'spc_setting_options' )) {
	function spc_setting_options() {
	    
	    // save MUGYUU! Options
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
	    include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/spc_setting.php' );
	}
}

/**
* Save questionnaire, Comment post 
* @author UNOTRUNG
*/
if ( !function_exists( 'add_comment_on_questions' )) {
	function add_comment_on_questions($post_id) {

	    if (isset( $_POST['submitted'] )) {
	        unset($_POST['submitted']);

	        $current_user = $_POST['name'];
	        $comment = $_POST['comment'];
	        $time = current_time('mysql');
	        $is_link = is_link_on_content($comment);
	        $comment = replace_duplicate_br_tag($comment);

	        $data = array(
	            'comment_post_ID' => $post_id,
	            'comment_author' => $current_user,
	            'comment_content' => $comment,
	            'comment_date' => $time,
	            'comment_approved' => ($is_link) ? 0 : 1
	        );
	        $count_comment =  wp_count_comments( $post_id );
	        $limited = get_post_meta( $post_id, '_limited_answer', true );
	        $orders = get_post_meta($post_id, '_question_comment_no', true);

	        if(($count_comment->approved < $limited && $limited > 0) || empty($limited)){
	            $comment_id = wp_insert_comment($data);

	            // insert comment number for each comment.
	            $orders[$comment_id] = ($orders) ? max($orders) + 1 : 1;
	            update_post_meta($post_id, '_question_comment_no', $orders);

	            add_comment_meta( $comment_id, '_question_comment', $_POST['answer'] );
	            add_comment_meta( $comment_id, '_question_comment_profile', $_POST['profile'] );
	        }

	        // wp_redirect( get_post_permalink($post_id) );
	        // exit;

	        echo '<script type="text/javascript">window.location.href = "'. get_post_permalink($post_id) .'";</script>';
			exit;
	    }
	}
}

if ( !function_exists( 'question_comment' )) {
	function question_comment($comment, $args, $depth) {
	    $GLOBALS['comment'] = $comment;
	    global $questions, $comment_no;

	    $list_no = array('①','②','③','④','⑤','⑥','⑦','⑧','⑨','⑩','⑪','⑫','⑬','⑭','⑮','⑯','⑰','⑱','⑲','⑳','㉑','㉒','㉓','㉔','㉕',
	            '㉖','㉗','㉘','㉙','㉚','㉛','㉜','㉝','㉞','㉟','㊱','㊲','㊳','㊴','㊵','㊶','㊷','㊸','㊹','㊺','㊻','㊼','㊽','㊾','㊿');
	    $user_profile = get_comment_meta($comment->comment_ID,'_question_comment_profile',true);
	?>
	        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	            <div id="comment-<?php comment_ID(); ?>" class="commentData">
	                <p class="data">
	                    <?php echo ($comment_no && array_key_exists($comment->comment_ID, $comment_no)) ? $comment_no[$comment->comment_ID] . '.' : ''; ?>
	                    <?php echo get_comment_date(('Y/m/d')); ?>
	                    <?php printf(__('%s'), get_comment_author_link()); ?>
	                </p>
	                <?php if(!ip_report_comment(get_comment_ID(), get_user_IP())): ?>
	                    <div class="report modal">
	                        <input id="modal-trigger-1" type="checkbox">
	                        <label for="modal-trigger-1">
	                		<?php
		                		if (is_plugin_active( 'report-content/report-content.php' )) {
	                                wprc_report_submission_form();
	                            }
		                	?>
	                        </label>
	                        <div class="modal-overlay">
	                            <div class="modal-wrap">
	                                <label for="modal-trigger-1">✖</label>
	                                <h3>このコメントを通報</h3>
	                                <p>このコメントを削除すべき不適切なコメントとして通報しますか？</p>
	                                <div class="btnArea">
	                                    <button type="button" class="reportBtn">通報
	                                    </button>
	                                    <button type="button" class="cancelBtn">やめる
	                                    </button>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                <?php endif; ?>
	                <?php if($user_profile): ?>
	                <div class="user_comment_info">
	                    <?php if (strlen($user_profile['baby_year']) > 0 || strlen($user_profile['baby_month']) > 0 || strlen($user_profile['baby_sex']) > 0) : ?>
	            		<div class="user-comment-info <?php echo $user_profile['baby_sex']; ?> <?php if($user_profile['baby_sex']=='male'){ 
	                                          echo 'user_comment_info_boy';
	            		                  }elseif($user_profile['baby_sex']=='female'){
	                                          echo 'user_comment_info_girl';
	                                      }
	                                ?> ">
	            			<label>
	                            <?php 
	                                if (strlen($user_profile['baby_year']) > 0) {
	                                    echo $user_profile['baby_year'] . '歳';
	                                }

	                                if (strlen($user_profile['baby_month']) > 0) {
	                                    echo $user_profile['baby_month'] . 'ヶ月';
	                                }
	                            ?>
	                            &nbsp;
	                        </label>
	            		</div>
	                    <?php endif;?>
	                    <?php if (strlen($user_profile['parent']) > 0 || strlen($user_profile['parent_age']) > 0) : ?>
	            		<div class="user-comment-info <?php echo $user_profile['parent']; ?> <?php if($user_profile['parent']=='mother'){
	                            		    echo 'user_comment_info_mother';
	                            		}elseif($user_profile['parent']=='father'){
	                            		    echo 'user_comment_info_father';
	                            		}
	            		            ?> ">
	            			<label>
	                        <?php if (strlen($user_profile['parent_age']) > 0) : ?>
	                        <?php echo $user_profile['parent_age']; ?>歳
	                        <?php endif; ?>
	                        &nbsp;
	                        </label>
	            		</div>
	                    <?php endif;?>

	                    <?php if (strlen($user_profile['style']) > 0) : ?>
	                        <div class="user-comment-info style">
	                            <label><?php echo $user_profile['style']; ?></label>
	                        </div>
	                    <?php endif;?>

	                </div>
	                <?php endif; ?>
	            </div>
	            <div class="userCommentArea answerArea">
	                    <?php 
	                        $_sort_question = get_post_meta($comment->comment_post_ID,'_sort_question',true);
	                        $answers = get_comment_meta($comment->comment_ID,'_question_comment',true);
	                        $GLOBALS['answers'] = $answers;

	                        if($answers){
	                            echo '<ul class="answerList">';
	                            foreach ($_sort_question as $ksort => $vsort) {
	                                foreach ($answers as $queskey => $answer) {
	                                    if($queskey == $vsort){
	                                        echo "<li>".$list_no[$ksort];
	                                        foreach ($answer as $key_ans => $las_ans) {
	                                            if(is_array($las_ans) && $key_ans == 'unit'){
	                                                $list_unit = $questions[key($questions)][$queskey]['answer'];
	                                                $answer_string = '';

	                                                if (strlen($las_ans[0]) > 0) {
	                                                    $answer_string .= $las_ans[0] . $list_unit[0];
	                                                }

	                                                if (strlen($las_ans[1]) > 0) {
	                                                    if (strlen($las_ans[0]) > 0) {
	                                                        $answer_string .= ' ' . $las_ans[1] . $list_unit[1];
	                                                    } else {
	                                                        $answer_string .= $las_ans[1] . $list_unit[1];
	                                                    }
	                                                }

	                                                ?>
	                                                	<label><?= $answer_string; ?></label>
	                                                <?php
	                                            }else{
	                                            ?>
	                                            	<label class="<?php echo (isset($answer) && count($answer)>1) ? 'check' : ''; ?>"><?php echo (isset($questions[key($questions)][$queskey]['answer'][$las_ans]) && $questions[key($questions)][$queskey]['answer'][$las_ans] != '') ? $questions[key($questions)][$queskey]['answer'][$las_ans] : $las_ans; ?></label>
	                                            <?php
	                                            }
	                                        }
	                                    }
	                                }
	                            }

	                            echo '</ul>';
	                        }
	                    ?>
	                <div class="comment">
	                    <?php comment_text(); ?>
	                </div>
	            </div>
	        </li>
	    <?php
	}
}

/**
 * is_link_on_content
 *
 * @param string $content
 * @return boolean
 * @author Dinh Van Huong
 */
if ( !function_exists( 'is_link_on_content' )) {
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
 * replace duplicate <br> tag
 * @param string $content
 * @return string
 * @author Dinh Van Huong
 */
if ( !function_exists( 'replace_duplicate_br_tag' )) {
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
 * Check if IP have reported comment
 * @author Hung Nguyen
 */
if ( !function_exists( 'ip_report_comment' )) {
	 function ip_report_comment($comment_id, $ip){
	    global $wpdb;

	    $query = $wpdb->prepare( "SELECT COUNT(*) FROM ". $wpdb->prefix ."contentreports WHERE comment_id=%d AND reporter_ip=%s;", $comment_id, $ip );
	    $count_ip = $wpdb->get_var( $query );
	     
	    if ($count_ip >0 ) {
	     	return true;
	    }
	    return false;
	 }
}

 /**
 * Get user IP for check report
 * @author Hung Nguyen
 */
if ( !function_exists( 'get_user_IP' )) {
	function get_user_IP() {
	    $client = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote = $_SERVER['REMOTE_ADDR'];

		if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
			$ip = $client;
		} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}

		return $ip;
	}
}

/**
 * Sort list comment by like count
 * @author Hung Nguyen
 */
if ( !function_exists( 'comment_compare_like_count' )) {
	function comment_compare_like_count($a, $b)
	{
	    $compared = 0;
	    $a_count = (get_comment_meta( $a->comment_ID, 'cld_like_count', true ))?get_comment_meta( $a->comment_ID, 'cld_like_count', true ):0;
	    $b_count = (get_comment_meta( $b->comment_ID, 'cld_like_count', true ))?get_comment_meta( $b->comment_ID, 'cld_like_count', true ):0;
	    
	    if ($a_count != $b_count) {
	        $compared = $a_count > $b_count ? -1:1;
	    }
	    return $compared;
	}
}

/**
 * Sort list comment by old date
 * @author Hung Nguyen
 */
if ( !function_exists( 'comment_compare_old' )) {
	function comment_compare_old($a, $b)
	{
	    $compared = 0;
	    $a_date = $a->comment_date_gmt;
	    $b_date = $b->comment_date_gmt;
	    
	    if ($a_date != $b_date) {
	        $compared = $a_date > $b_date ? 1:-1;
	    }
	    return $compared;
	}
}

/**
 * Sort list comment by new date
 * @author Hung Nguyen
 */
if ( !function_exists( 'comment_compare_new' )) {
	function comment_compare_new($a, $b)
	{
	    $compared = 0;
	    $a_date = $a->comment_date_gmt;
	    $b_date = $b->comment_date_gmt;
	    
	    if ($a_date != $b_date) {
	        $compared = $a_date > $b_date ? -1:1;
	    }
	    return $compared;
	}
}

/**
 * Function for call ajax upload image when add thread on front
 * @author Hung Nguyen
 */
if ( !function_exists( 'upload_image_thread' )) {
	function upload_image_thread() {
	    $file = 'content_image';
	    $attach_id = media_handle_upload( $file );
	    $post_image = get_post($attach_id);
	    $image_link = $post_image->guid;
	    $image_title = $post_image->post_title;
	    $return = array(
	        'status' => 'OK',
	        'id'    => $attach_id,
	        'image_link' => $image_link,
	        'image_title' => $image_title
	    );
	    wp_send_json($return);
	}
}

add_action('wp_ajax_upload_image_thread', 'upload_image_thread');
add_action('wp_ajax_nopriv_upload_image_thread', 'upload_image_thread');

/**
 * Function for call ajax change category
 * @author Hung Nguyen
 */
if ( !function_exists( 'thread_change_category' )) {
	function thread_change_category() {
	    $id = $_POST['id'];
	    if ($id > 0) {
	        $child_categories = get_categories( array( 'parent' => $id, 'hide_empty'=>false ) );
	    } else {
	        $child_categories = [];
	    }
	    wp_send_json($child_categories);
	}
}
add_action('wp_ajax_thread_change_category', 'thread_change_category');
add_action('wp_ajax_nopriv_thread_change_category', 'thread_change_category');


/*---------------------------------------------------------------*/
/* WordPressの投稿作成画面で必須項目を作る（空欄ならJavaScriptのアラート）
/*---------------------------------------------------------------*/
// add_action( 'admin_head-post-new.php', 'mytheme_post_edit_required' ); // 新規投稿画面でフック
// add_action( 'admin_head-post.php', 'mytheme_post_edit_required' );     // 投稿編集画面でフック
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

/**
 * check is japanese or not
 * @param string $words
 * @return boolean
 * @author Dinh Van Huong
 */
if ( !function_exists( 'isJapanese' )) {
	function isJapanese($words) 
	{
	    return preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $words);
	}
}

/**
 * make random slug post
 * @return string
 * @author Dinh Van Huong
 */
if ( !function_exists( 'make_slug_random' )) {
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
 * Change report status when unapprove comment
 * @author Hung Nguyen
 */
add_action('transition_comment_status', 'unapprove_comment_callback', 10, 3);
if ( !function_exists( 'unapprove_comment_callback' )) {
	function unapprove_comment_callback($new_status, $old_status, $comment) {
	    if ( $old_status == 'approved'  &&  $new_status != 'approved' ) {
	        if ( function_exists ( 'wprc_table_name' ) ){
	            global $wpdb;
	            $table_name = wprc_table_name();
	            $query = "UPDATE $table_name SET status='processed' WHERE comment_id = $comment->comment_ID";
	            
	            $wpdb->query($query);
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
if ( !function_exists( 'wphd_rewrite_slug_before_save' )) {
	function wphd_rewrite_slug_before_save($post_id)
	{
	    if (isset($_POST['post_type']) && in_array($_POST['post_type'], array('thread_post', 'question_post'))) {
	        
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
 * Change status of report when unpublish thread
 * @author Hung Nguyen
 */
add_action( 'transition_post_status', 'post_unpublished', 10, 3 );
if ( !function_exists( 'post_unpublished' )) {
	function post_unpublished( $new_status, $old_status, $post ) {
	    if($post->post_type == 'thread_post' || $post->post_type == 'question_post')
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

/* end custommize */