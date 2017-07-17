<?php if( cf_is_mobile()) : ?>
	<?php include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/question_comments-sp.php' ); ?>
<?php else : ?>
	<?php include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/question_comments-pc.php' ); ?>
<?php endif; ?>
