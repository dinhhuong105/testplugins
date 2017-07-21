<?php
global $post;
global $comment;
$form_options = get_option('wprc_form_settings');
$permissions = get_option('wprc_permissions_settings');
$required_fields = $form_options['required_fields'];
$reasons = explode("\n", $form_options['report_reasons']);
?>
<div class="wprc-content">
	<input type="hidden" class="post-id" value="<?php echo $post->ID; ?>">
	<input type="hidden" class="comment-id" value="<?php comment_ID() ?>">
	<span class="wprc-submit"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>通報する</span>
	<img class="loading-img" style="display:none;"
				 src="<?php echo plugins_url('static/img/loading.gif', dirname(__FILE__)); ?>"/>
</div>
