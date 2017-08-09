<?php
/*
Template Name: Add Thread
*/
?>
<?php $spc_option = get_option('spc_options'); ?>
<?php $nowthemes = get_template_directory(); ?>
<?php if( $spc_option['allowpost']) :?>
<?php
	wphd_thread_include_page_template('add-thread');
?>
<?php else : ?>
	<?php get_template_part('404'); ?>
<?php endif; ?>