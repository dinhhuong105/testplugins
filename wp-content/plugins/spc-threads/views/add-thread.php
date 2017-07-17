<?php
/*
Template Name: Add Thread
*/
?>
<?php $spc_option = get_option('spc_options'); ?>
<?php if( $spc_option['allowpost']) :?>
    <?php if( cf_is_mobile()) : ?>
    	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/add-thread-sp.php' ); ?>
    <?php else : ?>
    	<?php include_once( WPHD_THREAD_PLUGIN_DIR . 'views/add-thread-pc.php' ); ?>
    <?php endif; ?>
<?php else : ?>
	<?php get_template_part('404'); ?>
<?php endif; ?>