<style type="text/css">
	.wp_comment_list{
		overflow-x: scroll;
	}
	.wp_comment_list tbody tr:nth-child(odd){
		background-color: #f9f9f9;
	}
	.wp_comment_list tbody tr td{ 
        padding: 5px 10px;
    }
	.wp_comment_list thead th{
		min-width: 130px;
		padding: 7px 0 7px 8px;
		border-top: 1px solid #ccc;
		border-bottom: 1px solid #ccc;
		text-align: left;
	}
	.wp_comment_list tbody tr td:last-child,
	.wp_comment_list thead th:last-child{
        text-align: center;
	}
	.pagination-links{
	    text-align: right;
		margin: 20px;

	}
	.exportCSV{
		text-align: right;
		margin: 20px;
	}
	.pagination-links .page-numbers{
		display: inline-block;
	    min-width: 17px;
	    border: 1px solid #ccc;
	    padding: 7px 5px 7px;
	    background: #e5e5e5;
	    font-size: 14px;
	    line-height: 1;
	    font-weight: 400;
	    text-align: center;
	    text-decoration: none;
	}
	.small {
	    height: 26px;
	    overflow:hidden;
	}
	.big {
	    height: auto;
	}
	#the-list .wrapper img{
	    width: 80px;
	    height: auto;
	    float: left;
	    margin-right: 5px;
	}
	.wp_comment_list tr.reported td {
		background: #f99fc5;
	}
</style>
<?php 
	define('DEFAULT_COMMENTS_PER_PAGE',20);
	$id=isset($post->ID)?$post->ID:$_GET['post'];

	$page = isset($_GET['paged']) ? $_GET['paged'] : 1; 
	// $page=2;
	$limit = DEFAULT_COMMENTS_PER_PAGE;

	$offset = ($page * $limit) - $limit;

	$param = array(
        'orderby' => 'comment_date_gmt',
        'order' => 'DESC',
        'offset'=>$offset,
	    'post_id'=>$id,
	    'number'=>$limit,

	);
	$total_comments = get_comments(
	        array(
                'orderby' => 'comment_date_gmt',
	            'order' => 'DESC',
	            'post_id'=>$id,
	            'parent'=>0
        	)
        );

	$pages = ceil(count($total_comments)/DEFAULT_COMMENTS_PER_PAGE);
	$comments = get_comments($param );

	// get list comment id by post id
	$report_comments_id = get_list_report_comment_id_by_post_id($id);
	$comment_no = get_post_meta($id, '_question_comment_no', true);
	
	$args = array(

	'base'         => @add_query_arg('paged','%#%'),

	'format'       => '?paged=%#%',

	'total'        => $pages,

	'current'      => $page,

	'show_all'     => False,

	'end_size'     => 1,

	'mid_size'     => 2,

	'prev_next'    => True,

	'prev_text'    => __('‹'),

	'next_text'    => __('›'),

	'type'         => 'plain');

	$question_meta = get_post_meta($id, '_question_type', TRUE);
	// echo "<pre>";print_r($question_meta); echo "</pre>";
	
?>
<div class="wp_comment_list postbox">
<h2 class="hndle ui-sortable-handle"><span>回答一覧</span></h2>
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th>順番</th>
			<th>
				回答日
			</th>
			<th>
				ニックネーム
			</th>
			<?php
				if (!empty($question_meta[$id])) {
					for ($i=1; $i <= count($question_meta[$id]); $i++) { 
						?>
						<th>
							設問<?=$i?>の回答 
						</th>
						<?php
					}
				}
			?>
			<th style="min-width: 200px;">
				コメント
			</th>
			<th>
				削除
			</th>
		</tr>
	</thead>
	<tbody id="the-list">
		<?php foreach ($comments as $comment): ?>
		<?php $class_report = (in_array($comment->comment_ID, $report_comments_id)) ? 'reported' : ''; ?>

		<tr id="comment_<?=$comment->comment_ID?>" class="<?php echo $class_report; ?>">
			<td><?php echo ($comment_no && isset($comment_no[$comment->comment_ID])) ? 'No.' . $comment_no[$comment->comment_ID] : ''; ?></td>
			<td><?=$comment->comment_date?></td>
			<td><?=$comment->comment_author?></td>
			<?php

				$comment_metas = get_comment_meta($comment->comment_ID,'_question_comment',TRUE);
				if (!empty($question_meta[$id])) :
				foreach ($question_meta[$id] as $kQuestion => $question) {
			?>
					<td>
						<?php
						if(isset($comment_metas[$kQuestion])){
							$first = true;
						    foreach ($comment_metas[$kQuestion] as $key => $answer) {

								if($question['type'] == 'unit'){
								    $list_unit = $question['answer'];
								    $answer_string = '';

								    if (strlen($answer[0]) > 0) {
                                        $answer_string .= $answer[0] . $list_unit[0];
                                    }

                                    if (strlen($answer[1]) > 0) {
                                        $ext = (strlen($answer[0]) > 0) ? ' ' : '';
                                        $answer_string .= $ext . $answer[1] . $list_unit[1];
                                    }

								    echo $answer_string;
								} elseif ($question['type'] == 'textbox') {
									echo $answer;
								} elseif (isset($question['other']) && $question['other'] == 'on') {
									if (array_search('other', $comment_metas[$kQuestion]) !== false) {
										echo $comment_metas[$kQuestion]['other'];
										continue(2);
									} else if (isset($question['answer'][$answer])) {
										echo $question['answer'][$answer];
									}
									
								} else if ((!isset($question['other']) || $question['other'] != 'on') && empty($comment_metas[$kQuestion]['other'])) {
								    if($first) {
									    echo isset($question['answer'][$answer]) ? $question['answer'][$answer] : '';
								    } else if (isset($question['answer'][$answer])) {
								        echo ', ' .$question['answer'][$answer];
								    }
								}

								$first = false;
							}
						}else{
							echo "---";
						}
						?>
					</td>
					<?php
				}
				endif;
			?>
			<td>
				<div class="wrapper">
    				<div class="small">
						<?=$comment->comment_content?> 
					</div>
					<?php if(strlen($comment->comment_content) > 65): ?>
					<a href="#" style="line-height: 25px;">もっと見る</a>
					<?php endif?>
				</div>
			</td>
			<td><button class="btn-public-comment page-title-action" data-status="<?=$comment->comment_approved?>" data-comment="<?=$comment->comment_ID?>"><?=($comment->comment_approved)?'公開停止':'公開中'?></button><span class="loading"></span></td>
		</tr>
		<?php endforeach ?>
		
		
	</tbody>
</table>
</div>

<div class="pagination-links">
	<?=paginate_links( $args )?>
</div>
<?php 
	wp_enqueue_script('jquery'); 
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.wrapper').on('click','a[href=#]', function (e) {
	    e.preventDefault();
	    if($(this).closest('.wrapper').find('.small').hasClass('big')){
	    	$(this).html('もっと見る');
	    }else{
	    	$(this).html('短くする');
	    }
	    $(this).closest('.wrapper').find('.small').toggleClass('big');
	    return false;
	});

	$('#the-list').on('click','.btn-public-comment',function(e){
		e.preventDefault();
		var comment_ID = $(this).attr('data-comment');
		var status = $(this).attr('data-status');
		var $button = $(this);
		$button.parent().find('.loading').append('処理...');
		$button.attr("disabled", true);
		$.ajax({
			type: 'POST',
			url : ajaxurl,
			data:{
				'action' : 'update_comment',
				'comment_ID' : comment_ID,
				'status' : status
			},
			success:function(res){
				if(status == 1){
					$button.html('公開中');
					$button.attr('data-status',0);
				}else{
					$button.html('公開停止');
					$button.attr('data-status',1);
				}
				$button.attr("disabled", false);
				$button.parent().find('.loading').empty();
			}
		});
	});
});
</script>