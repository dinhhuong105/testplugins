<?php
global $post;
$id = isset($post->ID)?$post->ID:$_GET['post'];
$param = array(
    'post_id'=> $id
);
$post = get_post($id);
$comments = get_comments($param);
$answer = array();
$comment_metas = array();
$question = array();
$number_answer = 0;
foreach ($comments as $comment) {
	$comment_metas[] = get_comment_meta($comment->comment_ID,'_question_comment',true);
}

foreach ($comment_metas as $key => $answ) {
	
	if($answ){
		$number_answer +=1;
		foreach ($answ as $id_ques => $ans_detail) {
			if(isset($question[$id_ques])){
				array_push($question[$id_ques],$ans_detail);
			}else{
				$question[$id_ques][0] = $ans_detail;
			}
		}
	}
}
$report_ans = array();
// global $post_metas;
$post_metas = get_post_meta($id, '_question_type', TRUE);

foreach ($question as $key => $value) {
	$_ans = array();
	foreach ($value as $v) {
		
		foreach ($v as $type => $answer) {
		    if($type === 'unit'){

		        $list_unit = $post_metas[key($post_metas)][$key]['answer'];
                $answer_string = '';

                if (strlen($answer[0]) > 0) {
                    $answer_string .= $answer[0] . $list_unit[0];
                }

                if (strlen($answer[1]) > 0) {
                    $ext = (strlen($answer[0]) > 0) ? ' ' : '';
                    $answer_string .= $ext . $answer[1] . $list_unit[1];
                }

		        array_push($_ans,$answer_string); 
		    }else
		      array_push($_ans,$answer);
		}
		
	}
	
	$report_ans[$key] = array_count_values($_ans);
}

$_limited_answer = get_metadata('post', $id, '_limited_answer');
$_unpublish_answer = get_metadata('post', $id, '_unpublish_answer');
$csv = array();

$count_comment =  count($comments);
?>
<style type="text/css">
	.report{
	    padding: 20px;
    	border: 1px solid #ccc;
	}
	.report ul li{
		padding-left: 20px;
		list-style-type: decimal;
	}
	.page-title-action{
	    margin-left: 4px;
	    padding: 4px 8px;
	    position: relative;
	    top: -3px;
	    text-decoration: none;
	    border: none;
	    border: 1px solid #ccc;
	    -webkit-border-radius: 2px;
	    border-radius: 2px;
	    background: #f7f7f7;
	    text-shadow: none;
	    font-weight: 600;
	    font-size: 13px;
	    cursor: pointer;
	}
	.page-title-action:hover {
	    border-color: #008EC2;
	    background: #00a0d2;
	    color: #fff;
	}
	.postbox{
		padding: 20px;
		margin-top: 20px;
		position:relative;
	}
	.btn{
		position: absolute;
		right: 20px;
	}
	h3.header-box{
		
	    padding: 15px 10px;
	    background-color: #0073aa;
	    margin-bottom: 0px;
	    color: #fff;

	}
	ul.info-box{
	    border: 1px solid #0073aa;
    	padding: 15px;
    	margin-top: 0px;
	}
	li.content-post img{
		width: 90%;
		height: auto;
		margin: 10px auto;
	}
</style>
<?php if($post_metas): ?>
<div class="row postbox" id="revisionsdiv">
	<div class="btn">
		<span id="loading"></span>
		<button class="btn-limit  page-title-action" data-post="<?=$id?>" data-status="<?php echo ($_unpublish_answer[0] == 1) ? 0: 1; ?>" 
		<?php
		$m = ($_limited_answer[0] < 0 )?$_limited_answer[0]*-1:$_limited_answer[0];
		if($count_comment < $m || empty($_limited_answer[0]) || $_unpublish_answer == 0) {
			//show
		}else{
			echo 'disabled="disabled"';
		} ?>
		><?php echo (isset($_unpublish_answer[0]) && $_unpublish_answer[0] == 1) ? '回答受付中' : '停止中'; ?></button>
		<button class="btn-public page-title-action" data-post="<?=$id?>" data-status="<?=get_post_status($id)?>" ><?=(get_post_status($id) == 'publish')?'公開停止':'公開中'?></button>
	</div>
<h2 class="hndle ui-sortable-handle"><span>アンケート詳細</span></h2>
<h3 class="header-box"><?=get_the_title( $id );?></h3>
	<ul class="info-box">
		<li class="report content-post">
			<label>アンケートの内容</label><br/><b><?=$post->post_content;?></b>
		</li>
		<li class="report">
			<label>回答数</label><br/><b><?=$number_answer?></b>件
		</li>
		<?php 
		$stt=1;
		foreach ($post_metas[$id] as $key => $value): 
			$csv[$key]['question'] = $value['question'];
		?>
			<li class="report">
				<label>設問 <?=$stt++?></label><br/>
				<h2 class="hndle ui-sortable-handle"><?=$value['question']?></h2><br/>
				<ul>
				<?php 
				if(isset($value['answer']) && $value['type'] !== 'unit') :
					foreach ($value['answer'] as $k_ques => $ans): 
						$csv[$key][$ans] = isset($report_ans[$key][$k_ques]) ? $report_ans[$key][$k_ques] : 0;
						?>
						<li><?=$ans?> ... <?php echo isset($report_ans[$key][$k_ques]) ?  $report_ans[$key][$k_ques] : 0; ?></li>
				<?php endforeach;
				else: 
					if($report_ans[$key]):
					 foreach ($report_ans[$key] as $answer => $count): 
						$csv[$key][$answer] = $count;
					?>
						<li><?=$answer?> ... <?=$count?></li>
					<?php endforeach; 
					endif;
				endif;
				?>
				</ul>
			</li>
		<?php endforeach; ?>
		<div class="exportCSV"> 
			<a class="page-title-action" href="/wp-admin/admin-post.php?action=exportcsv&post=<?=$id?>"> CSV出力 </a>
		</div>
	</ul>
</div>

<?php endif;
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.btn-limit').on('click',function(e){
		e.preventDefault();
		var post_id = $(this).attr('data-post');
		var status = $(this).attr('data-status');
		var $button = $(this);
		$button.parent().find('#loading').append('処理...');
		$button.attr("disabled", true);
		$.ajax({
			type: 'POST',
			url : ajaxurl,
			data:{
				'action' : 'limited_comment',
				'post_ID' : post_id,
				'status' : status
			},
			success:function(res){
				if(res['status'] == 1){
					$button.html('回答受付中');
					status = 0;
				}else{
					$button.html('停止中');
					status = 1;
				}
				
				$button.attr('data-status',status);
				$button.attr("disabled", false);
				$button.parent().find('#loading').empty();
			}
		});
	});
	$('.btn-public').on('click',function(e){
		// return;
		e.preventDefault();
		var post_id = $(this).attr('data-post');
		var status = $(this).attr('data-status');
		var $button = $(this);
		$button.parent().find('#loading').append('処理...');
		$button.attr("disabled", true);
		$.ajax({
			type: 'POST',
			url : ajaxurl,
			data:{
				'action' : 'post_status',
				'post_ID' : post_id,
				'status' : status
			},
			success:function(res){
				if(res['status'] == 'publish'){
					$button.html('公開停止');
				}else{
					$button.html('公開中');
				}
				$button.attr('data-status',res['status']);
				$button.attr("disabled", false);
				$button.parent().find('#loading').empty();
			}
		});
	});

	// Scroll to comment
	var comment_id_scroll = <?php if(isset($_GET['comment_id_scroll'])) echo $_GET['comment_id_scroll']; else echo 0?>;
	if(comment_id_scroll > 0){
		if($("#comment_"+comment_id_scroll).offset()){
    		$('html, body').animate({
    		        scrollTop: $("#comment_"+comment_id_scroll).offset().top - 32
    		    }, 2000);
		}
	}
});
</script>

