<?php
/*
Template Name: List NoticeBoard
*/
?>
<?php $nowthemes = get_template_directory(); ?>
<?php if( cf_is_mobile()) : ?>
	<?php
		$nowthemesfile = file_exists($nowthemes.'/page-notice-sp.php');
		if ($nowthemesfile) {
            include_once($nowthemes.'/page-notice-sp.php');
        } else {
            include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/page-notice-sp.php' );
        }
	?>
<?php else : ?>
	<?php
		$nowthemesfile = file_exists($nowthemes.'/page-notice-pc.php');
		if ($nowthemesfile) {
            include_once($nowthemes.'/page-notice-pc.php');
        } else {
            include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/page-notice-pc.php' );
        }
	?>
<?php endif; ?>
