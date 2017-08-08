<?php $nowthemes = get_template_directory(); ?>
<?php if( cf_is_mobile()) : ?>
	<?php
		$nowthemesfile = file_exists($nowthemes.'/single-question_post-sp.php');
		if ($nowthemesfile) {
            include_once($nowthemes.'/single-question_post-sp.php');
        } else {
            include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/single-question_post-sp.php' );
        }
	?>
<?php else : ?>
	<?php
		$nowthemesfile = file_exists($nowthemes.'/single-question_post-pc.php');
		if ($nowthemesfile) {
            include_once($nowthemes.'/single-question_post-pc.php');
        } else {
            include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/single-question_post-pc.php' );
        }
	?>
<?php endif; ?>
