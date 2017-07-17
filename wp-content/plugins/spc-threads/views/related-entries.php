<?php if( cf_is_mobile()) : ?>
	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/related-entries-sp.php' ); ?>
<?php else : ?>
	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/related-entries-pc.php' ); ?>
<?php endif; ?>
