<?php

if (!class_exists('WPRC_List_Table')) {
	require_once('class-wprc-list-table.php');
}

class WPRC_Table extends WPRC_List_Table
{

	function __construct()
	{
		global $status, $page;

		global $wpdb;
		$this->table = $wpdb->prefix . "contentreports";

		//Set parent defaults
		parent::__construct(array(
			'singular' => 'report',     //singular name of the listed records
			'plural'   => 'reports',    //plural name of the listed records
			'ajax'     => false        //does this table support ajax?
		));
	}

	function column_default($item, $column_name)
	{
		return $item[ $column_name ];
	}

	function column_reporter($item)
	{
		return $item['reporter_name'] . '<br/><a href="mailto:' . $item['reporter_email'] . '">' . $item['reporter_email'] . '</a>';
	}

	function column_post($item)
	{
		define('DEFAULT_COMMENTS_PER_PAGE',20);
		$comment = ($item['comment_id']>0)?'&comment_id_scroll='.$item['comment_id']:'';
	    $post = get_post($item['post_id']);

		if (is_a($post, 'WP_Post')){
			if(get_post_type($post->ID) == 'question_post'){
			    $total_comments = get_comments(
		            array(
	                    'orderby' => 'comment_date_gmt',
			            'order' => 'ASC',
			            'post_id'=>$post->ID,
			            'parent'=>0
		            )
	            );
			    $per_page = get_option( 'comments_per_page' );
		        $position = 0;
		        foreach($total_comments as $comments){
		            $position ++;
		            if($comments->comment_ID == $item['comment_id']){
		                break;
		            }
		        }
		        $page_scroll = ($position>DEFAULT_COMMENTS_PER_PAGE)?'&paged='.ceil($position/DEFAULT_COMMENTS_PER_PAGE):'';
		        
			    return '<a href="' . get_admin_url(). 'edit.php?post_type=question_post&page=review&post='. $post->ID . $comment . $page_scroll .'">確認する</a>';
			}else{
		        return '<a href="' . get_edit_post_link($post->ID) . $comment . '">確認する</a>';
			}
		}

		return 'Post Not Found';
	}

	function column_status($item)
	{
		if($item['status'] == 'processed'){
		    return '<span>対応完了</span>';
		}else{
		    return '<button class="action-done" data-id='. $item['id'] .'>済</button>';
		}
	}
	
	function column_type($item)
	{
	    if($item['report_type'] == 'thread_post'){
	        return '<span>掲示板</span>';
	    }elseif($item['report_type'] == 'question_post'){
	        return '<span>アンケート</span>';
	    }else{
	        return '<span>コメント</span>';
	    }
	}
	
	function column_title($item)
	{
        return '<span>'.get_the_title($item['post_id']).'</span>';
	}

	function column_content($item)
	{
		if (!empty($item['comment_id'])) {
			$comment_text = get_comment_text($item['comment_id']);
		} else {
			$comment_text = get_post_field('post_content', $item['post_id']);
		}

		$content = '<div class="comment-text-wrapper">';
		if (strlen($comment_text) > 100) {
			$content .= '<div class="small">'. $comment_text .'</div>';
			$content .= '<a href="#" style="line-height: 25px;">もっと見る</a>';
		} else {
			$content .= '<div class="full-text">'. $comment_text .'</div>';
		}
		$content .= '</div>';
					
		return $content;
	}

	function column_nickname($item)
	{
		$post = get_post($item['post_id']);

		if (!empty($item['comment_id'])) {
			$comment_text = get_comment_author($item['comment_id']);
		} else {
			$comment_text = get_the_author_meta('display_name', $post->post_author);
		}

		return '<span>'. $comment_text .'</span>';
	}

	function column_rep_no($item)
	{
		if ($item['report_type'] == 'thread_post') {
			return;
		}

		if ($item['report_type'] == 'question_post') {
			$comment_no = get_post_meta($item['post_id'], '_question_comment_no', true);
			return (!empty($comment_no) && isset($comment_no[$item['comment_id']])) ? 'No.' . $comment_no[$item['comment_id']] : '';
		}

		if ($item['report_type'] == 'comment') {
			$comment_no = get_post_meta($item['post_id'], '_thread_comment_no', true);
			return (!empty($comment_no) && isset($comment_no[$item['comment_id']])) ? 'No.' . $comment_no[$item['comment_id']] : '';
		}

		return;
	}

	function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/
			$this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/
			$item['id']                //The value of the checkbox should be the record's id
		);
	}

	function get_bulk_actions()
	{
		$permission_options = get_option('wprc_permissions_settings');
		$bulk_action_permission = (isset($permission_options['minimum_role_change'])) ? $permission_options['minimum_role_change'] : 'activate_plugins';
		if (!current_user_can($bulk_action_permission))
			return array();

		$actions = array(
			'comment'        => 'Comment',
			'thread_post'    => 'Notice Board',
	        'question_post'  => 'Questionnaire'
		);
		return $actions;
	}

	function prepare_items()
	{
		global $wpdb; //This is used only if making any database queries
		// check setting option
		$spc_option = get_option('spc_options');
		if(isset($spc_option['report_no']) && $spc_option['report_no'] > 0){
		    $per_page = $spc_option['report_no'];
		}else{
		  $per_page = 10;
	    }

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();
		
		// Search by conditions
		$where = '';
		if (!empty($_REQUEST['status']) && $_REQUEST['status'] == 1) {
		    $where .= ' AND status = "new"';
		}
		
		if (!empty($_REQUEST['report_type'])) {
		    $where .= ' AND report_type = "'. $_REQUEST['report_type'] . '"';
		}

		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'time'; //If no sort, default to title
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
		$query = "SELECT * FROM $this->table WHERE id>0 $where ORDER BY $orderby $order";
		$data = $wpdb->get_results($query, ARRAY_A);

		$data = array_reverse($data);

		$stt = 1;
		foreach ($data as $data_key => $data_value) {
			$data[$data_key] = $data_value;
			$data[$data_key]['rep_no'] = 0;
			if ($data_value['report_type'] != 'thread_post') {
				$data[$data_key]['rep_no'] = $stt;
				$stt++;	
			}
		}

		$data = array_reverse($data);

		$current_page = $this->get_pagenum();

		$total_items = count($data);

		$data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

		$this->items = $data;

		$this->set_pagination_args(array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
		));
	}

	function get_columns()
	{
		$columns = array(
			// Update 2017.04.08 Hung Nguyen start
			// Change table in admin
			//'cb'       => '<input type="checkbox" />', //Render a checkbox instead of text
			'time'     => '受付日時',
			'type'     => '通報ジャンル',
			'rep_no'   => 'コメントNo',
	        'nickname' => 'ニックネーム',
	        'title'    => '投稿タイトル',
			'content'  => '内容',
			'post'     => '確認',
	        'status'   => '処理',
		);
		return $columns;
	}

	function get_sortable_columns()
	{
		$sortable_columns = array(
			'time' => array('time', true),     //true means it's already sorted
	        'type' => array('report_type', true)
		);
		return $sortable_columns;
	}

	function process_bulk_action()
	{
		global $wpdb;
		//Detect when a bulk action is being triggered...
		if ('delete' === $this->current_action()) {
			$id_string = join(',', $_GET['report']);
			$query = "DELETE FROM $this->table WHERE id IN ($id_string)";
			$wpdb->query($query);
		}

		if ('change_status' === $this->current_action() && is_array($_GET['report'])) {
			$id_string = join(',', $_GET['report']);
			$query = "UPDATE $this->table SET status='old' WHERE id IN ($id_string)";
			$wpdb->query($query);
		}

	}

}