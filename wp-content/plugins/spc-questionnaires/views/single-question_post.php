<?php if( cf_is_mobile()) : ?>
	<?php include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/single-question_post-sp.php' ); ?>
<?php else : ?>
	<?php include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/single-question_post-pc.php' ); ?>
<?php endif; ?>
