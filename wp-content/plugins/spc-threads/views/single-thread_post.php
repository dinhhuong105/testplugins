<?php if( cf_is_mobile()) : ?>
	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/single-thread_post-sp.php' ); ?>
<?php else : ?>
	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/single-thread_post-pc.php' ); ?>
<?php endif; ?>
