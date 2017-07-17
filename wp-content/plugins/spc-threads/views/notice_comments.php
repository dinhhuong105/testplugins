<?php if( cf_is_mobile()) : ?>
	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/notice_comments-sp.php' ); ?>
<?php else : ?>
	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/notice_comments-pc.php' ); ?>
<?php endif; ?>
