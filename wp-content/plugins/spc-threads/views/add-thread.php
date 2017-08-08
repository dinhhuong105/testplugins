<?php
/*
Template Name: Add Thread
*/
?>
<?php $spc_option = get_option('spc_options'); ?>
<?php $nowthemes = get_template_directory(); ?>
<?php if( $spc_option['allowpost']) :?>
    <?php if( cf_is_mobile()) : ?>
    	<?php
			if (file_exists($nowthemes.'/add-thread-sp.php')) {
	            include_once($nowthemes.'/add-thread-sp.php');
	        } else {
	            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/add-thread-sp.php' );
	        }
    	?>
    <?php else : ?>
    	<?php
			if (file_exists($nowthemes.'/add-thread-pc.php')) {
	            include_once($nowthemes.'/add-thread-pc.php');
	        } else {
	            include_once( WPHD_THREAD_PLUGIN_DIR . 'views/add-thread-pc.php' );
	        }
    	?>
    <?php endif; ?>
<?php else : ?>
	<?php get_template_part('404'); ?>
<?php endif; ?>