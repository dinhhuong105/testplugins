<?php 
	$_question_description = get_post_meta($post->ID, '_question_description', TRUE);
	$_limited_answer = get_metadata('post', $post->ID, '_limited_answer');
	$_unpublish_answer = get_metadata('post', $post->ID, '_unpublish_answer');
	$profile_require = get_post_meta($post->ID, '_question_profile_require', TRUE);
	$post_metas = get_metadata('post', $post->ID, '_question_type');
	$GLOBALS['post_metas'] = $post_metas[0];
	
	$unit1 = get_option('spc_options')['list_unit1'];
	if($unit1){
	   $list_unit1 = @explode(',', $unit1);
	}else{
	    $list_unit1 = array();
	}
	$unit2 = get_option('spc_options')['list_unit2'];
	if($unit2){
	   $list_unit2 = @explode(',', $unit2);
	}else{
	    $list_unit2 = array();
	}

	$count_comment = wp_count_comments($post->ID);
?>
<style type="text/css">
	li.ui-state-default{
		padding: 20px;
		border: 1px solid #ccc;
		margin: 10px 0; 
		position: relative;
		min-height: 90px;
	}
	.btn-group{
		position: absolute;
		right: 5px;
		top: 5px;
		display: none;
		cursor: pointer;
	}
	.btn-add{
	    display: inline-block;
	    border: 1px solid #eee;
	    padding: 6px 0 4px;
	    margin-top: 10px;
	    margin-left: 24px;
	    width: 170px;
	    text-align: center;
	}
	.btn-add[data-type=pulldown]{
		margin-left: 0px;
	}
	
	.btn-remove{
		display: none;
	}
	li.ui-state-default:hover .btn-remove,
	li.ui-state-default:hover .btn-group{
		display: inline-block;
	}
	.btn-group a{
		display: block;
	    padding: 1px 3px;
	    border: 1px solid #f1f1f1;
	}
	.row{
		padding: 10px 0;
	}
	.dragged {
	  position: absolute;
	  opacity: 0.5;
	  z-index: 2000;
	}
	div#ui-sortabl div.box-question {
	  position: relative;
	  /** More li styles **/
	}
	
	a.btn.btn-restore span{
		font-size: 16px;
		vertical-align: middle;
	}

</style>

<div id="frm_question" class="meta-box-sortables ui-sortable">
	<ul id="sortable">
	<?php 
		if($post_metas[0]){
			foreach ($post_metas[0] as $key => $post_meta) {
				foreach ($post_meta as $id => $meta) {
					$check = isset($meta['required'])?"checked='checked'":"";
					if($meta['type'] == 'checkbox'){
						echo '<li class="ui-state-default">';
						echo '<div class="box-question holddiv" id="ques'.$id.'">
						<div class="btn-group">
							<a class="btn_first btn"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
							<a class="btn_up btn"><span class="dashicons dashicons-arrow-up"></span></a>
							<a class="btn_trash"><span class="dashicons dashicons-no-alt"></span></a>
							<a class="btn_down btn"><span class="dashicons dashicons-arrow-down"></span></a>
							<a class="btn_last btn"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
							<input type="hidden" name="_sort_question[]" value="'.$id.'"/>
						</div>';
						echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
						echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目 </label>';
						echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><label> 必須 :  <input type="checkbox" name="question['. $key .']['. $id .'][required]" '.$check.' ></label><br/>';
						$i=0;
						foreach ($meta['answer'] as $answer) {
							echo '<input type="checkbox" name="posid_'. $key .'_answer_'. $id .'_' . $i . '"> 
							<input type="text" name="question['. $key .']['. $id .'][answer]['.$i.']" value="'.$answer.'">
							<a class="btn btn-remove" title="Remove"><span class="dashicons dashicons-trash"></span></a> 
							<br/>';
							$i++;
						}
						echo '</div><a class="btn btn-add" data-id="'. $id .'" data-type="checkbox"><span class="dashicons dashicons-plus"></span></a></li>';
					}elseif($meta['type'] == 'radio'){
						echo '<li class="ui-state-default">';
						echo '<div class="box-question holddiv" id="ques'.$id.'">
						<div class="btn-group">
							<a class="btn_first btn"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
							<a class="btn_up btn"><span class="dashicons dashicons-arrow-up"></span></a>
							<a class="btn_trash"><span class="dashicons dashicons-no-alt"></span></a>
							<a class="btn_down btn"><span class="dashicons dashicons-arrow-down"></span></a>
							<a class="btn_last btn"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
							<input type="hidden" name="_sort_question[]" value="'.$id.'"/>
						</div>';
						echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
						echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目</label>';
						echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><label> 必須 :  <input type="checkbox" name="question['. $key .']['. $id .'][required]" '.$check.' ></label><br/>';
						$i=0;
						foreach ($meta['answer'] as $answer) {
							echo '<input type="radio" name="posid_'. $key .'_answer_'. $id .'"> 
							<input type="text" name="question['. $key .']['. $id .'][answer]['.$i.']" value="'.$answer.'">
							<a class="btn btn-remove" title="Remove"><span class="dashicons dashicons-trash"></span></a> 
							<br/>';
							$i++;
						}
						echo '</div><a class="btn btn-add" data-id="'. $id .'" data-type="radio"><span class="dashicons dashicons-plus"></span></a></li>';
					}elseif($meta['type'] == 'pulldown'){
						echo '<li class="ui-state-default">';
						echo '<div class="box-question holddiv" id="ques'.$id.'">
						<div class="btn-group">
							<a class="btn_first btn"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
							<a class="btn_up btn"><span class="dashicons dashicons-arrow-up"></span></a>
							<a class="btn_trash"><span class="dashicons dashicons-no-alt"></span></a>
							<a class="btn_down btn"><span class="dashicons dashicons-arrow-down"></span></a>
							<a class="btn_last btn"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
							<input type="hidden" name="_sort_question[]" value="'.$id.'"/>
						</div>';
						echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
						echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目</label>';
						echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><label> 必須 :  <input type="checkbox" name="question['. $key .']['. $id .'][required]" '.$check.' ></label><br/>';
						$i=0;
						foreach ($meta['answer'] as $answer) {
							echo '<input type="text" name="question['. $key .']['. $id .'][answer]['.$i.']" value="'.$answer.'">
							<a class="btn btn-remove" title="Remove"><span class="dashicons dashicons-trash"></span></a> 
							<br/>';
							$i++;
						}
						echo '</div><a class="btn btn-add" data-id="'. $id .'" data-type="pulldown"><span class="dashicons dashicons-plus"></span></a></li>';
					}elseif($meta['type'] == 'unit'){
						echo '<li class="ui-state-default">';
						echo '<div class="box-question holddiv" id="ques'.$id.'">
						<div class="btn-group">
							<a class="btn_first btn"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
							<a class="btn_up btn"><span class="dashicons dashicons-arrow-up"></span></a>
							<a class="btn_trash"><span class="dashicons dashicons-no-alt"></span></a>
							<a class="btn_down btn"><span class="dashicons dashicons-arrow-down"></span></a>
							<a class="btn_last btn"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
							<input type="hidden" name="_sort_question[]" value="'.$id.'"/>
						</div>';
						echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
						echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目</label>';
						echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><label> 必須 :  <input type="checkbox" name="question['. $key .']['. $id .'][required]" '.$check.' ></label><br/>';
						$unit = '';
						$unit .= '<select required class="option1_enable" name="question['. $key .']['. $id .'][answer][0]">';
						$unit .= '<option value="">単位</option>';
						foreach($list_unit1 as $unit_value){
						    $is_selected = ($unit_value == $meta['answer'][0])?'selected':'';
						    $unit .= '<option value="'.$unit_value.'"'.$is_selected.'>'.$unit_value.'</option>';
						}
						$unit .= '</select>';
						$is_disable = ($meta['answer'][0])?'':'disabled';
						$unit .= '<select ' . $is_disable . ' name="question['. $key .']['. $id .'][answer][1]">';
						$unit .= '<option value="">単位</option>';
						foreach($list_unit2 as $unit_value){
						    $is_selected = ($unit_value == $meta['answer'][1])?'selected':'';
						    $unit .= '<option ' . $is_disable .' value="'.$unit_value.'"'.$is_selected.'>'.$unit_value.'</option>';
						}
						$unit .= '</select>';
						echo $unit;
						echo '</div></li>';
					}elseif($meta['type'] == 'textarea'){
						echo '<li class="ui-state-default">';
						echo '<div class="box-question holddiv" id="ques'.$id.'">
						<div class="btn-group">
							<a class="btn_first btn"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
							<a class="btn_up btn"><span class="dashicons dashicons-arrow-up"></span></a>
							<a class="btn_trash"><span class="dashicons dashicons-no-alt"></span></a>
							<a class="btn_down btn"><span class="dashicons dashicons-arrow-down"></span></a>
							<a class="btn_last btn"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
							<input type="hidden" name="_sort_question[]" value="'.$id.'"/>
						</div>';
						echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
						echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目</label>';
						echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><label> 必須 :  <input type="checkbox" name="question['. $key .']['. $id .'][required]" '.$check.' ></label><br/>';
						echo '</div></li>';
					}elseif($meta['type'] == 'textbox'){
					    echo '<li class="ui-state-default">';
					    echo '<div class="box-question holddiv" id="ques'.$id.'">
						<div class="btn-group">
							<a class="btn_first btn"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
							<a class="btn_up btn"><span class="dashicons dashicons-arrow-up"></span></a>
							<a class="btn_trash"><span class="dashicons dashicons-no-alt"></span></a>
							<a class="btn_down btn"><span class="dashicons dashicons-arrow-down"></span></a>
							<a class="btn_last btn"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
							<input type="hidden" name="_sort_question[]" value="'.$id.'"/>
						</div>';
					    echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
					    echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目</label>';
					    echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><label> 必須 :  <input type="checkbox" name="question['. $key .']['. $id .'][required]" '.$check.' ></label><br/>';
					    echo '</div></li>';
					}
					
				}
			}
		}
	?>
	</ul>
<?php wp_enqueue_script( array(">jquery", "jquery-ui-core", "interface", "jquery-ui-sortable", "wp-lists", "jquery-ui-sortable") ); ?>
	
</div>
<label for="link_download">アンケート項目 </label>
<select name="question_type">
	<option value="">設問タイプ</option>
	<option value="radio">ラジオボタン</option>
	<option value="checkbox">チェックボックス</option>
	<option value="pulldown">プルダウン</option>
	<option value="textbox">テキストボックス</option>
	<option value="textarea">テキストエリア</option>
	<option value="unit">単位のみ</option>
</select>
<input type="number" name="no_of_item" value="2" placeholder="（項目数）">
<button class="btn_create">作成</button>
<hr>
<div class="row">
	<label> コメント説明欄 :  <input type="text" name="ques_description" value="<?=$_question_description?>" style="width: 70%"></label>
</div>
<hr>
<label for="limited_answer">リミット回答数 ></label>
<input <?php echo ($_unpublish_answer[0]) ? 'disabled="disabled"' : '' ; ?> min="0" type="number" name="limited_answer" id="limited_answer" value="<?php echo $_limited_answer[0]; ?>" placeholder="回答数を入力"><label> 件 <?php echo ($_unpublish_answer[0]) ? ' &nbsp; <strong>(停止中)</strong>' : '' ; ?></label>
<?php 
	wp_enqueue_script('jquery'); 
?>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
var post_id = <?=$post->ID?>;
var list_unit1 = <?php echo json_encode(get_option('spc_options')['list_unit1']); ?>;
var arr_unit1 = [];
if(list_unit1){
	arr_unit1 = list_unit1.split(',');
}
var list_unit2 = <?php echo json_encode(get_option('spc_options')['list_unit2']); ?>;
var arr_unit2 = [];
if(list_unit2){
	arr_unit2 = list_unit2.split(',');
}

jQuery(document).ready(function($){
	var id_frm = $.now();
	var count_comment = <?=$count_comment->all?>;
	$('.btn_create').on('click',function(e){
		e.preventDefault();
		var id = id_frm++;
		var number_question = $('input[name=no_of_item]').val();
		if(number_question <= 0) { $('input[name=no_of_item]').focus(); return false; }
		var selected = $('select[name=question_type] :selected').val();
		var btnAdd = '<a class="btn btn-add" data-id="'+ id +'" data-type="'+ selected +'"><span class="dashicons dashicons-plus"></span></a>';

		var str = '';
			str += '<div class="box-question">';
			str += '<div class="btn-group"><a class="btn_first btn"><span class="dashicons dashicons-arrow-up-alt2"></span></a><a class="btn_up btn"><span class="dashicons dashicons-arrow-up"></span></a><a class="btn_trash"><span class="dashicons dashicons-no-alt"></span></a><a class="btn_down btn"><span class="dashicons dashicons-arrow-down"></span></a><a class="btn_last btn"><span class="dashicons dashicons-arrow-down-alt2"></span></a><input type="hidden" name="_sort_question[]" value="'+id+'"/></div>';
			str += hidden($('select[name=question_type] :selected').val(),id);
			str += question_input(id,1);
			str += "<br/><br/>"
		if( selected == 'checkbox'){
			str += checkbox( id,number_question );
		}else if( selected == 'radio'){
			str += radio( id,number_question );
		}else if( selected == 'pulldown'){
			str += pulldown( id,number_question );
		}else if( selected == 'textbox'){
			btnAdd = '';
			str += textbox(id,number_question);
		}else if( selected == 'textarea'){
			btnAdd = '';
		}else if(selected == 'unit'){
			btnAdd = '';
			str += unit(id,number_question);
		}else{
			return;
		}

		str += '</div>'+btnAdd;
		$('#frm_question ul').append('<li class="ui-state-default">'+str+'</li>');

	});

	$('select[name=question_type]').on('change',function(){
		if($(this).val() == 'textbox' || $(this).val() == 'textarea' || $(this).val() == 'unit'){
			$('input[name=no_of_item]').attr('disabled','disabled');
		}else{
			$('input[name=no_of_item]').removeAttr('disabled');
		}
	});

	$( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();

    $('#frm_question').on('click','li a.btn_up',function(){
	  var current = $(this).closest('li.ui-state-default');
	  console.log(current);
	  current.prev().before(current);
	});

	$('#frm_question').on('click','li a.btn_first',function(){
	  var current = $(this).parents('li');
	  var prevli = current.prev();
	  while(prevli.length > 0){
	 	current.prev().before(current);
	  	prevli = current.prev();
	  }
	  $('html, body').animate({ scrollTop: $("#frm_question").offset().top - 200 }, 200);
	});

	/* button remove */
	$('#frm_question').on('click','.btn-remove',function(){
		var currentInput = $(this).prev('input');
		currentInput.attr('disabled','disabled');
		$(this).html('<span class="dashicons dashicons-image-rotate"></span>');
		$(this).removeClass('btn-remove');
		$(this).addClass('btn-restore').attr('title','Undo');
	});

	/* button restore */
	$('#frm_question').on('click','.btn-restore',function(){
		var currentInput = $(this).prev('input');
		currentInput.removeAttr('disabled');
		$(this).html('<span class="dashicons dashicons-trash"></span>');
		$(this).removeClass('btn-restore');
		$(this).addClass('btn-remove').attr('title','Remove');
	});

	/* button add */
	$('#frm_question').on('click','.btn-add',function(){
		var id = $(this).attr('data-id');
		var number_question = 1;
		var type = $(this).attr('data-type');
		var customID = $(this).closest('li.ui-state-default').find('input[type=text]').length - 1;// minus textfile qusetion.
		if(type == 'checkbox'){
			$(this).closest('li.ui-state-default').children('.box-question').append(checkbox( id,number_question,customID ));
		}else if(type == 'radio'){
			$(this).closest('li.ui-state-default').children('.box-question').append(radio( id,number_question,customID ));
		}else if(type == 'pulldown'){
			$(this).closest('li.ui-state-default').children('.box-question').append(pulldown(id,number_question,customID));
		}else{
			return;
		}
	});

	$('#frm_question').on('click','li a.btn_down',function(){
	  var current = $(this).closest('li.ui-state-default');
	  current.next().after(current);
	});

	$('#frm_question').on('click','li a.btn_last',function(){
	  var current = $(this).parents('li');
	  var nextli = current.next();
	  while(nextli.length > 0){
	 	current.next().after(current);
	  	nextli = current.next();
	  }
	  $('html, body').animate({ scrollTop: $("#frm_question").offset().top + $("#frm_question").outerHeight(true)-200  }, 200);
	});

	$(function () {
		if(count_comment > 0)
	     $('a.btn.btn-remove').hide();
	 });


});
jQuery(document).on('click', '.btn_trash', function($){
	var res = confirm('あなたはそれを削除したいですか？');
		if(res) jQuery(this).closest('li').remove();
		
});
jQuery(document).on("change", 'select.option1_enable',function(e){
	var target = jQuery(this);
	if(!target.val()){
		target.next().val('');
		target.next().prop("disabled", true);
	}else{
		target.next().prop("disabled", false);
	}
});

function question_input($id,$multi = 1){
	var $str = '';
	//posid_'+ post_id +'_question_' + $id + '
	for(var i=0; i<$multi; i++){
		$str += '<label for="posid_'+ post_id +'_question_' + $id + '">アンケート項目 </label><input id="posid_'+ post_id +'_question_' + $id + '" type="text" name="question['+ post_id +']['+ $id +'][question]" required> <label> 必須 :  <input type="checkbox" name="question['+post_id+']['+ $id +'][required]" checked="checked" ></label>';
	}
	return $str;
}

function checkbox($id,$multi, $customID = null){
	var $str = '';
	for(var i=0; i<$multi; i++){
		if($customID != null) i = $customID;
		$str += '<input type="checkbox" name="posid_'+ post_id +'_answer_'+ $id +'_' + i + '"> <input type="text" name="question['+ post_id +']['+ $id +'][answer]['+i+']"><a class="btn btn-remove" title="Remove"><span class="dashicons dashicons-trash"></span></a> <br/>';
	}
	return $str;
}

function radio($id,$multi, $customID = null){
	var $str = '';
	for(var i=0; i<$multi; i++){
		if($customID != null) i = $customID;
		$str += '<input type="radio" name="posid_'+ post_id +'_answer_'+ $id +'"> <input type="text" name="question['+ post_id +']['+ $id +'][answer]['+i+']"><a class="btn btn-remove" title="Remove"><span class="dashicons dashicons-trash"></span></a> <br/>';
	}
	return $str;
}

function pulldown($id, $multi, $customID = null){
	var $str = '';
	for(var i=0; i<$multi; i++){
		if($customID != null) i = $customID;
		$str += '<input id="posid_'+ post_id +'_answer_' + $id + '" type="text" name="question['+ post_id +']['+ $id +'][answer]['+i+']"><a class="btn btn-remove" title="Remove"><span class="dashicons dashicons-trash"></span></a> <br>';
	}
	return $str;
}

function unit($id, $multi = 1){
	var textbox_html = '';
	textbox_html += '<select required class="option1_enable" name="question['+ post_id +']['+ $id +'][answer][]">';
	textbox_html += '<option value="">単位</option>';
	for(var j in arr_unit1){
		textbox_html += '<option value="'+arr_unit1[j]+'">'+arr_unit1[j]+'</option>';
	}
	textbox_html += '</select>';
	textbox_html += '<select disabled name="question['+ post_id +']['+ $id +'][answer][]">';
	textbox_html += '<option value="">単位</option>';
	for(var j in arr_unit2){
		textbox_html += '<option value="'+arr_unit2[j]+'">'+arr_unit2[j]+'</option>';
	}
	textbox_html += '</select>';
	return textbox_html;
}

function textarea($id, $multi = 1){
	return '<textarea name="question['+ post_id +']['+ $id +'][answer]['+i+']"></textarea><br>';
}
function textbox($id, $multi = 1){
	return '';
}

function hidden($type,$id){
	return '<input type="hidden" name="question['+ post_id +']['+ $id +'][type]" value="'+$type+'">';
}
</script>
