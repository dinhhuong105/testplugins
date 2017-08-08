<?php $nowthemes = get_template_directory(); ?>
<?php if( cf_is_mobile()) : ?>
	<?php
		if (file_exists($nowthemes.'/related-entries-sp.php')) {
            include_once($nowthemes.'/related-entries-sp.php');
        } else {
            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/related-entries-sp.php' );
        }
	?>
<?php else : ?>
	<?php
		if (file_exists($nowthemes.'/related-entries-pc.php')) {
            include_once($nowthemes.'/related-entries-pc.php');
        } else {
            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/related-entries-pc.php' );
        }
	?>
<?php endif; ?>
