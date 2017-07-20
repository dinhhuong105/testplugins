<?php get_header();?>
	<div id="breadcrumb">
		<ul class="breadcrumbList">
			<li><a href="<?php echo home_url('/'); ?>">トップ</a></li>
			<li><i class="fa fa-angle-right arrowIcon"></i><a href="<?php echo home_url('/'); ?>notice"><span>質問掲示板</span></a></li>
			<li class="fixPage"><i class="fa fa-angle-right arrowIcon"></i><span>新規スレッド作成</span></li>
		</ul>
	</div>
    <div class="mainWrap single addthread">
        <div class="mainArea">
        <form id="threadAddForm" method="POST" enctype="multipart/form-data">
        	<section class="threadFormArea inputForm">
                <h1 class="heading">新規スレッド作成</h1>
                <div class="formArea">
                        <ul class="threadList">
                            <li>
                                <div class="imgArea">
                                    <img src="<?php echo WPHD_THREAD_PLUGIN_URI; ?>images/noimage.png" id="no_image" alt="画像" width="130" height="130">
                                    <label class="imgBtn">
                                        <i class="fa fa-camera" aria-hidden="true"></i>画像を選択
                                        <input id="thread_thumb" name="thread_thumb" type="file">
                                    </label>
                                </div>
                            </li>
                            <li>
                                <div class="ttl">
                                    <input type="text" name="thread_title" id="thread_title" required placeholder="スレッドタイトルを入力してください">
                                </div>
                                <div class="content" id="contentArea">
                            		<div class="textArea">
                                        <div id="textareaEditor" contenteditable></div>
                                    </div>
                                	<label class="imgBtn">
                                        <i class="fa fa-camera" aria-hidden="true"></i>画像を選択
                                        <input type="file" id="content_image" name="content_image">
                                    </label>
                                </div>
                            </li>
                        </ul>
                        <!--<ul class="answerInpotList">
                            <li>
                                <div class="checkArea">
                                    <label for="anonymous">
                                        <input style="-webkit-appearance: checkbox;" type="checkbox" id="anonymous" name="anonymous">匿名を投稿
                                    </label>
                                    <label for="displayID">
                                        <input style="-webkit-appearance: checkbox;" type="checkbox" id="displayID" name="displayID">IDを表示してなりまし防止
                                    </label>
                                </div>
                            </li>
                        </ul>-->
                        <!--<ul class="categoryArea">
                            <li>
                                <label for="select" class="selectArea">
                                <select name="parent_cat" id="parent_cat"> 
                                    <option value="0">親カテゴリー</option> 
                                    <?php 
                                    $categories = get_categories( array( 'parent' => 0, 'hide_empty'=>false ) );
                                    foreach ( $categories as $category ) {
                                        printf( '<option value="%1$s">%2$s</option>',
                                            esc_attr( $category->term_id ),
                                            esc_html( $category->cat_name )
                                        );
                                    }
                                    ?>
                                </select>
                                <select name="child_cat" id="child_cat">
                                    <option value="0">子カテゴリー</option>
                                </select>
                                <select name="grandchild_cat" id="grandchild_cat" class="cd-select">
                                    <option value="0">孫カテゴリー</option>
                                </select>
                                </label>
                            </li>
                        </ul>-->
                        <textarea name="thread_content" id="thread_content" class="textareaCustom"></textarea>
                        <input type="hidden" name="action" value="add_thread_front" />
                        <input type="hidden" name="submitted" id="submitted" value="true" />
                        <button id="preview" type="submit" name="action" value="send" class="sendBtn">投稿内容を確認</button>
                    
                </div>
            </section>
            <section class="threadFormArea confirm">
                <h1 class="heading">新規スレッド作成</h1>
                <div class="formArea">
                    <ul class="threadList">
                            <li>
                                <div class="imgArea">
                                    <img src="" id="confirm_no_image" alt="画像" width="130" height="130">
                                </div>
                            </li>
                            <li>
                                <div class="ttl">
                                    <label id="confirm_thread_title">title</label>
                                </div>
                                <div class="content" id="contentArea">
                                    <div class="confirm_textArea">
                                        <label id="confirm_thread_content">message</label>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    <div class="row" align="center">
                        
                        <button id="backBtn" class="sendBtn">編集画面に戻る</button>
                        <button type="submit" name="action" value="send" class="sendBtn" id="submitBtn">スレッドを作成する</button>
                    </div>
                </div>
            </section>
            </form>
            <section class="threadFormArea addthread-result">
                <div class="formArea" align="center" id="result-message"><h1>投稿完了しました</h1>
                    <?php
                        $spc_option = get_option('spc_options');
                        if ( isset($spc_option['notice_slug']) && !empty($spc_option['notice_slug'])) {
                            $red_link = home_url() .'/'. $spc_option['notice_slug'];
                        } else {
                            $red_link = home_url() . '/notice';
                        }
                    ?>
                    <a href="<?php echo $red_link; ?>" class="sendBtn">質問掲示板に戻る</a>
                </div>
            </section>
        </div>
		<?php get_sidebar(); ?>
    </div>
<?php get_footer(); ?>
<script>
	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
	var max_upload_picture = "<?php echo get_option('spc_options')['thread_img_no']; ?>";
</script>
<script src="<?php echo WPHD_THREAD_PLUGIN_URI; ?>views/js/notice-board.js"></script>