<?php
/*
Template Name: List NoticeBoard
*/
?>
<?php if( cf_is_mobile()) : ?>
	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/page-notice-sp.php' ); ?>
<?php else : ?>
	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/page-notice-pc.php' ); ?>
<?php endif; ?>
