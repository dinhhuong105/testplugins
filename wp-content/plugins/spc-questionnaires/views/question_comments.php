<?php $nowthemes = get_template_directory(); ?>
<?php if( cf_is_mobile()) : ?>
	<?php
		$nowthemesfile = file_exists($nowthemes.'/question_comments-sp.php');
		if ($nowthemesfile) {
            include_once($nowthemes.'/question_comments-sp.php');
        } else {
            include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/question_comments-sp.php' );
        }
	?>
<?php else : ?>
	<?php
		$nowthemesfile = file_exists($nowthemes.'/question_comments-pc.php');
		if ($nowthemesfile) {
            include_once($nowthemes.'/question_comments-pc.php');
        } else {
            include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/question_comments-pc.php' );
        }
	?>
<?php endif; ?>
