<?php
/*
Template Name: List NoticeBoard
*/
?>
<?php $nowthemes = get_template_directory(); ?>
<?php if( cf_is_mobile()) : ?>
	<?php
		if (file_exists($nowthemes.'/page-notice-sp.php')) {
            include_once($nowthemes.'/page-notice-sp.php');
        } else {
            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/page-notice-sp.php' );
        }
	?>
<?php else : ?>
	<?php
		if (file_exists($nowthemes.'/page-notice-pc.php')) {
            include_once($nowthemes.'/page-notice-pc.php');
        } else {
            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/page-notice-pc.php' );
        }
	?>
<?php endif; ?>
