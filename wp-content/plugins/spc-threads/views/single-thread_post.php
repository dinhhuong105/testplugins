<?php $nowthemes = get_template_directory(); ?>
<?php if( cf_is_mobile()) : ?>
	<?php
		if (file_exists($nowthemes.'/single-thread_post-sp.php')) {
            include_once($nowthemes.'/single-thread_post-sp.php');
        } else {
            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/single-thread_post-sp.php' );
        }
	?>
<?php else : ?>
	<?php
		if (file_exists($nowthemes.'/single-thread_post-pc.php')) {
            include_once($nowthemes.'/single-thread_post-pc.php');
        } else {
            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/single-thread_post-pc.php' );
        }
	?>
<?php endif; ?>