<?php get_header();?>
	<div id="breadcrumb">
		<ul class="breadcrumbList">
			<li><a href="<?php echo home_url('/'); ?>">トップ</a></li>
            <?php $spc_option = get_option('spc_options'); ?>
            <?php $add_thread_link = (isset($spc_option['notice_slug']) && !empty($spc_option['notice_slug']) && is_page($spc_option['add_thread_slug'])) ? $spc_option['notice_slug'] : 'notice'; ?>
			<li><i class="fa fa-angle-right arrowIcon"></i><a href="<?php echo home_url('/') . $add_thread_link; ?>"><span>質問掲示板</span></a></li>
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
    var max_upload_picture   = "<?php echo get_option('spc_options')['thread_img_no']; ?>";
	<?php $spc_options = get_option('spc_options'); ?>
    var max_upload_file_size = "<?php echo (isset($spc_options['upload_max_filesize']) && !empty($spc_options['upload_max_filesize'])) ? $spc_options['upload_max_filesize'] : (int) ini_get('post_max_size') ; ?>";
</script>
<script src="<?php echo WPHD_THREAD_PLUGIN_URI; ?>views/js/notice-board.js"></script>