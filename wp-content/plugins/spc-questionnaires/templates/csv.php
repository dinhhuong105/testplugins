<?php
function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   // fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}


$param = array(
    'post_id'=>$post->ID
);
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
$post_metas = get_post_meta($_GET['post'],'_question_type', TRUE);
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
            
            if ($answer_string) {
              array_push($_ans,$answer_string);
            }
		    } else {
		      array_push($_ans,$answer);
        }
		}
		
	}
	
	$report_ans[$key] = array_count_values($_ans);
}

$csv = array();
$no = 1;
 foreach ($post_metas[$_GET['post']] as $key => $value){
	$csv[$key]['設問 '.$no++] = $value['question'];
	if(isset($value['answer']) && $value['type'] != 'unit'){ 
		foreach ($value['answer'] as $k_ques => $ans){
			$csv[$key][$ans] = $report_ans[$key][$k_ques];
		}
	}else{
		foreach ($report_ans[$key] as $answer => $count){
			$csv[$key][$answer] = $count;
		}
	}
}

$a = array ();

foreach ($csv as $value) {
    foreach ($value as $k => $v) {
        array_push($a,array($k,$v));
    }
}

/**
 * Fix csv encoding
 *
 * @author Edward <duc.nguyen@spc-vn.com>
 * @date 2017-05-04
 */

$str_csv=array2csv($a);

ob_clean(); // Clear all specific string before render downloading
download_send_headers("data_export_" . date("Y-m-d") . ".csv");
echo "\xEF\xBB\xBF"; // UTF-8 BOM
echo $str_csv;
die();