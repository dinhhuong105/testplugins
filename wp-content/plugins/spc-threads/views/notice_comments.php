<?php $nowthemes = get_template_directory(); ?>
<?php if( cf_is_mobile()) : ?>
	<?php
		if (file_exists($nowthemes.'/notice_comments-sp.php')) {
            include_once($nowthemes.'/notice_comments-sp.php');
        } else {
            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/notice_comments-sp.php' );
        }
	?>
<?php else : ?>
	<?php
		if (file_exists($nowthemes.'/notice_comments-pc.php')) {
            include_once($nowthemes.'/notice_comments-pc.php');
        } else {
            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/notice_comments-pc.php' );
        }
	?>
<?php endif; ?>
