<?php
    $post = get_post();
    $GLOBALS['thread_no'] = get_post_meta($post->ID, '_thread_comment_no', true);
?>
<section class="commentArea">
    <?php $comment_arr = array(); ?>
	<?php if(have_comments()): ?>
    <?php $comment_order_default = get_option('comment_order'); ?>
	<label for="qaSort" class="sortWrap">
		<select id="qaSort" name="qaSort" class="sort">
			<option value="old" <?php if((isset($_GET['comment_order_by']) && $_GET['comment_order_by'] == 'old') || (!isset($_GET['comment_order_by']) && $comment_order_default != 'desc')) echo 'selected' ?>>古い順</option>
			<option value="new" <?php if((isset($_GET['comment_order_by']) && $_GET['comment_order_by'] == 'new') || (!isset($_GET['comment_order_by']) && $comment_order_default == 'desc')) echo 'selected' ?>>新着順</option>
			<option value="like_count" <?php if(isset($_GET['comment_order_by']) && $_GET['comment_order_by'] == 'like_count') echo 'selected' ?>>共感順</option>
		</select>
	</label>
	<?php 
    	$page = intval( get_query_var( 'cpage' ) );
        if ( 0 == $page ) {
            $page = 1;
            set_query_var( 'cpage', $page );
        }
        
        $comments_per_page = get_option( 'comments_per_page' );
        $comment_arr = get_comments( array( 'status' => 'approve', 'post_id' => $post->ID, 'order' => $comment_order_default ) );
    ?>
   　<ul class="commentList">
	   <?php
	       if(isset($_GET['comment_order_by'])){
	           if($_GET['comment_order_by'] == 'like_count'){
                   usort($comment_arr, 'comment_compare_like_count');
	           }else{
	               if($_GET['comment_order_by'] == 'old'){
	                   usort($comment_arr, 'comment_compare_old');
    	           }else{
    	               usort($comment_arr, 'comment_compare_new');
        	       }
	           }
	       }
	       
	       wp_list_comments( array (
	               'per_page'      => $comments_per_page,
	               'page'          => $page,
	               'reverse_top_level' => false,
	               'callback'      => 'noticetheme_comment'
	       ), $comment_arr );
	   ?>
	</ul>
	 <?php endif; ?>
	 <?php
     global $wp_query;
     if ($comment_arr) {
        $wp_query->comments = $comment_arr;
         if(get_comment_pages_count($comment_arr,$comments_per_page, true) > 1){
             echo '<div style="margin-top:15px; text-align:center;" class="notice_pagination">';
             //ページナビゲーションの表示
             paginate_comments_links([
                'next_text'    => __('›'),
                'prev_text'    => __('‹')
                ]);
             echo '</div>';
         }
     }
     ?>
</section>
<section class="commentFormArea" id="send">
	<div class="commentFormWrap">
    	<div class="ttlArea">
            <h2>コメントを投稿する</h2>
            <p>
                <span class="red">※</span>は必須項目になります。
            </p>
        </div>
    	<form action="" id="formComment" method="POST">
            <ul>
                <li>
                    <h3>ニックネーム<span class="red">※</span></h3>
                    <input type="text" name="name" required placeholder="ニックネームを入力してください">
                </li>
                <li>
                    <h3>コメント<span class="red">※</span></h3>
                    <p>ご自身の状況や良かった点、困った点などを具体的に書きましょう！
                        育児で困ってる方の参考になり共感ボタンをもらいやすくなります！
                        説明が難しい場合は画像などもあるとわかりやすいです。</p>
                    <div class="textArea" id="contentArea">
                        <div id="textareaEditor" contenteditable></div>
                        <label class="imgBtn">
                            <i class="fa fa-camera" aria-hidden="true"></i>画像を選択する
                            <input type="file" id="content_image" name="content_image">
                        </label>
                    </div>
                </li>
                <li>
                    <textarea name="comment" id="thread_content" class="textareaCustom"></textarea>
                	<input type="hidden" name="submitted" id="submitted" value="true" />
                    <button type="submit" name="action" value="send" class="sendBtn">コメントを投稿</button>
                </li>
            </ul>
        </form>
	</div>
</section>
<script>
	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
	var max_upload_picture = "<?php echo get_option('spc_options')['less_img_no']; ?>";

	jQuery('#qaSort').on("change", function(e){
		var target = jQuery(this);
		var current_link = window.location.origin + window.location.pathname;
		window.location = current_link + '?comment_order_by=' + target.val();
    });
</script>
<script src="<?php echo WPHD_THREAD_PLUGIN_URI; ?>views/js/notice-board.js"></script>
<?php add_comment_on_notice(get_the_ID()) ?>