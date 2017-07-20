<?php 
    $post = get_post();
    $description = get_post_meta( $post->ID, '_question_description', true );
    $limited = get_post_meta( $post->ID, '_limited_answer', true );
    $unpublish = get_post_meta( $post->ID, '_unpublish_answer', true );
    $questions = get_post_meta( $post->ID, '_question_type', true );
    $profile_require = get_post_meta($post->ID, '_question_profile_require', TRUE);
    $GLOBALS['questions'] = $questions; 
    $count_comment = wp_count_comments($post->ID);
    $list_tyles = array('慎重', '普通', 'お気楽');
    $GLOBALS['comment_no'] = get_post_meta($post->ID, '_question_comment_no', true);

    //check avalible for button submit comment form
    $boolAvalible = false;

    if (isset($unpublish) && $unpublish == false) {
        if( ($count_comment->approved < $limited && $limited > 0) || (strlen($limited) < 1) ){
            $boolAvalible = true;
        }
    }
?>
<section class="commentArea">
    <?php $comment_arr = array(); ?>
    <?php if(have_comments()): ?>
    <?php $comment_order_default = get_option('comment_order'); ?>
    <div class="question_filter">
        <div class="icon_search">
        	<i class="fa fa-search" aria-hidden="true"></i> 絞り込む
        </div>
        <label for="qaFilter" class="sortWrap lbFilter">
            <select id="qaFilter" name="qaFilter" class="sort">
                <option value="" >口コミ時のアンケート項目の内容</option>
            <?php foreach ($questions[$post->ID] as $qkey => $question) { 
                if($question['type'] != 'textarea' && $question['type'] != 'textbox' && $question['type'] != 'unit'){
                    $str = mb_strlen($question['question'])<16?$question['question']:mb_substr($question['question'],0,12)."...";
                    echo '<optgroup label="'.$str.'">';
                    foreach ($question['answer'] as $anskey => $ansval) {
                        $ansKeys = $qkey.','.$anskey;
                ?>
                    <option value="<?=$ansKeys?>" <?=(isset($_GET['comment_filter_by']) && $_GET['comment_filter_by'] == $ansKeys)?'selected':''?> >┗ <?=mb_strlen($ansval)<10?$ansval:mb_substr($ansval,0,10)."..."?></option>
            <?php } 
            echo '</optgroup>';
                }
            } ?>
            </select>
        </label>
    </div>
    <div class="row">
        <label for="qaSort" class="sortWrap">
            <select id="qaSort" name="qaSort" class="sort">
                <option value="old" <?php if((isset($_GET['comment_order_by']) && $_GET['comment_order_by'] == 'old') || (!isset($_GET['comment_order_by']) && $comment_order_default != 'desc')) echo 'selected' ?>>古い順</option>
                <option value="new" <?php if((isset($_GET['comment_order_by']) && $_GET['comment_order_by'] == 'new') || (!isset($_GET['comment_order_by']) && $comment_order_default == 'desc')) echo 'selected' ?>>新着順</option>
                <option value="like_count" <?php if(isset($_GET['comment_order_by']) && $_GET['comment_order_by'] == 'like_count') echo 'selected' ?>>共感順</option>
            </select>
        </label>

    </div>

	<ul class="commentList">
       <?php 
       $page = intval( get_query_var( 'cpage' ) );
        if ( 0 == $page ) {
            $page = 1;
            set_query_var( 'cpage', $page );
        }
        
        $comments_per_page = get_option( 'comments_per_page' );
        $comment_arr = get_comments( array( 'status' => 'approve', 'post_id' => $post->ID, 'order' => $comment_order_default ) );

        if(isset($_GET['comment_filter_by'])){
            $param = explode(',',$_GET['comment_filter_by']);
            $comment_filter = array();
            foreach ($comment_arr as $comment) {
                $comment_meta = get_comment_meta($comment->comment_ID,'_question_comment',true);
                if(@array_key_exists($param[0],$comment_meta)){
                    if(in_array($param[1],$comment_meta[$param[0]])){
                        array_push($comment_filter,$comment);
                    }
                }
            }
            $comment_arr = $comment_filter;
        }

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
               'callback'      => 'question_comment'
       ), $comment_arr );
       ?>
    </ul>
     <?php endif; ?>
     <?php
     global $wp_query;
     if ($comment_arr) {
        $wp_query->comments = $comment_arr;
         if(get_comment_pages_count($comment_arr,$comments_per_page, true) > 1){
             echo '<div style="margin-top:20px; text-align:center;" class="notice_pagination">';
             //ページナビゲーションの表示
             paginate_comments_links([
                'next_text'    => __('<i class="fa fa-angle-right"></i>'),
                'prev_text'    => __('<i class="fa fa-angle-left"></i>')
                ]);
             echo '</div>';
         }
     }
     ?>
</section>
<section id="send" class="commentFormArea">


    <div class="commentFormWrap">
        <?php if( $boolAvalible ): ?>
        <div class="ttlArea">
            <h1>アンケートに答える</h1>
            <p>
                <span class="red">※</span>は必須項目になります。
            </p>
        </div>
        <form action="" id="formComment" method="POST">
            <ul class="answerInpotList" >
                <li>
                    <h3>ニックネーム<span class="red">※</span></h3>
                    <input required type="text" name="name" placeholder="ニックネームを入力してください">
                </li>
                <?php if($profile_require): ?>
                <li class="user_pro">
                    <!-- <h3>よくある質問</h3> -->
                    <div class="list-option-profile">
                        <ul>
                        	<?php if($profile_require['baby_sex']): ?>
                        	<li class="baby_sex">
                        		<h3 class="radio_h4">お子さんの性別</h3>
                        		<label>
                        			<input type="radio" value="male" name="profile[baby_sex]" required>男の子
                        		</label>
                        		<label>
                        			<input type="radio" value="female" name="profile[baby_sex]" required>女の子
                        		</label>
                        	</li>
                        	<?php endif; ?>
                        	<?php if($profile_require['baby_age']): ?>
                        	<li class="user_baby_age">
                        		<h3>お子さんの年齢</h3>
                        		<label>
                        			<input type="number" name="profile[baby_year]" required min="0" max="20">歳
                        		</label>
                        		<label>
                        			<input type="number" name="profile[baby_month]" required min="0" max="11">ヶ月
                        		</label>
                        	</li>
                        	<?php endif; ?>
                        	<?php if($profile_require['parent_sex']): ?>
                        	<li class="user_parent">
                        		<h3 class="radio_h4">回答する人</h3>
                        		<label>
                        			<input type="radio" value="mother" name="profile[parent]" required>ママ
                        		</label>
                        		<label>
                        			<input type="radio" value="father" name="profile[parent]" required>パパ
                        		</label>
                        	</li>
                        	<?php endif; ?>
                        	<?php if($profile_require['parent_age']): ?>
                        	<li class="user_parent_age">
                        		<h3>回答する人の年齢</h3>
                        		<label>
                        			<input type="number" name="profile[parent_age]" required min="0" max="99">歳
                        		</label>
                        	</li>
                        	<?php endif; ?>
                        	<?php if ($profile_require['style']) : ?>
                            <li class="user-option-profile-style">
                                <h3 class="radio_h4">あなたのタイプは</h3>
                                <?php foreach ($list_tyles as $style) : ?>
                                <label><input type="radio" name="profile[style]" value="<?php echo $style; ?>" required /><?php echo $style; ?></label>
                                <?php endforeach;?>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>
                
                <?php 
                if($questions[$post->ID]){
                    foreach ($questions[$post->ID] as $qkey => $question) {
                        $required = isset($question['required'])?"required":"";
                        $star = isset($question['required'])?'<span class="red">※</span>':"";
                        if($question['type'] == 'checkbox'){
                            ?>
                            <li>
                                <h3><?=$question['question'].$star?></h3>
                                <div class="checkArea" >
                                    <?php foreach ($question['answer'] as $anskey => $ansval) {
                                        ?>
                                    <label>
                                        <input <?=$required?> value="<?=$anskey?>" name="answer[<?=$qkey?>][]" type="checkbox" id="option-<?=$anskey?>"><?=$ansval?>
                                    </label>
                                        <?php
                                    } ?>
                                </div>
                            </li>
                            <?php
                        }elseif($question['type'] == 'radio'){
                            ?>
                            <li>
                                <h3><?=$question['question'].$star?></h3>
                                <?php foreach ($question['answer'] as $anskey => $ansval) {
                                    ?>
                                    <label >
                                        <input <?=$required?> value="<?=$anskey?>" name="answer[<?=$qkey?>][]" type="radio" ><?=$ansval?>
                                    </label>
                                <?php
                                } ?>
                            </li>
                            <?php
                        }elseif($question['type'] == 'pulldown'){
                            ?>
                            <li>
                                <h3><?=$question['question'].$star?></h3>
                                <label for="select" class="selectArea">
                                    <select name="answer[<?=$qkey?>][]">
                                    <?php foreach ($question['answer'] as $anskey => $ansval) {
                                        ?>
                                        <option value="<?=$anskey?>"><?=$ansval?></option>
                                        <?php
                                    } ?>
                                    </select>
                                </label>
                            </li>
                            <?php
                        }elseif($question['type'] == 'textbox'){
                            ?>
                            <li>
                                <h3><?=$question['question'].$star?></h3>
                                <input <?= $required?> name="answer[<?=$qkey?>][textbox]" type="text" placeholder="回答を入力してください" >
                            </li>
                            <?php
                        }elseif($question['type'] == 'unit'){
                            ?>
                            <li>
                                <h3><?=$question['question'].$star?></h3>
                                <?php if($question['answer'][0]){?>
                                    <div class="answer_unit">
                                    <?php foreach ($question['answer'] as $anskey => $ansval) {
                                        ?>
                                            <?php if($ansval){?>
                                            	<label><input <?=$required?> name="answer[<?=$qkey?>][unit][]" type="number" min="0" placeholder="<?=$ansval?>" ><?=$ansval?></label>
                                            <?php
                                            } ?>
                                        <?php
                                    } ?>
                                    </div>
                                <?php
                                } ?>
                            </li>
                            <?php
                        }elseif($question['type'] == 'textarea'){
                            ?>
                            <li>
                                <h3><?=$question['question'].$star?></h3>
                                <textarea <?=$required?> name="answer[<?=$qkey?>][textarea]" placeholder="回答を入力してください" style="resize: vertical;"></textarea>
                            </li>
                            <?php
                        }
                    }
                } ?>
                
                <li>
                    <h3>コメント<span class="red">※</span></h3>
                    <p class="notes">
                        <?=($description != '')?$description:'ご自身の状況や良かった点、困った点などを具体的に書きましょう！
                        育児で困ってる方の参考になり共感ボタンをもらいやすくなります！
                        説明が難しい場合は画像などもあるとわかりやすいです。' ?>
                        
                    </p>
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
                    <?php
                    if( $boolAvalible ): ?>
                        <button type="submit" name="submitted" value="send" class="sendBtn">アンケートに回答する</button>
                    <?php else: ?>
                        <button type="submit" name="submitted" value="send" class="sendBtn btnDisable" disabled="disabled">回答締め切りました。</button>
                    <?php endif; ?> 
                </li>
            </ul>
        </form>
    <?php else: ?>
    <div align="center">回答を締め切りました。<br/>
ご回答ありがとうございました！</div>
        <?php endif ?>
    </div>
   
</section>
<script type="text/javascript">
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    var max_upload_picture = "<?php echo get_option('spc_options')['a_img_no']; ?>";

    jQuery('button[type=submit]').on('click',function(){
        $cbx_group = jQuery("input:checkbox[id^='option-']"); // name is not always helpful ;)
        $cbx_group.prop('required', true);
        if($cbx_group.is(":checked")){
          $cbx_group.prop('required', false);
        }

    });

    jQuery('#qaFilter').on('change',function(){
        <?php set_query_var( 'cpage', 0 );?>
        var target = jQuery(this);

        var sort = "<?php echo isset($_GET['comment_order_by']) ? $_GET['comment_order_by'] : ''; ?>";

        var get_sort = 'comment_order_by=' + sort;
        var get_filter = 'comment_filter_by=' + target.val();
        
        var listParam = window.location.pathname.split('/');
        var lastParam = listParam[listParam.length-1];
        var path = window.location.pathname;
        if(/^comment-page-[0-9]/g.test(lastParam)){
            path = window.location.pathname.replace('/'+lastParam,'');
        }
        var current_link = window.location.origin + path;
        if(sort.length>0) {
            current_link += '?';
            if(target.val().length>0){
                current_link += get_filter;
                current_link += '&' 
                current_link += get_sort;
            }else{
                current_link += get_sort;
            }
        }else{
            if(target.val().length>0){
                current_link += '?';
                current_link += get_filter;
            }
        }

        window.location = current_link;
    });

    jQuery('#qaSort').on('change',function(){
        var target = jQuery(this);

        var filter = "<?php echo isset($_GET['comment_filter_by']) ? $_GET['comment_filter_by'] : ''; ?>";

        var get_filter = 'comment_filter_by=' + filter;
        var get_sort = 'comment_order_by=' + target.val();
        
        var current_link = window.location.origin + window.location.pathname;
        
        if(filter.length>0) {
            current_link += '?';
            if(target.val().length>0){
                current_link += get_sort;
                current_link += '&' 
                current_link += get_filter;
            }else{
                current_link += get_filter;
            }
        }else{
            if(target.val().length>0){
                current_link += '?';
                current_link += get_sort;
            }
        }

        window.location = current_link;
    });
</script>
<script src="<?php echo SPCV_CUSTOME_PLUGIN_URI; ?>views/js/notice-board.js"></script>
<?php add_comment_on_questions(get_the_ID()); ?>